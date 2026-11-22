<?php
/**
 * Liste des actualités (et archives, recherche).
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_search() ) {
	/* translators: %s: recherche. */
	$elazyo_title = sprintf( 'Recherche : %s', get_search_query() );
	$elazyo_intro = '';
} elseif ( is_archive() ) {
	$elazyo_title = wp_strip_all_tags( get_the_archive_title() );
	$elazyo_intro = wp_strip_all_tags( get_the_archive_description() );
} else {
	$elazyo_title = 'Actualités';
	$elazyo_intro = 'Conseils, nouveautés de l’appli et vie des clubs de foot amateur.';
}
?>
<main id="contenu">
  <section class="blog theme-light">
    <div class="container">
      <header class="blog__head">
        <h1 class="display display--1"><?php echo esc_html( $elazyo_title ); ?></h1>
        <?php if ( $elazyo_intro ) : ?><p class="lead"><?php echo esc_html( $elazyo_intro ); ?></p><?php endif; ?>
      </header>

      <?php if ( have_posts() ) : ?>
      <ul class="blog__grid">
        <?php
        while ( have_posts() ) :
          the_post();
          ?>
        <li>
          <article class="post-card">
            <?php if ( has_post_thumbnail() ) : ?>
            <a class="post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'elazyo-carte', array( 'alt' => '', 'loading' => 'lazy' ) ); ?></a>
            <?php endif; ?>
            <div class="post-card__body">
              <p class="post-card__date"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time></p>
              <h2 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
              <p class="post-card__excerpt"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
            </div>
          </article>
        </li>
        <?php endwhile; ?>
      </ul>
        <?php
        the_posts_pagination(
          array(
            'mid_size'           => 1,
            'prev_text'          => 'Articles plus récents',
            'next_text'          => 'Articles plus anciens',
            'screen_reader_text' => 'Pages des actualités',
          )
        );
        ?>
      <?php else : ?>
      <p class="blog__empty">Aucun article pour l’instant. Revenez bientôt.</p>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php
get_footer();
