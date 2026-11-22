<?php
/**
 * En-tête de la page d’accueil : bandeau Clubs Fondateurs, barre de navigation, menu mobile.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;
$a = elazyo_asset_base();
?>
<header class="site-header" data-header>
  <?php if ( elazyo_mod( 'annonce_afficher' ) ) : ?>
  <div class="announce" id="annonce">
    <p class="container announce__inner">
      <span class="announce__text"><span class="announce__long"><?php echo elazyo_announce_text(); // Échappé dans la fonction. ?></span><span class="announce__short"><?php echo elazyo_announce_text( true ); // Échappé dans la fonction. ?></span></span>
      <a class="announce__link" href="#clubs-fondateurs">Candidater<svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></a>
    </p>
  </div>
  <?php endif; ?>
  <div class="site-header__bar">
  <div class="container site-header__inner">
    <a class="site-header__logo" href="#accueil">
      <img src="<?php echo $a; ?>img/logo/elazyo-fond-fonce.png" alt="ELAZYO, retour en haut de page" width="157" height="28">
    </a>
    <nav class="site-nav" aria-label="Navigation principale">
      <ul>
        <li><a href="#application" data-nav>L’appli</a></li>
        <li><a href="#profils" data-nav>Pour qui</a></li>
        <li><a href="#my-card" data-nav>MY CARD</a></li>
        <li><a href="#clubs-fondateurs" data-nav>Clubs Fondateurs</a></li>
        <li><a href="#contact" data-nav>Contact</a></li>
      </ul>
    </nav>
    <div class="site-header__actions">
      <a class="btn btn--elan btn--md" <?php elazyo_store_attrs(); ?>>Essayer gratuitement</a>
      <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="menu-mobile" data-menu-toggle>
        <span class="sr-only" data-menu-label>Ouvrir le menu</span>
        <svg class="icon icon--menu" aria-hidden="true"><use href="#i-menu"/></svg>
        <svg class="icon icon--close" aria-hidden="true"><use href="#i-close"/></svg>
      </button>
    </div>
  </div>
  </div>
  <div class="mobile-menu" id="menu-mobile" data-menu hidden>
    <nav class="container mobile-menu__inner" aria-label="Navigation mobile">
      <ul class="mobile-menu__list">
        <li><a href="#application">L’appli</a></li>
        <li><a href="#profils">Pour qui</a></li>
        <li><a href="#my-card">MY CARD</a></li>
        <li><a href="#clubs-fondateurs">Clubs Fondateurs</a></li>
        <li><a href="#faq">Questions</a></li>
        <?php if ( elazyo_news_url() ) : ?><li><a href="<?php echo esc_url( elazyo_news_url() ); ?>">Actualités</a></li><?php endif; ?>
        <li><a href="#contact">Contact</a></li>
      </ul>
      <div class="mobile-menu__actions">
        <a class="btn btn--elan btn--lg" <?php elazyo_store_attrs(); ?>>Essayer gratuitement<svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></a>
      </div>
      <p class="mobile-menu__signature">L’élan du collectif.</p>
    </nav>
  </div>
</header>
