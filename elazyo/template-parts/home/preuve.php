<?php
/**
 * Encadré « Club qui utilise ELAZYO » (section Clubs Fondateurs).
 * Réglages : Apparence > Personnaliser > ELAZYO > Club qui utilise ELAZYO.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;

if ( ! elazyo_mod( 'preuve_afficher' ) ) {
	return;
}
$elazyo_chiffres = array();
foreach ( array( 1, 2, 3 ) as $elazyo_i ) {
	$elazyo_v = trim( (string) elazyo_mod( 'preuve_v' . $elazyo_i ) );
	$elazyo_t = trim( (string) elazyo_mod( 'preuve_t' . $elazyo_i ) );
	if ( '' !== $elazyo_v && '' !== $elazyo_t ) {
		$elazyo_chiffres[] = array( $elazyo_v, $elazyo_t );
	}
}
$elazyo_citation = trim( (string) elazyo_mod( 'temoignage_texte' ) );
$elazyo_auteur   = trim( (string) elazyo_mod( 'temoignage_auteur' ) );
?>
        <figure class="preuve" data-reveal>
          <?php if ( elazyo_mod( 'preuve_phrase' ) ) : ?>
          <blockquote class="preuve__club"><?php echo esc_html( elazyo_mod( 'preuve_phrase' ) ); ?></blockquote>
          <?php endif; ?>
          <?php if ( $elazyo_chiffres ) : ?>
          <dl class="preuve__chiffres">
            <?php foreach ( $elazyo_chiffres as $elazyo_c ) : ?>
            <div><dt><?php echo esc_html( $elazyo_c[1] ); ?></dt><dd><?php echo elazyo_nbsp( esc_html( $elazyo_c[0] ) ); // Échappé. ?></dd></div>
            <?php endforeach; ?>
          </dl>
          <?php endif; ?>
          <?php if ( '' !== $elazyo_citation ) : ?>
          <blockquote class="preuve__citation">
            <p>«&nbsp;<?php echo elazyo_nbsp( esc_html( $elazyo_citation ) ); // Échappé. ?>&nbsp;»</p>
            <?php if ( '' !== $elazyo_auteur ) : ?><cite><?php echo esc_html( $elazyo_auteur ); ?></cite><?php endif; ?>
          </blockquote>
          <?php endif; ?>
          <?php if ( elazyo_mod( 'preuve_source' ) ) : ?>
          <figcaption><?php echo esc_html( elazyo_mod( 'preuve_source' ) ); ?></figcaption>
          <?php endif; ?>
        </figure>
