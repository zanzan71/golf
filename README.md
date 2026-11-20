# Site ELAZYO

Le site vitrine d’ELAZYO : une page d’accueil en 12 sections, les mentions légales, la politique de confidentialité et un formulaire de contact relié à une vraie fonction serveur.

HTML, CSS et JavaScript, **sans aucune dépendance ni étape de construction**. Le site se déploie tel quel sur Vercel, Netlify, OVH ou n’importe quel hébergement statique.

## Identité

Tout vient du design system **« ELAZYO »** (artefact Claude, `tokens.json`) :

| Rôle | Valeur |
|---|---|
| Bleu ELAZYO | `#01284F` |
| Lime Élan (toujours en aplat, jamais en texte sur fond clair) | `#CCF709` |
| Night (fond sombre de marque) | `#09131D` |
| Texte | Manrope 500 à 800 |
| Affiche | Saira italique 800, capitales |
| Motif | le chevron du maillon, seul motif autorisé |

Le logo SVG (`assets/img/logo/`) reprend les tracés du design system. Les écrans montrés sont les **vraies captures de l’appli** (branche `elazyo-video-assets`, club de démonstration). La barre d’onglets, qui affiche encore l’ancien nom PLAYCARD, est coupée.

## Arborescence

```
index.html                 page d’accueil (contenu, SEO, données structurées)
mentions-legales.html      à compléter (voir plus bas)
confidentialite.html       à compléter (voir plus bas)
merci.html, 404.html
api/contact.js             fonction serverless du formulaire (Vercel, Node 20+)
assets/css/site.css        styles, organisés en couches (@layer) : tokens, composants, sections, mouvement
assets/js/site.js          script chargé par les pages, assemblé depuis main.js + assets/js/modules/
                           (npm run build:js après chaque modification d’un module)
assets/img, video, icons   images AVIF + WebP en deux tailles, vidéo réelle du covoiturage
scripts/preparer-images.py régénère les images à partir des sources
tests/api-contact.test.mjs tests de la fonction du formulaire (npm test)
vercel.json                en-têtes de sécurité (CSP stricte, HSTS…), cache, URLs propres
```

## Lancer en local

```bash
python3 -m http.server 8080      # puis http://localhost:8080
npm run build:js                 # réassemble assets/js/site.js après une modification des modules
npm test                         # 12 tests de /api/contact
```

## Mettre en ligne (Vercel)

1. Importer ce dépôt dans Vercel, branche `elazyo-site`, sans commande de build.
2. Définir les variables d’environnement du formulaire (au choix) :
   - **Back-end ELAZYO** : `CONTACT_FORWARD_URL` (la route qui envoie déjà le mail interne « [Site ELAZYO] Contact — … », `routes/public.py`) et `CONTACT_FORWARD_TOKEN` si besoin ;
   - **ou e-mail direct** : `RESEND_API_KEY`, `CONTACT_TO`, `CONTACT_FROM`.
   - `ALLOWED_ORIGINS` si le domaine diffère de `https://www.elazyo.fr,https://elazyo.fr`.
3. Brancher le domaine `www.elazyo.fr` (l’adresse canonique est `https://www.elazyo.fr/`).

Sans variable configurée, le formulaire répond « envoi pas encore activé » : il n’affiche jamais un faux succès.

## À compléter avant la mise en ligne

- [ ] **Mentions légales et confidentialité** : les champs surlignés en jaune (adresse du siège, SIREN, directeur de la publication, hébergeur, prestataire d’e-mail, lien vers la politique de l’appli).
- [ ] **Adresse e-mail de contact** à afficher, si vous en voulez une en plus du formulaire.
- [ ] **Liens App Store et Google Play** : le site renvoie pour l’instant vers app.elazyo.fr.
- [ ] **Photo** de la section « Chaque maillon compte » : un vrai club, avec l’accord des personnes (et des parents pour les mineurs). L’emplacement est signalé sur la page.
- [ ] **Captures** « Accueil coach » et « Fiche séance », si vous voulez les ajouter à « L’appli en action ».
- [ ] **Offre Club Fondateur** : le texte reprend le kit de recrutement (10 places, gratuit jusqu’au 30 juin 2027). À confirmer avant publication.
- [ ] **Polices hébergées sur le site** (recommandé, RGPD et vitesse) : déposer Manrope et Saira (licence OFL) en WOFF2 dans `assets/fonts/`, remplacer le lien Google Fonts par un `@font-face` local, retirer `fonts.googleapis.com` et `fonts.gstatic.com` de la CSP et le paragraphe « Polices » de la page confidentialité.

## Animations et accessibilité

- Une seule séquence d’ouverture (hero), en CSS pur : elle n’attend pas le JavaScript.
- Rien n’est caché par défaut : seuls les blocs situés sous l’écran au chargement attendent d’arriver à l’écran pour apparaître. Si le script ne tourne pas, tout reste visible.
- Un seul script classique (`site.js`) plutôt que des modules : il fonctionne aussi dans les cadres isolés (aperçus, intégrations), où les modules sont bloqués.
- Révélations au défilement, parallaxe multicouche, chaîne des maillons, manifeste éclairé mot à mot, téléphone qui change d’écran avec les étapes, saison qui se remplit, cartes MY CARD en 3D.
- `prefers-reduced-motion` respecté partout : les animations disparaissent et tout le contenu reste visible.
- Les contenus qui bougent seuls ont un bouton pause (écrans du hero, défilement des valeurs) ; la vidéo est lue une fois (4 s).
- Navigation au clavier, focus visible, onglets au modèle WAI-ARIA, menu mobile avec piège du focus et touche Échap, formulaire avec erreurs reliées aux champs.

## Mesures (tests Playwright, 20 novembre 2026)

| Profil | LCP | CLS | Blocage |
|---|---|---|---|
| Mobile, 4G lente, processeur ÷4 | 1,9 s | 0,001 | ≈ 140 ms |
| Ordinateur, fibre | 0,3 s | 0,04 | ≈ 50 ms |

Mesures en laboratoire, polices servies localement. À confirmer en conditions réelles (PageSpeed Insights, rapport Core Web Vitals de la Search Console).

## Pourquoi pas Next.js

Le brief recommandait Next.js. Les paquets npm ne pouvaient pas être installés dans la session où le site a été construit. Le site est donc écrit sans dépendance. Il est rapide et facile à reprendre. Le passage à Next.js reste simple : chaque section correspond à un futur composant, les tokens deviennent la configuration Tailwind, et `api/contact.js` devient une route `app/api/contact/route.ts`.
