# Project Tasks Checklist

## Backlog / Todo

### Fase 1 — Setup & Auth
- [ ] Setup Laravel 12 baru & Laravel Breeze (Blade)
- [ ] Setup enum role ('admin', 'buyer') di migration user & buat masing-masing Middleware
- [ ] Daftarkan middleware di `bootstrap/app.php` & konfigurasi redirect login
- [ ] Buat Database Seeder untuk akun default Admin dan Buyer

### Fase 2 — Database & Migration
- [ ] Buat semua migration file lengkap (`categories` sampai `user_interactions` dengan cascadeOnDelete)

### Fase 3 — Model & Relasi
- [ ] Buat semua Eloquent Model beserta relasi lengkapnya
- [ ] Tambahkan $fillable, $casts, Accessor `formatPrice()`, Scope `activeProducts()`, dan method `averageRating()`

### Fase 4 — Seeder Produk & Interaksi
- [ ] Buat CategorySeeder (6 kategori gaming gear)
- [ ] Buat ProductSeeder & Factory (120 produk dengan harga & nama realistis)
- [ ] Buat UserInteractionSeeder (15 akun buyer dummy + riwayat order & interaksi)

### Fase 5 — Fitur Buyer (Katalog & Belanja)
- [ ] Buat Halaman Utama (Katalog, kategori, hero banner)
- [ ] Buat Filter & Search di halaman `/products`
- [ ] Buat Detail Produk beserta pencatatan `user_interactions` (view, cart, purchase)
- [ ] Buat Fitur Keranjang, Checkout, dan validasi pengurangan stok

### Fase 6 — Dashboard Admin
- [ ] Buat Dashboard (/admin/dashboard) + Grafik penjualan Chart.js
- [ ] Buat CRUD Manajemen Produk & Kategori (dengan upload gambar)
- [ ] Buat Manajemen Pesanan (Update status & pembatalan balikkan stok)
- [ ] Buat Laporan Penjualan + Export PDF via `laravel-dompdf`

### Fase 7 — Algoritma Collaborative Filtering (Inti Skripsi)
- [ ] Buat `app/Services/CollaborativeFilteringService.php` (Langkah 1 s/d 4 + Fallback)
- [ ] Buat `RecommendationController` & endpoint GET `/recommendations` untuk testing JSON
- [ ] Integrasikan hasil rekomendasi ke halaman utama dan halaman detail produk

### Fase 8 — Review, Rating & Polish
- [ ] Buat fitur review (1 buyer, 1 produk, hanya status delivered)
- [ ] Terapkan flash message Bootstrap, custom 404, dan badge warna stok

### Fase 9 — Pengujian Akhir (White-Box & Black-Box)
- [ ] Buat `TestSeeder` dengan variasi pola interaksi yang signifikan untuk 20 user
- [ ] Buat Artisan Command `php artisan app:reset-demo`
- [ ] Buat Halaman Log Rekomendasi (`/admin/recommendations-log`) untuk bukti White-Box Testing (Export PDF)
- [ ] Buat Artisan Command `php artisan test:whitebox {userId}` untuk verifikasi logika ASCII di terminal
- [ ] Buat tabel Markdown berisi 30 skenario Black-Box Testing untuk Bab IV

---

## In Progress
- [ ] Fase 7 — Algoritma Collaborative Filtering (Inti Skripsi)

---

## Done
- [ ] Fase 1 Authentication & Roles
- [ ] Fase 2 Database & Migration
- [ ] Fase 3 Model & Relationships
- [ ] Fase 4 Seeder & Dummy Data
- [ ] Fase 5 Buyer Features
- [ ] Fase 6 Admin Dashboard