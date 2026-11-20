// MY CARD : les cinq paliers en éventail. Le palier choisi passe devant ;
// la carte active suit légèrement le pointeur, avec un reflet.
import { prefersReducedMotion, hasFinePointer } from './env.js';

export function initCards() {
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
