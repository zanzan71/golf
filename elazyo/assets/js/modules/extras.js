// Petits comportements : compteurs, défilement des valeurs avec pause,
// pré-remplissage du formulaire depuis les boutons d'appel.
import { prefersReducedMotion } from './env.js';

export function initCounters() {
  const els = [...document.querySelectorAll('[data-count-to]')];
  if (!els.length || prefersReducedMotion() || !('IntersectionObserver' in window)) return;
  const io = new IntersectionObserver((entries) => {
    for (const entry of entries) {
      if (!entry.isIntersecting) continue;
      io.unobserve(entry.target);
      count(entry.target);
    }
  }, { threshold: 0.6 });
  els.forEach((el) => io.observe(el));
}

function count(el) {
  const to = Number(el.dataset.countTo);
  const duration = 1200;
  const start = performance.now();
  el.setAttribute('aria-hidden', 'true');
  const tick = (now) => {
    const t = Math.min(1, (now - start) / duration);
    const eased = 1 - Math.pow(1 - t, 4);
    el.textContent = String(Math.round(eased * to));
    if (t < 1) requestAnimationFrame(tick);
    else el.removeAttribute('aria-hidden');
  };
  requestAnimationFrame(tick);
}

export function initMarquee() {
  const toggle = document.querySelector('[data-marquee-toggle]');
  const band = toggle?.closest('.valeurs');
  if (!toggle || !band) return;
  toggle.addEventListener('click', () => {
    const paused = band.classList.toggle('is-paused');
    toggle.setAttribute('aria-pressed', String(paused));
    toggle.querySelector('.sr-only').textContent = paused
      ? 'Reprendre le défilement des valeurs'
      : 'Mettre en pause le défilement des valeurs';
  });
}

export function initPrefill() {
  const form = document.querySelector('[data-contact-form]');
  if (!form) return;
  document.querySelectorAll('[data-prefill-objet], [data-prefill-profil]').forEach((link) => {
    link.addEventListener('click', () => {
      const objet = link.dataset.prefillObjet;
      const profil = link.dataset.prefillProfil;
      if (objet && form.objet) form.objet.value = objet;
      if (profil && form.profil && !form.profil.value) form.profil.value = profil;
      const delay = prefersReducedMotion() ? 0 : 700;
      setTimeout(() => form.nom?.focus({ preventScroll: true }), delay);
    });
  });
}
