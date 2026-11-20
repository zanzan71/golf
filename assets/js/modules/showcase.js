// « L'appli en action » : sur ordinateur, le téléphone reste à l'écran et change
// d'écran au fil des étapes ; sur mobile, les étapes défilent à l'horizontale.
import { onScrollFrame, prefersReducedMotion, clamp } from './env.js';

const MATCH_OVERFLOW = 21.4; // % de la capture « match » qui dépasse de l'écran

export function initShowcase() {
  const root = document.querySelector('[data-showcase]');
  if (!root) return;
  const steps = [...root.querySelectorAll('.step')];
  const shots = [...root.querySelectorAll('[data-showcase-screens] .phone__shot')];
  const dots = [...root.querySelectorAll('.showcase__dot')];
  const list = root.querySelector('[data-showcase-steps]');
  const video = root.querySelector('video');
  const desktop = window.matchMedia('(min-width: 900px)');
  let active = 0;

  const setActive = (i) => {
    if (i === active && shots[i].classList.contains('is-active')) return;
    active = i;
    steps.forEach((s, j) => s.classList.toggle('is-active', j === i));
    shots.forEach((s, j) => {
      s.classList.toggle('is-active', j === i);
      if (j === i) s.querySelector('img')?.setAttribute('loading', 'eager');
    });
    dots.forEach((d, j) => {
      d.classList.toggle('is-active', j === i);
      d.setAttribute('aria-pressed', String(j === i));
    });
    handleVideo(i);
  };

  // Vidéo réelle du covoiturage : lue une fois (4 s) quand l'étape devient active.
  function handleVideo(i) {
    if (!video) return;
    const isVideoStep = shots[i]?.contains(video);
    if (isVideoStep && !prefersReducedMotion()) {
      video.preload = 'auto';
      try { video.currentTime = 0; } catch { /* vidéo pas encore prête */ }
      const play = video.play();
      if (play && typeof play.catch === 'function') play.catch(() => {});
    } else if (!isVideoStep) {
      video.pause();
    }
  }

  if ('IntersectionObserver' in window) {
    // Ordinateur : l'étape au milieu de l'écran pilote le téléphone.
    const pageIO = new IntersectionObserver((entries) => {
      if (!desktop.matches) return;
      for (const entry of entries) {
        if (entry.isIntersecting) setActive(steps.indexOf(entry.target));
      }
    }, { rootMargin: '-48% 0px -48% 0px' });
    steps.forEach((s) => pageIO.observe(s));

    // Mobile : la carte la plus visible dans le carrousel pilote le téléphone.
    const rowIO = new IntersectionObserver((entries) => {
      if (desktop.matches) return;
      for (const entry of entries) {
        if (entry.isIntersecting && entry.intersectionRatio >= 0.6) setActive(steps.indexOf(entry.target));
      }
    }, { root: list, threshold: [0.6] });
    steps.forEach((s) => rowIO.observe(s));
  }

  dots.forEach((dot) => {
    dot.addEventListener('click', () => {
      const i = Number(dot.dataset.goto);
      const step = steps[i];
      if (desktop.matches) {
        const top = step.getBoundingClientRect().top + window.scrollY - window.innerHeight / 2 + step.offsetHeight / 2;
        window.scrollTo({ top, behavior: prefersReducedMotion() ? 'auto' : 'smooth' });
      } else {
        list.scrollTo({ left: step.offsetLeft - list.offsetLeft - parseFloat(getComputedStyle(list).paddingLeft || '0'), behavior: prefersReducedMotion() ? 'auto' : 'smooth' });
      }
      setActive(i);
    });
  });

  // La capture « match » défile avec la page tant que son étape est active.
  const matchShot = root.querySelector('.phone__shot--scroll');
  const matchImg = matchShot?.querySelector('img');
  const matchStep = steps[shots.indexOf(matchShot)];
  if (matchImg && matchStep && !prefersReducedMotion()) {
    onScrollFrame((vh) => {
      if (!desktop.matches) {
        matchImg.style.transform = '';
        return;
      }
      const rect = matchStep.getBoundingClientRect();
      if (rect.bottom < -vh || rect.top > vh * 2) return;
      const p = clamp((vh * 0.75 - rect.top) / (rect.height + vh * 0.1));
      matchImg.style.transform = `translateY(${(-p * MATCH_OVERFLOW).toFixed(2)}%)`;
    });
  }

  setActive(0);
}
