<?php
/**
 * Fonctions du thème, feuilles de style, scripts et nettoyage de l’en-tête.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'elazyo_setup' );
/**
 * Déclare ce que le thème prend en charge.
 */
function elazyo_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );
	add_image_size( 'elazyo-carte', 720, 405, true );
}

add_action( 'wp_enqueue_scripts', 'elazyo_assets' );
/**
 * Polices, feuilles de style et script du site.
 */
function elazyo_assets() {
	wp_enqueue_style(
		'elazyo-polices',
		'https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Saira:ital,wght@1,700;1,800&display=swap',
		array(),
		null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google Fonts gère ses versions.
	);
	wp_enqueue_style( 'elazyo', elazyo_asset( 'css/site.css' ), array( 'elazyo-polices' ), ELAZYO_VERSION );

	if ( ! is_front_page() ) {
		wp_enqueue_style( 'elazyo-pages', elazyo_asset( 'css/pages.css' ), array( 'elazyo' ), ELAZYO_VERSION );
	}

	wp_enqueue_script(
		'elazyo',
		elazyo_asset( 'js/site.js' ),
		array(),
		ELAZYO_VERSION,
		array(
			'strategy'  => 'defer',
			'in_footer' => false,
		)
	);

	// La page d’accueil n’utilise pas les blocs : on n’y charge pas leurs styles.
	if ( is_front_page() ) {
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
	}
}

add_filter( 'wp_resource_hints', 'elazyo_resource_hints', 10, 2 );
/**
 * Connexion anticipée à Google Fonts.
 *
 * @param array  $urls          Adresses.
 * @param string $relation_type Type de lien.
 * @return array
 */
function elazyo_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}

add_action( 'wp_head', 'elazyo_head_early', 0 );
/**
 * Avant les feuilles de style : la classe « js » (les animations n’attendent que si le script
 * est là, et se désactivent au bout de 3 s s’il ne s’exécute pas), la couleur du navigateur
 * et l’icône du site si aucune n’a été choisie dans Réglages > Général.
 */
function elazyo_head_early() {
	echo '<script>document.documentElement.classList.add("js");setTimeout(function(){if(!window.__elazyo)document.documentElement.classList.remove("js")},3000);</script>' . "\n";
	echo '<meta name="theme-color" content="#09131D">' . "\n";
	if ( ! has_site_icon() ) {
		printf( '<link rel="icon" href="%s" type="image/png" sizes="64x64">' . "\n", esc_url( elazyo_asset( 'icons/favicon-64.png' ) ) );
		printf( '<link rel="apple-touch-icon" href="%s">' . "\n", esc_url( elazyo_asset( 'icons/apple-touch-icon.png' ) ) );
	}
}

// Moins de code inutile dans l’en-tête.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );

// Pas de commentaires sur le site : ils ne sont pas affichés et attirent les robots.
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 10 );
add_action( 'admin_menu', 'elazyo_hide_comments_menu' );
/**
 * Retire le menu Commentaires de l’administration.
 */
function elazyo_hide_comments_menu() {
	remove_menu_page( 'edit-comments.php' );
}

add_filter( 'excerpt_length', 'elazyo_excerpt_length' );
/**
 * Extraits courts dans la liste des actualités.
 *
 * @return int
 */
function elazyo_excerpt_length() {
	return 26;
}

add_filter( 'excerpt_more', 'elazyo_excerpt_more' );
/**
 * Fin des extraits.
 *
 * @return string
 */
function elazyo_excerpt_more() {
	return '…';
}
