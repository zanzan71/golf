// Effets liés au défilement : parallaxe multicouche, chaîne des maillons,
// manifeste qui s'éclaire mot à mot, saison qui se remplit, écran qui défile.
import { onScrollFrame, whenNear, prefersReducedMotion, clamp, isDesktop } from './env.js';

export function initScrollEffects() {
  const reduce = prefersReducedMotion();
  initChain(reduce);
  initSeason(reduce);
  if (reduce) return;
  initParallax();
  initWords();
  initScreenScroll();
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
