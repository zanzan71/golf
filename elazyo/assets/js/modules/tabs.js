// Onglets des profils, selon le modèle WAI-ARIA « Tabs » (activation automatique).

export function initTabs() {
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
