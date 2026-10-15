import './bootstrap';

document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
    toggle.addEventListener('click', () => {
        const field = toggle.closest('.password-field');
        const input = field?.querySelector('[data-password-input]');

        if (!(input instanceof HTMLInputElement)) {
            return;
        }

        const isVisible = input.type === 'text';
        input.type = isVisible ? 'password' : 'text';
        toggle.setAttribute('aria-label', isVisible ? 'Tampilkan password' : 'Sembunyikan password');
        toggle.querySelector('[data-eye]')?.toggleAttribute('hidden', !isVisible);
        toggle.querySelector('[data-eye-off]')?.toggleAttribute('hidden', isVisible);
    });
});

document.querySelectorAll('[data-demo-auth]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        event.preventDefault();

        const feedback = form.querySelector('[data-auth-feedback]');
        const password = form.querySelector('[name="password"]');
        const confirmation = form.querySelector('[data-password-confirmation]');

        if (!(feedback instanceof HTMLElement) || !(password instanceof HTMLInputElement)) {
            return;
        }

        if (confirmation instanceof HTMLInputElement && password.value !== confirmation.value) {
            feedback.textContent = 'Password dan konfirmasi password belum sama.';
            feedback.dataset.state = 'error';
            confirmation.focus();
            return;
        }

        feedback.textContent = 'Login berhasil! Mengalihkan ke Beranda...';
        feedback.dataset.state = 'success';
        setTimeout(() => {
            window.location.href = '/beranda';
        }, 600);
    });
});

// Interactive Bottom Navigation Tabs
document.querySelectorAll('.beranda-bottom-nav .nav-tab').forEach((tab) => {
    tab.addEventListener('click', (event) => {
        const href = tab.getAttribute('href');
        if (href && href.startsWith('#')) {
            event.preventDefault();
            document.querySelectorAll('.beranda-bottom-nav .nav-tab').forEach((t) => {
                t.classList.remove('active');
                t.removeAttribute('aria-current');
            });
            tab.classList.add('active');
            tab.setAttribute('aria-current', 'page');
        }
    });
});
