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

Le logo SVG (`assets/img/logo/`) reprend les tracés du design system. Les écrans montrés sont les **vraies captures de l’appli**, sur un club de démonstration :

- captures récentes (20 novembre 2026) : accueil du président, planning, gestion, finance, communication, équipe du coach, séances, connexion ; branche `elazyo-video-assets`, dossier `ecrans-v2/` ;
- anciennes captures, gardées tant que l’appli récente n’a pas d’équivalent : détail d’un match et transport (covoiturage). Attention : ces deux écrans ont été complétés à la main pour la vidéo de présentation (joueurs, présences, voitures). Le site ne les présente donc pas comme des captures brutes. À remplacer par de vraies captures récentes dès que possible ;
- la vidéo du covoiturage, enregistrée dans l’appli.

Pour mettre un écran à jour : déposer la nouvelle capture du téléphone (1080 × 2412) dans les sources, puis relancer `scripts/preparer-images.py`.

Les **photos d’ambiance** (terrains, joueurs, familles, matériel) sont les images du film de marque, générées par IA (branche `elazyo-video-assets`, dossier `photos/`). Chacune porte la mention « Image générée par IA », reprise dans le pied de page et les mentions légales : depuis le 2 août 2026, l’article 50 du règlement européen sur l’IA impose de signaler certains contenus générés. Ne pas retirer ces mentions sans avis juridique.

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
assets/img, video, icons   images AVIF + WebP en deux tailles, morceaux d’écran du hero
                           (assets/img/fragments), photos d’ambiance (assets/img/photos),
                           vidéo réelle du covoiturage
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
- [ ] **Espace Séances** : l’étape « Séances » de « L’appli en action » indique qu’une partie de l’espace est en cours d’ouverture (cadenas sur la capture). À mettre à jour quand tout est ouvert.
- [ ] **Photos de vrais clubs** (quand vous en aurez, avec les accords) : elles remplaceront les photos générées par IA, à commencer par le manifeste et les Clubs Fondateurs.
- [ ] **Captures plus nettes** (facultatif) : les écrans exportés directement depuis l’appli (sans barre du téléphone ni du navigateur), en 1290 px de large, rendraient le hero et le mur d’écrans encore plus nets.
- [ ] **Offre Club Fondateur** : le texte reprend le kit de recrutement (10 places, gratuit jusqu’au 30 juin 2027). À confirmer avant publication.
- [ ] **Polices hébergées sur le site** (recommandé, RGPD et vitesse) : déposer Manrope et Saira (licence OFL) en WOFF2 dans `assets/fonts/`, remplacer le lien Google Fonts par un `@font-face` local, retirer `fonts.googleapis.com` et `fonts.gstatic.com` de la CSP et le paragraphe « Polices » de la page confidentialité.

## Animations et accessibilité

- Une seule séquence d’ouverture (hero), en CSS pur : elle n’attend pas le JavaScript. Le terrain se trace, le téléphone se relève depuis la pelouse, les morceaux d’écran en sortent et se placent en profondeur (CSS 3D, aucune bibliothèque). Le script ne pose que l’inclinaison suivant le pointeur et l’ouverture au défilement.
- Sur téléphone et tablette, la scène 3D passe avant le titre : on ouvre le site sur le produit, le titre et « Essayer gratuitement » restent dans le premier écran (vérifié de 360 à 430 px de large). Sur téléphone, le titre est visible dès le premier affichage : c’est lui que mesure le LCP.
- Règle à garder : ne jamais animer l’opacité d’un élément en `preserve-3d` (Chrome l’aplatit et les plaques d’épaisseur du téléphone passeraient devant l’écran).
- Rien n’est caché par défaut : seuls les blocs situés sous l’écran au chargement attendent d’arriver à l’écran pour apparaître. Si le script ne tourne pas, tout reste visible.
- Un seul script classique (`site.js`) plutôt que des modules : il fonctionne aussi dans les cadres isolés (aperçus, intégrations), où les modules sont bloqués.
- Révélations au défilement, parallaxe multicouche, chaîne des maillons, mur d’écrans dont les colonnes glissent en sens opposés, manifeste éclairé mot à mot, téléphone qui change d’écran avec les étapes, saison qui se remplit, cartes MY CARD en 3D.
- `prefers-reduced-motion` respecté partout : les animations disparaissent et tout le contenu reste visible.
- Les contenus qui bougent seuls ont un bouton pause (scène du hero : écrans et flottement ; défilement des valeurs) ; la vidéo est lue une fois (4 s). Hors de l’écran, la scène du hero s’arrête.
- Navigation au clavier, focus visible, onglets au modèle WAI-ARIA, menu mobile avec piège du focus et touche Échap, formulaire avec erreurs reliées aux champs.

## Mesures (tests Playwright, 20 novembre 2026)

| Profil | LCP | CLS | Blocage |
|---|---|---|---|
| Mobile, 4G lente, processeur ÷4 | 2,0 à 2,4 s | 0,003 | ≈ 170 ms |
| Ordinateur, fibre | 0,4 s | 0,015 | ≈ 40 ms |

Mesures en laboratoire, polices servies localement. À confirmer en conditions réelles (PageSpeed Insights, rapport Core Web Vitals de la Search Console).

## Pourquoi pas Next.js

Le brief recommandait Next.js. Les paquets npm ne pouvaient pas être installés dans la session où le site a été construit. Le site est donc écrit sans dépendance. Il est rapide et facile à reprendre. Le passage à Next.js reste simple : chaque section correspond à un futur composant, les tokens deviennent la configuration Tailwind, et `api/contact.js` devient une route `app/api/contact/route.ts`.
