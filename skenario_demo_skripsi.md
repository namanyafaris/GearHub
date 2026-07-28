# Skenario Demo Aplikasi Skripsi - GearHub

**Judul Skripsi:** Sistem Rekomendasi Produk E-Commerce Menggunakan Algoritma User-Based Collaborative Filtering (Cosine Similarity)
**Platform:** Laravel 12

Dokumen ini berisi panduan *step-by-step* untuk mendemokan fungsionalitas keseluruhan sistem dan menonjolkan fitur utama (Rekomendasi Produk) di depan dosen penguji saat sidang skripsi.

---

## ⚙️ TAHAP 1: PERSIAPAN (PRA-DEMO)
*Tujuan: Memastikan database dalam keadaan bersih namun siap digunakan dengan data uji (dummy) yang valid.*

1. Buka Terminal / Command Prompt di folder project Anda.
2. Jalankan perintah khusus yang telah dibuat:
   ```bash
   php artisan app:reset-demo
   ```
   **Apa yang terjadi?**
   Perintah ini akan mengosongkan seluruh tabel transaksi dan membuat ulang *dummy data*:
   - Akun Admin default.
   - 20 Akun Buyer dengan pola interaksi yang spesifik (Penyuka Mouse, Keyboard, dll).
   - 120 Produk beserta deskripsi bahasa Indonesianya.

3. Jalankan server lokal:
   ```bash
   php artisan serve
   ```

---

## 🧑‍💻 TAHAP 2: SIMULASI PENGGUNA (BUYER JOURNEY)
*Tujuan: Memperlihatkan alur kerja sistem dari sudut pandang pembeli, serta membuktikan algoritma rekomendasi bekerja.*

### A. Fitur Cold-Start (Pengguna Belum Login / Pengguna Baru)
1. Buka browser dan akses `http://127.0.0.1:8000`.
2. Halaman utama terbuka sebagai *Guest* (Tamu).
3. Gulir ke bawah hingga bagian **"Rekomendasi Untukmu"**.
4. **Jelaskan ke Penguji:**
   *"Karena saya belum login dan sistem belum mengenali preferensi saya (Cold Start Problem), algoritma Fallback bekerja dengan menampilkan 8 produk Best-Seller secara global di toko ini."*

### B. Registrasi & Login
1. Klik tombol **Login** di pojok kanan atas.
2. Lakukan login menggunakan akun Buyer uji coba, misalnya:
   - **Email:** `buyer01@gaminggear.com`
   - **Password:** `password`
   *(Atau demonstrasikan form Registrasi jika diminta)*.

### C. Pembuktian Collaborative Filtering (Inti Skripsi)
1. Setelah login, sistem mengarahkan Anda kembali ke halaman utama (*Home*).
2. Tunjukkan kembali bagian **"Rekomendasi Untukmu"**. Anda akan melihat produk-produk spesifik (misal: lebih banyak muncul Mouse).
3. **Jelaskan ke Penguji:**
   *"Di sinilah algoritma Collaborative Filtering bekerja. Sistem secara otomatis membaca seluruh riwayat klik (view), keranjang (cart), dan pembelian (purchase) dari akun saya, kemudian menggunakan rumus Cosine Similarity untuk mencari 5 pengguna lain di database yang seleranya paling mirip dengan saya. Produk yang disukai oleh ke-5 orang tersebutlah yang dirangking ulang (Weighted Score) dan ditampilkan di sini."*

### D. Interaksi & Keranjang (Feedback Loop)
1. Klik salah satu produk rekomendasi (misal: "Mouse Gaming").
2. **Jelaskan ke Penguji:** *"Aksi melihat detail produk (View) ini sudah langsung dicatat oleh sistem secara real-time dengan bobot 1 untuk dipelajari algoritma."*
3. Masukkan jumlah pesanan (`qty = 2`), lalu klik **Tambah ke Keranjang**.
4. **Jelaskan ke Penguji:** *"Aksi memasukkan ke keranjang (Cart) ini dicatat dengan bobot 2."*

### E. Checkout & Pembayaran Transfer
1. Buka halaman **Keranjang**, lalu klik **Checkout**.
2. Isi data alamat pengiriman.
3. Pilih metode pembayaran: **Transfer Bank**.
4. Klik **Buat Pesanan**. *(Aksi Purchase ini dicatat dengan bobot tertinggi yaitu 3).*
5. Anda akan diarahkan ke halaman **Detail Pesanan** yang berstatus `PENDING`.
6. Tunjukkan bahwa sistem menampilkan instruksi transfer (Nomor Rekening BCA a.n Syawal Alfarisi).
7. Lakukan **Upload Bukti Pembayaran** (gunakan sembarang gambar struk format JPG/PNG di bawah 2MB).
8. Klik **Kirim Bukti Pembayaran**. Status *alert* akan berubah menjadi hijau (Menunggu Konfirmasi Admin).

---

## 👨‍💼 TAHAP 3: VERIFIKASI ADMIN & LAPORAN
*Tujuan: Memperlihatkan halaman Back-Office, verifikasi manual, dan sistem pelaporan untuk admin.*

### A. Konfirmasi Pesanan (Approve)
1. Buka tab/browser baru (mode Incognito agar sesi Buyer tidak tertimpa).
2. Login menggunakan akun Admin:
   - **Email:** `admin@gaminggear.com`
   - **Password:** `password`
3. Masuk ke menu **Pesanan**.
4. Klik pesanan yang baru saja dibuat oleh Buyer tadi.
5. Klik tombol **Lihat Bukti Transfer** untuk memvalidasi struk secara manual.
6. Ubah status pesanan menjadi **Delivered (Selesai)**. Klik *Update*.

