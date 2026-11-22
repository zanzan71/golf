<?php
/**
 * Pied de page, commun à toutes les pages.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;
$a = elazyo_asset_base();
?>
<footer class="site-footer theme-night">
  <div class="container">
    <p class="site-footer__signature" aria-hidden="true">L’élan du collectif.</p>
    <div class="site-footer__grid">
      <div class="site-footer__brand">
        <img src="<?php echo $a; ?>img/logo/elazyo-fond-fonce.png" alt="ELAZYO" width="157" height="28" loading="lazy">
        <p>L’appli des clubs de foot amateur. L’élan du collectif.</p>
      </div>
      <nav class="site-footer__nav" aria-label="Plan du site">
        <ul>
          <li><a class="link" href="<?php echo esc_url( elazyo_anchor( 'application' ) ); ?>">L’appli</a></li>
          <li><a class="link" href="<?php echo esc_url( elazyo_anchor( 'profils' ) ); ?>">Pour qui</a></li>
          <li><a class="link" href="<?php echo esc_url( elazyo_anchor( 'my-card' ) ); ?>">MY CARD</a></li>
          <li><a class="link" href="<?php echo esc_url( elazyo_anchor( 'clubs-fondateurs' ) ); ?>">Clubs Fondateurs</a></li>
          <li><a class="link" href="<?php echo esc_url( elazyo_anchor( 'faq' ) ); ?>">Questions</a></li>
          <?php if ( elazyo_news_url() ) : ?><li><a class="link" href="<?php echo esc_url( elazyo_news_url() ); ?>">Actualités</a></li><?php endif; ?>
          <li><a class="link" href="<?php echo esc_url( elazyo_anchor( 'contact' ) ); ?>">Contact</a></li>
        </ul>
      </nav>
      <div class="site-footer__app">
        <a class="btn btn--elan btn--md" href="<?php echo esc_url( elazyo_mod( 'app_url' ) ); ?>" rel="noopener">Ouvrir l’appli<svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></a>
      </div>
    </div>
    <div class="site-footer__legal">
      <p>© <?php echo esc_html( wp_date( 'Y' ) ); ?> ELAZYO</p>
      <p class="site-footer__credits">Photos d’illustration générées par intelligence artificielle. Écrans de l’appli&nbsp;: données d’un club de démonstration.</p>
      <ul>
        <li><a class="link" href="<?php echo esc_url( elazyo_page_url( 'mentions-legales' ) ); ?>">Mentions légales</a></li>
        <li><a class="link" href="<?php echo esc_url( elazyo_page_url( 'confidentialite' ) ); ?>">Confidentialité et cookies</a></li>
      </ul>
    </div>
  </div>
</footer>
