<?php
/**
 * Page d’accueil : section « hero ».
 *
 * Généré depuis la page statique par convertir.py, puis complété à la main si besoin.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;
$a = elazyo_asset_base();
?>
  <section class="hero theme-night" id="accueil" aria-labelledby="hero-titre">
    <div class="container hero__grid">
      <div class="hero__copy">
        <p class="label hero__label" data-hero-step="0"><span class="chev" aria-hidden="true"></span><?php echo esc_html( elazyo_mod( 'hero_label' ) ); ?></p>
        <h1 class="display display--hero" id="hero-titre" data-hero-title>
          <?php foreach ( elazyo_hero_lines() as $line ) : ?>
          <span class="line"><span><?php echo $line; // Échappé dans elazyo_hero_lines(). ?></span></span>
          <?php endforeach; ?>
        </h1>
        <p class="lead hero__lead" data-hero-step="1"><?php echo elazyo_nbsp( esc_html( elazyo_mod( 'hero_lead' ) ) ); ?></p>
        <div class="hero__ctas" data-hero-step="2">
          <a class="btn btn--elan btn--lg" href="<?php echo esc_url( elazyo_mod( 'app_url' ) ); ?>" rel="noopener"><?php echo esc_html( elazyo_mod( 'hero_cta' ) ); ?><svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></a>
          <a class="btn btn--ghost btn--lg" href="#application"><svg class="icon icon--fill" aria-hidden="true"><use href="#i-play"/></svg><?php echo esc_html( elazyo_mod( 'hero_cta2' ) ); ?></a>
        </div>
        <ul class="hero__proof" data-hero-step="3">
          <?php foreach ( array( 'hero_proof1', 'hero_proof2', 'hero_proof3' ) as $key ) : if ( elazyo_mod( $key ) ) : ?>
          <li><?php echo esc_html( elazyo_mod( $key ) ); ?></li>
          <?php endif; endforeach; ?>
        </ul>
        <p class="hero__caption hero__caption--copy">Animation&nbsp;: score réel du F.C. Epagny Metz-Tessy&nbsp;; convocations, covoiturage, bilan et carte donnés en exemple.</p>
      </div>

      <div class="hero__visual" data-hero-visual>
        <div class="scene" data-scene data-beat="0">
          <div class="scene__glow" aria-hidden="true"></div>
          <div class="scene__pitch" aria-hidden="true">
            <svg class="pitch" viewBox="0 0 1050 680" preserveAspectRatio="xMidYMid meet" focusable="false">
              <rect x="10" y="10" width="1030" height="660" pathLength="1"/>
              <path d="M525 10V670" pathLength="1"/>
              <circle cx="525" cy="340" r="91.5" pathLength="1"/>
              <rect x="10" y="138.4" width="165" height="403.2" pathLength="1"/>
              <rect x="875" y="138.4" width="165" height="403.2" pathLength="1"/>
              <rect x="10" y="248.4" width="55" height="183.2" pathLength="1"/>
              <rect x="985" y="248.4" width="55" height="183.2" pathLength="1"/>
              <path d="M175 266.9A91.5 91.5 0 0 1 175 413.1" pathLength="1"/>
              <path d="M875 266.9A91.5 91.5 0 0 0 875 413.1" pathLength="1"/>
              <circle class="pitch__spot" cx="525" cy="340" r="4"/>
              <circle class="pitch__spot" cx="120" cy="340" r="4"/>
              <circle class="pitch__spot" cx="930" cy="340" r="4"/>
            </svg>
          </div>
          <div class="scene__stage" data-tilt>
            <span class="scene__shadow" aria-hidden="true"></span>
            <figure class="phone phone--hero scene__phone">
              <div class="phone__frame">
                <div class="phone__screen" data-beat-screens>
                  <picture class="phone__shot is-active"><source type="image/webp" srcset="<?php echo $a; ?>img/ecrans/planning-president-360.webp 360w, <?php echo $a; ?>img/ecrans/planning-president-720.webp 720w" sizes="(min-width: 1100px) 290px, (min-width: 700px) 260px, 60vw"><img src="<?php echo $a; ?>img/ecrans/planning-president-720.webp" width="1080" height="2004" alt="Écran Planning de l’appli ELAZYO : matchs et entraînements de la semaine, convoqués et confirmés." fetchpriority="high" decoding="async"></picture>
                  <picture class="phone__shot"><source type="image/webp" srcset="<?php echo $a; ?>img/ecrans/covoiturage-360.webp 360w, <?php echo $a; ?>img/ecrans/covoiturage-720.webp 720w" sizes="(min-width: 1100px) 290px, (min-width: 700px) 260px, 60vw"><img src="<?php echo $a; ?>img/ecrans/covoiturage-720.webp" width="1080" height="2004" alt="Écran Transport : trajets proposés par les familles pour un déplacement." fetchpriority="low" decoding="async"></picture>
                  <picture class="phone__shot"><source type="image/webp" srcset="<?php echo $a; ?>img/ecrans/match-360.webp 360w, <?php echo $a; ?>img/ecrans/match-720.webp 720w" sizes="(min-width: 1100px) 290px, (min-width: 700px) 260px, 60vw"><img src="<?php echo $a; ?>img/ecrans/match-720.webp" width="1080" height="2004" alt="Écran Match : fiche du match avec le score et les faits de jeu." fetchpriority="low" decoding="async"></picture>
                  <picture class="phone__shot"><source type="image/webp" srcset="<?php echo $a; ?>img/ecrans/equipe-coach-360.webp 360w, <?php echo $a; ?>img/ecrans/equipe-coach-720.webp 720w" sizes="(min-width: 1100px) 290px, (min-width: 700px) 260px, 60vw"><img src="<?php echo $a; ?>img/ecrans/equipe-coach-720.webp" width="1080" height="2004" alt="Écran Équipe du coach : effectif, présences et prochain match." fetchpriority="low" decoding="async"></picture>
                </div>
              </div>
            </figure>

            <div class="beat beat--convoc" data-beat-card="0" aria-hidden="true">
              <p class="beat__tag"><svg class="icon"><use href="#i-calendar"/></svg>Convocations</p>
              <p class="beat__meta">Match à 11 · dimanche</p>
              <p class="beat__big"><span data-beat-count="14">14</span><small>/14</small></p>
              <p class="beat__sub">joueurs confirmés</p>
              <ol class="beat__dots"><li style="--i:0"></li><li style="--i:1"></li><li style="--i:2"></li><li style="--i:3"></li><li style="--i:4"></li><li style="--i:5"></li><li style="--i:6"></li><li style="--i:7"></li><li style="--i:8"></li><li style="--i:9"></li><li style="--i:10"></li><li style="--i:11"></li><li style="--i:12"></li><li style="--i:13"></li></ol>
            </div>
            <div class="beat beat--covoit" data-beat-card="1" aria-hidden="true">
              <p class="beat__tag"><svg class="icon"><use href="#i-car"/></svg>Covoiturage</p>
              <p class="beat__meta">Déplacement de dimanche</p>
              <ul class="beat__cars"><li class="beat__car" style="--c:0"><svg class="icon" aria-hidden="true"><use href="#i-car"/></svg><span style="--i:0"></span><span style="--i:1"></span><span style="--i:2"></span><span style="--i:3"></span></li><li class="beat__car" style="--c:1"><svg class="icon" aria-hidden="true"><use href="#i-car"/></svg><span style="--i:4"></span><span style="--i:5"></span><span style="--i:6"></span><span style="--i:7"></span></li><li class="beat__car" style="--c:2"><svg class="icon" aria-hidden="true"><use href="#i-car"/></svg><span style="--i:8"></span><span style="--i:9"></span><span style="--i:10"></span><span style="--i:11"></span></li><li class="beat__car" style="--c:3"><svg class="icon" aria-hidden="true"><use href="#i-car"/></svg><span style="--i:12"></span><span style="--i:13"></span><span style="--i:14"></span><span style="--i:15"></span></li></ul>
              <p class="beat__sub beat__sub--ok"><svg class="icon"><use href="#i-checkcircle"/></svg>Tout le monde a sa place</p>
            </div>
            <div class="beat beat--match" data-beat-card="2" aria-hidden="true">
              <p class="beat__tag beat__tag--live"><span class="beat__live"></span>En direct</p>
              <p class="beat__meta">U15 F · 2<sup>e</sup> mi-temps</p>
              <div class="beat__score"><span class="beat__team">Epagny</span><b class="beat__goals"><span class="beat__flip"><span>2</span><span>3</span></span><span class="beat__sep">–</span>2</b><span class="beat__team">Marignier</span></div>
              <p class="beat__sub beat__sub--win">Victoire 3-2&nbsp;!</p>
            </div>
            <div class="beat beat--bilan" data-beat-card="3" aria-hidden="true">
              <p class="beat__tag"><svg class="icon"><use href="#i-whistle"/></svg>Bilan du coach</p>
              <ul class="beat__bars"><li style="--v:7;--i:0"><span>Technique</span><b>7</b><i></i></li><li style="--v:6;--i:1"><span>Tactique</span><b>6</b><i></i></li><li style="--v:8;--i:2"><span>Physique</span><b>8</b><i></i></li><li style="--v:7;--i:3"><span>Psychologique</span><b>7</b><i></i></li></ul>
              <p class="beat__sub">Objectifs Argent&nbsp;: <b>7/7</b></p>
            </div>
            <span class="xp" style="--i:0" aria-hidden="true"></span><span class="xp" style="--i:1" aria-hidden="true"></span><span class="xp" style="--i:2" aria-hidden="true"></span><span class="xp" style="--i:3" aria-hidden="true"></span><span class="xp" style="--i:4" aria-hidden="true"></span><span class="xp" style="--i:5" aria-hidden="true"></span><span class="xp" style="--i:6" aria-hidden="true"></span>
            <div class="hcard" aria-hidden="true">
              <div class="card card--bronze hcard__card" data-hcard>
                <div class="card__inner">
                  <picture class="hcard__art hcard__art--bronze"><img src="<?php echo $a; ?>img/mycard/mascotte-bronze.webp" width="294" height="398" alt="" decoding="async"></picture>
                  <picture class="hcard__art hcard__art--argent"><img src="<?php echo $a; ?>img/mycard/mascotte-argent.webp" width="294" height="398" alt="" decoding="async"></picture>
                  <div class="card__top"><img class="card__logo" src="<?php echo $a; ?>img/logo/elazyo-fond-fonce.png" alt="" width="84" height="15"><span class="card__brand">MY CARD</span><span class="card__tier"><span class="hcard__t hcard__t--bronze">Bronze</span><span class="hcard__t hcard__t--argent">Argent</span></span></div>
                  <div class="card__bottom"><span class="card__season">Saison 2026-2027</span></div>
                </div>
                <span class="hcard__sheen"></span>
              </div>
            </div>
            <p class="sr-only">Une semaine de club dans l’appli. Mardi, les 14 joueurs convoqués, 11 titulaires et 3 remplaçants, confirment leur présence. Jeudi, quatre voitures emmènent tout le monde au match. Samedi, l’équipe gagne 3-2, suivie en direct. Lundi, le bilan du coach fait passer la carte MY CARD d’un joueur de Bronze à Argent.</p>
          </div>
        </div>
      </div>

      <div class="hero__meta">
        <div class="semaine" role="group" aria-label="Une semaine de club, étape par étape">
          <button class="motion-toggle" type="button" aria-pressed="false" data-slideshow-toggle>
            <svg class="icon icon--pause" aria-hidden="true"><use href="#i-pause"/></svg>
            <svg class="icon icon--play" aria-hidden="true"><use href="#i-play-line"/></svg>
            <span class="sr-only">Mettre en pause l’animation</span>
          </button>
          <ol class="semaine__jours">
            <li><button type="button" class="semaine__jour" data-goto-beat="0" aria-pressed="true"><span class="semaine__j">Mar.</span><span class="semaine__quoi">Convocations</span><span class="semaine__barre" aria-hidden="true"></span></button></li>
            <li><button type="button" class="semaine__jour" data-goto-beat="1" aria-pressed="false"><span class="semaine__j">Jeu.</span><span class="semaine__quoi">Covoiturage</span><span class="semaine__barre" aria-hidden="true"></span></button></li>
            <li><button type="button" class="semaine__jour" data-goto-beat="2" aria-pressed="false"><span class="semaine__j">Sam.</span><span class="semaine__quoi">Match</span><span class="semaine__barre" aria-hidden="true"></span></button></li>
            <li><button type="button" class="semaine__jour" data-goto-beat="3" aria-pressed="false"><span class="semaine__j">Lun.</span><span class="semaine__quoi">MY CARD</span><span class="semaine__barre" aria-hidden="true"></span></button></li>
          </ol>
        </div>
        <p class="hero__caption">Score réel du F.C. Epagny Metz-Tessy. Convocations, covoiturage, bilan et carte&nbsp;: exemples.</p>
      </div>
    </div>
    <a class="hero__scroll" href="#manifeste"><span>Découvrir</span><span class="hero__scroll-line" aria-hidden="true"></span></a>
  </section>