### B. White-Box Testing (Log Rekomendasi)
1. Di Dashboard Admin, masuk ke menu **Log Rekomendasi** (Whitebox Testing).
2. Cari nama *Buyer* yang tadi Anda gunakan.
3. Klik tombol **Cetak PDF**.
4. Buka file PDF yang terunduh.
5. **Jelaskan ke Penguji:**
   *"Ini adalah pembuktian transparansi algoritma (White-box). Di PDF ini, kita bisa melihat langsung perhitungan matematis di balik layar: mulai dari User-Item Matrix, nilai hitungan Cosine Similarity untuk mencari tetangga terdekat, hingga pembobotan akhir rekomendasi yang tadi muncul di halaman Home."*

### C. Laporan Penjualan (Export PDF)
1. Buka menu **Laporan Penjualan**.
2. Tunjukkan filter rentang waktu (Start Date & End Date).
3. Pilih tanggal hari ini, lalu klik **Filter**.
4. Klik **Cetak PDF** untuk membuktikan laporan terekap dengan rapi melalui DomPDF.

---

## 🌟 TAHAP 4: PENYELESAIAN (END OF JOURNEY)
*Tujuan: Menunjukkan penyelesaian transaksi (Invoice & Review).*

1. Kembali ke tab/browser milik **Buyer**.
2. *Refresh* halaman Detail Pesanan.
3. Tunjukkan bahwa form upload bukti telah hilang.
4. Klik tombol merah **"Download Invoice PDF"** di bagian atas untuk mencetak nota pembelian resmi.
5. Gulir ke bawah halaman Detail Pesanan tersebut. Karena status sudah *Delivered*, kini muncul form **Beri Ulasan Produk**.
6. Pilih bintang 5, tulis komentar, dan klik **Kirim Review**.
7. *(Opsional)* Kunjungi halaman Detail Produk tersebut untuk membuktikan bahwa *rating* rata-rata langsung terupdate berkat ulasan barusan.

---

**✨ DEMO SELESAI ✨**

*Tips Tambahan untuk Sidang:*
- *Kuasai rumus Cosine Similarity (Dot Product dibagi Magnitudes) karena dosen penguji teknis hampir pasti akan menanyakannya.*
- *Buka file `CollaborativeFilteringService.php` di VSCode dan bersiaplah jika disuruh menunjukkan blok kodenya.*


Tentu! Ini sangat penting untuk Anda kuasai saat sidang karena dosen penguji pasti akan bertanya: "Dari mana sistem tahu preferensi user dan dapat angka bobot 1, 2, atau 3 itu?"

Anda tidak perlu khawatir, kodenya sudah kita rancang dengan sangat rapi dan terpusat (Clean Code). Jika disuruh menunjukkan, berikut adalah alur dan lokasi kodenya:

1. Pusat Pencatatannya (UserInteractionService.php)
Sistem menggunakan sebuah Service terpusat yang berada di app/Services/UserInteractionService.php. Di sinilah fungsi penentuan bobot (weight) 1 sampai 3 itu terjadi menggunakan struktur match bawaan PHP 8:

php
// File: app/Services/UserInteractionService.php (Baris 19-24)
$weight = match ($type) {
    'view' => 1.0,     // Jika diklik/dilihat, bobot = 1
    'cart' => 2.0,     // Jika masuk keranjang, bobot = 2
    'purchase' => 3.0, // Jika dibeli/checkout, bobot = 3
    default => 1.0,
};
Sistem akan menyimpan data ID User, ID Produk, dan Weight tersebut ke dalam tabel database user_interactions.

2. Kapan Bobot Tersebut Diberikan? (Alur Trigger-nya)
Service di atas akan "dipanggil" secara otomatis di 3 tempat berbeda sesuai dengan aktivitas yang dilakukan user:

Bobot 1 (View): Terjadi ketika user sekadar mengklik dan membuka halaman detail suatu produk. 📍 Lokasi Kode: app/Http/Controllers/Buyer/ProductController.php (pada fungsi show).

php
$this->interactionService->log($user, $product->id, 'view');
Bobot 2 (Cart): Terjadi ketika user menekan tombol "Tambah ke Keranjang" di halaman detail produk. 📍 Lokasi Kode: app/Http/Controllers/Buyer/CartController.php (pada fungsi store).

php
$this->interactionService->log($user, $product->id, 'cart');
Bobot 3 (Purchase): Terjadi ketika user menekan tombol "Buat Pesanan" (Selesai Checkout). Sistem akan me-looping (memeriksa) semua barang yang ada di keranjangnya saat itu dan memberikan bobot 3 untuk masing-masing barang. 📍 Lokasi Kode: app/Http/Controllers/Buyer/CheckoutController.php (pada fungsi store).

php
$this->interactionService->log($user, $item->product_id, 'purchase');
3. Eksekusi di Algoritma
Setelah semua interaksi dari View, Cart, dan Purchase menumpuk di database, class CollaborativeFilteringService tinggal menjumlahkan (sum) seluruh weight tersebut per produk untuk dijadikan sebagai nilai absolut dalam perhitungan matriks Cosine Similarity. (Contoh: Produk A di-view 3 kali dan di-cart 1 kali = Bobot total 5).

Tips Untuk Sidang: Jika penguji bertanya, Anda cukup buka file app/Services/UserInteractionService.php di laptop Anda, blok kodenya, dan jelaskan dengan gaya bahasa di atas. Dijamin mereka akan sangat puas karena arsitektur kodenya bersih dan mudah dipahami!