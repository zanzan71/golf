// Environnement partagé : préférences de mouvement, type de pointeur, boucle d'images.

const reduceQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
const finePointerQuery = window.matchMedia('(hover: hover) and (pointer: fine)');

export const prefersReducedMotion = () => reduceQuery.matches;
export const hasFinePointer = () => finePointerQuery.matches;
export const isDesktop = (min = 900) => window.innerWidth >= min;

export const clamp = (v, min = 0, max = 1) => Math.min(max, Math.max(min, v));

// Une seule boucle requestAnimationFrame pour tous les effets liés au défilement.
const subscribers = new Set();
let ticking = false;

function run() {
  ticking = false;
  const vh = window.innerHeight;
  for (const fn of subscribers) fn(vh);
}

export function requestFrame() {
  if (!ticking) {
    ticking = true;
    requestAnimationFrame(run);
  }
}

export function onScrollFrame(fn) {
  subscribers.add(fn);
  requestFrame();
  return () => subscribers.delete(fn);
}

window.addEventListener('scroll', requestFrame, { passive: true });
window.addEventListener('resize', requestFrame, { passive: true });

// Ne calcule un effet que lorsque son élément est proche de l'écran.
export function whenNear(el, margin = '25%') {
  const state = { near: false };
  if (!('IntersectionObserver' in window)) {
    state.near = true;
    return state;
  }
  new IntersectionObserver(([entry]) => {
    state.near = entry.isIntersecting;
    if (state.near) requestFrame();
  }, { rootMargin: `${margin} 0px ${margin} 0px` }).observe(el);
  return state;
}
