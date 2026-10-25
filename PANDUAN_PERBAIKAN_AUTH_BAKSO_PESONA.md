# Panduan Perbaikan Bug Login Admin dan Pelanggan — Bakso Pesona

## Tujuan

Perbaiki bentrokan autentikasi dan otorisasi agar:

- `/login` hanya menerima akun pelanggan (`customer`).
- `/admin/login` hanya menerima akun admin (`admin`).
- Pelanggan tidak dapat masuk ke dashboard atau endpoint admin.
- Admin tidak dapat menggunakan fitur khusus pelanggan, seperti checkout dan membuat pesanan.
- Menu navbar menampilkan identitas dan tautan sesuai role.
- Struktur project dirapikan tanpa menghapus file/folder yang masih dibutuhkan.

> **Instruksi penting untuk developer/AI coding agent:** Audit implementasi yang benar-benar ada sebelum mengubah kode. Jangan mengasumsikan nama route, kolom role, middleware, guard, atau struktur tabel tanpa memeriksa project. Pertahankan desain UI, fitur produk, data, dan konfigurasi yang sudah berjalan. Jangan menulis ulang seluruh aplikasi hanya untuk memperbaiki autentikasi.

---

## 1. Diagnosis yang wajib diverifikasi

Gejala yang dilaporkan: admin yang login di panel admin tetap terlihat sebagai pengguna di halaman pelanggan; admin bisa menggunakan alur belanja; akun pelanggan dapat membuka panel admin tetapi tidak bisa mengubah data.

Kemungkinan penyebab yang harus diperiksa:

1. Controller login admin dan pelanggan sama-sama menggunakan guard default (`Auth::attempt()`, `Auth::user()`, `Auth::check()`), sehingga keduanya memakai identitas sesi yang sama.
2. Middleware autentikasi dan role tidak diterapkan konsisten pada seluruh route grup admin/pelanggan.
3. Route admin mungkin hanya memeriksa login, bukan memastikan `role === 'admin'`.
4. Route keranjang atau checkout mungkin berada di luar middleware pelanggan. Keranjang publik boleh jadi memang disengaja, tetapi operasi checkout/pembuatan pesanan harus memeriksa role dan kepemilikan.
5. Controller mungkin melakukan redirect berdasarkan `Auth::check()` sebelum memvalidasi role.
6. Nilai role di database, nama kolom, atau default role mungkin tidak konsisten.
7. Navbar mungkin hanya memeriksa `auth()->check()` dan tidak membedakan admin dari pelanggan.

Jangan menganggap seluruh poin di atas pasti menjadi penyebab sebelum membuktikannya melalui kode, `route:list`, dan data role.

## 2. Tetapkan kebijakan autentikasi

Pilih dan terapkan satu kebijakan secara konsisten:

- Gunakan tabel `users` dan kolom role yang sudah ada jika sesuai; jangan membuat tabel pengguna kedua tanpa alasan arsitektural yang kuat.
- Gunakan nilai role kanonis yang ditemukan di project. Jika project memakai `admin` dan `customer`, gunakan persis nilai tersebut.
- Login pelanggan hanya berhasil untuk role pelanggan.
- Login admin hanya berhasil untuk role admin.
- Setelah login berhasil, regenerasi session ID untuk mencegah session fixation.
- Saat login gagal, jangan mengungkapkan apakah email atau password tertentu terdaftar.
- Logout harus membersihkan autentikasi dan meregenerasi token sesi.
- Jangan pernah mengandalkan penyembunyian tombol di UI sebagai perlindungan. Semua endpoint harus memeriksa izin di server.
- Jangan menyimpan password dalam teks biasa atau mengubah password akun saat migrasi role.

### Pilihan sesi

**Pilihan A — satu sesi aktif per browser (implementasi lebih sederhana):** ketika berpindah role, logout dari sesi sebelumnya terlebih dahulu, kemudian login pada panel yang sesuai. Jangan mengklaim bahwa dua panel memiliki sesi independen.

