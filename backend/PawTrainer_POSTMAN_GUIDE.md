# 🧪 PawTrainer — Panduan Testing & Demo Walkthrough

> Testing guide lengkap: 12 skenario dari register hingga error handling

---

## 🛠️ Setup Postman

### Import Collection

1. Buka Postman → **Import**
2. Pilih file `PawTrainer_v2.postman_collection.json`
3. Import `PawTrainer_Env.postman_environment.json`

### Buat Environment Variables

Di Postman, buat **Environment** baru: `PawTrainer Local`

| Variable        | Initial Value             |
|-----------------|---------------------------|
| `base_url`      | `http://localhost:8000`   |
| `token`         | *(kosongkan dulu)*        |
| `admin_token`   | *(kosongkan dulu)*        |
| `tutorial_id`   | `1`                       |
| `category_id`   | `1`                       |
| `comment_id`    | *(kosongkan dulu)*        |

---

## 📋 Demo Flow Lengkap (12 Skenario)

---

### 🔵 SKENARIO 1 — Register Akun Baru

**POST** `{{base_url}}/api/auth/register`

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
    "name": "Andi Pratama",
    "email": "andi@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Expected Response (201):**
```json
{
    "success": true,
    "message": "Registrasi berhasil",
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "user": {
            "id": 3,
            "name": "Andi Pratama",
            "email": "andi@example.com",
            "role": "user",
            "created_at": "2024-01-15T10:30:00.000000Z"
        }
    }
}
```

---

### 🔵 SKENARIO 2 — Login

**POST** `{{base_url}}/api/auth/login`

**Request Body:**
```json
{
    "email": "admin@pawtrainer.com",
    "password": "admin123"
}
```

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiO...",
        "token_type": "bearer",
        "expires_in": 3600,
        "user": {
            "id": 1,
            "name": "Admin PawTrainer",
            "email": "admin@pawtrainer.com",
            "role": "admin",
            "created_at": "2024-01-01T00:00:00.000000Z"
        }
    }
}
```

> 💡 **Postman Automation:** Tambahkan script di tab **Tests**:
> ```javascript
> if (pm.response.code === 200) {
>     const data = pm.response.json();
>     pm.environment.set("token", data.data.token);
>     pm.environment.set("admin_token", data.data.token);
>     console.log("Token saved:", data.data.token.substring(0, 30) + "...");
> }
> ```

---

### 🔵 SKENARIO 3 — Mendapat JWT Token & Akses Profile

**GET** `{{base_url}}/api/profile`

**Headers:**
```
Authorization: Bearer {{token}}
```

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Data profil berhasil diambil",
    "data": {
        "user": {
            "id": 1,
            "name": "Admin PawTrainer",
            "email": "admin@pawtrainer.com",
            "role": "admin",
            "created_at": "2024-01-01T00:00:00.000000Z"
        }
    }
}
```

---

### 🔵 SKENARIO 4 — Akses Gateway Endpoint (List Tutorial)

**GET** `{{base_url}}/api/gateway/tutorials`

**Headers:**
```
Authorization: Bearer {{token}}
```

**Query Parameters (opsional):**
```
category_id=1
difficulty=beginner
search=duduk
sort_by=created_at
sort_order=desc
per_page=5
```

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Daftar tutorial berhasil diambil",
    "data": {
        "tutorials": [
            {
                "id": 1,
                "category_id": 1,
                "title": "Mengajarkan Kucing Duduk",
                "content": "Langkah pertama...",
                "difficulty": "beginner",
                "estimated_time": 15,
                "youtube_url": null,
                "image_url": null,
                "comments_count": 3,
                "favorites_count": 7,
                "created_at": "2024-01-10T08:00:00.000000Z",
                "updated_at": "2024-01-10T08:00:00.000000Z",
                "category": {
                    "id": 1,
                    "name": "Basic Training"
                }
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 5,
            "total": 12,
            "last_page": 3
        }
    }
}
```

---

### 🔵 SKENARIO 5 — CRUD Tutorial (Admin)

#### 5a. Create Tutorial
**POST** `{{base_url}}/api/gateway/tutorials`

**Headers:**
```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

