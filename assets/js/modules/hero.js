// Hero : séquence d'ouverture, écrans réels qui se succèdent dans le téléphone,
// léger relief qui suit le pointeur (ordinateur uniquement).
import { prefersReducedMotion, hasFinePointer } from './env.js';

const SLIDE_MS = 3600;

export function initHero() {
  const hero = document.querySelector('.hero');
  if (!hero) return;

  // Deux images pour que les états de départ soient peints avant la transition.
  requestAnimationFrame(() => requestAnimationFrame(() => hero.classList.add('is-ready')));

  initSlideshow(hero);
  initTilt(hero);
}

function initSlideshow(hero) {
  const screen = hero.querySelector('[data-slideshow]');
  const toggle = hero.querySelector('[data-slideshow-toggle]');
  if (!screen) return;
  const shots = [...screen.querySelectorAll('.phone__shot')];
  if (shots.length < 2) return;

  let index = 0;
  let timer = null;
  let inView = true;
  let pausedByUser = false;
  let hovered = false;

  const show = (i) => {
    shots[index].classList.remove('is-active');
    index = (i + shots.length) % shots.length;
    const next = shots[index];
    next.querySelector('img')?.setAttribute('loading', 'eager');
    next.classList.add('is-active');
  };

  const canRun = () => !prefersReducedMotion() && !pausedByUser && !hovered && inView && !document.hidden;
  const stop = () => { clearInterval(timer); timer = null; };
  const start = () => {
    stop();
    if (canRun()) timer = setInterval(() => show(index + 1), SLIDE_MS);
  };

  // Précharge les écrans suivants une fois la page chargée.
  window.addEventListener('load', () => {
    shots.forEach((s) => s.querySelector('img')?.setAttribute('loading', 'eager'));
  }, { once: true });

  if ('IntersectionObserver' in window) {
    new IntersectionObserver(([entry]) => {
      inView = entry.isIntersecting;
      start();
    }).observe(screen);
  }
  document.addEventListener('visibilitychange', start);
  const stage = hero.querySelector('.hero__stage');
  stage?.addEventListener('pointerenter', () => { hovered = true; start(); });
  stage?.addEventListener('pointerleave', () => { hovered = false; start(); });

  if (toggle) {
    if (prefersReducedMotion()) toggle.hidden = true;
    toggle.addEventListener('click', () => {
      pausedByUser = !pausedByUser;
      toggle.setAttribute('aria-pressed', String(pausedByUser));
      toggle.querySelector('.sr-only').textContent = pausedByUser
        ? 'Reprendre le défilement des écrans'
        : 'Mettre en pause le défilement des écrans';
      start();
    });
  }
  start();
}

function initTilt(hero) {
  const stage = hero.querySelector('[data-tilt]');
  if (!stage || prefersReducedMotion() || !hasFinePointer()) return;
  const chips = [...stage.querySelectorAll('[data-depth]')];
  let frame = 0;
  let target = { x: 0, y: 0 };

  const apply = () => {
    frame = 0;
    stage.style.setProperty('--rx', `${(-target.y * 5).toFixed(2)}deg`);
    stage.style.setProperty('--ry', `${(target.x * 7).toFixed(2)}deg`);
    chips.forEach((chip) => {
      const d = Number(chip.dataset.depth) || 1;
      chip.style.translate = `${(target.x * 14 * d).toFixed(1)}px ${(target.y * 10 * d).toFixed(1)}px`;
    });
  };

  hero.addEventListener('pointermove', (event) => {
    if (window.innerWidth < 1100) return;
    const rect = hero.getBoundingClientRect();
    target = {
      x: (event.clientX - rect.left) / rect.width - 0.5,
      y: (event.clientY - rect.top) / rect.height - 0.5,
    };
    if (!frame) frame = requestAnimationFrame(apply);
  });
  hero.addEventListener('pointerleave', () => {
    target = { x: 0, y: 0 };
    if (!frame) frame = requestAnimationFrame(apply);
  });
}
