// Hero : séquence d'ouverture (en CSS), écrans réels qui se succèdent dans le téléphone,
// scène 3D qui suit le pointeur et s'ouvre au défilement. Le JavaScript ne pose que
// trois variables (--tilt-x, --tilt-y, --s) : la mise en scène reste dans le CSS.
import { prefersReducedMotion, hasFinePointer, onScrollFrame, clamp } from './env.js';

const SLIDE_MS = 3800;

export function initHero() {
  const hero = document.querySelector('.hero');
  if (!hero) return;

  // Deux images pour que les états de départ soient peints avant la transition.
  requestAnimationFrame(() => requestAnimationFrame(() => hero.classList.add('is-ready')));

  const scene = hero.querySelector('[data-scene]');
  const slideshow = initSlideshow(hero);
  initPauseToggle(hero, scene, slideshow);
  if (!scene || prefersReducedMotion()) return;
  initVisibility(scene, slideshow);
  initPointer(hero, scene);
  initSceneScroll(scene);
}

function initSlideshow(hero) {
  const screen = hero.querySelector('[data-slideshow]');
  const shots = screen ? [...screen.querySelectorAll('.phone__shot')] : [];
  const state = { inView: true, pausedByUser: false, hovered: false, start() {} };
  if (shots.length < 2) return state;

  let index = 0;
  let timer = null;

  const show = (i) => {
    shots[index].classList.remove('is-active');
    index = (i + shots.length) % shots.length;
    const next = shots[index];
    next.querySelector('img')?.setAttribute('loading', 'eager');
    next.classList.add('is-active');
  };

  const canRun = () => !prefersReducedMotion() && !state.pausedByUser && !state.hovered && state.inView && !document.hidden;
  state.start = () => {
    clearInterval(timer);
    timer = null;
    if (canRun()) timer = setInterval(() => show(index + 1), SLIDE_MS);
  };

  // Précharge les écrans suivants une fois la page chargée.
  window.addEventListener('load', () => {
    shots.forEach((s) => s.querySelector('img')?.setAttribute('loading', 'eager'));
  }, { once: true });

  document.addEventListener('visibilitychange', () => state.start());
  const phone = hero.querySelector('.scene__phone');
  phone?.addEventListener('pointerenter', () => { state.hovered = true; state.start(); });
  phone?.addEventListener('pointerleave', () => { state.hovered = false; state.start(); });
  state.start();
  return state;
}

// Un seul bouton met en pause tout ce qui bouge seul dans le hero (WCAG 2.2.2).
function initPauseToggle(hero, scene, slideshow) {
  const toggle = hero.querySelector('[data-slideshow-toggle]');
  if (!toggle) return;
  if (prefersReducedMotion()) { toggle.hidden = true; return; }
  toggle.addEventListener('click', () => {
    const paused = !slideshow.pausedByUser;
    slideshow.pausedByUser = paused;
    scene?.classList.toggle('is-paused', paused);
    toggle.setAttribute('aria-pressed', String(paused));
    toggle.querySelector('.sr-only').textContent = paused ? 'Reprendre les animations' : 'Mettre en pause les animations';
    slideshow.start();
  });
}

// Hors de l'écran, plus rien ne tourne.
function initVisibility(scene, slideshow) {
  if (!('IntersectionObserver' in window)) return;
  new IntersectionObserver(([entry]) => {
    scene.classList.toggle('is-offscreen', !entry.isIntersecting);
    slideshow.inView = entry.isIntersecting;
    slideshow.start();
  }).observe(scene);
}

// Ordinateur : la scène s'oriente vers le pointeur. Les profondeurs (translateZ)
// font le reste, sans calcul par élément.
function initPointer(hero, scene) {
  if (!hasFinePointer()) return;
  let frame = 0;
  let target = { x: 0, y: 0 };
  const apply = () => {
    frame = 0;
    scene.style.setProperty('--tilt-x', target.x.toFixed(3));
    scene.style.setProperty('--tilt-y', target.y.toFixed(3));
  };
  const queue = () => { if (!frame) frame = requestAnimationFrame(apply); };

  hero.addEventListener('pointermove', (event) => {
    if (window.innerWidth < 1100 || event.pointerType !== 'mouse') return;
    const rect = scene.getBoundingClientRect();
    target = {
      x: clamp((event.clientX - (rect.left + rect.width / 2)) / (window.innerWidth / 2), -1, 1),
      y: clamp((event.clientY - (rect.top + rect.height / 2)) / (window.innerHeight / 2), -1, 1),
    };
    queue();
  });
  hero.addEventListener('pointerleave', () => { target = { x: 0, y: 0 }; queue(); });
}

// Défilement : quand la scène remonte vers le haut de l'écran, elle s'incline et les
// morceaux s'écartent. Mesuré sur la scène elle-même pour qu'elle soit toujours vue
// au repos en arrivant (sur mobile, elle est sous le texte).
function initSceneScroll(scene) {
  let last = -1;
  onScrollFrame((vh) => {
    const rect = scene.getBoundingClientRect();
    if (rect.bottom < -200 || rect.top > vh) return;
    const s = Math.round(clamp((vh * 0.22 - rect.top) / (rect.height * 0.9)) * 1000) / 1000;
    if (s !== last) {
      last = s;
      scene.style.setProperty('--s', String(s));
    }
  });
}
