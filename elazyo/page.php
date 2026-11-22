<?php
/**
 * Page simple (mentions légales, confidentialité, remerciement…).
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="contenu">
<?php
while ( have_posts() ) :
	the_post();
	?>
  <section class="legal theme-light">
    <div class="container legal__body prose">
      <h1 class="display display--1"><?php the_title(); ?></h1>
      <?php the_content(); ?>
    </div>
  </section>
<?php endwhile; ?>
</main>
<?php
get_footer();
