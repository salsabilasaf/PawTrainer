// ============================================================
//  PawTrainer — API Service Layer
//  Semua HTTP call ke Laravel backend ada di sini
// ============================================================

const BASE_URL = 'http://127.0.0.1:8000/api';   // Ganti sesuai URL backend kamu

// ── Axios Instance ────────────────────────────────────────────
const api = axios.create({
    baseURL: BASE_URL,
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }
});

// ── Request Interceptor — inject Bearer Token ─────────────────
api.interceptors.request.use(
    config => {
        const token = Auth.getToken();
        if (token) config.headers['Authorization'] = `Bearer ${token}`;
        return config;
    },
    error => Promise.reject(error)
);

// ── Response Interceptor — handle token expired / 401 ─────────
api.interceptors.response.use(
    response => response,
    error => {
        if (error.response) {
            const status = error.response.status;
            const message = error.response.data?.message || '';

            if (status === 401) {
                Auth.clear();
                // Jangan redirect kalau sedang di halaman login/register
                if (!window.location.pathname.includes('login') && !window.location.pathname.includes('register')) {
                    window.location.href = 'login.html';
                }
            }
        } else if (!error.response) {
            // Network error
            UI.showToast('Tidak dapat terhubung ke server. Periksa koneksi kamu.', 'error');
        }
        return Promise.reject(error);
    }
);

// ============================================================
//  AUTH — Token Management
// ============================================================
const Auth = {
    getToken:  ()      => localStorage.getItem('pawtrainer_token'),
    getUser:   ()      => JSON.parse(localStorage.getItem('pawtrainer_user') || 'null'),
    isLoggedIn:()      => !!localStorage.getItem('pawtrainer_token'),
    setToken:  (token) => localStorage.setItem('pawtrainer_token', token),
    setUser:   (user)  => localStorage.setItem('pawtrainer_user', JSON.stringify(user)),

    save(token, user) {
        this.setToken(token);
        this.setUser(user);
    },

    clear() {
        localStorage.removeItem('pawtrainer_token');
        localStorage.removeItem('pawtrainer_user');
    },

    requireAuth() {
        if (!this.isLoggedIn()) {
            window.location.href = 'login.html';
            return false;
        }
        return true;
    },

    isAdmin() {
        return this.getUser()?.role === 'admin';
    },

    requireAdmin() {
        if (!this.isLoggedIn()) {
            window.location.href = 'login.html';
            return false;
        }
        if (!this.isAdmin()) {
            window.location.href = 'dashboard.html';
            return false;
        }
        return true;
    }
};

// ============================================================
//  API METHODS
// ============================================================

// ── Auth ──────────────────────────────────────────────────────
const AuthAPI = {
    register: (name, email, password, passwordConfirmation) =>
        api.post('/auth/register', {
            name,
            email,
            password,
            password_confirmation: passwordConfirmation
        }),

    login: (email, password) =>
        api.post('/auth/login', { email, password }),

    logout: () =>
        api.post('/auth/logout'),

    profile: () =>
        api.get('/profile')
};

// ── Tutorials ─────────────────────────────────────────────────
const TutorialAPI = {
    getAll: (page = 1) =>
        api.get(`/gateway/tutorials?page=${page}`),

    getById: (id) =>
        api.get(`/gateway/tutorials/${id}`),

    getComments: (tutorialId) =>
        api.get(`/gateway/tutorials/${tutorialId}/comments`),

    create: (data) =>
        api.post('/gateway/tutorials', data),

    update: (id, data) =>
        api.put(`/gateway/tutorials/${id}`, data),

    delete: (id) =>
        api.delete(`/gateway/tutorials/${id}`)
};

// ── Comments ──────────────────────────────────────────────────
const CommentAPI = {
    add: (tutorialId, comment) =>
        api.post('/gateway/comments', { tutorial_id: tutorialId, comment }),

    delete: (id) =>
        api.delete(`/gateway/comments/${id}`)
};

// ── Favorites ─────────────────────────────────────────────────
const FavoriteAPI = {
    getAll: () =>
        api.get('/gateway/favorites'),

    toggle: (tutorialId) =>
        api.post('/gateway/favorites', { tutorial_id: tutorialId })
};

// ── External APIs ─────────────────────────────────────────────
const ExternalAPI = {
    getBreeds: () =>
        api.get('/gateway/breeds'),

    getFacts: (limit = 5) =>
        api.get(`/gateway/facts?limit=${limit}`)
};

// ── Categories ────────────────────────────────────────────────
const CategoryAPI = {
    getAll: () =>
        api.get('/gateway/categories')
};

// ── Admin ─────────────────────────────────────────────────────
const AdminAPI = {
    // Mengambil semua user (memanfaatkan endpoint profile dengan fallback)
    getAllUsers: () =>
        api.get('/gateway/users'),

    // Total stats menggunakan endpoint yang sudah ada
    getTutorialStats: (page = 1) =>
        api.get(`/gateway/tutorials?page=${page}`),

    getCategoryStats: () =>
        api.get('/gateway/categories'),

    // Semua komentar (admin bisa lihat semua)
    getAllComments: (tutorialId) =>
        api.get(`/gateway/tutorials/${tutorialId}/comments`),
};

// ============================================================
//  UI HELPERS
// ============================================================
const UI = {
    showToast(message, type = 'info') {
        const existing = document.querySelector('.toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = `toast toast--${type}`;
        toast.innerHTML = `<span>${message}</span>`;
        document.body.appendChild(toast);

        requestAnimationFrame(() => toast.classList.add('toast--visible'));
        setTimeout(() => {
            toast.classList.remove('toast--visible');
            setTimeout(() => toast.remove(), 400);
        }, 3500);
    },

    setLoading(el, loading) {
        if (!el) return;
        if (loading) {
            el.disabled = true;
            el._origText = el.innerHTML;
            el.innerHTML = `<span class="spinner"></span> Memuat...`;
        } else {
            el.disabled = false;
            el.innerHTML = el._origText || el.innerHTML;
        }
    },

    parseError(error) {
        if (error.response?.data?.errors) {
            return Object.values(error.response.data.errors).flat().join(' ');
        }
        return error.response?.data?.message
            || error.message
            || 'Terjadi kesalahan, coba lagi.';
    },

    difficultyBadge(level) {
        const map = {
            beginner:     { label: 'Pemula',   cls: 'badge--green'  },
            intermediate: { label: 'Menengah', cls: 'badge--yellow' },
            advanced:     { label: 'Mahir',    cls: 'badge--red'    }
        };
        const d = map[level] || { label: level, cls: 'badge--gray' };
        return `<span class="badge ${d.cls}">${d.label}</span>`;
    },

    formatDate(dateStr) {
        return new Date(dateStr).toLocaleDateString('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric'
        });
    }
};