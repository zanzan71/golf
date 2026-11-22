<?php
/**
 * Un article des actualités.
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
  <article class="legal theme-light article">
    <div class="container legal__body prose">
      <p class="article__meta">
        <?php if ( elazyo_news_url() ) : ?><a class="link" href="<?php echo esc_url( elazyo_news_url() ); ?>">Actualités</a><span aria-hidden="true">, </span><?php endif; ?>
        <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
      </p>
      <h1 class="display display--1"><?php the_title(); ?></h1>
      <?php if ( has_post_thumbnail() ) : ?>
      <figure class="article__cover"><?php the_post_thumbnail( 'large', array( 'loading' => 'eager' ) ); ?></figure>
      <?php endif; ?>
      <?php the_content(); ?>

      <aside class="article__cta theme-night" aria-label="Essayer ELAZYO">
        <p class="article__cta-title">Votre club veut essayer ELAZYO&#8239;?</p>
        <p>Gratuit pour les joueurs et les familles, sur iPhone, Android et le web.</p>
        <div class="article__cta-actions">
          <a class="btn btn--elan btn--md" href="<?php echo esc_url( elazyo_mod( 'app_url' ) ); ?>" rel="noopener">Essayer gratuitement</a>
          <a class="btn btn--ghost btn--md" href="<?php echo esc_url( home_url( '/#contact' ) ); ?>">Demander une démo</a>
        </div>
      </aside>
    </div>
  </article>
<?php endwhile; ?>
</main>
<?php
get_footer();
