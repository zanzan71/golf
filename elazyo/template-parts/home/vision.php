<?php
/**
 * Page d’accueil : section « vision ».
 *
 * Généré depuis la page statique par convertir.py, puis complété à la main si besoin.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;
$a = elazyo_asset_base();
?>
  <section class="vision theme-light section--bande" id="vision" aria-labelledby="vision-titre">
    <div class="bande bande--night theme-night">
      <div class="bande__media">
        <picture class="bande__photo">
          
          <source type="image/webp" srcset="<?php echo $a; ?>img/photos/action-960.webp 960w, <?php echo $a; ?>img/photos/action-1672.webp 1672w" sizes="100vw">
          <img src="<?php echo $a; ?>img/photos/action-1672.webp" width="1672" height="941" alt="" loading="lazy" decoding="async">
        </picture>
        <span class="photo-ia" aria-hidden="true">Image générée par IA</span>
      </div>
      <div class="container">
        <div class="section-head bande__head">
          <p class="label"><span class="chev" aria-hidden="true"></span>Notre vision</p>
          <h2 class="display display--1 vision__title" id="vision-titre" data-reveal="lines">
            <span class="line"><span>Le sport amateur</span></span>
            <span class="line"><span>mérite de voir grand.</span></span>
          </h2>
          <p class="lead section-head__lead" data-reveal>Nous voulons qu’ELAZYO devienne l’outil de référence du sport amateur, d’abord dans le football puis dans d’autres sports, sans jamais perdre le lien de proximité avec les clubs.</p>
        </div>
      </div>
    </div>
    <div class="container">

      <ul class="missions" data-reveal="stagger">
        <li class="mission"><span class="mission__verb">Simplifier</span><span class="mission__text">la vie de ceux qui organisent le club.</span></li>
        <li class="mission"><span class="mission__verb">Rassembler</span><span class="mission__text">joueurs, éducateurs, dirigeants, parents, bénévoles et partenaires.</span></li>
        <li class="mission"><span class="mission__verb">Valoriser</span><span class="mission__text">la progression et l’engagement de chacun, sur le terrain comme en dehors.</span></li>
        <li class="mission"><span class="mission__verb">Redistribuer</span><span class="mission__text">une partie de la valeur créée au profit du sport amateur.</span></li>
      </ul>

      <div class="roadmap">
        <h3 class="roadmap__title">La suite, par étapes</h3>
        <ol class="roadmap__list" data-roadmap>
          <li class="roadmap__step roadmap__step--live">
            <span class="roadmap__status">Disponible</span>
            <p>La gestion du club et des équipes&nbsp;: planning, convocations, présences, covoiturage, bénévoles, communication, finances.</p>
          </li>
          <li class="roadmap__step roadmap__step--live">
            <span class="roadmap__status">Disponible</span>
            <p>La progression des joueurs et MY CARD, leur carte de saison.</p>
          </li>
          <li class="roadmap__step">
            <span class="roadmap__status">Puis</span>
            <p>ELAZYO ACADEMY, des compétitions, des partenariats et d’autres sports que le football.</p>
          </li>
        </ol>
      </div>

      <div class="partners" data-reveal>
        <p class="partners__text">Clubs, associations, collectivités, marques&nbsp;: parlons de ce que nous pouvons faire ensemble pour le sport amateur.</p>
        <a class="btn btn--primary btn--lg" href="#contact" data-prefill-objet="partenariat" data-prefill-profil="partenaire">Devenir partenaire<svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></a>
      </div>
    </div>
  </section>
