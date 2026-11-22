/* ELAZYO — script du site, assemblé depuis assets/js/modules par scripts/assembler-js.mjs.
   Ne pas modifier ce fichier à la main : modifier les modules puis relancer l'assemblage. */
(function () {
'use strict';
// ---- env.js ----
// Environnement partagé : préférences de mouvement, type de pointeur, boucle d'images.

const reduceQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
const finePointerQuery = window.matchMedia('(hover: hover) and (pointer: fine)');

const prefersReducedMotion = () => reduceQuery.matches;
const hasFinePointer = () => finePointerQuery.matches;
const isDesktop = (min = 900) => window.innerWidth >= min;

const clamp = (v, min = 0, max = 1) => Math.min(max, Math.max(min, v));

// Une seule boucle requestAnimationFrame pour tous les effets liés au défilement.
const subscribers = new Set();
let ticking = false;

function run() {
  ticking = false;
  const vh = window.innerHeight;
  for (const fn of subscribers) fn(vh);
}

function requestFrame() {
  if (!ticking) {
    ticking = true;
    requestAnimationFrame(run);
  }
}

function onScrollFrame(fn) {
  subscribers.add(fn);
  requestFrame();
  return () => subscribers.delete(fn);
}

window.addEventListener('scroll', requestFrame, { passive: true });
window.addEventListener('resize', requestFrame, { passive: true });

// Ne calcule un effet que lorsque son élément est proche de l'écran.
function whenNear(el, margin = '25%') {
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

// ---- header.js ----
// En-tête : passe en mode compact au défilement, indique la section en cours,
// gère le menu mobile (piège du focus, Échap, verrouillage du défilement).

function initHeader() {
  const header = document.querySelector('[data-header]');
  if (!header) return;

  const update = () => header.classList.toggle('is-scrolled', window.scrollY > 8);
  update();
  window.addEventListener('scroll', update, { passive: true });

  // Section en cours dans la navigation
  const links = [...header.querySelectorAll('[data-nav]')];
  const byId = new Map(links.map((a) => [a.getAttribute('href').slice(1), a]));
  if ('IntersectionObserver' in window && byId.size) {
    const io = new IntersectionObserver((entries) => {
      for (const entry of entries) {
        const link = byId.get(entry.target.id);
        if (!link) continue;
        if (entry.isIntersecting) {
          links.forEach((a) => {
            a.classList.toggle('is-current', a === link);
            if (a === link) a.setAttribute('aria-current', 'true');
            else a.removeAttribute('aria-current');
          });
        } else if (link.classList.contains('is-current')) {
          link.classList.remove('is-current');
          link.removeAttribute('aria-current');
        }
      }
    }, { rootMargin: '-45% 0px -50% 0px' });
    byId.forEach((_, id) => {
      const section = document.getElementById(id);
      if (section) io.observe(section);
    });
  }

  // Menu mobile
  const toggle = header.querySelector('[data-menu-toggle]');
  const menu = header.querySelector('[data-menu]');
  const label = header.querySelector('[data-menu-label]');
  if (!toggle || !menu) return;

  menu.querySelectorAll('.mobile-menu__list li').forEach((li, i) => li.style.setProperty('--i', i));

  const focusables = () => [toggle, ...menu.querySelectorAll('a, button')];

  const open = () => {
    menu.hidden = false;
    header.classList.add('menu-open');
    toggle.setAttribute('aria-expanded', 'true');
    label.textContent = 'Fermer le menu';
    document.documentElement.style.overflow = 'hidden';
    menu.querySelector('a')?.focus({ preventScroll: true });
  };
  const close = (returnFocus = true) => {
    menu.hidden = true;
    header.classList.remove('menu-open');
    toggle.setAttribute('aria-expanded', 'false');
    label.textContent = 'Ouvrir le menu';
    document.documentElement.style.overflow = '';
    if (returnFocus) toggle.focus({ preventScroll: true });
  };

  toggle.addEventListener('click', () => (menu.hidden ? open() : close()));
  menu.addEventListener('click', (event) => {
    if (event.target.closest('a')) close(false);
  });
  document.addEventListener('keydown', (event) => {
    if (menu.hidden) return;
    if (event.key === 'Escape') {
      event.preventDefault();
      close();
    } else if (event.key === 'Tab') {
      const items = focusables();
      const first = items[0];
      const last = items[items.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    }
  });
  window.addEventListener('resize', () => {
    if (!menu.hidden && window.innerWidth > 1180) close(false);
  });
}

// ---- reveal.js ----
// Révélations au défilement : titres ligne par ligne, masques sur les images,
// blocs et listes en séquence. Les états de départ sont dans site.css (layer motion).
//
// Principe « complet au repos » : rien n'est caché par défaut. Seuls les éléments
// situés sous l'écran au chargement reçoivent .reveal-pending, puis se révèlent en
// arrivant à l'écran. Si le script ne tourne pas, ou si la page est affichée en
// entier d'un coup (aperçu, capture), tout reste visible.

function initReveal() {
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

// ---- hero.js ----
// Hero : séquence d'ouverture (en CSS), puis une semaine de club racontée en quatre étapes,
// scène 3D qui suit le pointeur et s'ouvre au défilement. Le JavaScript ne pose que
// trois variables (--tilt-x, --tilt-y, --s) : la mise en scène reste dans le CSS.

// Durée de chaque étape de la semaine (mardi, jeudi, samedi, lundi), en millisecondes.
// Chaque étape s'arrête environ une demi-seconde après la fin de ses animations.
const BEAT_MS = [1700, 2200, 2100, 2900];
// La première étape attend que le téléphone soit levé (animation d'entrée du CSS).
const FIRST_DELAY = 1400;

function initHero() {
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
        later(() => scene.classList.add('is-xp'), 800);
        later(() => scene.classList.add('is-argent'), 1350);
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

// ---- scroll-effects.js ----
// Effets liés au défilement : parallaxe multicouche, chaîne des maillons,
// manifeste qui s'éclaire mot à mot, saison qui se remplit, écran qui défile.

function initScrollEffects() {
  const reduce = prefersReducedMotion();
  initChain(reduce);
  initSeason(reduce);
  if (reduce) return;
  initParallax();
  initWords();
  initScreenScroll();
  initMur();
}

// Mur d'écrans : les colonnes glissent en sens opposés pendant que le mur traverse l'écran.
function initMur() {
  const frame = document.querySelector('[data-mur]');
  const plan = frame?.querySelector('.mur__plan');
  if (!plan) return;
  const near = whenNear(frame);
  let last = null;
  onScrollFrame((vh) => {
    if (!near.near) return;
    const rect = frame.getBoundingClientRect();
    const p = clamp((rect.top + rect.height / 2 - vh / 2) / (vh / 2 + rect.height / 2), -1, 1);
    const v = Math.round(p * 1000) / 1000;
    if (v !== last) {
      last = v;
      plan.style.setProperty('--p', String(v));
    }
  });
}

function initParallax() {
  const els = [...document.querySelectorAll('[data-parallax]')].map((el) => ({
    el,
    factor: Number(el.dataset.parallax) || 0,
    near: whenNear(el),
    y: 0,
  }));
  if (!els.length) return;

  onScrollFrame((vh) => {
    const enabled = window.innerWidth >= 768;
    for (const item of els) {
      if (!enabled) {
        if (item.y !== 0) { item.y = 0; item.el.style.translate = ''; }
        continue;
      }
      if (!item.near.near) continue;
      const rect = item.el.getBoundingClientRect();
      const center = rect.top - item.y + rect.height / 2;
      const y = Math.round((center - vh / 2) * item.factor * 10) / 10;
      if (y !== item.y) {
        item.y = y;
        item.el.style.translate = `0 ${y}px`;
      }
    }
  });
}

function initChain(reduce) {
  const chain = document.querySelector('[data-chain]');
  if (!chain) return;
  const items = [...chain.querySelectorAll('.chain__item')];
  if (reduce) {
    chain.style.setProperty('--p', '1');
    items.forEach((item) => item.classList.add('is-on'));
    return;
  }
  const near = whenNear(chain);
  let last = -1;
  onScrollFrame((vh) => {
    if (!near.near) return;
    const rect = chain.getBoundingClientRect();
    const p = isDesktop()
      ? clamp((vh * 0.88 - rect.top) / (vh * 0.5))
      : clamp((vh * 0.62 - rect.top) / Math.max(rect.height - 40, 1));
    const rounded = Math.round(p * 1000) / 1000;
    if (rounded === last) return;
    last = rounded;
    chain.style.setProperty('--p', String(rounded));
    items.forEach((item, i) => item.classList.toggle('is-on', rounded >= i / (items.length - 1) - 0.02));
  });
}

function initSeason(reduce) {
  const season = document.querySelector('[data-season]');
  if (!season) return;
  if (reduce) {
    season.style.setProperty('--p', '1');
    return;
  }
  const near = whenNear(season);
  let last = -1;
  onScrollFrame((vh) => {
    if (!near.near) return;
    const rect = season.getBoundingClientRect();
    const p = Math.round(clamp((vh * 0.85 - rect.top) / (rect.height + vh * 0.15)) * 1000) / 1000;
    if (p === last) return;
    last = p;
    season.style.setProperty('--p', String(p));
  });
}

// Le manifeste s'éclaire mot après mot, au rythme de la lecture.
function initWords() {
  const block = document.querySelector('[data-words]');
  if (!block) return;
  const words = [];
  block.querySelectorAll('p').forEach((p) => {
    const parts = p.textContent.split(/(\s+)/);
    p.textContent = '';
    for (const part of parts) {
      if (!part) continue;
      if (/^\s+$/.test(part)) {
        p.append(part);
      } else {
        const span = document.createElement('span');
        span.className = 'w';
        span.textContent = part;
        p.append(span);
        words.push(span);
      }
    }
  });
  block.classList.add('is-split');
  const near = whenNear(block);
  let lit = -1;
  onScrollFrame((vh) => {
    if (!near.near) return;
    const rect = block.getBoundingClientRect();
    // Entièrement éclairé quand le haut du texte atteint 55 % de l'écran ; ne s'éteint jamais.
    const p = clamp((vh * 0.92 - rect.top) / (rect.height * 0.55 + vh * 0.37));
    const count = Math.round(p * words.length);
    if (count <= lit) return;
    for (let i = Math.max(lit, 0); i < count; i++) words[i].classList.add('is-lit');
    lit = count;
  });
}

// Une capture plus haute que l'écran défile avec la page (mouvement déclenché par la personne).
function initScreenScroll() {
  document.querySelectorAll('.phone--scroll').forEach((phone) => {
    const img = phone.querySelector('img');
    if (!img) return;
    const near = whenNear(phone);
    onScrollFrame((vh) => {
      if (!near.near) return;
      const rect = phone.getBoundingClientRect();
      const p = clamp((vh * 0.9 - rect.top) / (vh * 0.9 + rect.height * 0.2));
      img.style.transform = `translateY(${(-p * 21.4).toFixed(2)}%)`;
    });
  });
}

// ---- tabs.js ----
// Onglets des profils, selon le modèle WAI-ARIA « Tabs » (activation automatique).

function initTabs() {
  document.querySelectorAll('[data-tabs]').forEach(setup);
}

function setup(root) {
  const list = root.querySelector('[role="tablist"]');
  const tabs = [...root.querySelectorAll('[role="tab"]')];
  const panels = tabs.map((t) => document.getElementById(t.getAttribute('aria-controls')));
  const vertical = window.matchMedia('(min-width: 900px)');

  const moveIndicator = () => {
    const current = tabs.find((t) => t.getAttribute('aria-selected') === 'true');
    if (!current) return;
    if (vertical.matches) {
      list.style.setProperty('--ind-y', `${current.offsetTop}px`);
      list.style.setProperty('--ind-h', `${current.offsetHeight}px`);
    } else {
      const target = current.offsetLeft - list.clientWidth / 2 + current.offsetWidth / 2;
      list.scrollTo({ left: Math.max(0, target), behavior: 'smooth' });
    }
  };

  const select = (tab, { focus = false } = {}) => {
    tabs.forEach((t, i) => {
      const selected = t === tab;
      t.setAttribute('aria-selected', String(selected));
      t.tabIndex = selected ? 0 : -1;
      const panel = panels[i];
      if (!panel) return;
      if (selected) {
        const wasHidden = panel.hidden;
        panel.hidden = false;
        if (wasHidden) {
          panel.classList.remove('is-entering');
          void panel.offsetWidth; // relance l'animation d'entrée
          panel.classList.add('is-entering');
        }
        panel.querySelectorAll('img[loading="lazy"]').forEach((img) => img.setAttribute('loading', 'eager'));
      } else {
        panel.hidden = true;
      }
    });
    if (focus) tab.focus();
    moveIndicator();
  };

  tabs.forEach((tab) => tab.addEventListener('click', () => select(tab)));

  list.addEventListener('keydown', (event) => {
    const i = tabs.indexOf(document.activeElement);
    if (i < 0) return;
    let next = null;
    switch (event.key) {
      case 'ArrowDown':
      case 'ArrowRight': next = tabs[(i + 1) % tabs.length]; break;
      case 'ArrowUp':
      case 'ArrowLeft': next = tabs[(i - 1 + tabs.length) % tabs.length]; break;
      case 'Home': next = tabs[0]; break;
      case 'End': next = tabs[tabs.length - 1]; break;
      default: return;
    }
    event.preventDefault();
    select(next, { focus: true });
  });

  window.addEventListener('resize', moveIndicator, { passive: true });
  if (document.fonts?.ready) document.fonts.ready.then(moveIndicator);
  moveIndicator();
}

// ---- cards.js ----
// MY CARD : les cinq paliers en éventail. Le palier choisi passe devant ;
// la carte active suit légèrement le pointeur, avec un reflet.

function initCards() {
  const stage = document.querySelector('[data-cards]');
  if (!stage) return;
  const cards = [...stage.querySelectorAll('[data-card]')];
  const buttons = [...document.querySelectorAll('[data-tier-buttons] [data-tier]')];
  const reduce = prefersReducedMotion();
  let active = 0;
  let userTouched = false;

  const setActive = (i) => {
    active = i;
    cards.forEach((card, j) => {
      const offset = j - i;
      card.style.setProperty('--o', String(offset));
      card.style.setProperty('--a', String(Math.abs(offset)));
      card.classList.toggle('is-active', j === i);
      card.setAttribute('aria-hidden', String(j !== i));
    });
    buttons.forEach((b, j) => {
      b.classList.toggle('is-active', j === i);
      b.setAttribute('aria-pressed', String(j === i));
    });
  };

  buttons.forEach((button, i) => button.addEventListener('click', () => {
    userTouched = true;
    setActive(i);
  }));
  cards.forEach((card, i) => card.addEventListener('click', () => {
    if (i === active) return;
    userTouched = true;
    setActive(i);
  }));

  setActive(0);

  // À l'arrivée : une montée de palier, de Bronze à Élite (moins de 5 s, une seule fois).
  if (!reduce && 'IntersectionObserver' in window) {
    const io = new IntersectionObserver(([entry]) => {
      if (!entry.isIntersecting) return;
      io.disconnect();
      let i = 0;
      const step = () => {
        if (userTouched || i >= cards.length - 1) return;
        i += 1;
        setActive(i);
        setTimeout(step, 1000);
      };
      setTimeout(step, 900);
    }, { threshold: 0.55 });
    io.observe(stage);
  }

  // Relief et reflet sur la carte active (souris uniquement).
  if (reduce || !hasFinePointer()) return;
  let frame = 0;
  let pos = null;
  const apply = () => {
    frame = 0;
    const card = cards[active];
    cards.forEach((c) => {
      if (c !== card) {
        c.style.removeProperty('--tx');
        c.style.removeProperty('--ty');
      }
    });
    if (!pos) {
      card.style.setProperty('--tx', '0deg');
      card.style.setProperty('--ty', '0deg');
      return;
    }
    card.style.setProperty('--tx', `${(-pos.y * 12).toFixed(2)}deg`);
    card.style.setProperty('--ty', `${(pos.x * 14).toFixed(2)}deg`);
    card.style.setProperty('--sx', `${(130 - (pos.x + 0.5) * 160).toFixed(1)}%`);
  };
  stage.addEventListener('pointermove', (event) => {
    const rect = cards[active].getBoundingClientRect();
    const x = (event.clientX - rect.left) / rect.width - 0.5;
    const y = (event.clientY - rect.top) / rect.height - 0.5;
    pos = Math.abs(x) <= 0.7 && Math.abs(y) <= 0.7 ? { x, y } : null;
    if (!frame) frame = requestAnimationFrame(apply);
  });
  stage.addEventListener('pointerleave', () => {
    pos = null;
    if (!frame) frame = requestAnimationFrame(apply);
  });
}

// ---- extras.js ----
// Petits comportements : compteurs, défilement des valeurs avec pause,
// pré-remplissage du formulaire depuis les boutons d'appel.

function initCounters() {
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

function initMarquee() {
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

function initPrefill() {
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

// Boutons « Essayer » : sur iPhone ou Android, ils ouvrent la fiche de l'appli dans la bonne
// boutique ; ailleurs, ils mènent au bloc de téléchargement de la page d'accueil.
function initStores() {
  const ua = navigator.userAgent || '';
  const ios = /iPhone|iPad|iPod/.test(ua) || (/Macintosh/.test(ua) && navigator.maxTouchPoints > 1);
  const android = /Android/.test(ua);
  if (!ios && !android) return;
  document.querySelectorAll('a[data-store]').forEach((link) => {
    const url = ios ? link.dataset.ios : link.dataset.android;
    if (url) link.href = url;
  });
}

// ---- contact.js ----
// Formulaire de contact : validation côté navigateur (la vraie validation est
// refaite côté serveur), envoi à /api/contact, ou, dans l'aperçu publié sur
// claude.ai, enregistrement dans la base de la page.

const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
const PROFILS = ['president', 'coach', 'tresorerie', 'parent', 'benevole', 'partenaire', 'autre'];
const OBJETS = ['club-fondateur', 'demo', 'question', 'partenariat'];
const FIELDS = ['nom', 'email', 'profil', 'objet', 'message', 'consentement'];

const TEXT = {
  nomVide: 'Indiquez votre nom et prénom.',
  nomCourt: 'Votre nom doit compter 2 caractères au moins.',
  emailVide: 'Indiquez votre adresse e-mail.',
  emailInvalide: 'Cette adresse e-mail n’est pas valide. Exemple : prenom@club.fr',
  profil: 'Choisissez votre profil.',
  objet: 'Choisissez l’objet de votre demande.',
  messageVide: 'Écrivez votre message.',
  messageCourt: 'Votre message doit compter 10 caractères au moins.',
  messageLong: 'Votre message dépasse 2000 caractères.',
  consentement: 'Cochez cette case pour que nous puissions vous répondre.',
  envoi: 'Envoi en cours…',
  bouton: 'Envoyer ma demande',
  succesApi: (email) => `Merci, votre demande est envoyée. Nous vous répondons à ${email}.`,
  succesApercu: 'Merci, votre demande est enregistrée dans la base de cet aperçu. Sur le site en ligne, elle part par e-mail à l’équipe ELAZYO.',
  erreurReseau: 'L’envoi n’a pas abouti. Vérifiez votre connexion, puis réessayez.',
  erreurLimite: 'Trop de demandes envoyées depuis cette connexion. Réessayez dans quelques minutes.',
  erreurConfig: 'L’envoi en ligne n’est pas encore activé. Réessayez un peu plus tard.',
  erreurApercu: 'Cet aperçu ne peut pas enregistrer votre demande avec ce compte. Ouvrez la page en étant connecté à claude.ai.',
  aCorriger: (n) => (n === 1 ? 'Un champ est à corriger.' : `${n} champs sont à corriger.`),
};

function initContact() {
  const form = document.querySelector('[data-contact-form]');
  if (!form) return;
  const status = form.querySelector('[data-form-status]');
  const submit = form.querySelector('[data-submit]');
  const submitLabel = form.querySelector('[data-submit-label]');
  const counter = form.querySelector('[data-count]');
  const time = form.querySelector('[data-form-time]');
  if (time) time.value = String(Date.now());

  const el = (name) => form.elements.namedItem(name);
  let attempted = false;

  el('message').addEventListener('input', () => {
    counter.textContent = String(el('message').value.length);
  });

  const validate = (name) => {
    const v = name === 'consentement' ? el(name).checked : el(name).value.trim();
    switch (name) {
      case 'nom': return !v ? TEXT.nomVide : v.length < 2 ? TEXT.nomCourt : '';
      case 'email': return !v ? TEXT.emailVide : !EMAIL_RE.test(v) || v.length > 254 ? TEXT.emailInvalide : '';
      case 'profil': return PROFILS.includes(v) ? '' : TEXT.profil;
      case 'objet': return OBJETS.includes(v) ? '' : TEXT.objet;
      case 'message': return !v ? TEXT.messageVide : v.length < 10 ? TEXT.messageCourt : v.length > 2000 ? TEXT.messageLong : '';
      case 'consentement': return v ? '' : TEXT.consentement;
      default: return '';
    }
  };

  const showError = (name, message) => {
    const input = el(name);
    const out = form.querySelector(`[data-error-for="${name}"]`);
    input.setAttribute('aria-invalid', message ? 'true' : 'false');
    if (out) out.textContent = message;
  };

  FIELDS.forEach((name) => {
    const input = el(name);
    const evt = input.type === 'checkbox' || input.tagName === 'SELECT' ? 'change' : 'blur';
    input.addEventListener(evt, () => { if (attempted) showError(name, validate(name)); });
    if (evt === 'blur') input.addEventListener('input', () => {
      if (attempted && input.getAttribute('aria-invalid') === 'true') showError(name, validate(name));
    });
  });

  const setStatus = (kind, message) => {
    status.classList.toggle('is-success', kind === 'success');
    status.classList.toggle('is-error', kind === 'error');
    status.textContent = message;
  };

  const setSending = (sending) => {
    form.classList.toggle('is-sending', sending);
    submit.setAttribute('aria-disabled', String(sending));
    submitLabel.textContent = sending ? TEXT.envoi : TEXT.bouton;
  };

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    if (form.classList.contains('is-sending')) return;
    attempted = true;
    const errors = FIELDS.map((name) => [name, validate(name)]);
    errors.forEach(([name, message]) => showError(name, message));
    const invalid = errors.filter(([, message]) => message);
    if (invalid.length) {
      setStatus('error', TEXT.aCorriger(invalid.length));
      el(invalid[0][0]).focus();
      return;
    }

    const data = {
      nom: el('nom').value.trim(),
      email: el('email').value.trim(),
      profil: el('profil').value,
      club: el('club').value.trim(),
      objet: el('objet').value,
      message: el('message').value.trim(),
      consentement: el('consentement').checked,
      site_web: el('site_web').value,
      t: Number(time?.value) || 0,
    };

    setSending(true);
    setStatus('', '');
    try {
      const result = await send(form, data);
      if (result.errors) {
        Object.entries(result.errors).forEach(([name, message]) => { if (el(name)) showError(name, message); });
        setStatus('error', TEXT.aCorriger(Object.keys(result.errors).length));
        return;
      }
      const email = data.email;
      form.reset();
      counter.textContent = '0';
      if (time) time.value = String(Date.now());
      attempted = false;
      FIELDS.forEach((name) => showError(name, ''));
      setStatus('success', result.mode === 'preview' ? TEXT.succesApercu : TEXT.succesApi(email));
    } catch (error) {
      const code = error?.message;
      setStatus('error',
        code === 'rate_limited' ? TEXT.erreurLimite
          : code === 'not_configured' ? TEXT.erreurConfig
            : code === 'preview_unavailable' ? TEXT.erreurApercu
              : TEXT.erreurReseau);
    } finally {
      setSending(false);
    }
  });
}

async function send(form, data) {
  // Champ piège rempli : un robot. On ne transmet rien et on ne lui dit rien.
  if (data.site_web) return { ok: true, mode: 'ignored' };
  const runtime = window.claude;
  if (runtime && typeof runtime.use === 'function') return sendToPreview(runtime, data);
  return sendToApi(form.getAttribute('action') || '/api/contact', data);
}

async function sendToApi(url, data) {
  const controller = new AbortController();
  const timer = setTimeout(() => controller.abort(), 12000);
  try {
    const response = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify(data),
      signal: controller.signal,
      credentials: 'same-origin',
    });
    const body = await response.json().catch(() => ({}));
    if (response.ok && body.ok) return { ok: true, mode: 'api' };
    if (response.status === 422 && body.errors) return { ok: false, errors: body.errors };
    throw new Error(body.error || `http_${response.status}`);
  } finally {
    clearTimeout(timer);
  }
}

// Aperçu claude.ai : la demande est rangée dans la base de la page, sous le
// sous-dossier de la personne connectée (règles « boîte à idées »).
async function sendToPreview(runtime, data) {
  const withTimeout = (promise, ms = 10000) => Promise.race([
    promise,
    new Promise((_, reject) => setTimeout(() => reject(new Error('timeout')), ms)),
  ]);
  const db = await withTimeout(runtime.use('db'));
  const user = await withTimeout(runtime.use('user'));
  const uid = user && typeof user.id === 'function' ? await withTimeout(user.id()) : null;
  if (!db || !uid) throw new Error('preview_unavailable');
  const { site_web: _hp, t: _t, ...demande } = data;
  try {
    await withTimeout(db.collection(`demandes/${uid}/envois`).add({
      ...demande,
      envoyeeLe: new Date().toISOString(),
      source: 'apercu',
    }));
  } catch (error) {
    if (error?.code === 'invalid_argument' || error?.code === 'not_granted') throw new Error('preview_unavailable');
    throw error;
  }
  return { ok: true, mode: 'preview' };
}

// ---- main.js ----
// ELAZYO — point d'entrée. Chaque module est indépendant : si l'un échoue,
// les autres continuent et le contenu reste lisible.








window.__elazyo = true;

const modules = [
  initHeader,
  initHero,
  initReveal,
  initScrollEffects,
  initTabs,
  initCards,
  initCounters,
  initMarquee,
  initPrefill,
  initContact,
  initStores,
];

for (const init of modules) {
  try {
    init();
  } catch (error) {
    console.error(`[ELAZYO] ${init.name} n'a pas pu démarrer`, error);
  }
}
})();
