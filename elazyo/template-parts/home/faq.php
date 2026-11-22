<?php
/**
 * Page d’accueil : section « faq ».
 *
 * Généré depuis la page statique par convertir.py, puis complété à la main si besoin.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;
?>
  <section class="faq theme-surface" id="faq" aria-labelledby="faq-titre">
    <div class="container faq__grid">
      <div class="faq__head">
        <h2 class="display display--1" id="faq-titre" data-reveal="lines">
          <span class="line"><span>Vos</span></span>
          <span class="line"><span>questions.</span></span>
        </h2>
        <p class="faq__more">Une autre question&nbsp;? <a class="link" href="#contact" data-prefill-objet="question">Écrivez-nous</a>.</p>
      </div>
      <div class="faq__list">
        <?php foreach ( elazyo_faq_items() as $item ) : ?>
        <details class="qa" name="faq">
          <summary><?php echo elazyo_nbsp( esc_html( $item['question'] ) ); // Échappé. ?><svg class="icon" aria-hidden="true"><use href="#i-plus"/></svg></summary>
          <div class="qa__body"><?php echo wp_kses_post( wpautop( $item['answer'] ) ); ?></div>
        </details>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
