function checkAuth(adminOnly = false) {
    const token = localStorage.getItem('pawtrainer_token') || sessionStorage.getItem('pawtrainer_token');
    const user = JSON.parse(localStorage.getItem('pawtrainer_user') || sessionStorage.getItem('pawtrainer_user') || 'null');

    if (!token) {
        window.location.href = 'login.html';
        return false;
    }

    if (adminOnly && user?.role !== 'admin') {
        window.location.href = 'dashboard.html';
        return false;
    }

    return true;
}

function logout() {
    localStorage.clear();
    sessionStorage.clear();
    window.location.href = 'login.html';
}