**Pilihan B — admin dan pelanggan login bersamaan dalam browser yang sama:** perlu rancangan multi-guard/session yang benar-benar diuji. Guard berbeda saja belum tentu menghasilkan cookie/session browser yang terisolasi. Jangan menerapkan pilihan ini secara setengah-setengah. Jika belum ada kebutuhan wajib, gunakan Pilihan A.

## 3. Periksa route dan middleware

1. Audit `routes/web.php` dan semua file route lain yang didaftarkan aplikasi.
2. Jalankan `php artisan route:list -v` dan catat middleware setiap route.
3. Kelompokkan endpoint dengan jelas:
   - Publik: beranda, katalog, detail produk, halaman informasi.
   - Autentikasi pelanggan: profil, alamat, riwayat pesanan, checkout, pembuatan pesanan, dan pembayaran pelanggan.
   - Admin: dashboard, pengelolaan produk/kategori/pesanan, dan semua endpoint mutasi admin.
4. Terapkan middleware `auth` dan pemeriksaan role yang sesuai di server.
5. Pastikan route admin tidak hanya dilindungi oleh `auth`; harus ada pemeriksaan role admin.
6. Pastikan endpoint pelanggan menolak role admin, termasuk request POST/PATCH/PUT/DELETE langsung.
7. Periksa route duplikat, nama route duplikat, route lama, serta redirect login yang saling mengarah.
8. Setelah perubahan, jalankan `php artisan route:list -v` kembali. Pastikan nama route yang dipakai Blade/controller memang terdaftar.

### Matriks akses yang wajib dipenuhi

| Area | Tamu | Pelanggan | Admin |
|---|---:|---:|---:|
| Beranda/katalog/detail produk | Ya | Ya | Ya |
| Login pelanggan | Ya | Tidak perlu login ulang; arahkan sesuai kebijakan | Ditolak sebagai pelanggan |
| Login admin | Ya | Ditolak sebagai admin | Tidak perlu login ulang; arahkan ke dashboard admin |
| Profil/riwayat pesanan pelanggan | Tidak | Ya, hanya data miliknya | Ditolak |
| Checkout/buat pesanan | Ditolak atau diarahkan login | Ya | Ditolak |
| Dashboard dan CRUD admin | Ditolak | Ditolak | Ya |
| Mutasi data admin melalui request langsung | Ditolak | Ditolak | Ya, setelah validasi/otorisasi |
| Logout | — | Mengakhiri sesi | Mengakhiri sesi |

**Catatan keranjang:** bila keranjang tamu saat ini didukung, jangan merusaknya. Tamu boleh menyimpan keranjang bila memang desain aplikasi demikian. Namun, admin tidak boleh membuat pesanan atau melakukan checkout sebagai pelanggan. Jangan menghapus keranjang sesi secara global dengan cara yang merusak sesi lain.

## 4. Perbaiki controller login

Untuk setiap controller login:

1. Validasi input secara server-side.
2. Batasi login sesuai role yang benar. Pastikan akun dengan role yang salah tidak terautentikasi melalui panel tersebut.
3. Tangani pengguna yang sudah login secara eksplisit. Jangan hanya memeriksa `Auth::check()` lalu mengarahkan semua role ke tujuan yang sama.
4. Terapkan regenerasi sesi setelah autentikasi berhasil.
5. Gunakan redirect yang jelas berdasarkan role yang sudah diverifikasi.
6. Pastikan logout membersihkan sesi secara aman.
7. Hindari duplikasi logika role yang berbeda-beda antara controller, middleware, dan navbar. Buat pola yang konsisten dengan arsitektur Laravel yang sudah dipakai project.

Jangan menambahkan guard baru atau mengubah `config/auth.php` sebelum memahami guard/provider saat ini. Jika memakai guard terpisah, audit seluruh pemanggilan `Auth`, middleware, logout, reset password, dan route agar menggunakan guard yang tepat secara konsisten.

## 5. Perbaiki navbar dan tampilan role

