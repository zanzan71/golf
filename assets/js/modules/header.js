// En-tête : passe en mode compact au défilement, indique la section en cours,
// gère le menu mobile (piège du focus, Échap, verrouillage du défilement).

export function initHeader() {
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
    if (!menu.hidden && window.innerWidth > 760) close(false);
  });
}
