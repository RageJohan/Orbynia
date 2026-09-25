const menuButton = document.querySelector('.menu-toggle');
const navigation = document.getElementById('site-nav');

if (menuButton && navigation) {
    menuButton.addEventListener('click', () => {
        const open = menuButton.getAttribute('aria-expanded') !== 'true';
        menuButton.setAttribute('aria-expanded', String(open));
        menuButton.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
        navigation.classList.toggle('is-open', open);
    });

    navigation.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            navigation.classList.remove('is-open');
            menuButton.setAttribute('aria-expanded', 'false');
            menuButton.setAttribute('aria-label', 'Abrir menú');
        });
    });
}


const siteHeader = document.querySelector('.site-header');
const pageSections = [...document.querySelectorAll('main > section, .site-footer')];

if (siteHeader && pageSections.length) {
    let scheduled = false;

    const updateHeader = () => {
        const probeY = siteHeader.getBoundingClientRect().bottom + 2;
        const section = pageSections.find((candidate) => {
            const bounds = candidate.getBoundingClientRect();
            return bounds.top <= probeY && bounds.bottom > probeY;
        });

        siteHeader.dataset.tone = section?.dataset.headerTheme || 'light';
        siteHeader.classList.toggle('is-scrolled', window.scrollY > 16);
        scheduled = false;
    };

    const scheduleHeaderUpdate = () => {
        if (scheduled) return;
        scheduled = true;
        window.requestAnimationFrame(updateHeader);
    };

    window.addEventListener('scroll', scheduleHeaderUpdate, { passive: true });
    window.addEventListener('resize', scheduleHeaderUpdate);
    updateHeader();
}
