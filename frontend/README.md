# 🐾 PawTrainer — Frontend

Frontend sederhana untuk aplikasi PawTrainer, terhubung ke Laravel REST API dengan JWT Auth.

## Struktur Folder

```
pawtrainer-frontend/
│
├── css/
│   └── main.css              ← Semua styling (CSS Variables, responsive, komponen)
│
├── js/
│   ├── api.js                ← Axios instance + semua API calls + Auth helper + UI helper
│   └── navbar.js             ← Komponen navbar yang diinject ke setiap halaman
│
├── login.html                ← Halaman login
├── register.html             ← Halaman registrasi
├── dashboard.html            ← Dashboard (stats + tutorial terbaru + cat fact)
├── tutorials.html            ← Daftar tutorial (filter, search, pagination)
├── tutorial-detail.html      ← Detail tutorial + komentar + favorit
├── favorites.html            ← Halaman favorit user
├── breeds.html               ← Breed explorer (modal detail, filter, search)
├── catfacts.html             ← Fakta unik kucing
└── README.md
```

## Setup & Konfigurasi

### 1. Jalankan Backend Laravel
```bash
cd PawTrainer
cp .env.example .env
composer install
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
php artisan serve
```

Backend akan jalan di: `http://localhost:8000`

### 2. Konfigurasi Base URL

Buka `js/api.js`, ubah baris ini sesuai URL backend kamu:

```javascript
const BASE_URL = 'http://localhost:8000/api';
```

### 3. Buka Frontend

Buka `login.html` langsung di browser, atau gunakan Live Server (VSCode extension).

> **Catatan CORS**: Pastikan backend sudah dikonfigurasi untuk mengizinkan request dari origin frontend kamu.

---

## API Endpoints yang Digunakan

| Halaman           | Endpoint                                  | Method |
|-------------------|-------------------------------------------|--------|
| Login             | `/api/auth/login`                         | POST   |
| Register          | `/api/auth/register`                      | POST   |
| Logout            | `/api/auth/logout`                        | POST   |
| Dashboard/Tutorial | `/api/gateway/tutorials`                 | GET    |
| Detail Tutorial   | `/api/gateway/tutorials/{id}`             | GET    |
| Komentar          | `/api/gateway/tutorials/{id}/comments`    | GET    |
| Tambah Komentar   | `/api/gateway/comments`                   | POST   |
| Hapus Komentar    | `/api/gateway/comments/{id}`              | DELETE |
| Favorit           | `/api/gateway/favorites`                  | GET    |
| Toggle Favorit    | `/api/gateway/favorites`                  | POST   |
| Ras Kucing        | `/api/gateway/breeds`                     | GET    |
| Fakta Kucing      | `/api/gateway/facts`                      | GET    |
| Kategori          | `/api/gateway/categories`                 | GET    |

---

## Fitur

- ✅ Login & Register dengan JWT token
- ✅ Token tersimpan di `localStorage`
- ✅ Authorization Bearer Token di setiap request
- ✅ Auto redirect ke login jika token expired / 401
- ✅ Logout (invalidate token + clear localStorage)
- ✅ Dashboard dengan stats
- ✅ Daftar tutorial dengan search, filter level & kategori, pagination
- ✅ Detail tutorial dengan konten, video YouTube embed
- ✅ Komentar: tambah & hapus (hanya milik sendiri)
- ✅ Favorit: toggle add/remove
- ✅ Breed explorer dengan search, filter asal, modal detail
- ✅ Cat facts dengan pilihan jumlah
- ✅ Loading skeleton state
- ✅ Error state & toast notification
- ✅ Responsive (mobile + desktop)
- ✅ Navbar dengan mobile hamburger menu

---

## Akun Default (dari Seeder)

```
Admin:  admin@pawtrainer.com  / password123
User:   user@pawtrainer.com   / password123
```

---

## Error Handling

| Kondisi              | Handling                                      |
|----------------------|-----------------------------------------------|
| Token expired (401)  | Auto clear localStorage, redirect ke login    |
| Validation error     | Tampil per-field error di bawah input         |
| Network error        | Toast "Tidak dapat terhubung ke server"       |
| Not found (404)      | Empty state / pesan error di halaman          |
| Forbidden (403)      | Toast dengan pesan dari server                |
