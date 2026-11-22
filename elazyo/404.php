<?php
/**
 * Page introuvable.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="contenu">
  <section class="legal theme-light">
    <div class="container legal__body">
      <h1 class="display display--1">Page introuvable.</h1>
      <p class="lead">Cette adresse ne mène nulle part : la page a peut-être été déplacée ou supprimée.</p>
      <p><a class="btn btn--primary btn--lg" href="<?php echo esc_url( home_url( '/' ) ); ?>">Revenir à l’accueil</a></p>
    </div>
  </section>
</main>
<?php
get_footer();
