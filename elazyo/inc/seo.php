<?php
/**
 * Référencement : titre, description, partage sur les réseaux et données structurées.
 *
 * Si une extension SEO (Yoast SEO, Rank Math, SEOPress, All in One SEO) est active, elle gère le
 * titre, la description et les balises de partage : le thème n’ajoute alors que les données
 * propres à ELAZYO (l’appli et la FAQ), sur la page d’accueil.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;

/**
 * Une extension de référencement est-elle active ?
 *
 * @return bool
 */
function elazyo_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

add_filter( 'pre_get_document_title', 'elazyo_front_title', 20 );
/**
 * Titre de la page d’accueil.
 *
 * @param string $title Titre.
 * @return string
 */
function elazyo_front_title( $title ) {
	if ( is_front_page() && ! elazyo_has_seo_plugin() ) {
		return (string) elazyo_mod( 'seo_title' );
	}
	return $title;
}

add_filter( 'document_title_separator', 'elazyo_title_separator' );
/**
 * Séparateur du titre des autres pages.
 *
 * @return string
 */
function elazyo_title_separator() {
	return '|';
}

add_action( 'wp_head', 'elazyo_meta', 2 );
/**
 * Description et balises de partage, sans extension SEO.
 */
function elazyo_meta() {
	if ( elazyo_has_seo_plugin() || is_404() ) {
		return;
	}
	$url         = '';
	$title       = '';
	$description = '';
	$image       = elazyo_asset( 'img/og-elazyo.jpg' );
	$type        = 'website';

	if ( is_front_page() ) {
		$url         = home_url( '/' );
		$title       = (string) elazyo_mod( 'seo_title' );
		$description = (string) elazyo_mod( 'seo_description' );
	} elseif ( is_singular() ) {
		$post        = get_queried_object();
		$url         = get_permalink( $post );
		$title       = get_the_title( $post );
		$description = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 28, '…' );
		if ( has_post_thumbnail( $post ) ) {
			$image = get_the_post_thumbnail_url( $post, 'large' );
		}
		$type = 'post' === $post->post_type ? 'article' : 'website';
	} elseif ( is_home() ) {
		$url         = get_permalink( (int) get_option( 'page_for_posts' ) );
		$title       = 'Actualités ELAZYO';
		$description = 'Conseils, nouveautés de l’appli et vie des clubs de foot amateur.';
	} else {
		return;
	}

	$description = trim( preg_replace( '/\s+/', ' ', $description ) );
	if ( $description ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	}
	$og = array(
		'og:type'      => $type,
		'og:site_name' => 'ELAZYO',
		'og:locale'    => 'fr_FR',
		'og:url'       => $url,
		'og:title'     => $title,
		'og:description' => $description,
		'og:image'     => $image,
	);
	foreach ( $og as $property => $content ) {
		if ( $content ) {
			printf( '<meta property="%s" content="%s">' . "\n", esc_attr( $property ), 'og:url' === $property || 'og:image' === $property ? esc_url( $content ) : esc_attr( $content ) );
		}
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
}

add_action( 'wp_head', 'elazyo_schema', 20 );
/**
 * Données structurées de la page d’accueil : l’organisation et le site (sans extension SEO),
 * l’appli et la FAQ (toujours).
 */
function elazyo_schema() {
	if ( ! is_front_page() ) {
		return;
	}
	$home  = home_url( '/' );
	$graph = array();
	if ( ! elazyo_has_seo_plugin() ) {
		$graph[] = array(
			'@type'  => 'Organization',
			'@id'    => $home . '#organisation',
			'name'   => 'ELAZYO',
			'url'    => $home,
			'logo'   => elazyo_asset( 'icons/icon-512.png' ),
			'slogan' => 'L’élan du collectif',
		);
		$graph[] = array(
			'@type'      => 'WebSite',
			'@id'        => $home . '#site',
			'url'        => $home,
			'name'       => 'ELAZYO',
			'inLanguage' => 'fr-FR',
			'publisher'  => array( '@id' => $home . '#organisation' ),
		);
	}
	$graph[] = array(
		'@type'               => 'SoftwareApplication',
		'name'                => 'ELAZYO',
		'url'                 => elazyo_mod( 'app_url' ),
		'applicationCategory' => 'SportsApplication',
		'operatingSystem'     => 'Web, Android, iOS',
		'description'         => 'L’appli des clubs de foot amateur : planning, convocations, présences, covoiturage, bénévoles, communication et finances du club.',
		'offers'              => array(
			'@type'         => 'Offer',
			'price'         => '0',
			'priceCurrency' => 'EUR',
			'description'   => 'Gratuit pour les joueurs et les familles',
		),
	);
	$questions = array();
	foreach ( elazyo_faq_items() as $item ) {
		$questions[] = array(
			'@type'          => 'Question',
			'name'           => wp_strip_all_tags( $item['question'] ),
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $item['answer'] ) ) ),
			),
		);
	}
	if ( $questions ) {
		$graph[] = array(
			'@type'      => 'FAQPage',
			'mainEntity' => $questions,
		);
	}
	$json = wp_json_encode(
		array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		),
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	);
	echo '<script type="application/ld+json">' . str_replace( '</', '<\/', $json ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON encodé, « </ » neutralisé.
}
