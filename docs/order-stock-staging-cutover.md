# Bakso Pesona: staging cutover for order stock workflow

This runbook is for a **dedicated staging database**, not production. It is not
authorization to run a migration. Obtain approval for the exact database,
maintenance window, backup, and operator before performing any write.

The workflow migration changes schema and backfills legacy order data, then
drops the legacy order triggers. MySQL/MariaDB DDL is not one atomic transaction:
a failed run can leave some schema changes or trigger changes behind.

## Current safety constraints

- The new checkout decrements product stock and creates a reservation in the
  same database transaction. Legacy trigger
  `trg_reduce_product_stock_on_order_item` also decrements stock after inserting
  an order item. Do not run old and new checkout writers concurrently.
- The new cancellation flow releases a reservation. Legacy trigger
  `trg_restore_product_stock_on_cancel` also restores stock on an order status
  update. Do not allow either mechanism to run alongside the other.
- Historical stock cannot be inferred reliably from order statuses alone. The
  legacy decrement trigger clamped values at zero, and unlogged stock changes
  may also have occurred.
- The workflow migration therefore marks old item reservations as
  `legacy_unreconciled`. It does not adjust product stock or declare old
  reservations consumed/released. Cancellation approval for these orders is
  blocked until stock reconciliation is completed.
- The old SQL installer can recreate the legacy stock triggers. Do not run
  `database/sql/bakso_pesona_advanced_db.sql` after cutover.
- Laravel recognizes `mysql` and `mariadb` as distinct configured driver names.
  The workflow now handles both; the target driver's DDL behavior still needs
  integration testing on the actual supported server version.

## Required inputs and owner decisions

Before preparing the migration, the business owner must provide or approve:

1. An authoritative physical stock count by product, with the count timestamp.
2. A list of products and any products no longer present in the catalog.
3. A complete list of legacy orders/items and their actual fulfillment and
   payment outcome, including cancelled, refunded, shipped, and completed
   orders.
4. Records of stock adjustments, sales outside the website, spoilage, returns,
   and any manual inventory corrections since the legacy trigger was introduced.
5. The date/time boundary at which inventory reconciliation is authoritative.
6. A reviewed method to reconcile each legacy item reservation as active,
   consumed, or released, with an actor, reason, and audit record.

If these records cannot establish the current stock, perform a physical count
and have the owner approve it as the new baseline. Do not derive it by summing
legacy order items or by treating the current database stock as proven.

## Preflight

1. Confirm the environment is an isolated staging database and record the
   database name, host identifier, server product/version, Laravel
   `DB_CONNECTION` driver name, and deployment revision. Do not put credentials
   in this record.
2. Verify the target is the intended staging database through the deployment
   platform or an approved read-only connection. Never infer the target from a
   repository `.env` file.
3. Record `php artisan migrate:status` and the current schema version.
4. On the target server, inspect `SHOW TRIGGERS`, `SHOW CREATE TRIGGER` for the
   three named triggers, and the definitions/presence of the legacy procedures
   and functions. Record whether they are installed; do not assume the migration
   history reflects live DB objects.
5. Check `information_schema.tables` for the storage engine of `users`,
   `products`, `orders`, `order_items`, and (if present) `order_status_logs`.
   Each transactional source table must use InnoDB. The migration explicitly
   creates its new workflow tables as InnoDB and refuses non-InnoDB legacy
   source tables.
6. Compare preflight counts and stock values to the approved reconciliation
   workbook. At minimum record product count and IDs/stocks, order count,
   order-item count, status-log count, and current named triggers.
7. Confirm migration DDL permissions, and confirm the operator can inspect
   trigger definitions and server engine metadata.
8. Confirm all code and configuration changes have been reviewed. Payment,
   WhatsApp, and QRIS deployment values must come from the business owner; no
   example values may be used.

## Backup and restore rehearsal

1. Take a transactionally consistent backup of the staging database using the
   organization's approved MySQL/MariaDB backup tool. Include schema, data,
   views, routines, events, and triggers. For a logical dump, verify the chosen
   options preserve routines and triggers and are appropriate to the server.
2. Record backup timestamp, server/version, database identity, backup location,
   and checksum in the restricted deployment record.
3. Restore the backup into a separate disposable clone. Verify tables, rows,
   routines, and triggers are present and the application can read the clone.
4. Do not proceed unless the restore rehearsal succeeds and the owner approves
   the recovery point.

## Cutover sequence

1. Schedule a maintenance window and notify testers. Block order/checkout and
   admin order/inventory writes at the ingress for every application node.
