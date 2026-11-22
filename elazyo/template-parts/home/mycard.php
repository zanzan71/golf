<?php
/**
 * Page d’accueil : section « mycard ».
 *
 * Généré depuis la page statique par convertir.py, puis complété à la main si besoin.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;
$a = elazyo_asset_base();
?>
  <section class="mycard theme-night" id="my-card" aria-labelledby="mycard-titre">
    <div class="container mycard__grid">
      <div class="mycard__copy">
        <p class="badge badge--lime"><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Disponible dans l’appli</p>
        <h2 class="display display--1" id="mycard-titre" data-reveal="lines">
          <span class="line"><span>MY CARD.</span></span>
          <span class="line"><span>La carte du joueur.</span></span>
        </h2>
        <p class="lead" data-reveal>Chaque joueur a sa carte, illustrée selon sa catégorie. Elle passe de palier en palier à mesure qu’il remplit ses objectifs, avec ses statistiques, son CV sportif et sa place dans le Top ELAZYO, qui récompense l’assiduité et la vie du club.</p>
        <div class="tiers" role="group" aria-label="Choisir un palier" data-tier-buttons>
          <button type="button" class="tier tier--bronze is-active" data-tier="0" aria-pressed="true">Bronze</button>
          <button type="button" class="tier tier--argent" data-tier="1" aria-pressed="false">Argent</button>
          <button type="button" class="tier tier--or" data-tier="2" aria-pressed="false">Or</button>
          <button type="button" class="tier tier--diamant" data-tier="3" aria-pressed="false">Diamant</button>
          <button type="button" class="tier tier--elite" data-tier="4" aria-pressed="false">Élite</button>
        </div>
        <p class="mycard__note">MY CARD est gratuite pour chaque joueur.</p>
      </div>

      <div class="mycard__stage" data-cards>
        <div class="deck" data-deck>
          <article class="card card--bronze" data-card="0" aria-label="Carte MY CARD, palier Bronze">
            <div class="card__inner">
              <div class="card__top"><img class="card__logo" src="<?php echo $a; ?>img/logo/elazyo-fond-fonce.png" alt="" width="84" height="15"><span class="card__brand">MY CARD</span><span class="card__tier">Bronze</span></div>
              <picture class="card__art"><img src="<?php echo $a; ?>img/mycard/mascotte-bronze.webp" width="294" height="398" alt="" loading="lazy" decoding="async"></picture>
              <div class="card__bottom"><span class="card__season">Saison 2026-2027</span></div>
            </div>
            <span class="card__sheen" aria-hidden="true"></span>
          </article>
          <article class="card card--argent" data-card="1" aria-label="Carte MY CARD, palier Argent">
            <div class="card__inner">
              <div class="card__top"><img class="card__logo" src="<?php echo $a; ?>img/logo/elazyo-fond-fonce.png" alt="" width="84" height="15"><span class="card__brand">MY CARD</span><span class="card__tier">Argent</span></div>
              <picture class="card__art"><img src="<?php echo $a; ?>img/mycard/mascotte-argent.webp" width="294" height="398" alt="" loading="lazy" decoding="async"></picture>
              <div class="card__bottom"><span class="card__season">Saison 2026-2027</span></div>
            </div>
            <span class="card__sheen" aria-hidden="true"></span>
          </article>
          <article class="card card--or" data-card="2" aria-label="Carte MY CARD, palier Or">
            <div class="card__inner">
              <div class="card__top"><img class="card__logo" src="<?php echo $a; ?>img/logo/elazyo-fond-fonce.png" alt="" width="84" height="15"><span class="card__brand">MY CARD</span><span class="card__tier">Or</span></div>
              <picture class="card__art"><img src="<?php echo $a; ?>img/mycard/mascotte-or.webp" width="294" height="398" alt="" loading="lazy" decoding="async"></picture>
              <div class="card__bottom"><span class="card__season">Saison 2026-2027</span></div>
            </div>
            <span class="card__sheen" aria-hidden="true"></span>
          </article>
          <article class="card card--diamant" data-card="3" aria-label="Carte MY CARD, palier Diamant">
            <div class="card__inner">
              <div class="card__top"><img class="card__logo" src="<?php echo $a; ?>img/logo/elazyo-fond-fonce.png" alt="" width="84" height="15"><span class="card__brand">MY CARD</span><span class="card__tier">Diamant</span></div>
              <picture class="card__art"><img src="<?php echo $a; ?>img/mycard/mascotte-diamant.webp" width="294" height="398" alt="" loading="lazy" decoding="async"></picture>
              <div class="card__bottom"><span class="card__season">Saison 2026-2027</span></div>
            </div>
            <span class="card__sheen" aria-hidden="true"></span>
          </article>
          <article class="card card--elite" data-card="4" aria-label="Carte MY CARD, palier Élite">
            <div class="card__inner">
              <div class="card__top"><img class="card__logo" src="<?php echo $a; ?>img/logo/elazyo-fond-fonce.png" alt="" width="84" height="15"><span class="card__brand">MY CARD</span><span class="card__tier">Élite</span></div>
              <picture class="card__art"><img src="<?php echo $a; ?>img/mycard/mascotte-elite.webp" width="294" height="398" alt="" loading="lazy" decoding="async"></picture>
              <div class="card__bottom"><span class="card__season">Saison 2026-2027</span></div>
            </div>
            <span class="card__sheen" aria-hidden="true"></span>
          </article>
        </div>
        <p class="mycard__caption">La mascotte ELAZYO, l’un des visuels de carte de l’appli. Paliers Or, Diamant et Élite&nbsp;: aperçus.</p>
      </div>
    </div>

    <div class="container mycard__how">
      <h3 class="mycard__how-title">Comment la carte se remplit</h3>
      <ul class="how">
        <li class="how__item">
          <h4 class="how__title">Le coach note, la carte avance.</h4>
          <p>Après chaque entraînement et chaque match, le coach note chaque joueur sur 10, axe par axe. En une minute avec l’évaluation rapide.</p>
          <ul class="how__axes" aria-label="Les six axes de la carte">
            <li>Technique</li><li>Tactique</li><li>Physique</li><li>Psychologique</li><li class="is-engagement">Assiduité</li><li class="is-engagement">Vie du club</li>
          </ul>
        </li>
        <li class="how__item">
          <h4 class="how__title">Une case vide n’est jamais un zéro.</h4>
          <p>Un joueur non évalué reste non évalué. Le coach peut corriger ses notes jusqu’au lundi soir, puis tout se verrouille.</p>
        </li>
        <li class="how__item">
          <h4 class="how__title">Un classement qui récompense l’engagement.</h4>
          <p>Le Top ELAZYO classe les joueurs sur l’assiduité et la vie du club, jamais sur le niveau. On peut y briller sans être le plus doué.</p>
        </li>
      </ul>
    </div>
  </section>
