<?php
/**
 * Page d’accueil : section « profils ».
 *
 * Généré depuis la page statique par convertir.py, puis complété à la main si besoin.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;
$a = elazyo_asset_base();
?>
  <section class="profils theme-bleu" id="profils" aria-labelledby="profils-titre">
    <div class="container">
      <div class="section-head section-head--split">
        <h2 class="display display--1" id="profils-titre" data-reveal="lines">
          <span class="line"><span>Chacun voit ce</span></span>
          <span class="line"><span>qui le concerne.</span></span>
        </h2>
        <p class="lead section-head__lead" data-reveal>Sept profils, une seule appli. Une même personne peut avoir plusieurs rôles, par exemple parent et coach, avec un seul compte.</p>
      </div>

      <div class="tabs" data-tabs>
        <div class="tabs__list" role="tablist" aria-label="Profils" aria-orientation="vertical">
          <button class="tabs__tab" role="tab" type="button" id="tab-president" aria-controls="panel-president" aria-selected="true">Président et direction</button>
          <button class="tabs__tab" role="tab" type="button" id="tab-coach" aria-controls="panel-coach" aria-selected="false" tabindex="-1">Éducateur et coach</button>
          <button class="tabs__tab" role="tab" type="button" id="tab-tresorerie" aria-controls="panel-tresorerie" aria-selected="false" tabindex="-1">Secrétariat et trésorerie</button>
          <button class="tabs__tab" role="tab" type="button" id="tab-familles" aria-controls="panel-familles" aria-selected="false" tabindex="-1">Familles et parents</button>
          <button class="tabs__tab" role="tab" type="button" id="tab-joueurs" aria-controls="panel-joueurs" aria-selected="false" tabindex="-1">Joueurs</button>
          <button class="tabs__tab" role="tab" type="button" id="tab-benevoles" aria-controls="panel-benevoles" aria-selected="false" tabindex="-1">Bénévoles</button>
          <button class="tabs__tab" role="tab" type="button" id="tab-supporters" aria-controls="panel-supporters" aria-selected="false" tabindex="-1">Supporters et collectionneurs<span class="tabs__soon">Bientôt</span></button>
        </div>

        <div class="tabs__panels">
          <div class="tabs__panel" role="tabpanel" id="panel-president" aria-labelledby="tab-president" tabindex="0">
            <div class="panel__text">
              <div class="panel__band"><picture><source type="image/webp" srcset="<?php echo $a; ?>img/photos/tablette-600.webp 600w, <?php echo $a; ?>img/photos/tablette-1100.webp 1100w" sizes="(min-width: 900px) 420px, 92vw"><img src="<?php echo $a; ?>img/photos/tablette-1100.webp" width="1152" height="941" alt="" loading="lazy" decoding="async"></picture><span class="photo-ia">Image générée par IA</span></div>
              <h3 class="display display--2">Piloter tout le club.</h3>
              <ul class="ticks">
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>La mise en route du club, étape par étape</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Ce qui reste à traiter&nbsp;: demandes, validations, actions</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>L’activité de chaque équipe et les prochains rendez-vous</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>La trésorerie, les cotisations et les licences impayées</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>La page publique du club et le direct des matchs</li>
              </ul>
            </div>
            <div class="panel__media"><figure class="phone phone--panel"><div class="phone__frame"><div class="phone__screen"><picture class="phone__shot is-active">
              
              <source type="image/webp" srcset="<?php echo $a; ?>img/ecrans/accueil-president-360.webp 360w, <?php echo $a; ?>img/ecrans/accueil-president-720.webp 720w" sizes="(min-width: 1100px) 250px, (min-width: 700px) 220px, 56vw">
              <img src="<?php echo $a; ?>img/ecrans/accueil-president-360.webp" width="1080" height="2004" alt="Accueil du président : mise en route du club et demandes à traiter." loading="lazy" decoding="async">
            </picture></div></div></figure></div>
          </div>

          <div class="tabs__panel" role="tabpanel" id="panel-coach" aria-labelledby="tab-coach" tabindex="0" hidden>
            <div class="panel__text">
              <div class="panel__band"><picture><source type="image/webp" srcset="<?php echo $a; ?>img/photos/entrainement-600.webp 600w, <?php echo $a; ?>img/photos/entrainement-1040.webp 1040w" sizes="(min-width: 900px) 420px, 92vw"><img src="<?php echo $a; ?>img/photos/entrainement-1040.webp" width="1040" height="941" alt="" loading="lazy" decoding="async"></picture><span class="photo-ia">Image générée par IA</span></div>
              <h3 class="display display--2">Convoquer, faire l’appel, transmettre.</h3>
              <ul class="ticks">
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Son équipe en un écran&nbsp;: effectif, présences, prochain match</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Ses joueurs invités par lien ou QR code</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Convocations, relances en un clic, appel et feuille de match</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Des séances prêtes à l’emploi, avec une bibliothèque d’exercices</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>L’évaluation après chaque séance, qui remplit MY CARD</li>
              </ul>
            </div>
            <div class="panel__media"><figure class="phone phone--panel"><div class="phone__frame"><div class="phone__screen"><picture class="phone__shot is-active">
              
              <source type="image/webp" srcset="<?php echo $a; ?>img/ecrans/equipe-coach-360.webp 360w, <?php echo $a; ?>img/ecrans/equipe-coach-720.webp 720w" sizes="(min-width: 1100px) 250px, (min-width: 700px) 220px, 56vw">
              <img src="<?php echo $a; ?>img/ecrans/equipe-coach-360.webp" width="1080" height="2004" alt="Écran Équipe du coach : effectif et présences, prochain match, bouton pour inviter un joueur." loading="lazy" decoding="async">
            </picture></div></div></figure></div>
          </div>

          <div class="tabs__panel" role="tabpanel" id="panel-tresorerie" aria-labelledby="tab-tresorerie" tabindex="0" hidden>
            <div class="panel__text">
              <h3 class="display display--2">Les comptes du club, sans tableur.</h3>
              <ul class="ticks">
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Trésorerie disponible, banque et caisse</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Recettes et dépenses de l’exercice</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Cotisations dues et remboursements en attente</li>
              </ul>
            </div>
            <figure class="panel__media phone phone--panel"><div class="phone__frame"><div class="phone__screen"><picture class="phone__shot is-active">
              
              <source type="image/webp" srcset="<?php echo $a; ?>img/ecrans/finance-v2-360.webp 360w, <?php echo $a; ?>img/ecrans/finance-v2-720.webp 720w" sizes="(min-width: 1100px) 250px, (min-width: 700px) 220px, 56vw">
              <img src="<?php echo $a; ?>img/ecrans/finance-v2-360.webp" width="1080" height="2004" alt="Écran Finance : exercice 2026-2027, trésorerie disponible, recettes et dépenses." loading="lazy" decoding="async">
            </picture></div></div></figure>
          </div>

          <div class="tabs__panel" role="tabpanel" id="panel-familles" aria-labelledby="tab-familles" tabindex="0" hidden>
            <div class="panel__text">
              <div class="panel__band"><picture><source type="image/webp" srcset="<?php echo $a; ?>img/photos/famille-600.webp 600w, <?php echo $a; ?>img/photos/famille-860.webp 860w" sizes="(min-width: 900px) 420px, 92vw"><img src="<?php echo $a; ?>img/photos/famille-860.webp" width="860" height="724" alt="" loading="lazy" decoding="async"></picture><span class="photo-ia">Image générée par IA</span></div>
              <h3 class="display display--2">Toutes les infos de l’enfant au même endroit.</h3>
              <ul class="ticks">
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Répondre aux convocations pour son enfant</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Recevoir les informations du club</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Proposer ou trouver une place de covoiturage</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Déclarer une indisponibilité, suivre plusieurs enfants</li>
              </ul>
            </div>
            <div class="panel__media"><figure class="phone phone--panel"><div class="phone__frame"><div class="phone__screen"><picture class="phone__shot is-active">
              
              <source type="image/webp" srcset="<?php echo $a; ?>img/ecrans/covoiturage-360.webp 360w, <?php echo $a; ?>img/ecrans/covoiturage-720.webp 720w" sizes="(min-width: 900px) 240px, 52vw">
              <img src="<?php echo $a; ?>img/ecrans/covoiturage-360.webp" width="1080" height="2004" alt="Écran Transport : les voitures proposées par les parents pour le match, avec les places restantes à l’aller et au retour." loading="lazy" decoding="async">
            </picture></div></div></figure></div>
          </div>

          <div class="tabs__panel" role="tabpanel" id="panel-joueurs" aria-labelledby="tab-joueurs" tabindex="0" hidden>
            <div class="panel__text">
              <div class="panel__band"><picture><source type="image/webp" srcset="<?php echo $a; ?>img/photos/joueur-600.webp 600w, <?php echo $a; ?>img/photos/joueur-880.webp 880w" sizes="(min-width: 900px) 420px, 92vw"><img src="<?php echo $a; ?>img/photos/joueur-880.webp" width="880" height="724" alt="" style="object-position: 50% 18%" loading="lazy" decoding="async"></picture><span class="photo-ia">Image générée par IA</span></div>
              <h3 class="display display--2">Sa saison, ses rendez-vous.</h3>
              <ul class="ticks">
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Les convocations et le planning de l’équipe</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>MY CARD, sa carte de saison</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Ses statistiques de match et le Top assiduité et vie du club</li>
              </ul>
            </div>
            <div class="panel__media panel__media--card">
              <div class="card card--argent card--static" aria-label="Aperçu de la carte MY CARD, palier Argent">
                <div class="card__inner">
                  <div class="card__top"><img class="card__logo" src="<?php echo $a; ?>img/logo/elazyo-fond-fonce.png" alt="" width="84" height="15"><span class="card__brand">MY CARD</span><span class="card__tier">Argent</span></div>
                  <picture class="card__art"><img src="<?php echo $a; ?>img/mycard/mascotte-argent.webp" width="294" height="398" alt="" loading="lazy" decoding="async"></picture>
                  <div class="card__bottom"><span class="card__season">Saison 2026-2027</span></div>
                </div>
              </div>
            </div>
          </div>

          <div class="tabs__panel" role="tabpanel" id="panel-benevoles" aria-labelledby="tab-benevoles" tabindex="0" hidden>
            <div class="panel__text">
              <h3 class="display display--2">La mission qui vous va.</h3>
              <ul class="ticks">
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Les missions du club&nbsp;: buvette, installation, goûter, arbitre de touche</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Se proposer sur les créneaux libres</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Des récompenses débloquées par les missions</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Les annonces du club au même endroit</li>
              </ul>
            </div>
            <figure class="panel__media phone phone--panel"><div class="phone__frame"><div class="phone__screen"><picture class="phone__shot is-active">
              
              <source type="image/webp" srcset="<?php echo $a; ?>img/ecrans/communication-v2-360.webp 360w, <?php echo $a; ?>img/ecrans/communication-v2-720.webp 720w" sizes="(min-width: 1100px) 250px, (min-width: 700px) 220px, 56vw">
              <img src="<?php echo $a; ?>img/ecrans/communication-v2-360.webp" width="1080" height="2004" alt="Écran Communication : nouvelle communication, annonces et messages." loading="lazy" decoding="async">
            </picture></div></div></figure>
          </div>

          <div class="tabs__panel" role="tabpanel" id="panel-supporters" aria-labelledby="tab-supporters" tabindex="0" hidden>
            <div class="panel__text">
              
              <h3 class="display display--2">Suivre le club, collectionner les cartes.</h3>
              <ul class="ticks">
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Rejoindre son club avec son lien ou son QR code</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Suivre la vie du club et réagir aux résultats</li>
                <li class="ticks__soon"><svg class="icon" aria-hidden="true"><use href="#i-clock"/></svg>Bientôt&nbsp;: les histoires et la collection MY CARD</li>
              </ul>
            </div>
            <div class="panel__media panel__media--card">
              <div class="card card--or card--static" aria-label="Aperçu de la carte MY CARD, palier Or">
                <div class="card__inner">
                  <div class="card__top"><img class="card__logo" src="<?php echo $a; ?>img/logo/elazyo-fond-fonce.png" alt="" width="84" height="15"><span class="card__brand">MY CARD</span><span class="card__tier">Or</span></div>
                  <picture class="card__art"><img src="<?php echo $a; ?>img/mycard/mascotte-or.webp" width="294" height="398" alt="" loading="lazy" decoding="async"></picture>
                  <div class="card__bottom"><span class="card__season">Saison 2026-2027</span></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
