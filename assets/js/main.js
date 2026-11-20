// ELAZYO — point d'entrée. Chaque module est indépendant : si l'un échoue,
// les autres continuent et le contenu reste lisible.
import { initHeader } from './modules/header.js';
import { initReveal } from './modules/reveal.js';
import { initHero } from './modules/hero.js';
import { initScrollEffects } from './modules/scroll-effects.js';
import { initShowcase } from './modules/showcase.js';
import { initTabs } from './modules/tabs.js';
import { initCards } from './modules/cards.js';
import { initCounters, initMarquee, initPrefill } from './modules/extras.js';
import { initContact } from './modules/contact.js';

window.__elazyo = true;

const modules = [
  initHeader,
  initHero,
  initReveal,
  initScrollEffects,
  initShowcase,
  initTabs,
  initCards,
  initCounters,
  initMarquee,
  initPrefill,
  initContact,
];

for (const init of modules) {
  try {
    init();
  } catch (error) {
    console.error(`[ELAZYO] ${init.name} n'a pas pu démarrer`, error);
  }
}