- Gunakan pengguna terautentikasi yang benar dan periksa role sebelum menampilkan menu.
- Admin melihat tautan panel admin, bukan profil/checkout pelanggan.
- Pelanggan melihat profil, pesanan, dan keranjang sesuai kebijakan.
- Tamu melihat tautan login/daftar dan katalog.
- Jangan menampilkan nama admin sebagai pelanggan hanya karena `auth()->check()` bernilai benar.
- Tampilan bukan pengganti middleware; akses langsung ke URL tetap harus ditolak bila role salah.

## 6. Audit data role dengan aman

1. Periksa skema tabel pengguna dan nilai role yang benar-benar ada.
2. Buat cadangan database sebelum mengubah data.
3. Identifikasi akun admin dan pelanggan berdasarkan data yang dapat diverifikasi.
4. Perbaiki hanya nilai role yang terbukti salah; jangan mengubah semua pengguna secara massal.
5. Jangan mencetak, membagikan, atau meminta password asli.
6. Pastikan proses registrasi publik tidak memberi pengguna kemampuan memilih role admin.
7. Pembuatan akun admin harus melalui mekanisme tepercaya yang sudah ditentukan project.

## 7. Pengujian regresi wajib

Jalankan pengujian otomatis jika test suite tersedia dan lakukan pengujian manual berikut:

- [ ] Login admin melalui `/admin/login` berhasil dan menuju dashboard admin.
- [ ] Login pelanggan melalui `/login` berhasil dan menuju area pelanggan.
- [ ] Akun pelanggan mencoba login melalui panel admin: ditolak.
- [ ] Akun admin mencoba login melalui panel pelanggan: ditolak atau diarahkan sesuai kebijakan yang terdokumentasi, tanpa menjadi pelanggan.
- [ ] Pelanggan mengakses URL dashboard admin secara langsung: `403` atau redirect aman.
- [ ] Pelanggan mengirim request mutasi ke endpoint admin: ditolak.
- [ ] Admin mengakses checkout dan endpoint pembuatan pesanan: ditolak.
- [ ] Pelanggan hanya melihat pesanan miliknya sendiri.
- [ ] Pengguna yang belum login membuka profil/checkout: diarahkan ke login pelanggan.
- [ ] Logout benar-benar mengakhiri sesi; tombol Back tidak membuka halaman privat dengan data aktif.
- [ ] Navbar berubah sesuai role setelah login/logout.
- [ ] Fitur katalog, detail produk, keranjang tamu (jika didukung), dan checkout pelanggan tetap berfungsi.
- [ ] Validasi dan pesan error tetap berfungsi.
- [ ] Tidak ada route duplikat atau nama route yang hilang.
- [ ] Tidak ada error baru di `storage/logs/laravel.log`.

Gunakan browser private/incognito atau hapus cookie situs di antara skenario untuk membedakan masalah sesi lama dari bug kode. Untuk menguji dua akun, jangan hanya berganti tab pada sesi yang sama tanpa membersihkan sesi.

## 8. Perapian folder/file yang tidak terpakai — lakukan dengan aman

**Jangan menghapus folder hanya karena ukurannya besar atau namanya tidak familiar.** Folder yang tidak digunakan oleh aplikasi runtime bisa tetap diperlukan untuk pengembangan, build, Git, deployment, atau pemulihan.

### A. Umumnya jangan dihapus

Jangan hapus folder/file berikut tanpa alasan terverifikasi:

- `app/`, `bootstrap/`, `config/`, `database/`, `public/`, `resources/`, `routes/`, `storage/`, `tests/`
- `artisan`, `composer.json`, `composer.lock`, `package.json`, `package-lock.json` (atau lockfile package manager yang memang dipakai)
- `.env` (jangan dibagikan ke publik), `.env.example`
- `vendor/` bila belum siap menjalankan `composer install`
- `node_modules/` bila belum siap menjalankan instalasi package frontend
- `.git/` — jangan dihapus; berisi riwayat versi dan membantu rollback
- migration, seeder, factory, file konfigurasi, assets, atau view yang belum terbukti tidak dipakai.

### B. Folder yang boleh dipertimbangkan untuk dibersihkan

