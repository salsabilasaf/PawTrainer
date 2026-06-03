document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('loginForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            const response = await AuthAPI.login(email, password);
            const data = unwrapData(response);

            localStorage.setItem('pawtrainer_token', data.token);
            localStorage.setItem('pawtrainer_user', JSON.stringify(data.user));

            window.location.href = data.user.role === 'admin'
                ? 'admin-dashboard.html'
                : 'dashboard.html';
        } catch (error) {
            UI.showToast(UI.parseError(error), 'error');
        }
    });
});
