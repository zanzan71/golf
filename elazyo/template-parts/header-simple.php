<?php
/**
 * En-tête des pages intérieures (actualités, pages légales).
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;
?>
<header class="simple-header">
  <div class="container">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( elazyo_asset( 'img/logo/elazyo-fond-fonce.png' ) ); ?>" alt="ELAZYO, retour à l’accueil" width="157" height="28"></a>
    <nav class="simple-header__nav" aria-label="Navigation">
      <?php if ( elazyo_news_url() ) : ?>
      <a class="link" href="<?php echo esc_url( elazyo_news_url() ); ?>"<?php echo is_home() || is_singular( 'post' ) || is_archive() ? ' aria-current="page"' : ''; ?>>Actualités</a>
      <?php endif; ?>
      <a class="link" href="<?php echo esc_url( home_url( '/#contact' ) ); ?>">Contact</a>
      <a class="btn btn--elan btn--md simple-header__cta" href="<?php echo esc_url( elazyo_mod( 'app_url' ) ); ?>" rel="noopener">Essayer gratuitement</a>
    </nav>
  </div>
</header>
