// Révélations au défilement : titres ligne par ligne, masques sur les images,
// blocs et listes en séquence. Les états de départ sont dans site.css (layer motion).

export function initReveal() {
  const els = [...document.querySelectorAll('[data-reveal]')];

  document.querySelectorAll('[data-reveal="stagger"]').forEach((list) => {
    [...list.children].forEach((child, i) => child.style.setProperty('--i', i));
  });

  const show = (el) => {
    el.classList.add('is-visible');
    if (el.dataset.reveal === 'mask') {
      const done = (event) => {
        if (event.target !== el || event.propertyName !== 'clip-path') return;
        el.classList.add('reveal-done');
        el.removeEventListener('transitionend', done);
      };
      el.addEventListener('transitionend', done);
      // Filet de sécurité si la transition n'a pas lieu (mouvement réduit, onglet en arrière-plan).
      setTimeout(() => el.classList.add('reveal-done'), 1600);
    }
  };

  if (!('IntersectionObserver' in window)) {
    els.forEach(show);
    return;
  }

  const io = new IntersectionObserver((entries) => {
    for (const entry of entries) {
      if (!entry.isIntersecting) continue;
      show(entry.target);
      io.unobserve(entry.target);
    }
  }, { rootMargin: '0px 0px -10% 0px', threshold: 0.01 });

  els.forEach((el) => io.observe(el));
}
