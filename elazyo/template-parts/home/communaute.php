<?php
/**
 * Page d’accueil : section « communaute ».
 *
 * Généré depuis la page statique par convertir.py, puis complété à la main si besoin.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;
$a = elazyo_asset_base();
?>
  <section class="communaute theme-bleu section--bande" id="communaute" aria-labelledby="communaute-titre">
    <div class="bande bande--bleu">
      <div class="bande__media">
        <picture class="bande__photo">
          
          <source type="image/webp" srcset="<?php echo $a; ?>img/photos/megaphone-960.webp 960w, <?php echo $a; ?>img/photos/megaphone-1672.webp 1672w" sizes="100vw">
          <img src="<?php echo $a; ?>img/photos/megaphone-1672.webp" width="1672" height="941" alt="" loading="lazy" decoding="async">
        </picture>
        <span class="photo-ia" aria-hidden="true">Image générée par IA</span>
      </div>
      <div class="container">
        <div class="section-head bande__head">
          <h2 class="display display--1" id="communaute-titre" data-reveal="lines">
            <span class="line"><span>Le club, c’est</span></span>
            <span class="line"><span>tout le monde.</span></span>
          </h2>
          <p class="lead section-head__lead" data-reveal>Un club ne fonctionne que grâce à l’engagement de tous, bénévoles compris. ELAZYO donne à chacun une façon simple de participer.</p>
        </div>
      </div>
    </div>

    <div class="valeurs" aria-labelledby="valeurs-titre">
      <h3 class="sr-only" id="valeurs-titre">Les valeurs que nous voulons transmettre aux enfants</h3>
      <div class="valeurs__track" data-marquee>
        <ul class="valeurs__list">
          <li>Respect</li><li>Solidarité</li><li>Engagement</li><li>Assiduité</li><li>Inclusion</li><li>Fair-play</li><li>Maîtrise de ses émotions</li>
        </ul>
        <ul class="valeurs__list" aria-hidden="true">
          <li>Respect</li><li>Solidarité</li><li>Engagement</li><li>Assiduité</li><li>Inclusion</li><li>Fair-play</li><li>Maîtrise de ses émotions</li>
        </ul>
      </div>
      <button class="motion-toggle valeurs__toggle" type="button" aria-pressed="false" data-marquee-toggle>
        <svg class="icon icon--pause" aria-hidden="true"><use href="#i-pause"/></svg>
        <svg class="icon icon--play" aria-hidden="true"><use href="#i-play-line"/></svg>
        <span class="sr-only">Mettre en pause le défilement des valeurs</span>
      </button>
    </div>

    <div class="container">
      <div class="communaute__grid" data-reveal="stagger">
        <article class="engage">
          <svg class="icon engage__icon" aria-hidden="true"><use href="#i-heart"/></svg>
          <h3 class="engage__title">Bénévoles</h3>
          <p>Buvette, maillots, goûter, arbitre de touche. Chacun se propose sur la mission qui lui va.</p>
        </article>
        <article class="engage">
          <svg class="icon engage__icon" aria-hidden="true"><use href="#i-car"/></svg>
          <h3 class="engage__title">Familles</h3>
          <p>Le covoiturage s’organise au même endroit. Chaque famille sait qui emmène qui.</p>
        </article>
        <article class="engage engage--soon">
          <svg class="icon engage__icon" aria-hidden="true"><use href="#i-megaphone"/></svg>
          <h3 class="engage__title">Supporters <span class="soon-inline">Bientôt</span></h3>
          <p>Suivre le club, ses équipes et ses joueurs, saison après saison.</p>
        </article>
        <article class="engage engage--academy">
          <p class="badge badge--on-bleu"><svg class="icon" aria-hidden="true"><use href="#i-clock"/></svg>Bientôt</p>
          <h3 class="engage__title engage__title--display">ELAZYO ACADEMY</h3>
          <p><strong>Des histoires qui font vivre les valeurs.</strong> Les enfants suivront les aventures de cinq jeunes joueurs d’un club amateur, en lien avec les thèmes du Programme Éducatif Fédéral. Les personnages se trompent, en discutent et changent&nbsp;: c’est ainsi que les valeurs se transmettent.</p>
        </article>
      </div>
    </div>
  </section>
