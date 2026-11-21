// Hero : séquence d'ouverture (en CSS), puis une semaine de club racontée en quatre étapes,
// scène 3D qui suit le pointeur et s'ouvre au défilement. Le JavaScript ne pose que
// trois variables (--tilt-x, --tilt-y, --s) : la mise en scène reste dans le CSS.
import { prefersReducedMotion, hasFinePointer, onScrollFrame, clamp } from './env.js';

// Durée de chaque étape de la semaine (mardi, jeudi, samedi, lundi), en millisecondes.
const BEAT_MS = [2300, 2600, 2400, 3200];
// La première étape attend que le téléphone soit levé (animation d'entrée du CSS).
const FIRST_DELAY = 1400;

export function initHero() {
  const hero = document.querySelector('.hero');
  if (!hero) return;

  // Deux images pour que les états de départ soient peints avant la transition.
  requestAnimationFrame(() => requestAnimationFrame(() => hero.classList.add('is-ready')));

  const scene = hero.querySelector('[data-scene]');
  if (!scene) return;
  const semaine = initSemaine(hero, scene);
  initPauseToggle(hero, scene, semaine);
  if (prefersReducedMotion()) return;
  initVisibility(scene, semaine);
  initPointer(hero, scene);
  initSceneScroll(scene);
}

// Une semaine de club en quatre étapes. Le CSS joue chaque étape (cartes, compteurs,
// sièges, score, barres) ; le JavaScript ne fait que changer d'étape et poser les classes.
function initSemaine(hero, scene) {
  const shots = [...scene.querySelectorAll('[data-beat-screens] .phone__shot')];
  const days = [...hero.querySelectorAll('[data-goto-beat]')];
  const counter = scene.querySelector('[data-beat-count]');
  const reduced = prefersReducedMotion();
  const state = { inView: true, pausedByUser: false, start() {} };

  let beat = 0;
  let timers = [];
  let tween = 0;
  let first = true;

  const later = (fn, ms) => timers.push(setTimeout(fn, ms));
  const clearAll = () => { timers.forEach(clearTimeout); timers = []; cancelAnimationFrame(tween); };

  const countUp = (delay) => {
    if (!counter) return;
    const to = Number(counter.dataset.beatCount) || 0;
    counter.textContent = '0';
    later(() => {
      const t0 = performance.now();
      const step = (now) => {
        const k = Math.min(1, (now - t0) / 800);
        counter.textContent = String(Math.round(to * (1 - Math.pow(1 - k, 3))));
        if (k < 1) tween = requestAnimationFrame(step);
      };
      tween = requestAnimationFrame(step);
    }, delay);
  };

  const show = (i) => {
    beat = (i + BEAT_MS.length) % BEAT_MS.length;
    scene.dataset.beat = String(beat);
    shots.forEach((s, k) => s.classList.toggle('is-active', k === beat));
    shots[beat]?.querySelector('img')?.setAttribute('loading', 'eager');
    days.forEach((d, k) => {
      d.setAttribute('aria-pressed', String(k === beat));
      d.classList.toggle('is-done', k < beat);
      d.style.setProperty('--beat-ms', `${BEAT_MS[k]}ms`);
    });
  };

  // Joue l'étape courante : relance les micro-animations, puis passe à la suivante.
  const play = () => {
    clearAll();
    const delay = first ? FIRST_DELAY : 0;
    first = false;
    scene.classList.remove('is-play', 'is-xp');
    if (beat === 0) scene.classList.remove('is-argent');
    // Restart des animations de la barre de progression.
    hero.classList.remove('is-playing');
    void scene.offsetWidth;
    later(() => {
      scene.classList.add('is-play');
      hero.classList.add('is-playing');
      if (beat === 0) countUp(150);
      if (beat === 3) {
        later(() => scene.classList.add('is-xp'), 900);
        later(() => scene.classList.add('is-argent'), 1500);
      }
      if (canRun()) later(() => { show(beat + 1); play(); }, BEAT_MS[beat]);
    }, delay);
  };

  const canRun = () => !state.pausedByUser && state.inView && !document.hidden;

  // Choisir un jour : on y va directement, l'animation continue depuis ce jour.
  days.forEach((d, k) => d.addEventListener('click', () => {
    first = false;
    show(k);
    if (reduced) { scene.classList.toggle('is-argent', k === 3 || k === 0); return; }
    play();
  }));

  if (reduced) {
    // Image fixe : 14/14 confirmés et la carte déjà passée en Argent.
    scene.classList.add('is-argent');
    show(0);
    return state;
  }

  scene.classList.add('is-live');
  show(0);
  window.addEventListener('load', () => {
    shots.forEach((s) => s.querySelector('img')?.setAttribute('loading', 'eager'));
  }, { once: true });
  later(() => scene.classList.add('is-started'), FIRST_DELAY + 1000);

  state.start = () => {
    if (canRun()) { hero.classList.remove('is-paused'); play(); }
    else { clearAll(); hero.classList.add('is-paused'); }
  };
  document.addEventListener('visibilitychange', () => state.start());
  play();
  return state;
}

// Un seul bouton met en pause tout ce qui bouge seul dans le hero (WCAG 2.2.2).
function initPauseToggle(hero, scene, semaine) {
  const toggle = hero.querySelector('[data-slideshow-toggle]');
  if (!toggle) return;
  if (prefersReducedMotion()) { toggle.hidden = true; return; }
  toggle.addEventListener('click', () => {
    const paused = !semaine.pausedByUser;
    semaine.pausedByUser = paused;
    scene.classList.toggle('is-paused', paused);
    toggle.setAttribute('aria-pressed', String(paused));
    toggle.querySelector('.sr-only').textContent = paused ? 'Reprendre l’animation' : 'Mettre en pause l’animation';
    semaine.start();
  });
}

// Hors de l'écran, plus rien ne tourne.
function initVisibility(scene, semaine) {
  if (!('IntersectionObserver' in window)) return;
  let wasIn = true;
  new IntersectionObserver(([entry]) => {
    scene.classList.toggle('is-offscreen', !entry.isIntersecting);
    semaine.inView = entry.isIntersecting;
    if (entry.isIntersecting !== wasIn) { wasIn = entry.isIntersecting; semaine.start(); }
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
    // L'ouverture commence quand la scène atteint le haut de l'écran, ou dès le premier
    // défilement si elle y est déjà au chargement (mobile : la scène ouvre la page).
    const start = Math.max(0, rect.top + window.scrollY - vh * 0.22);
    const s = Math.round(clamp((window.scrollY - start) / (rect.height * 0.9)) * 1000) / 1000;
    if (s !== last) {
      last = s;
      scene.style.setProperty('--s', String(s));
    }
  });
}
