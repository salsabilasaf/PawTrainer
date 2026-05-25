# 🐾 PawTrainer API — Dokumentasi Lengkap

> Platform tutorial melatih kucing — Laravel 10 + JWT + Swagger/OpenAPI 3.0

---

## 📁 Struktur Folder

```
PawTrainer/
├── app/
│   ├── Exceptions/
│   │   └── Handler.php                 # Global exception handler
│   ├── Helpers/
│   │   └── ResponseHelper.php          # ✅ Reusable JSON response helper
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── SwaggerController.php   # ✅ OpenAPI global annotations
│   │   │   └── Api/
│   │   │       ├── AuthController.php  # Register, Login, Logout, Profile
│   │   │       └── Gateway/
│   │   │           ├── TutorialController.php
│   │   │           ├── CategoryController.php
│   │   │           ├── CommentController.php
│   │   │           ├── FavoriteController.php
│   │   │           └── ExternalApiController.php
│   │   ├── Middleware/
│   │   │   └── RoleMiddleware.php      # ✅ Role-based access control
│   │   ├── Requests/
│   │   │   ├── Tutorial/
│   │   │   │   ├── StoreTutorialRequest.php
│   │   │   │   └── UpdateTutorialRequest.php
│   │   │   ├── Category/
│   │   │   │   ├── StoreCategoryRequest.php
│   │   │   │   └── UpdateCategoryRequest.php
│   │   │   ├── Comment/
│   │   │   │   └── StoreCommentRequest.php
│   │   │   └── Favorite/
│   │   │       └── StoreFavoriteRequest.php
│   │   └── Kernel.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Tutorial.php
│   │   ├── Category.php
│   │   ├── Comment.php
│   │   └── Favorite.php
│   └── Services/                       # ✅ Reusable business logic layer
│       ├── TutorialService.php
│       ├── CategoryService.php
│       ├── CommentService.php
│       ├── FavoriteService.php
│       └── ExternalApiService.php
├── config/
│   ├── auth.php
│   ├── services.php
│   └── swagger.php                     # ✅ Konfigurasi l5-swagger
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php                         # ✅ Clean route definitions
├── docs/
│   └── POSTMAN_GUIDE.md                # ✅ Panduan testing Postman
├── .env.example
└── README.md
```

---

## ⚙️ Environment Setup

### 1. Clone & Install Dependencies

```bash
# Clone project
git clone <repo-url> pawtrainer
cd pawtrainer

# Install PHP dependencies
composer install

# Salin file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 2. Konfigurasi `.env`

```env
APP_NAME=PawTrainer
APP_URL=http://localhost:8000

# Database MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pawtrainer
DB_USERNAME=root
DB_PASSWORD=your_password

# JWT
JWT_SECRET=           # Di-generate otomatis
JWT_TTL=60            # Token berlaku 60 menit
JWT_REFRESH_TTL=20160 # Refresh token 2 minggu

# External APIs
CAT_API_KEY=your_cat_api_key      # https://thecatapi.com/signup
YOUTUBE_API_KEY=your_youtube_key  # Google Cloud Console
```

### 3. Setup Database & JWT

```bash
# Buat database MySQL
mysql -u root -p -e "CREATE DATABASE pawtrainer;"

# Generate JWT secret
php artisan jwt:secret

# Jalankan migrasi + seeder
php artisan migrate --seed

# Jalankan server
php artisan serve
```

### 4. Install Swagger Package

```bash
# Install l5-swagger
composer require darkaonline/l5-swagger

# Publish config
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"

# Generate dokumentasi
php artisan l5-swagger:generate
```

### 5. Akses Swagger UI

```
http://localhost:8000/api/documentation
```

---

## 📦 Package Dependencies

Tambahkan ke `composer.json`:

```json
{
    "require": {
        "php": "^8.1",
        "laravel/framework": "^10.0",
        "tymon/jwt-auth": "^2.0",
        "darkaonline/l5-swagger": "^8.5"
    }
}
```

---

## 🔐 Role & Akses

| Endpoint              | admin | user |
|-----------------------|:-----:|:----:|
| Register / Login      | ✅    | ✅   |
| GET tutorials         | ✅    | ✅   |
| GET categories        | ✅    | ✅   |
| GET comments          | ✅    | ✅   |
| GET favorites         | ✅    | ✅   |
| POST/PUT/DELETE tutorial | ✅ | ❌   |
| POST/PUT/DELETE category | ✅ | ❌   |
| POST/DELETE comment   | ✅    | ✅   |
| POST favorite (toggle)| ✅    | ✅   |
| External APIs         | ✅    | ✅   |

---

## 🚀 Cara Menjalankan Project

```bash
# 1. Pastikan MySQL berjalan

# 2. Jalankan migrasi
php artisan migrate:fresh --seed

# 3. Generate Swagger docs
php artisan l5-swagger:generate

# 4. Jalankan server
php artisan serve

# Server berjalan di: http://localhost:8000
# Swagger UI       : http://localhost:8000/api/documentation
```

---

## 🧪 Demo Accounts (Seeder)

| Email                    | Password  | Role  |
|--------------------------|-----------|-------|
| admin@pawtrainer.com     | admin123  | admin |
| user@pawtrainer.com      | user123   | user  |

---

## 🗺️ Semua Endpoint

### Auth
| Method | Endpoint           | Auth | Role |
|--------|--------------------|------|------|
| POST   | /api/auth/register | ❌   | -    |
| POST   | /api/auth/login    | ❌   | -    |
| POST   | /api/auth/logout   | ✅   | any  |
| GET    | /api/profile       | ✅   | any  |

### Tutorial (Gateway)
| Method | Endpoint                    | Auth | Role  |
|--------|-----------------------------|------|-------|
| GET    | /api/gateway/tutorials      | ✅   | any   |
| GET    | /api/gateway/tutorials/{id} | ✅   | any   |
| POST   | /api/gateway/tutorials      | ✅   | admin |
| PUT    | /api/gateway/tutorials/{id} | ✅   | admin |
| DELETE | /api/gateway/tutorials/{id} | ✅   | admin |

### Category (Gateway)
| Method | Endpoint                     | Auth | Role  |
|--------|------------------------------|------|-------|
| GET    | /api/gateway/categories      | ✅   | any   |
| GET    | /api/gateway/categories/{id} | ✅   | any   |
| POST   | /api/gateway/categories      | ✅   | admin |
| PUT    | /api/gateway/categories/{id} | ✅   | admin |
| DELETE | /api/gateway/categories/{id} | ✅   | admin |

### Comment (Gateway)
| Method | Endpoint                                    | Auth | Role     |
|--------|---------------------------------------------|------|----------|
| GET    | /api/gateway/tutorials/{tutorialId}/comments| ✅   | any      |
| POST   | /api/gateway/comments                       | ✅   | any      |
| DELETE | /api/gateway/comments/{id}                  | ✅   | owner/admin |

### Favorite (Gateway)
| Method | Endpoint               | Auth | Role |
|--------|------------------------|------|------|
| GET    | /api/gateway/favorites | ✅   | any  |
| POST   | /api/gateway/favorites | ✅   | any  |

### External API (Gateway)
| Method | Endpoint                       | Auth | Role |
|--------|--------------------------------|------|------|
| GET    | /api/gateway/facts             | ✅   | any  |
| GET    | /api/gateway/breeds            | ✅   | any  |
| GET    | /api/gateway/videos/{keyword}  | ✅   | any  |
