<?php
/**
 * Réglages par défaut et fonctions utiles aux modèles.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;

/**
 * Valeurs par défaut de tous les réglages modifiables (Apparence > Personnaliser > ELAZYO).
 * Ce sont les textes du site au 22 novembre 2026.
 *
 * @return array<string, mixed>
 */
function elazyo_defaults() {
	return array(
		// Liens et contact.
		'app_url'            => 'https://app.elazyo.fr/',
		'contact_email'      => '',
		// Bandeau et offre Clubs Fondateurs.
		'annonce_afficher'   => true,
		'fondateurs_places'  => 10,
		'fondateurs_date'    => '30 juin 2027',
		// Haut de page.
		'hero_label'         => 'L’appli de gestion des clubs de foot amateur',
		'hero_l1'            => 'Le sport nous',
		'hero_l2'            => 'rassemble.',
		'hero_l3'            => 'ELAZYO nous',
		'hero_l4'            => 'fait grandir.',
		'hero_accent'        => 'grandir.',
		'hero_lead'          => 'Convocations, présences, covoiturage, licences : ELAZYO simplifie la vie des coachs et des présidents, et transforme chaque saison en aventure pour les joueurs.',
		'hero_cta'           => 'Essayer gratuitement',
		'hero_cta2'          => 'Voir l’appli en action',
		'hero_proof1'        => 'Gratuit pour les joueurs',
		'hero_proof2'        => 'Web, Android et iPhone',
		'hero_proof3'        => 'Conçu par des gens du foot amateur',
		// Club qui utilise ELAZYO.
		'preuve_afficher'    => true,
		'preuve_phrase'      => 'Déjà sur le terrain au F.C. Epagny Metz-Tessy, en Haute-Savoie.',
		'preuve_v1'          => '68',
		'preuve_t1'          => 'membres actifs',
		'preuve_v2'          => '5',
		'preuve_t2'          => 'équipes',
		'preuve_v3'          => '92 %',
		'preuve_t3'          => 'de réponses à une convocation U15',
		'preuve_source'      => 'Chiffres relevés dans l’appli du club, novembre 2026.',
		'temoignage_texte'   => '',
		'temoignage_auteur'  => '',
		// Référencement, utilisé seulement sans extension SEO (Yoast, Rank Math, SEOPress).
		'seo_title'          => 'ELAZYO : l’appli de gestion des clubs de foot amateur',
		'seo_description'    => 'Gérer son club de foot amateur sans tableur ni groupes WhatsApp : convocations, présences, covoiturage, bénévoles, cotisations. Gratuit pour les joueurs.',
		'seo_google'         => '',
		'seo_bing'           => '',
		'instagram_url'      => '',
	);
}

/**
 * Lit un réglage, avec sa valeur par défaut.
 *
 * @param string $key Nom du réglage.
 * @return mixed
 */
function elazyo_mod( $key ) {
	$defaults = elazyo_defaults();
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * Adresse du dossier assets du thème, échappée, avec la barre finale.
 *
 * @return string
 */
function elazyo_asset_base() {
	return esc_url( trailingslashit( get_theme_file_uri( 'assets' ) ) );
}

/**
 * Adresse d’un fichier du dossier assets (non échappée).
 *
 * @param string $path Chemin dans assets/.
 * @return string
 */
function elazyo_asset( $path ) {
	return get_theme_file_uri( 'assets/' . ltrim( $path, '/' ) );
}

/**
 * Lien vers une section de la page d’accueil : « #id » sur l’accueil, adresse complète ailleurs.
 *
 * @param string $id Identifiant de la section.
 * @return string
 */
function elazyo_anchor( $id ) {
	return is_front_page() ? '#' . $id : home_url( '/#' . $id );
}

/**
 * Adresse d’une page par son identifiant (slug), même si elle a été supprimée.
 *
 * @param string $slug Identifiant de la page.
 * @return string
 */
function elazyo_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
}

/**
 * Adresse de la page des actualités, ou chaîne vide s’il n’y a encore aucun article publié.
 *
 * @return string
 */
function elazyo_news_url() {
	static $url = null;
	if ( null !== $url ) {
		return $url;
	}
	$url    = '';
	$counts = wp_count_posts( 'post' );
	$page   = (int) get_option( 'page_for_posts' );
	if ( $counts && ! empty( $counts->publish ) && $page ) {
		$url = get_permalink( $page );
	}
	return $url;
}

/**
 * Lignes du grand titre d’accueil, échappées. Le mot mis en couleur est entouré d’un <em>.
 *
 * @return string[]
 */
function elazyo_hero_lines() {
	$lines = array();
	foreach ( array( 'hero_l1', 'hero_l2', 'hero_l3', 'hero_l4' ) as $key ) {
		$text = trim( (string) elazyo_mod( $key ) );
		if ( '' !== $text ) {
			$lines[] = esc_html( $text );
		}
	}
	$accent = trim( (string) elazyo_mod( 'hero_accent' ) );
	if ( '' !== $accent ) {
		$needle = esc_html( $accent );
		// On colore la dernière occurrence : c’est là que la phrase se termine.
		for ( $i = count( $lines ) - 1; $i >= 0; $i-- ) {
			$pos = strrpos( $lines[ $i ], $needle );
			if ( false !== $pos ) {
				$lines[ $i ] = substr_replace( $lines[ $i ], '<em class="accent">' . $needle . '</em>', $pos, strlen( $needle ) );
				break;
			}
		}
	}
	return $lines;
}

/**
 * Texte du bandeau Clubs Fondateurs.
 *
 * @param bool $short Version courte pour les téléphones.
 * @return string HTML sûr.
 */
function elazyo_announce_text( $short = false ) {
	$places = (int) elazyo_mod( 'fondateurs_places' );
	$text   = '<strong>Clubs Fondateurs</strong>&nbsp;: ' . esc_html( $places ) . ' places';
	if ( ! $short ) {
		$text .= ', ELAZYO gratuit pour tout le club jusqu’au ' . esc_html( elazyo_mod( 'fondateurs_date' ) ) . '.';
	}
	return $text;
}

/**
 * Espaces insécables de la typographie française : fine avant « ; ? ! », normale avant « : % ».
 *
 * @param string $text Texte déjà échappé.
 * @return string
 */
function elazyo_nbsp( $text ) {
	$text = preg_replace( '/ ([;?!])/u', "\u{202F}$1", $text );
	return preg_replace( '/ ([%:»])/u', "\u{00A0}$1", $text );
}
