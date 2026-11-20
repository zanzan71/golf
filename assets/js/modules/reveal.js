// Révélations au défilement : titres ligne par ligne, masques sur les images,
// blocs et listes en séquence. Les états de départ sont dans site.css (layer motion).
//
// Principe « complet au repos » : rien n'est caché par défaut. Seuls les éléments
// situés sous l'écran au chargement reçoivent .reveal-pending, puis se révèlent en
// arrivant à l'écran. Si le script ne tourne pas, ou si la page est affichée en
// entier d'un coup (aperçu, capture), tout reste visible.
import { onScrollFrame } from './env.js';

export function initReveal() {
  const els = [...document.querySelectorAll('[data-reveal]')];

  document.querySelectorAll('[data-reveal="stagger"]').forEach((list) => {
    [...list.children].forEach((child, i) => child.style.setProperty('--i', i));
  });

  const show = (el) => {
    if (el.classList.contains('is-visible')) return;
    el.classList.add('is-visible');
    if (el.dataset.reveal === 'mask') {
      const done = (event) => {
        if (event.target !== el || event.propertyName !== 'clip-path') return;
        el.classList.add('reveal-done');
        el.removeEventListener('transitionend', done);
      };
      el.addEventListener('transitionend', done);
      setTimeout(() => el.classList.add('reveal-done'), 1600);
    }
  };

  const vh = window.innerHeight;
  const pending = els.filter((el) => el.getBoundingClientRect().top > vh * 0.9);
  if (!pending.length || !('IntersectionObserver' in window)) return;

  pending.forEach((el) => el.classList.add('reveal-pending'));

  const io = new IntersectionObserver((entries) => {
    for (const entry of entries) {
      if (!entry.isIntersecting) continue;
      show(entry.target);
      io.unobserve(entry.target);
    }
  }, { rootMargin: '0px 0px -8% 0px', threshold: 0.01 });
  pending.forEach((el) => io.observe(el));

  // Filet de sécurité : si l'observateur ne se déclenche pas, la position à l'écran suffit.
  onScrollFrame((h) => {
    for (const el of pending) {
      if (!el.classList.contains('is-visible') && el.getBoundingClientRect().top < h * 0.95) show(el);
    }
  });
}
