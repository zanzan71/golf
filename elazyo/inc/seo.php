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
		$description = elazyo_page_description( $post );
		if ( has_post_thumbnail( $post ) ) {
			$image = get_the_post_thumbnail_url( $post, 'large' );
		}
		$type = 'post' === $post->post_type ? 'article' : 'website';
	} elseif ( is_home() ) {
		$url         = get_permalink( (int) get_option( 'page_for_posts' ) );
		$title       = 'Actualités et conseils pour les clubs de foot amateur';
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
		$instagram = (string) elazyo_mod( 'instagram_url' );
		if ( $instagram ) {
			$graph[ count( $graph ) - 1 ]['sameAs'] = array( $instagram );
		}
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
		'url'                 => $home,
		'applicationCategory' => 'SportsApplication',
		'operatingSystem'     => 'Android, iOS',
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
	$stores = array_values( array_filter( array( (string) elazyo_mod( 'appstore_url' ), (string) elazyo_mod( 'playstore_url' ) ) ) );
	if ( $stores ) {
		foreach ( $graph as $i => $node ) {
			if ( 'SoftwareApplication' === $node['@type'] ) {
				$graph[ $i ]['downloadUrl'] = 1 === count( $stores ) ? $stores[0] : $stores;
			}
		}
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

/**
 * Description d’une page ou d’un article : l’extrait s’il existe, sinon le début du texte,
 * sans la ligne « Mise à jour : … » qui ouvre les pages légales.
 *
 * @param WP_Post $post Contenu.
 * @return string
 */
function elazyo_page_description( $post ) {
	if ( has_excerpt( $post ) ) {
		return get_the_excerpt( $post );
	}
	$text = preg_replace( '#</(p|li|h[1-6]|blockquote|div|figcaption|td|th|dd|dt)>|<br\s*/?>#i', '$0 ', strip_shortcodes( excerpt_remove_blocks( $post->post_content ) ) );
	$text = html_entity_decode( wp_strip_all_tags( $text ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$text = trim( preg_replace( '/[\s\x{00A0}\x{202F}]+/u', ' ', $text ) );
	$text = preg_replace( '/^Mise à jour ?:[^.]*\.\s*/u', '', $text );
	return wp_trim_words( $text, 28, '…' );
}

add_filter( 'document_title_parts', 'elazyo_title_parts' );
/**
 * Nom du site écrit comme la marque, et titre plus parlant pour la page des actualités.
 *
 * @param array $parts Morceaux du titre.
 * @return array
 */
function elazyo_title_parts( $parts ) {
	if ( elazyo_has_seo_plugin() ) {
		return $parts;
	}
	if ( isset( $parts['site'] ) ) {
		$parts['site'] = 'ELAZYO';
	}
	if ( is_home() && ! is_front_page() ) {
		$parts['title'] = 'Actualités et conseils pour les clubs de foot amateur';
	}
	return $parts;
}

/**
 * Y a-t-il au moins un article publié ?
 *
 * @return bool
 */
function elazyo_has_posts() {
	$count = wp_count_posts( 'post' );
	return isset( $count->publish ) && (int) $count->publish > 0;
}

add_action( 'wp_head', 'elazyo_head_seo', 1 );
/**
 * Adresse de référence de la page des actualités (WordPress ne la donne que pour les pages
 * et les articles) et balises de validation des outils pour webmasters.
 */
function elazyo_head_seo() {
	foreach ( array( 'seo_google' => 'google-site-verification', 'seo_bing' => 'msvalidate.01' ) as $key => $name ) {
		$code = trim( (string) elazyo_mod( $key ) );
		if ( preg_match( '/content=["\']([^"\']+)["\']/', $code, $m ) ) {
			$code = $m[1];
		}
		if ( $code ) {
			printf( '<meta name="%s" content="%s">' . "\n", esc_attr( $name ), esc_attr( $code ) );
		}
	}
	if ( elazyo_has_seo_plugin() || ! is_home() || is_front_page() ) {
		return;
	}
	$page = (int) get_option( 'page_for_posts' );
	if ( ! $page ) {
		return;
	}
	$url   = get_permalink( $page );
	$paged = (int) get_query_var( 'paged' );
	if ( $paged > 1 ) {
		$url = trailingslashit( $url ) . user_trailingslashit( 'page/' . $paged, 'paged' );
	}
	printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
}

add_filter( 'wp_robots', 'elazyo_robots_actualites' );
/**
 * Tant qu’aucun article n’est publié, la page des actualités est vide : Google ne doit pas l’indexer.
 *
 * @param array $robots Directives.
 * @return array
 */
function elazyo_robots_actualites( $robots ) {
	if ( is_home() && ! is_front_page() && ! elazyo_has_posts() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
}

add_filter( 'wp_sitemaps_posts_query_args', 'elazyo_sitemap_exclusions', 10, 2 );
/**
 * Le plan du site ne liste que des pages utiles : ni la page de remerciement (non indexée),
 * ni la page des actualités tant qu’elle est vide.
 *
 * @param array  $args      Arguments de la requête.
 * @param string $post_type Type de contenu.
 * @return array
 */
function elazyo_sitemap_exclusions( $args, $post_type ) {
	if ( 'page' !== $post_type ) {
		return $args;
	}
	$exclude = array();
	$merci   = get_page_by_path( 'merci' );
	if ( $merci ) {
		$exclude[] = (int) $merci->ID;
	}
	if ( ! elazyo_has_posts() && get_option( 'page_for_posts' ) ) {
		$exclude[] = (int) get_option( 'page_for_posts' );
	}
	if ( $exclude ) {
		$args['post__not_in'] = array_merge( isset( $args['post__not_in'] ) ? (array) $args['post__not_in'] : array(), $exclude );
	}
	return $args;
}

add_action( 'wp_head', 'elazyo_article_schema', 21 );
/**
 * Données structurées des articles : l’article lui-même et son fil d’Ariane.
 */
function elazyo_article_schema() {
	if ( elazyo_has_seo_plugin() || ! is_singular( 'post' ) ) {
		return;
	}
	$post  = get_queried_object();
	$home  = home_url( '/' );
	$url   = get_permalink( $post );
	$blog  = (int) get_option( 'page_for_posts' );
	$image = has_post_thumbnail( $post ) ? get_the_post_thumbnail_url( $post, 'large' ) : elazyo_asset( 'img/og-elazyo.jpg' );
	$graph = array(
		array(
			'@type'            => 'BlogPosting',
			'@id'              => $url . '#article',
			'headline'         => wp_strip_all_tags( get_the_title( $post ) ),
			'description'      => elazyo_page_description( $post ),
			'image'            => array( $image ),
			'datePublished'    => get_the_date( 'c', $post ),
			'dateModified'     => get_the_modified_date( 'c', $post ),
			'inLanguage'       => 'fr-FR',
			'mainEntityOfPage' => $url,
			'author'           => array(
				'@type' => 'Person',
				'name'  => get_the_author_meta( 'display_name', (int) $post->post_author ),
			),
			'publisher'        => array(
				'@type' => 'Organization',
				'name'  => 'ELAZYO',
				'url'   => $home,
				'logo'  => elazyo_asset( 'icons/icon-512.png' ),
			),
		),
	);
	$crumbs = array(
		array( 'ELAZYO', $home ),
	);
	if ( $blog ) {
		$crumbs[] = array( 'Actualités', get_permalink( $blog ) );
	}
	$crumbs[] = array( wp_strip_all_tags( get_the_title( $post ) ), $url );
	$items    = array();
	foreach ( $crumbs as $i => $crumb ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $crumb[0],
			'item'     => $crumb[1],
		);
	}
	$graph[] = array(
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	);
	$json = wp_json_encode(
		array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		),
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	);
	echo '<script type="application/ld+json">' . str_replace( '</', '<\/', $json ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON encodé, « </ » neutralisé.
}
