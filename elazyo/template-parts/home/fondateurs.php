<?php
/**
 * Page d’accueil : section « fondateurs ».
 *
 * Généré depuis la page statique par convertir.py, puis complété à la main si besoin.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;
$a = elazyo_asset_base();
?>
  <section class="fondateurs theme-night" id="clubs-fondateurs" aria-labelledby="fondateurs-titre">
    <div class="container fondateurs__grid">
      <div class="fondateurs__aside">
        <div class="ticket" data-reveal>
          <p class="ticket__count"><span class="ticket__num" data-count-to="<?php echo esc_attr( elazyo_mod( 'fondateurs_places' ) ); ?>"><?php echo esc_html( elazyo_mod( 'fondateurs_places' ) ); ?></span><span class="ticket__unit">places</span></p>
          <p class="ticket__label">de Club Fondateur cette saison</p>
          <p class="ticket__offer">ELAZYO gratuit pour tout le club jusqu’au <strong><?php echo esc_html( elazyo_mod( 'fondateurs_date' ) ); ?></strong></p>
          <svg class="ticket__chevron" aria-hidden="true"><use href="#i-big-chevron"/></svg>
        </div>
        <figure class="photo-cadre fondateurs__photo" data-reveal="mask">
          <picture>
            
            <source type="image/webp" srcset="<?php echo $a; ?>img/photos/entree-terrain-640.webp 640w, <?php echo $a; ?>img/photos/entree-terrain-1100.webp 1100w" sizes="(min-width: 1000px) 480px, 92vw">
            <img src="<?php echo $a; ?>img/photos/entree-terrain-1100.webp" width="1672" height="941" alt="" loading="lazy" decoding="async">
          </picture>
          <span class="photo-ia" aria-hidden="true">Image générée par IA</span>
        </figure>
      </div>

      <div class="fondateurs__copy">
        <h2 class="display display--1" id="fondateurs-titre" data-reveal="lines">
          <span class="line"><span>Devenez</span></span>
          <span class="line"><span>Club Fondateur.</span></span>
        </h2>
        <p class="lead" data-reveal>Nous installons ELAZYO avec vous, équipe par équipe, sans bousculer ce qui marche déjà.</p>

        <?php get_template_part( 'template-parts/home/preuve' ); ?>

        <div class="engagements" data-reveal>
          <div class="engagements__col">
            <h3 class="engagements__title">ELAZYO s’engage à</h3>
            <ul class="ticks">
              <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>L’appli gratuite pour tout le club jusqu’au <?php echo esc_html( elazyo_mod( 'fondateurs_date' ) ); ?></li>
              <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>L’installation avec vos coachs</li>
              <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Un tutoriel vidéo d’une minute pour les familles</li>
              <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Une réponse sous 48&nbsp;h à tout problème</li>
              <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Un tarif Fondateur réservé pour la saison suivante, annoncé avant le 31 mars 2027</li>
              <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>L’export de vos données à tout moment</li>
            </ul>
          </div>
          <div class="engagements__col">
            <h3 class="engagements__title">Le club s’engage à</h3>
            <ul class="ticks">
              <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Utiliser ELAZYO avec au moins 2 équipes pendant 8 semaines</li>
              <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Désigner un référent</li>
              <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Faire un point de 15 minutes toutes les deux semaines</li>
              <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Nous autoriser à citer le club, s’il est satisfait</li>
            </ul>
          </div>
        </div>

        <ol class="steps-mini" data-reveal="stagger">
          <li><span class="steps-mini__n">1</span>Une démo de 30 minutes, en visio ou au club</li>
          <li><span class="steps-mini__n">2</span>Le choix de 2 ou 3 équipes pour commencer</li>
          <li><span class="steps-mini__n">3</span>L’installation avec chaque coach, 20 minutes</li>
          <li><span class="steps-mini__n">4</span>Le reste du club suit, quand vous le décidez</li>
        </ol>

        <div class="cta-row cta-row--left">
          <a class="btn btn--elan btn--lg" href="#contact" data-prefill-objet="club-fondateur" data-prefill-profil="president">Candidater<svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></a>
          <p class="cta-row__note">Un simple accord par e-mail suffit pour réserver votre place.</p>
        </div>
      </div>
    </div>
  </section>
