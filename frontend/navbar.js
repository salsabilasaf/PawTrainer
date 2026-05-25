// ============================================================
//  PawTrainer — Navbar Component
//  Inject navbar ke setiap halaman
// ============================================================

function renderNavbar(activePage = '') {
    const user = Auth.getUser();

    const navLinks = [
        { href: 'dashboard.html',  label: '🏠 Dashboard',     id: 'dashboard'  },
        { href: 'tutorials.html',  label: '📚 Tutorial',      id: 'tutorials'  },
        { href: 'favorites.html',  label: '❤️ Favorit',       id: 'favorites'  },
        { href: 'breeds.html',     label: '🐱 Ras Kucing',    id: 'breeds'     },
        { href: 'catfacts.html',   label: '💡 Fakta Kucing',  id: 'catfacts'   },
    ];

    const linksHtml = navLinks.map(l =>
        `<a href="${l.href}" class="nav-link ${activePage === l.id ? 'active' : ''}">${l.label}</a>`
    ).join('');

    const initials = user?.name
        ? user.name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2)
        : '?';

    const html = `
    <nav class="navbar">
        <a href="dashboard.html" class="navbar__brand">
            <span class="paw">🐾</span> PawTrainer
        </a>
        <div class="navbar__nav">${linksHtml}</div>
        <div class="navbar__right">
            <div class="navbar__user">
                <div class="navbar__avatar">${initials}</div>
                <span class="d-none d-sm-inline">${user?.name || ''}</span>
            </div>
            <button class="btn btn--ghost btn--sm" id="logoutBtn">Keluar</button>
        </div>
        <button class="navbar__hamburger" id="hamburgerBtn" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </nav>
    <div class="nav-mobile" id="navMobile">
        <div class="nav-mobile__panel">
            <div style="font-size:0.8rem;color:var(--text-muted);padding:0 0.5rem 0.75rem;border-bottom:1px solid var(--sand);margin-bottom:0.5rem;">
                Halo, <strong>${user?.name || ''}</strong>
            </div>
            ${navLinks.map(l =>
                `<a href="${l.href}" class="nav-link ${activePage === l.id ? 'active' : ''}">${l.label}</a>`
            ).join('')}
            <div style="margin-top:auto;padding-top:1rem;border-top:1px solid var(--sand);">
                <button class="btn btn--outline btn--full" id="logoutBtnMobile">Keluar</button>
            </div>
        </div>
    </div>`;

    document.body.insertAdjacentHTML('afterbegin', html);

    // Logout handler
    const handleLogout = async () => {
        try {
            await AuthAPI.logout();
        } catch (_) {}
        Auth.clear();
        window.location.href = 'login.html';
    };

    document.getElementById('logoutBtn')?.addEventListener('click', handleLogout);
    document.getElementById('logoutBtnMobile')?.addEventListener('click', handleLogout);

    // Hamburger toggle
    const hamburger = document.getElementById('hamburgerBtn');
    const mobileNav  = document.getElementById('navMobile');

    hamburger?.addEventListener('click', () => {
        mobileNav?.classList.toggle('open');
    });

    mobileNav?.addEventListener('click', e => {
        if (e.target === mobileNav) mobileNav.classList.remove('open');
    });
}
