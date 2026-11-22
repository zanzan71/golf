# Site ELAZYO — thème WordPress

- `elazyo/` : le thème (à zipper puis téléverser dans Apparence > Thèmes). Notice : `elazyo/LISEZ-MOI.md`.
- `convertir.py` : a généré les sections de l’accueil (`elazyo/template-parts/home/`) depuis la page statique de la branche `elazyo-site`.
- `pages_blocs.py` : a converti les pages légales statiques en blocs WordPress (`elazyo/inc/pages/`).
- `banc/` : banc d’essai qui imite WordPress pour afficher le thème sans l’installer
  (`php -S 127.0.0.1:8090 banc/routeur.php`). Il n’est pas livré avec le thème.

Le thème est désormais la référence du site : les prochaines modifications se font ici, pas sur `elazyo-site`.