**Request Body:**
```json
{
    "category_id": 1,
    "title": "Mengajarkan Kucing Berjabat Tangan",
    "content": "# Pendahuluan\n\nMengajarkan kucing berjabat tangan adalah trik yang menggemaskan.\n\n## Langkah 1: Persiapan\nSiapkan camilan kecil sebagai reward.\n\n## Langkah 2: Posisi Awal\nMinta kucing duduk terlebih dahulu.\n\n## Langkah 3: Latihan\nSentuh telapak kaki depan kucing sambil ucapkan 'shake'.",
    "difficulty": "intermediate",
    "estimated_time": 20,
    "youtube_url": "https://www.youtube.com/watch?v=example",
    "image_url": "https://example.com/images/handshake.jpg"
}
```

**Expected Response (201):**
```json
{
    "success": true,
    "message": "Tutorial berhasil dibuat",
    "data": {
        "tutorial": {
            "id": 5,
            "category_id": 1,
            "title": "Mengajarkan Kucing Berjabat Tangan",
            "difficulty": "intermediate",
            "estimated_time": 20,
            "category": {
                "id": 1,
                "name": "Basic Training"
            }
        }
    }
}
```

> 💡 Simpan `id` dari response:
> ```javascript
> pm.environment.set("tutorial_id", pm.response.json().data.tutorial.id);
> ```

#### 5b. Update Tutorial
**PUT** `{{base_url}}/api/gateway/tutorials/{{tutorial_id}}`

**Request Body:**
```json
{
    "title": "Mengajarkan Kucing Berjabat Tangan (Updated)",
    "estimated_time": 25
}
```

#### 5c. Delete Tutorial
**DELETE** `{{base_url}}/api/gateway/tutorials/{{tutorial_id}}`

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Tutorial berhasil dihapus"
}
```

---

### 🔵 SKENARIO 6 — Add Comment

**POST** `{{base_url}}/api/gateway/comments`

**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Request Body:**
```json
{
    "tutorial_id": 1,
    "body": "Tutorial ini luar biasa! Kucing saya, si Mochi, berhasil berjabat tangan setelah 3 hari latihan. Sangat recommend!"
}
```

**Expected Response (201):**
```json
{
    "success": true,
    "message": "Komentar berhasil ditambahkan",
    "data": {
        "comment": {
            "id": 8,
            "tutorial_id": 1,
            "user_id": 3,
            "body": "Tutorial ini luar biasa! ...",
            "created_at": "2024-01-15T11:00:00.000000Z",
            "user": {
                "id": 3,
                "name": "Andi Pratama"
            }
        }
    }
}
```

> 💡 Simpan `comment_id`:
> ```javascript
> pm.environment.set("comment_id", pm.response.json().data.comment.id);
> ```

---

### 🔵 SKENARIO 7 — Add Favorite (Toggle)

**POST** `{{base_url}}/api/gateway/favorites`

**Headers:**
```
Authorization: Bearer {{token}}
Content-Type: application/json
```

**Request Body:**
```json
{
    "tutorial_id": 1
}
```

**Expected Response — Pertama kali (Added):**
```json
{
    "success": true,
    "message": "Tutorial berhasil ditambahkan ke favorit",
    "data": {
        "action": "added",
        "tutorial_id": 1
    }
}
```

**Expected Response — Kedua kali (Removed / Toggle):**
```json
{
    "success": true,
    "message": "Tutorial berhasil dihapus dari favorit",
    "data": {
        "action": "removed",
        "tutorial_id": 1
    }
}
```

---

### 🔵 SKENARIO 8 — Fetch Cat Facts

**GET** `{{base_url}}/api/gateway/facts?limit=3`

**Headers:**
```
Authorization: Bearer {{token}}
```

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Fakta kucing berhasil diambil",
    "data": {
        "source": "Cat Fact Ninja (catfact.ninja)",
        "facts": [
            {
                "fact": "Cats have 32 muscles that control the outer ear while humans have only 6.",
                "length": 75
            },
            {
                "fact": "A group of cats is called a clowder.",
                "length": 36
            },
            {
                "fact": "A cat can jump 5 times as high as it is tall.",
                "length": 46
            }
        ],
        "total": 332,
        "per_page": 3,
        "current_page": 1
    }
}
```

---

### 🔵 SKENARIO 9 — Fetch Breed Data

**GET** `{{base_url}}/api/gateway/breeds`

