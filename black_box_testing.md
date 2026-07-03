# Skenario Black-Box Testing (Equivalence Partitioning & Boundary Value Analysis)

Berikut adalah 30 skenario pengujian Black-Box untuk aplikasi E-Commerce Gaming Gear yang siap digunakan pada Bab IV Skripsi.

## A. Modul Autentikasi (Auth)

| No | ID Skenario | Skenario Pengujian | Data Uji | Hasil yang Diharapkan | Status |
|:---|:---|:---|:---|:---|:---|
| 1 | `TC-AUTH-01` | Registrasi dengan format email yang salah | Email: `buyer.com` | Muncul pesan error validasi email ("Email harus berupa alamat email yang valid") | Valid |
| 2 | `TC-AUTH-02` | Registrasi dengan password kurang dari 8 karakter | Pass: `12345` | Muncul pesan error panjang password minimal | Valid |
| 3 | `TC-AUTH-03` | Registrasi dengan konfirmasi password tidak cocok | Pass: `password`, Confirm: `12345678` | Muncul pesan error konfirmasi password tidak cocok | Valid |
| 4 | `TC-AUTH-04` | Registrasi berhasil dengan data yang valid | Nama: `Budi`, Email: `budi@demo.com`, Pass: `password` | Berhasil login dan dialihkan ke Beranda Buyer | Valid |
| 5 | `TC-AUTH-05` | Login dengan email yang tidak terdaftar | Email: `anon@demo.com`, Pass: `12345678` | Muncul pesan error kredensial tidak valid | Valid |
| 6 | `TC-AUTH-06` | Login dengan password yang salah | Email: `buyer01@gaminggear.com`, Pass: `salahpass` | Muncul pesan error kredensial tidak valid | Valid |
| 7 | `TC-AUTH-07` | Login sebagai Admin | Email: `admin@gaminggear.com`, Pass: `password` | Berhasil masuk ke Dashboard Admin | Valid |
| 8 | `TC-AUTH-08` | Login sebagai Buyer | Email: `buyer01@gaminggear.com`, Pass: `password` | Berhasil masuk ke Halaman Utama Buyer | Valid |
| 9 | `TC-AUTH-09` | Logout dari sistem | Klik tombol Logout | Berhasil keluar, sesi dihapus, dialihkan ke Halaman Utama (Guest) | Valid |
| 10 | `TC-AUTH-10` | Akses halaman Admin oleh Buyer (Otorisasi) | Mengakses URL `/admin/dashboard` dengan akun buyer | Ditolak (Redirect ke home / Forbidden 403) | Valid |

## B. Modul Manajemen Produk (Admin)

| No | ID Skenario | Skenario Pengujian | Data Uji | Hasil yang Diharapkan | Status |
|:---|:---|:---|:---|:---|:---|
| 11 | `TC-ADM-01` | Tambah Kategori tanpa memasukkan Nama Kategori | Form Nama dikosongkan | Pesan error validasi "Nama Kategori wajib diisi" | Valid |
| 12 | `TC-ADM-02` | Tambah Produk dengan format Harga berupa huruf | Harga: `Seratus Ribu` | Pesan error validasi "Harga harus berupa angka" | Valid |
| 13 | `TC-ADM-03` | Tambah Produk dengan file gambar bukan JPG/PNG | Gambar: `document.pdf` | Pesan error validasi format file gambar | Valid |
| 14 | `TC-ADM-04` | Tambah Produk dengan stok 0 | Stok: `0` | Produk tersimpan, tetapi dilabeli "Habis" di halaman buyer | Valid |
| 15 | `TC-ADM-05` | Berhasil menambahkan Produk baru dengan data valid | Data produk diisi lengkap dan gambar (JPG) diunggah | Produk muncul di daftar tabel produk Admin | Valid |
| 16 | `TC-ADM-06` | Edit Produk dan mengubah status menjadi Nonaktif | Uncheck `is_active` | Produk tidak tampil di Katalog Buyer | Valid |
| 17 | `TC-ADM-07` | Admin mengubah status Pesanan menjadi "Dikirim" (Shipped) | Klik Update Status menjadi "Shipped" | Status pesanan di buyer berubah, flash message sukses muncul | Valid |
| 18 | `TC-ADM-08` | Admin membatalkan (Cancel) Pesanan | Klik tombol "Batalkan Pesanan" pada order Pending | Status menjadi "Cancelled", stok produk kembali seperti semula | Valid |
| 19 | `TC-ADM-09` | Mengakses Export Laporan PDF tanpa ada data pesanan | Range tanggal filter diisi hari esok | File PDF tercetak kosong / menampilkan "Tidak ada transaksi" | Valid |
| 20 | `TC-ADM-10` | Lihat halaman Log Rekomendasi CF untuk user spesifik | Klik "Lihat Proses CF" pada Buyer tertentu | Menampilkan perhitungan vektor dan rekomendasi secara detail | Valid |

## C. Modul Transaksi & Review (Buyer)

| No | ID Skenario | Skenario Pengujian | Data Uji | Hasil yang Diharapkan | Status |
|:---|:---|:---|:---|:---|:---|
| 21 | `TC-TRX-01` | Tambah produk ke keranjang untuk stok yang habis | Produk dengan Stok: `0` | Tombol "Tambah ke Keranjang" disabled (non-klikable) | Valid |
| 22 | `TC-TRX-02` | Masukkan Qty keranjang melebihi batas stok | Qty Input: `10` (Stok: `5`) | Pesan error "Jumlah melebihi stok tersedia" | Valid |
| 23 | `TC-TRX-03` | Tambah produk ke keranjang sebagai Guest (belum login) | Guest klik produk | Harus login terlebih dahulu sebelum bisa memproses | Valid |
| 24 | `TC-TRX-04` | Checkout produk dengan form data pengiriman kosong | Alamat dikosongkan | Muncul validasi error alamat harus diisi | Valid |
| 25 | `TC-TRX-05` | Berhasil menyelesaikan Checkout (Transaksi) | Form diisi valid, metode Transfer | Pesanan terbentuk, stok berkurang, cart kosong, masuk ke "Pending" | Valid |
| 26 | `TC-TRX-06` | Buyer memberi Review pada produk yang belum pernah dibeli | Submit rating 5 pada produk random | Muncul alert error "Kamu hanya bisa memberikan review untuk produk yang sudah diterima" | Valid |
| 27 | `TC-TRX-07` | Buyer memberi Review pada produk berstatus "Delivered" | Submit rating 5, komentar diisi | Review tersimpan dan rata-rata bintang pada detail produk langsung terupdate | Valid |
| 28 | `TC-TRX-08` | Buyer memberi Review 2 kali untuk produk yang sama | Submit form review ulang | Muncul alert error "Kamu sudah memberikan review untuk produk ini" | Valid |
| 29 | `TC-TRX-09` | Tampilan Rekomendasi untuk Guest / Pengguna Baru | Akses Halaman Utama sebagai Guest / Akun Baru | Menampilkan section produk Best-Seller secara global | Valid |
| 30 | `TC-TRX-10` | Tampilan Rekomendasi (Collaborative Filtering) untuk Buyer aktif | Akses Halaman Utama dengan akun Buyer aktif | Menampilkan section "Rekomendasi Untukmu" dengan badge CF (Dihitung on-the-fly) | Valid |
