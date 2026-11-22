<?php
/**
 * Thème ELAZYO : chargement des modules.
 *
 * - inc/helpers.php     réglages par défaut et petites fonctions utiles aux modèles
 * - inc/setup.php       fonctions du thème, feuilles de style, scripts, nettoyage de l’en-tête
 * - inc/customizer.php  textes modifiables dans Apparence > Personnaliser > ELAZYO
 * - inc/content.php     FAQ, pages créées à l’activation, page d’accueil et page des actualités
 * - inc/contact.php     formulaire « Parlons de votre club » et liste des demandes
 * - inc/seo.php         titre, description et données structurées (si aucune extension SEO)
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;

define( 'ELAZYO_VERSION', '1.0.1' );

foreach ( array( 'helpers', 'setup', 'customizer', 'content', 'contact', 'seo' ) as $elazyo_module ) {
	require_once get_template_directory() . '/inc/' . $elazyo_module . '.php';
}
unset( $elazyo_module );