**Headers:**
```
Authorization: Bearer {{token}}
```

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Daftar ras kucing berhasil diambil",
    "data": {
        "source": "The Cat API (thecatapi.com)",
        "total": 67,
        "breeds": [
            {
                "id": "abys",
                "name": "Abyssinian",
                "origin": "Egypt",
                "temperament": "Active, Energetic, Independent, Intelligent, Gentle",
                "description": "The Abyssinian is easy to care for, and a joy to have in your home.",
                "life_span": "14 - 15",
                "weight_kg": "3 - 5",
                "wikipedia": "https://en.wikipedia.org/wiki/Abyssinian_(cat)"
            }
        ]
    }
}
```

---

### 🔴 SKENARIO 10 — Role Restriction (User Coba Buat Tutorial)

Login sebagai **user biasa**, lalu coba:

**POST** `{{base_url}}/api/gateway/tutorials`

**Headers:**
```
Authorization: Bearer {{user_token}}   ← token dari login user@pawtrainer.com
```

**Request Body:** *(sama seperti skenario 5a)*

**Expected Response (403 Forbidden):**
```json
{
    "success": false,
    "message": "Hanya admin yang dapat membuat tutorial."
}
```

---

### 🔴 SKENARIO 11 — Validation Failed

**POST** `{{base_url}}/api/auth/register`

**Request Body (dengan field tidak lengkap/salah):**
```json
{
    "name": "",
    "email": "email-tidak-valid",
    "password": "123",
    "password_confirmation": "berbeda"
}
```

**Expected Response (422 Unprocessable Entity):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "name": ["The name field is required."],
        "email": ["The email must be a valid email address."],
        "password": [
            "The password must be at least 8 characters.",
            "The password confirmation does not match."
        ]
    }
}
```

---

### 🔴 SKENARIO 12 — Unauthorized Response

Akses endpoint protected **tanpa token**:

**GET** `{{base_url}}/api/gateway/tutorials`

**Headers:** *(tanpa Authorization)*

**Expected Response (401 Unauthorized):**
```json
{
    "success": false,
    "message": "Token tidak valid atau sudah kadaluarsa"
}
```

Atau dengan token **expired/salah**:

**Headers:**
```
Authorization: Bearer token_yang_sudah_expired_atau_salah
```

**Expected Response (401):**
```json
{
    "success": false,
    "message": "Token tidak valid atau sudah kadaluarsa"
}
```

---

## ⚡ Quick Test dengan cURL

```bash
# 1. Login Admin
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@pawtrainer.com","password":"admin123"}' | jq .

# 2. Simpan token ke variable
TOKEN="paste_token_here"

# 3. List tutorials
curl http://localhost:8000/api/gateway/tutorials \
  -H "Authorization: Bearer $TOKEN" | jq .

# 4. Cat facts
curl "http://localhost:8000/api/gateway/facts?limit=3" \
  -H "Authorization: Bearer $TOKEN" | jq .

# 5. Buat tutorial (admin)
curl -X POST http://localhost:8000/api/gateway/tutorials \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "category_id": 1,
    "title": "Test Tutorial cURL",
    "content": "Content tutorial test",
    "difficulty": "beginner",
    "estimated_time": 10
  }' | jq .
```

---

## 🔗 Swagger UI

Akses dokumentasi interaktif:

```
http://localhost:8000/api/documentation
```

**Cara menggunakan Swagger UI:**
1. Buka URL di atas
2. Klik tombol **Authorize** 🔒 di pojok kanan atas
3. Masukkan token: `Bearer eyJ0eXAiOiJKV1Q...`
4. Klik **Authorize** → **Close**
5. Pilih endpoint yang ingin dicoba → **Try it out** → **Execute**

---

## ⚠️ Error Code Reference

| Code | Arti                          | Contoh Kasus                          |
|------|-------------------------------|---------------------------------------|
| 200  | OK                            | GET berhasil, update berhasil         |
| 201  | Created                       | POST register/tutorial/comment        |
| 401  | Unauthorized                  | Token tidak ada, expired, salah       |
| 403  | Forbidden                     | Role tidak cukup (user coba admin)    |
| 404  | Not Found                     | Tutorial/category ID tidak ada        |
| 422  | Unprocessable Entity          | Validasi form gagal                   |
| 500  | Internal Server Error         | Bug server / exception tak tertangani |
| 502  | Bad Gateway                   | External API tidak merespons          |
| 503  | Service Unavailable           | API key belum dikonfigurasi           |
