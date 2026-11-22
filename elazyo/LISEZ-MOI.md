# Thème WordPress ELAZYO — version 1.0.0

Thème sur mesure du site vitrine ELAZYO (l’appli des clubs de foot amateur).

## Installation

1. Dans WordPress : **Apparence > Thèmes > Ajouter > Téléverser un thème**, choisir `elazyo.zip`, puis **Activer**.
2. À l’activation, le thème crée (sans rien écraser) :
   - les pages **Accueil**, **Actualités**, **Mentions légales**, **Confidentialité et cookies**, **Merci, c’est envoyé.** ;
   - les 7 questions de la **FAQ** ;
   - si le site est neuf, il règle **Accueil** comme page d’accueil et **Actualités** comme page des articles.
3. **Réglages > Permaliens** : choisir « Titre de la publication », puis **Enregistrer**.

Prérequis : WordPress 6.3 ou plus récent, PHP 7.4 ou plus récent.

## Où modifier quoi

| Contenu | Où |
|---|---|
| Bandeau et offre Clubs Fondateurs (places, date), haut de page, encadré du club, témoignage, adresse de l’appli, e-mail qui reçoit les demandes | Apparence > Personnaliser > ELAZYO |
| Questions et réponses | Menu **FAQ** (l’ordre se règle dans « Attributs ») |
| Demandes envoyées par le formulaire | Menu **Demandes** (supprimées automatiquement après 3 ans) |
| Articles | Articles > Ajouter (affichés dans Actualités) |
| Pages légales | Pages (compléter les passages surlignés en jaune) |
| Autres textes de l’accueil | Fichiers `template-parts/home/*.php` |

## Extensions conseillées

- **WP Mail SMTP** : indispensable pour que les e-mails des demandes arrivent (sinon ils finissent souvent en spam ou ne partent pas ; les demandes restent visibles dans le menu Demandes).
- Une extension de cache (celle de l’hébergeur, ou **WP Super Cache**).
- Une extension de sauvegarde (**UpdraftPlus**).
- Facultatif : **Rank Math** ou **Yoast SEO**. Sans elles, le thème gère le titre, la description et les données structurées.

## Fichiers

- `front-page.php` : ordre des sections de l’accueil.
- `template-parts/home/` : une section par fichier.
- `inc/` : réglages (`customizer.php`), FAQ et pages (`content.php`), formulaire (`contact.php`), référencement (`seo.php`).
- `assets/js/site.js` : script assemblé depuis `assets/js/modules/` (ne pas modifier à la main).