Hanya setelah memeriksa `.gitignore`, konfigurasi build, script package, dan alur kerja project:

- `node_modules/`: dapat dibuat ulang dengan `npm ci` jika lockfile tersedia dan sesuai.
- `vendor/`: dapat dibuat ulang dengan `composer install` jika Composer dan lockfile tersedia.
- `storage/framework/cache/*`, `storage/framework/views/*`, `storage/framework/sessions/*`: bersihkan hanya cache runtime yang aman; jangan menghapus struktur folder atau file penting yang dibutuhkan aplikasi.
- `bootstrap/cache/*.php`: cache framework yang dihasilkan dapat dibersihkan lewat perintah Artisan yang sesuai, bukan dengan menghapus folder `bootstrap`.
- File log lama di `storage/logs/`: hanya jika tidak dibutuhkan untuk diagnosis/audit; simpan log error terbaru terlebih dahulu.
- Folder eksperimen atau backup: hanya jika sudah dibandingkan dengan versi aktif, dipastikan tidak direferensikan, dan sudah ada cadangan/riwayat Git.

Folder `.claude/` pada arsip project berisi skill/instruksi untuk workflow bantuan coding. Itu bukan bagian dari runtime Laravel, tetapi bisa dibutuhkan oleh workflow developer/AI. **Jangan hapus otomatis**; pertahankan kecuali pemilik project memastikan tidak memerlukannya.

### C. Cara memutuskan sebelum menghapus

Untuk setiap kandidat folder/file:

1. Catat path dan tujuan folder.
2. Cari referensinya di `composer.json`, `package.json`, Vite config, file PHP/Blade, script, workflow CI, dan dokumentasi.
3. Periksa apakah masuk ke Git dan apakah dibutuhkan untuk build/deploy.
4. Buat backup atau pastikan dapat dipulihkan dari Git.
5. Utamakan memindahkan ke folder karantina sementara daripada menghapus permanen.
6. Jalankan tes, build frontend, dan alur utama aplikasi setelah pemindahan.
7. Hapus permanen hanya setelah semua pemeriksaan lulus.

Jangan menggunakan perintah penghapusan massal seperti `rm -rf` atau `Remove-Item -Recurse -Force` terhadap banyak folder tanpa daftar path yang sudah diverifikasi.

## 9. Perintah pemeriksaan setelah perubahan

Jalankan dari root project:

```bash
php artisan optimize:clear
php artisan route:list -v
php artisan test
npm run build
```

Jika project belum memiliki test atau script build, catat dengan jelas; jangan menganggap perintah gagal sebagai bukti bahwa kode autentikasi rusak. Jalankan `composer validate` bila sesuai. Setelah semua lolos, uji manual seluruh matriks akses di atas.

## 10. Kriteria selesai

Pekerjaan dianggap selesai hanya jika:

- Setiap panel menerima role yang sesuai.
- Endpoint terlindungi di server, bukan hanya di UI.
- Tidak ada admin yang dapat checkout/membuat pesanan sebagai pelanggan.
- Tidak ada pelanggan yang dapat membuka atau memutasi fitur admin.
- Sesi/logout dan navbar konsisten.
- Fitur lama tetap berfungsi.
- Test/build dan pengujian manual dilaporkan dengan hasil nyata.
- Daftar folder yang dibersihkan disertai alasan dan dampaknya.
- Tidak ada file atau data penting yang dihapus tanpa verifikasi.

## Instruksi laporan akhir

Setelah melakukan pekerjaan, laporkan:

1. Penyebab bug yang terbukti beserta file dan route terkait.
2. File yang diubah dan ringkasan perubahan.
3. File/folder yang dihapus atau dibersihkan beserta alasan; jika tidak ada yang aman dihapus, katakan demikian.
4. Hasil setiap test dan build yang benar-benar dijalankan.
5. Langkah manual yang perlu dilakukan pemilik project.

Jangan mengklaim tes lulus bila belum dijalankan. Jangan menghapus data database, file `.env`, `.git`, migration, atau folder inti Laravel untuk mencoba memperbaiki bug autentikasi.
