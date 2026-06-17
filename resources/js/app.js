import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const landingPage = document.body.classList.contains('landing-non-login');

    if (!landingPage) {
        return;
    }

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const revealElements = document.querySelectorAll('.reveal');

    if (prefersReducedMotion) {
        document.body.classList.add('page-ready');
        revealElements.forEach((element) => element.classList.add('reveal-visible'));
        return;
    }

    requestAnimationFrame(() => {
        document.body.classList.add('page-ready');
    });

    if (!('IntersectionObserver' in window)) {
        revealElements.forEach((element) => element.classList.add('reveal-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            entry.target.classList.add('reveal-visible');
            observer.unobserve(entry.target);
        });
    }, {
        threshold: 0.12,
        rootMargin: '0px 0px -6% 0px',
    });

    revealElements.forEach((element) => observer.observe(element));
});