2. Enable application maintenance mode on all nodes as a secondary control.
   Drain in-flight web requests; stop queue workers, scheduled commands,
   external automation, and any operator scripts or direct SQL integrations
   that can write orders, order items, order statuses, or product stock.
3. Confirm there are no active writers or pending jobs. Maintenance mode on one
   web node alone is insufficient.
4. Deploy the reviewed code revision to every node **while writes remain
   blocked**. Do not let old and new application versions serve requests at the
   same time.
5. From the verified staging release and connection, run `php artisan
   migrate --force` once. Do not use `migrate:fresh`, `db:wipe`, or rollback as
   an automatic recovery step. Capture complete command output.
6. If the command fails or its outcome is uncertain, keep all writers stopped.
   Follow “Failure handling”; do not retry blindly.
7. After success, independently verify:
   - migration status includes the workflow migration;
   - all expected workflow tables, columns, indexes, and foreign keys exist;
   - new reservation and movement tables use InnoDB;
   - the three named legacy triggers are absent when inspected on the actual
     target server; run the cutover on separate clones for both `mysql` and
     `mariadb` driver configurations if both are supported operationally;
   - legacy item records are marked `legacy_unreconciled` and no stock value
     was changed by the backfill;
   - event/movement/reservation counts match the precomputed expectations;
   - order statuses were normalized only as specified;
   - product stock matches the owner-approved baseline/reconciliation.
8. Resolve legacy stock markers only through the reviewed reconciliation
   procedure. Do not start checkout while legacy orders requiring stock
   decisions remain unresolved.
9. Run the staging smoke tests below using clearly identified test products
   and test accounts. Verify resulting product stock, reservations, and
   movement records directly.
10. Re-enable workers and the scheduler only after the smoke tests. Remove the
    ingress write block and application maintenance mode only after the
    deployment owner signs off.

## Smoke tests and acceptance gates

- New checkout with a known stock quantity: product stock decreases once,
  exactly one active reservation and checkout movement are recorded, and an
  order/item is created.
- Retry the same checkout token: it returns the existing order without another
  stock decrement, reservation, or movement.
- Verify payment: reservation becomes consumed; product stock does not change.
- Cancel an eligible unpaid new order twice: only the first accepted operation
  releases stock; a repeated operation does not add stock again.
- Attempt to cancel a `legacy_unreconciled` order: application rejects it and
  does not alter stock.
- Confirm no named legacy triggers exist and do not run the legacy SQL
  installer.
- Complete customer/admin authorization, order details, payment approval,
  payment verification, refund, and rating workflow smoke tests.

Opening staging traffic is blocked until every gate passes. Production requires
a separate approval, production backup/restore rehearsal, owner-approved stock
reconciliation, and its own deployment plan.

## MySQL/MariaDB integration tests still required

SQLite tests do not prove server DDL, InnoDB row locking, deadlock behavior, or
trigger behavior. Run these tests on an isolated MySQL staging clone matching
the target server version, and repeat for MariaDB if that driver is supported:

1. Two separate database connections submit checkouts concurrently for one
   remaining unit; exactly one succeeds and stock never goes negative.
2. Concurrent checkouts include the same product and multiple products in
   different cart orders; verify transactions do not partially create orders,
   reservations, or movements and document deadlock retries.
3. Retry the same token concurrently and sequentially; only one order and one
   stock decrement are committed.
4. Submit cancellation concurrently/repeatedly; one reservation is released
   at most once and stock is restored once.
5. Submit payment verification concurrently/repeatedly; payment and reservation
   state are committed once and stock is not changed during consumption.
6. With each named legacy trigger installed, run the workflow migration only
   on a disposable clone and verify the migration removes them before the app
   accepts traffic. With the migration applied, verify checkout/cancellation
   does not execute legacy trigger effects.
7. Exercise migration failure points on a disposable clone: before DDL, after
   some new tables exist, after trigger removal, and during backfill. Verify the
   operator can identify partial state and restore the tested backup without
   opening writes.
8. Verify database users have the required privileges to inspect/drop named
   triggers and that lack of permission causes a visible migration failure.

Do not report these scenarios as passed until executed on the matching server
engine and version.

## Failure handling and recovery choices

Keep all application and external writers stopped whenever the migration
fails, is interrupted, or has uncertain output.

1. Preserve migration output and read-only evidence of migration-table state,
   table/column/index presence, trigger definitions, and row counts.
2. Do not run the migration again until the partial state is understood. DDL
   may have committed even though the migration is not recorded as complete;
   this migration's backfill is not a general-purpose restartable recovery
   command.
