<?php
/**
 * Page d’accueil : section « application ».
 *
 * Généré depuis la page statique par convertir.py, puis complété à la main si besoin.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;
$a = elazyo_asset_base();
?>
  <section class="appli theme-night" id="application" aria-labelledby="application-titre">
    <div class="container">
      <div class="appli__head">
        <h2 class="display display--1" id="application-titre" data-reveal="lines">
          <span class="line"><span>L’appli</span></span>
          <span class="line"><span>en action.</span></span>
        </h2>
        <p class="lead" data-reveal>Six écrans, six moments de la vie du club. Les pastilles montrent à quoi sert chaque bouton.</p>
      </div>

      <div class="appli__tabs" data-tabs>
        <div class="appli__list" role="tablist" aria-label="Écrans de l’appli">
          <button class="appli__tab" role="tab" type="button" id="appli-tab-planning" aria-controls="appli-planning" aria-selected="true"><svg class="icon" aria-hidden="true"><use href="#i-calendar"/></svg>Planning</button><button class="appli__tab" role="tab" type="button" id="appli-tab-seances" aria-controls="appli-seances" aria-selected="false" tabindex="-1"><svg class="icon" aria-hidden="true"><use href="#i-whistle"/></svg>Séances</button><button class="appli__tab" role="tab" type="button" id="appli-tab-match" aria-controls="appli-match" aria-selected="false" tabindex="-1"><svg class="icon" aria-hidden="true"><use href="#i-flag"/></svg>Match</button><button class="appli__tab" role="tab" type="button" id="appli-tab-covoiturage" aria-controls="appli-covoiturage" aria-selected="false" tabindex="-1"><svg class="icon" aria-hidden="true"><use href="#i-car"/></svg>Covoiturage</button><button class="appli__tab" role="tab" type="button" id="appli-tab-communication" aria-controls="appli-communication" aria-selected="false" tabindex="-1"><svg class="icon" aria-hidden="true"><use href="#i-megaphone"/></svg>Communication</button><button class="appli__tab" role="tab" type="button" id="appli-tab-finances" aria-controls="appli-finances" aria-selected="false" tabindex="-1"><svg class="icon" aria-hidden="true"><use href="#i-wallet"/></svg>Finances</button>
        </div>
        <div class="appli__panels">
          <div class="appli__panel" role="tabpanel" id="appli-planning" aria-labelledby="appli-tab-planning" tabindex="0">
            <figure class="appli__device panel__media">
              <div class="phone phone--appli"><div class="phone__frame"><div class="phone__screen">
                <picture class="phone__shot is-active"><source type="image/webp" srcset="<?php echo $a; ?>img/ecrans/planning-president-360.webp 360w, <?php echo $a; ?>img/ecrans/planning-president-720.webp 720w" sizes="(min-width: 1100px) 320px, (min-width: 700px) 280px, 66vw"><img src="<?php echo $a; ?>img/ecrans/planning-president-720.webp" width="720" height="1336" alt="Écran Planning : matchs et entraînements de novembre, bouton pour créer un événement de club et import de calendrier." decoding="async"></picture>
                <div class="spots" aria-hidden="true"><span class="spot" style="--x:40%;--y:15%" data-spot="1">1</span><span class="spot" style="--x:87%;--y:55%" data-spot="2">2</span><span class="spot" style="--x:44%;--y:77.5%" data-spot="3">3</span></div>
              </div></div></div>
            </figure>
            <div class="appli__text panel__text">
              <h3 class="appli__title">Le planning de tout le club.</h3>
              <p class="appli__lead">Matchs, entraînements et événements, avec l’heure de rendez-vous et le coup d’envoi.</p>
              <ol class="callouts"><li data-spot="1"><span class="callouts__n" aria-hidden="true">1</span>Un match, un entraînement ou un événement de club se crée en quelques secondes.</li><li data-spot="2"><span class="callouts__n" aria-hidden="true">2</span>Un calendrier existant s’importe : rien à ressaisir.</li><li data-spot="3"><span class="callouts__n" aria-hidden="true">3</span>Ce qui reste à préparer saute aux yeux.</li></ol>
            </div>
          </div>
          <div class="appli__panel" role="tabpanel" id="appli-seances" aria-labelledby="appli-tab-seances" tabindex="0" hidden>
            <figure class="appli__device panel__media">
              <div class="phone phone--appli"><div class="phone__frame"><div class="phone__screen">
                <picture class="phone__shot is-active"><source type="image/webp" srcset="<?php echo $a; ?>img/ecrans/seances-coach-360.webp 360w, <?php echo $a; ?>img/ecrans/seances-coach-720.webp 720w" sizes="(min-width: 1100px) 320px, (min-width: 700px) 280px, 66vw"><img src="<?php echo $a; ?>img/ecrans/seances-coach-720.webp" width="720" height="1336" alt="Écran Séances du coach : parcourir la bibliothèque, créer une séance, importer un entraînement, mes séances." decoding="async" loading="lazy"></picture>
                <div class="spots" aria-hidden="true"><span class="spot" style="--x:32%;--y:49%" data-spot="1">1</span><span class="spot" style="--x:63%;--y:49%" data-spot="2">2</span><span class="spot" style="--x:60%;--y:65.5%" data-spot="3">3</span></div>
              </div></div></div>
            </figure>
            <div class="appli__text panel__text">
              <h3 class="appli__title">Des séances prêtes à animer.</h3>
              <p class="appli__lead">Le coach prépare ses entraînements, les réutilise et les partage avec son équipe.</p>
              <ol class="callouts"><li data-spot="1"><span class="callouts__n" aria-hidden="true">1</span>Une bibliothèque d’exercices classés par thème et par catégorie.</li><li data-spot="2"><span class="callouts__n" aria-hidden="true">2</span>Une séance se construit en ajoutant des exercices, un objectif et des notes.</li><li data-spot="3"><span class="callouts__n" aria-hidden="true">3</span>Une bonne séance se duplique en modèle pour la semaine suivante.</li></ol>
            </div>
          </div>
          <div class="appli__panel" role="tabpanel" id="appli-match" aria-labelledby="appli-tab-match" tabindex="0" hidden>
            <figure class="appli__device panel__media">
              <div class="phone phone--appli"><div class="phone__frame"><div class="phone__screen">
                <picture class="phone__shot is-active"><source type="image/webp" srcset="<?php echo $a; ?>img/ecrans/match-360.webp 360w, <?php echo $a; ?>img/ecrans/match-720.webp 720w" sizes="(min-width: 1100px) 320px, (min-width: 700px) 280px, 66vw"><img src="<?php echo $a; ?>img/ecrans/match-720.webp" width="720" height="1700" alt="Écran d’un match : 11 présents, 2 absents, 1 en attente, informations du match, renfort et missions." decoding="async" loading="lazy"></picture>
                <div class="spots" aria-hidden="true"><span class="spot" style="--x:79%;--y:17.8%" data-spot="1">1</span><span class="spot" style="--x:70%;--y:67%" data-spot="2">2</span><span class="spot" style="--x:55%;--y:82%" data-spot="3">3</span></div>
              </div></div></div>
            </figure>
            <div class="appli__text panel__text">
              <h3 class="appli__title">Le jour du match, tout est prêt.</h3>
              <p class="appli__lead">Présences, lieu, covoiturage, renforts et missions : le coach voit tout sur un seul écran.</p>
              <ol class="callouts"><li data-spot="1"><span class="callouts__n" aria-hidden="true">1</span>Présents, absents, en attente : l’appel en un coup d’œil.</li><li data-spot="2"><span class="callouts__n" aria-hidden="true">2</span>Le lieu et le covoiturage du match, au même endroit.</li><li data-spot="3"><span class="callouts__n" aria-hidden="true">3</span>Un renfort d’une autre catégorie, avec l’accord de son coach.</li></ol>
            </div>
          </div>
          <div class="appli__panel" role="tabpanel" id="appli-covoiturage" aria-labelledby="appli-tab-covoiturage" tabindex="0" hidden>
            <figure class="appli__device panel__media">
              <div class="phone phone--appli"><div class="phone__frame"><div class="phone__screen">
                <picture class="phone__shot is-active"><source type="image/webp" srcset="<?php echo $a; ?>img/ecrans/covoiturage-360.webp 360w, <?php echo $a; ?>img/ecrans/covoiturage-720.webp 720w" sizes="(min-width: 1100px) 320px, (min-width: 700px) 280px, 66vw"><img src="<?php echo $a; ?>img/ecrans/covoiturage-720.webp" width="720" height="1336" alt="Écran Transport : organisation complète, 12 participants, voitures proposées par les familles." decoding="async" loading="lazy"></picture>
                <div class="spots" aria-hidden="true"><span class="spot" style="--x:92%;--y:13.5%" data-spot="1">1</span><span class="spot" style="--x:38%;--y:66%" data-spot="2">2</span><span class="spot" style="--x:82%;--y:69%" data-spot="3">3</span></div>
              </div></div></div>
            </figure>
            <div class="appli__text panel__text">
              <h3 class="appli__title">Le covoit’ s’organise tout seul.</h3>
              <p class="appli__lead">Les parents proposent leurs places, le point de rendez-vous et l’heure de départ.</p>
              <ol class="callouts"><li data-spot="1"><span class="callouts__n" aria-hidden="true">1</span>Le coach sait tout de suite si chaque convoqué a sa place.</li><li data-spot="2"><span class="callouts__n" aria-hidden="true">2</span>Les places peuvent être attribuées automatiquement.</li><li data-spot="3"><span class="callouts__n" aria-hidden="true">3</span>Un imprévu au retour ? Le parent le signale en un geste.</li></ol>
            </div>
          </div>
          <div class="appli__panel" role="tabpanel" id="appli-communication" aria-labelledby="appli-tab-communication" tabindex="0" hidden>
            <figure class="appli__device panel__media">
              <div class="phone phone--appli"><div class="phone__frame"><div class="phone__screen">
                <picture class="phone__shot is-active"><source type="image/webp" srcset="<?php echo $a; ?>img/ecrans/communication-v2-360.webp 360w, <?php echo $a; ?>img/ecrans/communication-v2-720.webp 720w" sizes="(min-width: 1100px) 320px, (min-width: 700px) 280px, 66vw"><img src="<?php echo $a; ?>img/ecrans/communication-v2-720.webp" width="720" height="1336" alt="Écran Communication : nouvelle communication, annonces, messages et vie du club." decoding="async" loading="lazy"></picture>
                <div class="spots" aria-hidden="true"><span class="spot" style="--x:88%;--y:45%" data-spot="1">1</span><span class="spot" style="--x:40%;--y:56.5%" data-spot="2">2</span><span class="spot" style="--x:78%;--y:56.5%" data-spot="3">3</span></div>
              </div></div></div>
            </figure>
            <div class="appli__text panel__text">
              <h3 class="appli__title">Les infos arrivent à tout le monde.</h3>
              <p class="appli__lead">Annonces, messages privés et moments du club : les nouvelles ne se perdent plus dans les groupes.</p>
              <ol class="callouts"><li data-spot="1"><span class="callouts__n" aria-hidden="true">1</span>Une annonce, un message ou une photo, depuis un seul bouton.</li><li data-spot="2"><span class="callouts__n" aria-hidden="true">2</span>Les annonces informent sans déclencher cinquante réponses.</li><li data-spot="3"><span class="callouts__n" aria-hidden="true">3</span>Les échanges privés avec un joueur, un parent ou le staff.</li></ol>
            </div>
          </div>
          <div class="appli__panel" role="tabpanel" id="appli-finances" aria-labelledby="appli-tab-finances" tabindex="0" hidden>
            <figure class="appli__device panel__media">
              <div class="phone phone--appli"><div class="phone__frame"><div class="phone__screen">
                <picture class="phone__shot is-active"><source type="image/webp" srcset="<?php echo $a; ?>img/ecrans/finance-v2-360.webp 360w, <?php echo $a; ?>img/ecrans/finance-v2-720.webp 720w" sizes="(min-width: 1100px) 320px, (min-width: 700px) 280px, 66vw"><img src="<?php echo $a; ?>img/ecrans/finance-v2-720.webp" width="720" height="1336" alt="Écran Finance : exercice 2026-2027, trésorerie disponible, recettes, dépenses, cotisations dues et remboursements en attente." decoding="async" loading="lazy"></picture>
                <div class="spots" aria-hidden="true"><span class="spot" style="--x:80%;--y:47%" data-spot="1">1</span><span class="spot" style="--x:85%;--y:55%" data-spot="2">2</span><span class="spot" style="--x:44%;--y:82%" data-spot="3">3</span></div>
              </div></div></div>
            </figure>
            <div class="appli__text panel__text">
              <h3 class="appli__title">La trésorerie, sans tableur.</h3>
              <p class="appli__lead">Le bureau suit l’exercice au même endroit, du premier euro à la clôture.</p>
              <ol class="callouts"><li data-spot="1"><span class="callouts__n" aria-hidden="true">1</span>Un exercice par saison, clôturé et exportable en PDF ou en tableur.</li><li data-spot="2"><span class="callouts__n" aria-hidden="true">2</span>La trésorerie en banque et en caisse, toujours à jour.</li><li data-spot="3"><span class="callouts__n" aria-hidden="true">3</span>Les cotisations dues, avec des relances automatiques.</li></ol>
            </div>
          </div>
        </div>
      </div>
      <p class="appli__caption">Écrans réels de l’appli, avec les données d’un club de démonstration.</p>

      <div class="cta-row" data-reveal>
        <a class="btn btn--elan btn--lg" <?php elazyo_store_attrs(); ?>>Essayer gratuitement<svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></a>
        <a class="btn btn--ghost btn--lg" href="#contact" data-prefill-objet="demo">Demander une démo</a>
      </div>
    </div>
  </section>
