<?php
/**
 * Routeur du banc d’essai : php -S 127.0.0.1:8090 banc/routeur.php (depuis elazyo-wp/).
 *
 * /                         page d’accueil (front-page.php)
 * /?sans-bandeau=1&temoignage=1&news=1   variantes de réglages
 * /actualites/              liste des actualités (index.php)
 * /un-article/              article (single.php)
 * /mentions-legales/, /confidentialite/, /merci/   pages (page.php)
 * /introuvable/             page 404
 * /wp-admin/admin-post.php  formulaire de contact
 * /wp-content/themes/elazyo/...   fichiers du thème
 */

$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );

if ( 0 === strpos( $path, '/wp-content/themes/elazyo/' ) ) {
	$file = realpath( __DIR__ . '/../elazyo/' . substr( $path, strlen( '/wp-content/themes/elazyo/' ) ) );
	if ( ! $file || 0 !== strpos( $file, realpath( __DIR__ . '/../elazyo' ) ) || ! is_file( $file ) ) {
		http_response_code( 404 );
		return true;
	}
	$types = array( 'css' => 'text/css', 'js' => 'text/javascript', 'webp' => 'image/webp', 'png' => 'image/png', 'jpg' => 'image/jpeg' );
	header( 'Content-Type: ' . ( $types[ pathinfo( $file, PATHINFO_EXTENSION ) ] ?? 'application/octet-stream' ) );
	readfile( $file );
	return true;
}

require __DIR__ . '/wp-stub.php';
require BANC_THEME . '/functions.php';
do_action( 'after_setup_theme' );
do_action( 'init' );

if ( ! empty( $_GET['sans-bandeau'] ) ) {
	$GLOBALS['banc_mods']['annonce_afficher'] = false;
}
if ( ! empty( $_GET['temoignage'] ) ) {
	$GLOBALS['banc_mods']['temoignage_texte']  = 'Depuis ELAZYO, on ne court plus après les réponses le vendredi soir : tout le monde confirme dans l’appli.';
	$GLOBALS['banc_mods']['temoignage_auteur'] = 'Prénom Nom, directeur sportif, F.C. Epagny Metz-Tessy';
}
$GLOBALS['banc_view']['news'] = ! empty( $_GET['news'] ) || '/' !== $path;

if ( '/wp-admin/admin-post.php' === $path ) {
	do_action( 'admin_post_nopriv_' . ( $_REQUEST['action'] ?? '' ) );
	return true;
}

$article = '<!-- wp:paragraph --><p>Un exemple d’article pour vérifier la mise en page : <strong>texte en gras</strong>, <a href="#">un lien</a>, puis une liste.</p><!-- /wp:paragraph -->'
	. '<h2 class="wp-block-heading">Un intertitre</h2><ul><li>Premier point</li><li>Deuxième point</li></ul>'
	. '<blockquote class="wp-block-quote"><p>Une citation pour voir le style.</p><cite>Quelqu’un du club</cite></blockquote>'
	. '<p>Dernier paragraphe, assez long pour voir la largeur de lecture et l’interligne sur ordinateur comme sur téléphone, avec une phrase de plus.</p>';
$thumb   = BANC_URL . '/wp-content/themes/elazyo/assets/img/photos/entrainement-1040.webp';
$pages   = array( 'mentions-legales' => 'Mentions légales', 'confidentialite' => 'Confidentialité et cookies', 'merci' => 'Merci, c’est envoyé.' );

if ( '/' === $path ) {
	require BANC_THEME . '/front-page.php';
} elseif ( '/actualites/' === $path ) {
	$GLOBALS['banc_view'] = array( 'home' => true, 'news' => true, 'title' => 'Actualités' );
	$GLOBALS['banc_posts'] = array(
		array( 'slug' => 'un-article', 'title' => 'Convocations : 5 conseils pour obtenir toutes les réponses avant vendredi', 'content' => $article, 'thumb' => $thumb ),
		array( 'slug' => 'un-article', 'title' => 'MY CARD : comment la carte du joueur se remplit', 'content' => $article, 'thumb' => '' ),
		array( 'slug' => 'un-article', 'title' => 'Les tips à Lolo : préparer une séance U9 en 20 minutes', 'content' => $article, 'thumb' => $thumb ),
	);
	require BANC_THEME . '/index.php';
} elseif ( '/un-article/' === $path ) {
	$GLOBALS['banc_view'] = array( 'singular' => 'post', 'news' => true, 'title' => 'Convocations' );
	$GLOBALS['banc_posts'] = array( array( 'slug' => 'un-article', 'title' => 'Convocations : 5 conseils pour obtenir toutes les réponses avant vendredi', 'content' => $article, 'thumb' => $thumb ) );
	require BANC_THEME . '/single.php';
} elseif ( isset( $pages[ trim( $path, '/' ) ] ) ) {
	$slug = trim( $path, '/' );
	$GLOBALS['banc_view'] = array( 'singular' => 'page', 'news' => true, 'title' => $pages[ $slug ] );
	$GLOBALS['banc_posts'] = array( array( 'slug' => $slug, 'title' => $pages[ $slug ], 'content' => elazyo_fill_placeholders( file_get_contents( BANC_THEME . '/inc/pages/' . $slug . '.html' ) ), 'thumb' => '' ) );
	require BANC_THEME . '/page.php';
} else {
	http_response_code( 404 );
	$GLOBALS['banc_view'] = array( '404' => true, 'news' => true, 'title' => 'Page introuvable' );
	require BANC_THEME . '/404.php';
}
return true;
