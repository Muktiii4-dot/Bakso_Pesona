<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('discount_amount', 12, 2)->default(0)->after('subtotal');
        });
    }

    public function down(): void
    {
        if (DB::table('orders')->where('discount_amount', '>', 0)->exists()) {
            throw new RuntimeException('Discount data exists; refusing to drop discount_amount.');
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('discount_amount');
        });
    }
};
