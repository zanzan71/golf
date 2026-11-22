<?php
/**
 * Page d’accueil : section « manifeste ».
 *
 * Généré depuis la page statique par convertir.py, puis complété à la main si besoin.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;
$a = elazyo_asset_base();
?>
  <section class="manifeste manifeste--photo theme-night" id="manifeste" aria-labelledby="manifeste-titre">
    <div class="manifeste__media">
      <picture class="manifeste__photo">
        
        <source type="image/webp" srcset="<?php echo $a; ?>img/photos/collectif-960.webp 960w, <?php echo $a; ?>img/photos/collectif-1672.webp 1672w" sizes="100vw">
        <img src="<?php echo $a; ?>img/photos/collectif-1672.webp" width="1672" height="941" alt="" loading="lazy" decoding="async">
      </picture>
      <span class="photo-ia" aria-hidden="true">Image générée par IA</span>
    </div>
    <div class="container manifeste__grid">
      <p class="label manifeste__label"><span class="chev" aria-hidden="true"></span>Pourquoi ELAZYO existe</p>
      <h2 class="display display--1 manifeste__title" id="manifeste-titre" data-reveal="lines">
        <span class="line"><span>Un club, ce n’est</span></span>
        <span class="line"><span>jamais seulement</span></span>
        <span class="line"><span>du sport.</span></span>
      </h2>
      <div class="manifeste__text" data-words>
        <p>Un club amateur ne tient pas seulement grâce aux joueurs. Derrière chaque match, il y a des dirigeants, des éducateurs, des parents, des bénévoles et des arbitres. Presque tous donnent leur temps gratuitement.</p>
        <p>Leur quotidien est pourtant éparpillé entre les messages, les tableaux, les calendriers, les feuilles papier et plusieurs applications. Il faut relancer, vérifier, recopier, souvent le soir et le week-end.</p>
      </div>
      <p class="manifeste__close" data-reveal>ELAZYO prend en compte le club entier&nbsp;: ses équipes, mais aussi sa trésorerie, ses bénévoles, ses familles et l’éducation de ses jeunes joueurs.</p>
    </div>
  </section>
