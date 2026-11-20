// Assemble les modules de assets/js/modules/ en un seul script classique,
// assets/js/site.js. Un script classique se charge partout, y compris dans les
// cadres isolés (aperçus, intégrations) où les modules ES sont bloqués.
//
// Usage : node scripts/assembler-js.mjs   (aucune dépendance)
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const jsDir = path.join(root, 'assets/js');
const main = fs.readFileSync(path.join(jsDir, 'main.js'), 'utf8');

// Ordre : dépendances partagées d'abord, puis les modules importés par main.js.
const order = ['env.js'];
for (const m of main.matchAll(/from '\.\/modules\/([\w-]+\.js)'/g)) if (!order.includes(m[1])) order.push(m[1]);

const strip = (code) => code
  .replace(/^import[\s\S]*?from\s+'[^']+';\s*$/gm, '')
  .replace(/^export\s+(?=(async\s+)?function|const|let|class)/gm, '');

const declared = new Map();
const parts = order.map((file) => {
  const code = strip(fs.readFileSync(path.join(jsDir, 'modules', file), 'utf8'));
  for (const m of code.matchAll(/^(?:async\s+)?(?:function\s+([\w$]+)|(?:const|let|class)\s+([\w$]+))/gm)) {
    const name = m[1] || m[2];
    if (declared.has(name)) throw new Error(`Nom en double « ${name} » dans ${file} et ${declared.get(name)}`);
    declared.set(name, file);
  }
  return `// ---- ${file} ----\n${code.trim()}\n`;
});
const entry = strip(main).trim();

const out = `/* ELAZYO — script du site, assemblé depuis assets/js/modules par scripts/assembler-js.mjs.
   Ne pas modifier ce fichier à la main : modifier les modules puis relancer l'assemblage. */
(function () {
'use strict';
${parts.join('\n')}
// ---- main.js ----
${entry}
})();
`;
fs.writeFileSync(path.join(jsDir, 'site.js'), out);
console.log(`assets/js/site.js : ${order.length + 1} fichiers, ${(out.length / 1024).toFixed(1)} Ko`);
