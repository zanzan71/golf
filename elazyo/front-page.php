<?php
/**
 * Page d’accueil.
 *
 * Chaque section est dans template-parts/home/. Pour en retirer une, supprimez sa ligne ici ;
 * pour en déplacer une, déplacez sa ligne.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="contenu">
<?php
foreach ( array( 'hero', 'manifeste', 'maillons', 'application', 'profils', 'mycard', 'communaute', 'vision', 'fondateurs', 'faq', 'contact' ) as $elazyo_section ) {
	get_template_part( 'template-parts/home/' . $elazyo_section );
}
?>
</main>
<?php
get_footer();
