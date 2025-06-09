# Changelog - InventoriKu

Semua perubahan penting pada projek ini akan didokumenkan dalam fail ini.
Format fail ini berdasarkan [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
dan projek ini cuba mematuhi [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.4.1] - 2025-06-04 

### Added
- Menambah ikon visual untuk status privasi (Umum/Peribadi) di sebelah nama item pada paparan senarai item (mod jadual dan mod kad).

## [1.4.0] - 2025-06-04 

### Added
- fungsi baru untuk Admin untuk menukar/memindahkan pemilikan item

### Changed
- tiada

## [1.3.1] - 2025-06-03 

### Added
- tiada

### Changed
- nama pada butang dipendekkan supaya sesuai semasa paparan di skrin telefon pintar

## [1.3.0] - 2025-06-02 

### Added
- tiada

### Changed
- membenarkan semua pengguna boleh menguruskan Kategori & Lokasi sendiri
- disamping dapat melihat semua Kategori & Lokasi yang di ciipta oleh Admin dan pengguna lain.
- semua dropdown Kategori & Lokasi akan memberi keutamaan paparan senarai yang dicipta oleh pengguna sendiri.
- view untuk urus Kategori & Lokasi akan sentiasa memberi keutamaan paparan senarai yang dicipta oleh pengguna sendiri.

## [1.2.0] - 2025-05-15 

### Added
- Implementasi pemilikan untuk Kategori dan Lokasi (lajur `owner_user_id`).
- Pengguna biasa kini boleh menambah Kategori dan Lokasi baru (akan jadi miliknya).
- `CategoryPolicy` dan `LocationPolicy` ditambah untuk mengawal akses edit/padam Kategori & Lokasi berdasarkan pemilikan atau peranan Admin.
- Panggilan kebenaran (`authorize` atau `Gate::allows`) ditambah pada `CategoryController` dan `LocationController`.
- Direktif `@can` pada butang Edit/Padam dalam view senarai Kategori & Lokasi dikemas kini untuk menggunakan Policy.

### Changed
- Laluan (routes) untuk Kategori dan Lokasi dialihkan dari group Admin ke group pengguna umum (jika berkenaan) untuk membolehkan akses pengguna biasa.
- Pautan navigasi untuk Kategori/Lokasi dikemas kini untuk membenarkan akses pengguna biasa (jika berkenaan).

## [1.1.1] - 2025-05-14 (Mencipta halaman utama (/reports))

### Added
- Mencipta halaman utama (/reports) untuk menyenaraikan semua laporan yang tersedia.
- Mengemas kini pautan navigasi "Laporan" untuk menghala ke halaman utama laporan baru.
- Menambah definisi laporan (nama, deskripsi, route) dalam `ReportController` untuk paparan dinamik di halaman utama laporan.

## [1.0.1] - 2025-05-14 (Penambahbaikan V1)

Ini adalah penambahbaikan kecil aplikasi InventoriKu.

### Added (Ciri Baru Ditambah)

* **Antaramuka Pengguna (UI) & Pengalaman Pengguna (UX):**
    * Tambah button baru 'Dashboard' pada Rekod Pergerakan Baru: Pilih Item.
    * Tambah button baru 'Imbas' pada Rekod Pergerakan Baru: Pilih Item.
    * Tambah button baru 'Imbas' pada Tambah Item Baru.

## [1.0.0] - 2025-05-09 (Tarikh Pelancaran V1.0.0)

Ini adalah keluaran stabil pertama aplikasi InventoriKu.

### Added (Ciri Baru Ditambah)

* **Pengurusan Pengguna Asas:**
    * Pendaftaran pengguna baru.
    * Log masuk & Log keluar.
    * Kemas kini profil pengguna (nama, emel, kata laluan).
    * Sistem pengesahan emel (menggunakan Mailgun untuk produksi).
    * Fungsi "Lupa Kata Laluan".
* **Pengurusan Kategori:**
    * CRUD penuh (Tambah, Lihat, Edit, Padam) untuk kategori item.
    * Validasi input (nama wajib & unik).
    * Sekatan pemadaman jika kategori masih digunakan oleh item.
* **Pengurusan Lokasi:**
    * CRUD penuh (Tambah, Lihat, Edit, Padam) untuk lokasi item.
    * Validasi input (nama wajib & unik).
    * Sekatan pemadaman jika lokasi masih digunakan oleh item.
* **Pengurusan Item Teras:**
    * CRUD penuh (Tambah, Lihat, Edit, Padam) untuk item inventori.
    * Medan-medan: Nama, Deskripsi, Kategori, Lokasi, Tarikh Beli, Harga Beli, Kuantiti, Nombor Siri, Tarikh Luput Jaminan, Status.
    * Fungsi muat naik dan paparan **lampiran PDF** (resit/manual) untuk setiap item (menyokong PDF, Teks, Dokumen Word & Excel).
    * Penambahan medan untuk **Kod Bar Produk** (EAN/UPC) pada item.
* **Pengurusan Pelbagai Gambar Item:**
    * Sokongan muat naik sehingga 5 gambar untuk setiap item.
    * Fungsi untuk menetapkan satu gambar sebagai "Gambar Utama".
    * Fungsi untuk menambah dan memadam gambar individu semasa mengedit item.
    * Paparan galeri gambar menggunakan **Lightbox/Modal (Fancybox)** di halaman butiran item.
    * Paparan thumbnail gambar utama di halaman senarai item.
    * Fail gambar fizikal dipadam dari storan apabila gambar atau item dipadam.
* **Penjejakan Pergerakan Item (Manual):**
    * Borang untuk merekodkan pelbagai jenis pergerakan item (PINDAH, GUNA, PINJAM, PULANG, ROSAK, HILANG, LUPUS, dll.).
    * Logik untuk mengemas kini status, lokasi, dan kuantiti item secara automatik berdasarkan jenis pergerakan.
    * Paparan sejarah pergerakan terperinci pada halaman butiran setiap item.
* **Penjanaan Kod QR:**
    * Setiap item mempunyai Kod QR unik yang dijana secara automatik (mengandungi URL ke halaman butiran item).
    * Kod QR dipaparkan pada halaman butiran item.
* **Pengimbasan Kod QR & Kod Bar Produk:**
    * Halaman pengimbas (`/scan`) menggunakan kamera peranti.
    * Imbasan Kod QR item akan menghala terus ke borang rekod pergerakan untuk item tersebut (jika pengguna dibenarkan lihat item).
    * Imbasan Kod Bar Produk akan mencari item dalam pangkalan data:
        * Jika jumpa & dibenarkan lihat: Menghala ke halaman butiran item.
        * Jika tidak jumpa: Menghala ke borang tambah item baru dengan medan kod bar diisi secara automatik.
* **Laporan Asas:**
    * Laporan Item Yang Sedang Dipinjam (mengikut skop privasi).
    * Laporan Item Mengikut Lokasi (mengikut skop privasi).
* **Kawalan Akses Berasaskan Peranan (RBAC) & Polisi:**
    * Pemasangan dan konfigurasi pakej `spatie/laravel-permission`.
    * Definisi Peranan (Roles) awal: 'Admin' dan 'User'.
    * Definisi Kebenaran (Permissions) asas (cth: `manage-categories`, `edit items`).
    * Peranan 'Admin' diberikan semua kebenaran.
    * Perlindungan route untuk modul Kategori & Lokasi (hanya Admin).
    * Paparan pautan navigasi dan butang tindakan (Edit/Padam) secara bersyarat menggunakan `@can` dan Polisi.
    * Implementasi `ItemPolicy` untuk kawalan akses terperinci berdasarkan pemilikan (`owner_user_id`) dan peranan Admin untuk tindakan edit/padam item.
* **Ciri Privasi Item:**
    * Item boleh ditetapkan sebagai 'Private' (hanya pemilik & Admin boleh lihat/urus) atau 'Public' (semua pengguna boleh lihat).
    * Lalai adalah 'Private' semasa menambah item baru.
    * Logik ini diaplikasikan pada senarai item, butiran item, dashboard, laporan, dan fungsi imbasan.
* **Antaramuka Pengguna (UI) & Pengalaman Pengguna (UX):**
    * Penggunaan Bootstrap 5 untuk reka bentuk responsif.
    * Layout aplikasi yang konsisten (`layouts/app.blade.php`).
    * Penggunaan Komponen Blade untuk elemen borang.
    * Paginasi dengan gaya Bootstrap 5.
    * Navbar dan bar butang tindakan tetap di bawah (`fixed-top`, `fixed-bottom`).
    * Ikon Bootstrap pada butang-butang.
    * Paparan imej placeholder untuk item tanpa gambar.
    * Baris jadual senarai item yang boleh diklik untuk ke halaman butiran.
    * Paparan mesej flash (success, error) yang diperkemas.
    * Fungsi susunan (sorting) pada jadual senarai item.
    * Footer dengan nombor versi aplikasi.
* **Progressive Web App (PWA) Asas:**
    * Pemasangan dan konfigurasi pakej `erag/laravel-pwa`.
    * Penjanaan `manifest.json` dan `service-worker.js` asas.
    * Konfigurasi ikon PWA sebenar.
    * Aplikasi boleh "dipasang" ke skrin utama dan mempunyai halaman luar talian asas.
* **Pengurusan Pentadbiran (Admin UI):**
    * CRUD penuh untuk Pengguna (termasuk penetapan peranan).
    * CRUD penuh untuk Peranan (termasuk penetapan kebenaran).
    * Paparan senarai Kebenaran (Permissions).
    * Fungsi untuk Admin mengesahkan emel pengguna secara manual.
* **Lain-lain:**
    * Persediaan server VPS dari kosong (Ubuntu, Apache, MySQL, PHP, Node, Composer, Git).
    * Deployment menggunakan Git.
    * Konfigurasi HTTPS menggunakan Certbot (Let's Encrypt).
    * Strategi backup asas (skrip `mysqldump` & `tar` ke Google Drive) dan ujian pemulihan bencana.

### Changed (Perubahan pada Fungsi Sedia Ada)

* Model `User` diubah suai untuk melaksanakan `MustVerifyEmail` dan menggunakan trait `HasRoles`.
* `ItemController` dan view berkaitan diubah suai secara meluas untuk menyokong pelbagai gambar dan ciri privasi.
* `DashboardController` dan `ReportController` dikemas kini untuk mematuhi skop privasi item.
* Fail `config/app.php`, `config/mail.php`, `routes/web.php`, `layouts/app.blade.php`, `bootstrap/app.php` dikemas kini untuk menyokong ciri-ciri baru (PWA, Spatie Permissions, Email, dll.).

### Fixed (Pembetulan Pepijat)

* Pelbagai isu konfigurasi server dan aplikasi diselesaikan semasa proses pembangunan dan deployment (cth: kebenaran fail, cache, ralat Vite, ralat middleware alias, isu penghantaran emel).
* Isu paparan visual seperti penjajaran butang dan footer 'terapung' telah dibetulkan.

---
