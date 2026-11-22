<?php
/**
 * Page d’accueil : section « contact ».
 *
 * Généré depuis la page statique par convertir.py, puis complété à la main si besoin.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;
$a = elazyo_asset_base();
?>
  <section class="contact theme-night" id="contact" aria-labelledby="contact-titre">
    <div class="container contact__grid">
      <div class="contact__intro">
        <h2 class="display display--1" id="contact-titre" data-reveal="lines">
          <span class="line"><span>Parlons de</span></span>
          <span class="line"><span>votre club.</span></span>
        </h2>
        <p class="lead" data-reveal>Une démo, une candidature Club Fondateur, une question ou un partenariat&nbsp;: écrivez-nous, nous vous répondons par e-mail.</p>
        <div class="contact__app" data-reveal>
          <p class="contact__app-title">Envie d’essayer tout de suite&#8239;?</p>
          <p>L’appli est en ligne, gratuite pour les joueurs, sur iPhone, Android et le web.</p>
          <a class="btn btn--ghost btn--md" href="<?php echo esc_url( elazyo_mod( 'app_url' ) ); ?>" rel="noopener">Ouvrir app.elazyo.fr<svg class="icon" aria-hidden="true"><use href="#i-external"/></svg></a>
        </div>
        <figure class="photo-cadre contact__photo" data-reveal="mask">
          <picture>
            
            <source type="image/webp" srcset="<?php echo $a; ?>img/photos/messages-600.webp 600w, <?php echo $a; ?>img/photos/messages-1100.webp 1100w" sizes="(min-width: 1000px) 460px, 92vw">
            <img src="<?php echo $a; ?>img/photos/messages-1100.webp" width="1112" height="941" alt="" loading="lazy" decoding="async">
          </picture>
          <span class="photo-ia" aria-hidden="true">Image générée par IA</span>
        </figure>
      </div>

      <form class="form" action="<?php echo esc_url( admin_url( 'admin-post.php?action=elazyo_contact' ) ); ?>" method="post" novalidate data-contact-form>
        <input type="hidden" name="action" value="elazyo_contact">
        <div class="form__row form__row--2">
          <div class="field">
            <label for="f-nom">Nom et prénom</label>
            <input id="f-nom" name="nom" type="text" autocomplete="name" required minlength="2" maxlength="80" aria-describedby="f-nom-err">
            <p class="field__error" id="f-nom-err" data-error-for="nom"></p>
          </div>
          <div class="field">
            <label for="f-email">Adresse e-mail</label>
            <input id="f-email" name="email" type="email" autocomplete="email" inputmode="email" required maxlength="254" aria-describedby="f-email-err">
            <p class="field__error" id="f-email-err" data-error-for="email"></p>
          </div>
        </div>
        <div class="form__row form__row--2">
          <div class="field">
            <label for="f-profil">Vous êtes</label>
            <div class="select">
              <select id="f-profil" name="profil" required aria-describedby="f-profil-err">
                <option value="">Choisissez</option>
                <option value="president">Président ou dirigeant</option>
                <option value="coach">Coach ou éducateur</option>
                <option value="tresorerie">Secrétaire ou trésorier</option>
                <option value="parent">Parent</option>
                <option value="benevole">Bénévole</option>
                <option value="partenaire">Partenaire, association ou collectivité</option>
                <option value="autre">Autre</option>
              </select>
              <svg class="icon" aria-hidden="true"><use href="#i-chevron-down"/></svg>
            </div>
            <p class="field__error" id="f-profil-err" data-error-for="profil"></p>
          </div>
          <div class="field">
            <label for="f-club">Votre club <span class="field__optional">facultatif</span></label>
            <input id="f-club" name="club" type="text" autocomplete="organization" maxlength="120">
          </div>
        </div>
        <div class="field">
          <label for="f-objet">Objet</label>
          <div class="select">
            <select id="f-objet" name="objet" required aria-describedby="f-objet-err">
              <option value="club-fondateur">Devenir Club Fondateur</option>
              <option value="demo">Demander une démo</option>
              <option value="question" selected>Poser une question</option>
              <option value="partenariat">Proposer un partenariat</option>
            </select>
            <svg class="icon" aria-hidden="true"><use href="#i-chevron-down"/></svg>
          </div>
          <p class="field__error" id="f-objet-err" data-error-for="objet"></p>
        </div>
        <div class="field">
          <label for="f-message">Votre message</label>
          <textarea id="f-message" name="message" rows="5" required minlength="10" maxlength="2000" aria-describedby="f-message-hint f-message-err"></textarea>
          <p class="field__hint" id="f-message-hint"><span data-count>0</span>&nbsp;/ 2000 caractères</p>
          <p class="field__error" id="f-message-err" data-error-for="message"></p>
        </div>
        <div class="field field--hp" aria-hidden="true">
          <label for="f-site">Laissez ce champ vide</label>
          <input id="f-site" name="site_web" type="text" tabindex="-1" autocomplete="off">
        </div>
        <input type="hidden" name="t" value="" data-form-time>
        <div class="field field--check">
          <input id="f-consent" name="consentement" type="checkbox" value="oui" required aria-describedby="f-consent-err">
          <label for="f-consent">J’accepte qu’ELAZYO utilise ces informations pour répondre à ma demande. <a class="link" href="<?php echo esc_url( elazyo_page_url( 'confidentialite' ) ); ?>">Politique de confidentialité</a></label>
          <p class="field__error" id="f-consent-err" data-error-for="consentement"></p>
        </div>
        <div class="form__submit">
          <button class="btn btn--elan btn--lg" type="submit" data-submit><span data-submit-label>Envoyer ma demande</span><svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></button>
          <?php $etat = isset( $_GET['demande'] ) ? sanitize_key( wp_unslash( $_GET['demande'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification ?>
          <p class="form__status<?php echo $etat ? ' is-error' : ''; ?>" role="status" aria-live="polite" data-form-status><?php echo esc_html( elazyo_contact_message( $etat ) ); ?></p>
        </div>
      </form>
    </div>
  </section>
