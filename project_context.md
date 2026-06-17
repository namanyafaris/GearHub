# Project Context: E-Commerce Gaming Gear (Single Seller)

## Tech Stack & Architecture
- **Framework:** Laravel 12 (Blade + Bootstrap 5)
- **Database:** MySQL
- **Authentication:** Laravel Breeze (Stack: Blade)
- **Scope:** Single Seller (Hanya 1 pemilik toko, tidak ada `seller_id`)
- **Roles:** 'admin' & 'buyer' (Enum pada tabel `users`)

## Database Schema (Summary)
1. `categories` (id, name, slug, description, image)
2. `products` (id, category_id, name, slug, description, price, stock, image, is_active)
3. `product_images` (id, product_id, image_path)
4. `carts` (id, buyer_id, product_id, quantity)
5. `orders` (id, buyer_id, total_price, status [pending, processing, shipped, delivered, cancelled], shipping_name, shipping_phone, shipping_address, payment_method [transfer, cod])
6. `order_items` (id, order_id, product_id, quantity, price)
7. `reviews` (id, buyer_id, product_id, rating [1-5], comment)
8. `user_interactions` (id, user_id, product_id, interaction_type [view:1, cart:2, purchase:3], weight)

## Core Recommendation Algorithm (Skripsi Core)
- **Method:** User-based Collaborative Filtering dengan Cosine Similarity.
- **Matrix:** 2D Matrix (Row: user_id, Column: product_id, Cell: accumulated weight).
- **Logic:** Ambil Top-5 Similar Users -> Rekomendasikan max 8 produk (exclude yang sudah diinteraksi user aktif).
- **Fallback:** - Cold Start (User baru): 8 Produk terlaris global.
  - No Similar User: Produk terlaris dari kategori yang paling sering diinteraksi user.

## Coding Standards & Rules
- Selalu gunakan Rupiah (`Rp 150.000`) untuk format harga di view.
- Gunakan Form Request Class terpisah untuk validasi (Bahasa Indonesia).
- Pakai Bootstrap alert untuk flash message.
- Semua submission form wajib handle anti-double submit (disable + spinner).