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

const minorCheckbox = document.getElementById('is_minor');
const minorFields = document.getElementById('minor-fields');

if (minorCheckbox && minorFields) {
    const updateMinorFields = () => {
        minorFields.hidden = !minorCheckbox.checked;
        minorFields.querySelectorAll('input').forEach((input) => {
            input.required = minorCheckbox.checked;
            input.disabled = !minorCheckbox.checked;
        });
    };

    minorCheckbox.addEventListener('change', updateMinorFields);
    updateMinorFields();
}

const evaluationForm = document.querySelector('[data-evaluation-form]');
if (evaluationForm) {
    const steps = [...evaluationForm.querySelectorAll('[data-step]')];
    const back = evaluationForm.querySelector('[data-wizard-back]');
    const next = evaluationForm.querySelector('[data-wizard-next]');
    const submit = evaluationForm.querySelector('[data-wizard-submit]');
    const progress = [...evaluationForm.querySelectorAll('.wizard-progress span')];
    let current = 0;

    const showStep = (index) => {
        current = index;
        steps.forEach((step, i) => {
            step.hidden = i !== current;
            step.querySelectorAll('input, select, textarea').forEach((field) => {
                field.disabled = i !== current;
            });
        });
        progress.forEach((item, i) => item.classList.toggle('active', i === current));
        back.hidden = current === 0;
        next.hidden = current === steps.length - 1;
        submit.hidden = current !== steps.length - 1;
    };

    next.addEventListener('click', () => {
        const fields = [...steps[current].querySelectorAll('input, select, textarea')];
        const invalid = fields.find((field) => !field.checkValidity());
        if (invalid) {
            invalid.reportValidity();
            return;
        }
        showStep(Math.min(current + 1, steps.length - 1));
    });
    back.addEventListener('click', () => showStep(Math.max(current - 1, 0)));
    evaluationForm.addEventListener('submit', () => {
        steps.forEach((step) => step.querySelectorAll('input, select, textarea').forEach((field) => { field.disabled = false; }));
    });
    showStep(0);

    const company = evaluationForm.querySelector('#evaluation-company');
    const slug = evaluationForm.querySelector('#evaluation-slug');
    const slugHelp = evaluationForm.querySelector('#slug-help');
    let slugEdited = slug.value.length > 0;
    let checkTimer;

    const suggestSlug = (value) => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 40);

    company.addEventListener('input', () => {
        if (!slugEdited) {
            slug.value = suggestSlug(company.value);
            slug.dispatchEvent(new Event('input'));
        }
    });
    slug.addEventListener('input', () => {
        if (document.activeElement === slug) slugEdited = true;
        clearTimeout(checkTimer);
        const candidate = slug.value;
        if (candidate.length < 3) {
            slugHelp.textContent = 'Escribe al menos tres caracteres para consultar disponibilidad.';
            return;
        }
        checkTimer = setTimeout(async () => {
            try {
                const url = evaluationForm.dataset.slugCheck.replace('__slug__', encodeURIComponent(candidate));
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                const result = await response.json();
                if (slug.value === candidate) slugHelp.textContent = result.available
                    ? 'Disponible por ahora. Se reservará cuando confirmes tu correo.'
                    : 'Ese subdominio no está disponible.';
            } catch {
                slugHelp.textContent = 'Comprobaremos la disponibilidad cuando confirmes tu correo.';
            }
        }, 450);
    });
}

const replacementForm = document.querySelector('[data-slug-replacement]');
if (replacementForm) {
    const slug = replacementForm.querySelector('#replacement-slug');
    const help = replacementForm.querySelector('#replacement-slug-help');
    let checkTimer;

    slug.addEventListener('input', () => {
        clearTimeout(checkTimer);
        const candidate = slug.value;
        if (candidate.length < 3) {
            help.textContent = 'Escribe al menos tres caracteres para consultar disponibilidad.';
            return;
        }

        checkTimer = setTimeout(async () => {
            try {
                const url = replacementForm.dataset.slugCheck.replace('__slug__', encodeURIComponent(candidate));
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                const result = await response.json();
                if (slug.value === candidate) help.textContent = result.available
                    ? 'Disponible por ahora. Se reservará al guardar.'
                    : 'Ese subdominio no está disponible.';
            } catch {
                if (slug.value === candidate) help.textContent = 'Comprobaremos la disponibilidad al guardar.';
            }
        }, 450);
    });
}