3. **Preferred recovery:** restore the complete verified pre-migration backup
   to the isolated staging target, then verify data, routines, and triggers
   before deploying the old compatible application version. This discards
   writes made after the backup, which is acceptable only in disposable
   staging and must be approved.
4. **Alternative:** prepare and review a forward-repair procedure on a clone
   that precisely matches the observed partial state. It must reconcile schema,
   backfills, trigger state, and migration history. Do not improvise SQL on the
   target.
5. Do not rely on `php artisan migrate:rollback`: its `down()` can be blocked
   after new workflow records exist and recreates the legacy triggers. Those
   triggers conflict with the new application if it remains active.
6. After restore or forward repair, rerun the entire preflight and smoke suite.
   Open writes only with explicit deployment-owner approval.

## Staging execution record

Record the following outside this repository in the restricted deployment
system: operator and approver, staging identity, server/driver version, code
revision, migration output, backup/restore proof, trigger inventory, stock
reconciliation approval, test results, and traffic reopen time.

## Payment configuration and payment cutover

The current application accepts QRIS only for new orders. Before enabling order
traffic, the owner must provide and independently verify:

- `BAKSO_PESONA_QRIS_IMAGE`: a path relative to `public/` for the official,
  static QRIS image. The application checks that a file exists; it cannot prove
  that the QR code belongs to the business.
- `BAKSO_PESONA_QRIS_MERCHANT_NAME`: the exact merchant identity shown by the
  official QRIS.
- `BAKSO_PESONA_WHATSAPP`: the verified business WhatsApp number, with country
  code or a supported Indonesian local format.
- `BAKSO_PESONA_CONTACT_EMAIL`, if the owner chooses to publish an email.

Do not use the logo, a sample image, a generated QR code, or a test asset as
the official QRIS. Keep these deployment values in the approved secret/config
system; never copy populated values into `.env.example` or this runbook. The
legacy bank-account environment keys are no longer used for new payment
instructions.

New checkout records the server-calculated subtotal and creates the stock
reservation, but leaves shipping cost, final total, and payment deadline
unset. Admin sets shipping; the server computes
`subtotal - discount_amount + shipping_cost`. Admin must open the WhatsApp
template, send it to the customer, then separately attest that it was actually
sent. Opening WhatsApp or generating the template does not persist an issue
time. A successful attestation stores the server time and a 24-hour deadline.
Every resend requires a reason and restarts the deadline from the recorded
send time; changing the total invalidates prior instructions and requires a
new actual send.

Check the customer phone is valid before sending. Compare the QRIS merchant
identity and exact final amount in the actual WhatsApp message before
attesting. The attestation is an operator record, not technical confirmation
from WhatsApp. Do not mark a payment paid from a screenshot or customer claim:
inspect the actual transaction, record its amount/reference and verification
note, and use manual review for late or mismatched payments. The scheduler only
flags expired payments for review; it does not mark them paid or release stock
automatically. A late payment marked “not received” during review cancels the
order and releases its reservation as an explicit audited admin action.

The application timezone is currently UTC. Database timestamps and scheduler
comparisons use that instant; admin/customer deadline displays convert to
`Asia/Jakarta` (WIB). Keep the scheduler enabled at one-minute intervals using
`php artisan schedule:run` from the platform scheduler, and verify its execution
and `withoutOverlapping` cache lock on every application environment. The
payment deadline begins only when the admin records the sent message, never at
checkout or when the WhatsApp link is opened.

The additive `discount_amount` migration must be included in staging preflight.
It defaults existing rows to zero; confirm its schema and the resulting total
calculation on the clone before enabling writes. Do not run a migration against
the current or production database as part of this code change. On a staging
clone, add the following payment-specific checks to the gates above:

1. Before send attestation and after merely opening the WhatsApp template,
   `payment_instructions_issued_at` and `payment_due_at` remain null.
2. Send attestation records server time, a deadline exactly 24 hours later,
   actor identity, and the audit event. A resend without a reason fails with
   no state change; a valid resend records its reason and replaces the deadline.
3. Change shipping after instructions were sent; confirm old instructions and
   deadline are invalidated and the prior/new total is preserved in audit
   history.
4. Compare valid, invalid, missing, and owner-approved QRIS/WhatsApp settings.
   Confirm no sample or nonexistent QR image enables the send action.
5. Test exact-amount verification, mismatched amount, duplicate verification,
   late exact payment, late mismatch, and explicit “not received” review. Check
   order status, payment status, audit events, reservations, and stock for each.
6. Confirm the dashboard counts and links match each actionable order filter,
   and that the low-stock link shows only available products at or below the
   configured threshold.
