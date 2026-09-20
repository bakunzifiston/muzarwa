function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

function markDone(el) {
    el.classList.add('motion-done');
    el.classList.remove('is-in');
}

function play(el) {
    el.classList.add('is-in');
    el.addEventListener('animationend', () => markDone(el), { once: true });
}

export function initStorefrontMotion() {
    const targets = [];

    document.querySelectorAll('.js-hero-animate').forEach((hero) => {
        [...hero.children].forEach((child, index) => {
            child.classList.add('js-reveal');
            child.style.setProperty('--reveal-delay', `${80 + index * 90}ms`);
            targets.push({ el: child, immediate: true });
        });
    });

    document.querySelectorAll('[data-animate]').forEach((el) => {
        const type = el.getAttribute('data-animate');
        if (type === 'left') el.classList.add('from-left');
        if (type === 'right') el.classList.add('from-right');
        if (type === 'scale') el.classList.add('from-scale');
        el.classList.add('js-reveal');
        targets.push({ el, immediate: false });
    });

    document.querySelectorAll('[data-stagger]').forEach((group) => {
        [...group.children].forEach((child, index) => {
            child.classList.add('js-reveal');
            child.style.setProperty('--reveal-delay', `${Math.min(index, 8) * 85}ms`);
            targets.push({ el: child, immediate: false });
        });
    });

    if (prefersReducedMotion()) {
        targets.forEach(({ el }) => el.classList.add('motion-done'));
        return;
    }

    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            play(entry.target);
            io.unobserve(entry.target);
        });
    }, { threshold: 0.14, rootMargin: '0px 0px -8% 0px' });

    targets.forEach(({ el, immediate }) => {
        if (immediate) {
            requestAnimationFrame(() => play(el));
            return;
        }
        io.observe(el);
    });
}

document.addEventListener('DOMContentLoaded', initStorefrontMotion);
