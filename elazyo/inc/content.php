<?php
/**
 * Contenus : FAQ (menu « FAQ » de l’administration) et pages créées à l’activation du thème.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'elazyo_register_faq' );
/**
 * Type de contenu FAQ : une question par élément, rangées par « Ordre » (attributs de la page).
 */
function elazyo_register_faq() {
	register_post_type(
		'elazyo_faq',
		array(
			'labels'          => array(
				'name'               => 'FAQ',
				'singular_name'      => 'Question',
				'menu_name'          => 'FAQ',
				'add_new'            => 'Ajouter une question',
				'add_new_item'       => 'Ajouter une question',
				'edit_item'          => 'Modifier la question',
				'new_item'           => 'Nouvelle question',
				'search_items'       => 'Rechercher une question',
				'not_found'          => 'Aucune question',
				'not_found_in_trash' => 'Aucune question dans la corbeille',
				'all_items'          => 'Toutes les questions',
			),
			'description'     => 'Questions affichées dans la section « Vos questions » de la page d’accueil. Le titre est la question, le contenu est la réponse. L’ordre se règle dans « Attributs ».',
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'show_in_rest'    => false,
			'menu_position'   => 21,
			'menu_icon'       => 'dashicons-editor-help',
			'supports'        => array( 'title', 'editor', 'page-attributes' ),
			'hierarchical'    => false,
			'capability_type' => 'page',
			'map_meta_cap'    => true,
		)
	);
}

/**
 * Questions et réponses par défaut (textes du site au 22 novembre 2026).
 * {date}, {app} et {confidentialite} sont remplacés à la création.
 *
 * @return array<int, array{question: string, answer: string}>
 */
function elazyo_faq_defaults() {
	return array(
		array(
			'question' => 'ELAZYO est-il gratuit ?',
			'answer'   => 'Oui pour les joueurs et les familles. Pour les clubs, l’appli est gratuite pendant le lancement : les Clubs Fondateurs en profitent jusqu’au {date}. MY CARD est gratuite pour chaque joueur.',
		),
		array(
			'question' => 'Sur quels appareils fonctionne ELAZYO ?',
			'answer'   => 'Sur iPhone et sur Android&nbsp;: téléchargez ELAZYO dans l’App Store ou sur Google Play.',
		),
		array(
			'question' => 'Comment passer de SportEasy à ELAZYO ?',
			'answer'   => 'Inutile de tout changer d’un coup. On commence avec 2 ou 3 équipes. Le coach envoie lui-même les liens d’activation à ses joueurs depuis l’appli, et chaque famille active son compte. Le reste du club suit quand il est prêt.',
		),
		array(
			'question' => 'Comment sont protégées les données des enfants ?',
			'answer'   => 'Chaque personne ne voit que ce qui la concerne, selon son rôle. Pour les plus jeunes, c’est un parent qui crée le compte. Une évaluation manquante ne compte jamais comme zéro, et le droit à l’image se règle personne par personne. La <a class="link" href="{confidentialite}">politique de confidentialité</a> détaille vos droits et la façon de les exercer.',
		),
		array(
			'question' => 'Un parent peut-il aussi être coach ?',
			'answer'   => 'Oui. Une même personne peut avoir plusieurs rôles, par exemple parent et coach, avec un seul compte.',
		),
		array(
			'question' => 'Et pour d’autres sports que le football ?',
			'answer'   => 'Le football d’abord. D’autres sports suivront, par étapes.',
		),
		array(
			'question' => 'Qui est derrière ELAZYO ?',
			'answer'   => 'Une équipe d’associés issus du football amateur, dont un directeur sportif de club.',
		),
	);
}

/**
 * Remplace les repères {date}, {app}, {confidentialite} d’un texte par défaut.
 *
 * @param string $text Texte.
 * @return string
 */
function elazyo_fill_placeholders( $text ) {
	return strtr(
		$text,
		array(
			'{date}'            => elazyo_mod( 'fondateurs_date' ),
			'{app}'             => esc_url( elazyo_mod( 'app_url' ) ),
			'{confidentialite}' => esc_url( elazyo_page_url( 'confidentialite' ) ),
			'{mentions}'        => esc_url( elazyo_page_url( 'mentions-legales' ) ),
			'{contact}'         => esc_url( home_url( '/#contact' ) ),
			'{accueil}'         => esc_url( home_url( '/' ) ),
		)
	);
}

/**
 * Questions à afficher : celles du menu FAQ, ou celles par défaut tant que la FAQ n’a pas été créée.
 *
 * @return array<int, array{question: string, answer: string}>
 */
function elazyo_faq_items() {
	static $items = null;
	if ( null !== $items ) {
		return $items;
	}
	if ( ! get_option( 'elazyo_faq_seeded' ) ) {
		$items = array();
		foreach ( elazyo_faq_defaults() as $item ) {
			$items[] = array(
				'question' => $item['question'],
				'answer'   => elazyo_fill_placeholders( $item['answer'] ),
			);
		}
		return $items;
	}
	$posts = get_posts(
		array(
			'post_type'      => 'elazyo_faq',
			'post_status'    => 'publish',
			'posts_per_page' => 30,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'ASC',
			),
		)
	);
	$items = array();
	foreach ( $posts as $post ) {
		$items[] = array(
			'question' => get_the_title( $post ),
			'answer'   => $post->post_content,
		);
	}
	return $items;
}

add_filter( 'use_block_editor_for_post_type', 'elazyo_faq_classic_editor', 10, 2 );
/**
 * Éditeur simple pour les réponses de la FAQ (quelques lignes, un lien au plus).
 *
 * @param bool   $use       Utiliser l’éditeur de blocs.
 * @param string $post_type Type de contenu.
 * @return bool
 */
function elazyo_faq_classic_editor( $use, $post_type ) {
	return 'elazyo_faq' === $post_type ? false : $use;
}

add_action( 'after_switch_theme', 'elazyo_install' );
/**
 * À l’activation du thème : pages légales, page de remerciement, page d’accueil et page des
 * actualités, questions de la FAQ. Rien n’est écrasé : ce qui existe déjà est conservé.
 */
function elazyo_install() {
	$dir   = get_template_directory() . '/inc/pages/';
	$pages = array(
		'accueil'          => array( 'Accueil', '' ),
		'actualites'       => array( 'Actualités', '' ),
		'mentions-legales' => array( 'Mentions légales', 'mentions-legales.html' ),
		'confidentialite'  => array( 'Confidentialité et cookies', 'confidentialite.html' ),
		'merci'            => array( 'Merci, c’est envoyé.', 'merci.html' ),
	);
	$ids   = array();
	foreach ( $pages as $slug => $page ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$ids[ $slug ] = $existing->ID;
			continue;
		}
		$content      = $page[1] && is_readable( $dir . $page[1] ) ? file_get_contents( $dir . $page[1] ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$ids[ $slug ] = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => $page[0],
				'post_name'    => $slug,
				'post_content' => $content,
			)
		);
	}
	// Les liens des pages légales pointent vers les autres pages : on les remplit une fois toutes créées.
	foreach ( array( 'mentions-legales', 'confidentialite', 'merci' ) as $slug ) {
		if ( ! empty( $ids[ $slug ] ) && ! is_wp_error( $ids[ $slug ] ) ) {
			$post = get_post( $ids[ $slug ] );
			if ( $post && false !== strpos( $post->post_content, '{' ) ) {
				wp_update_post(
					array(
						'ID'           => $post->ID,
						'post_content' => elazyo_fill_placeholders( $post->post_content ),
					)
				);
			}
		}
	}

	// Page d’accueil et page des actualités, seulement sur un site encore réglé par défaut.
	if ( 'posts' === get_option( 'show_on_front' ) && ! get_option( 'page_on_front' ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', (int) $ids['accueil'] );
		update_option( 'page_for_posts', (int) $ids['actualites'] );
	}
	if ( in_array( get_option( 'blogdescription' ), array( '', 'Un site utilisant WordPress', 'Just another WordPress site' ), true ) ) {
		update_option( 'blogdescription', 'L’appli des clubs de foot amateur' );
	}

	// Questions de la FAQ.
	if ( ! get_option( 'elazyo_faq_seeded' ) ) {
		$order = 0;
		foreach ( elazyo_faq_defaults() as $item ) {
			wp_insert_post(
				array(
					'post_type'    => 'elazyo_faq',
					'post_status'  => 'publish',
					'post_title'   => $item['question'],
					'post_content' => elazyo_fill_placeholders( $item['answer'] ),
					'menu_order'   => $order++,
				)
			);
		}
		update_option( 'elazyo_faq_seeded', 1 );
	}

	if ( function_exists( 'elazyo_schedule_cleanup' ) ) {
		elazyo_schedule_cleanup();
	}
}

add_filter( 'wp_robots', 'elazyo_robots' );
/**
 * La page de remerciement n’a rien à faire dans Google.
 *
 * @param array $robots Directives.
 * @return array
 */
function elazyo_robots( $robots ) {
	if ( is_page( 'merci' ) ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
}

add_action( 'admin_init', 'elazyo_upgrade' );
/**
 * Mises à jour des contenus créés par une version précédente du thème, une seule fois.
 * 1.0.3 : l’appli n’existe que sur iPhone et Android (plus de version web).
 */
function elazyo_upgrade() {
	// Faite dans l’administration par un compte autorisé à enregistrer du HTML, pour que
	// WordPress n’enlève rien des pages (blocs, surlignages « à compléter »).
	if ( ! current_user_can( 'unfiltered_html' ) || version_compare( (string) get_option( 'elazyo_content_version', '1.0.0' ), '1.0.3', '>=' ) ) {
		return;
	}
	$replacements = array(
		'Sur iPhone, sur Android et sur le web, à l’adresse <a class="link" href="https://app.elazyo.fr/" rel="noopener">app.elazyo.fr</a>.' => 'Sur iPhone et sur Android&nbsp;: téléchargez ELAZYO dans l’App Store ou sur Google Play.',
		'L’application ELAZYO, accessible à l’adresse <a class="link" href="https://app.elazyo.fr/" rel="noopener">app.elazyo.fr</a>, dispose de ses propres conditions d’utilisation.' => 'L’application ELAZYO, disponible sur l’App Store et sur Google Play, dispose de ses propres conditions d’utilisation.',
		'accessible depuis <a class="link" href="https://app.elazyo.fr/" rel="noopener">app.elazyo.fr</a>' => 'accessible depuis l’application et sa fiche sur l’App Store et Google Play',
	);
	$posts = get_posts(
		array(
			'post_type'      => array( 'elazyo_faq', 'page' ),
			'post_status'    => 'any',
			'posts_per_page' => 50,
			's'              => 'app.elazyo.fr',
		)
	);
	foreach ( $posts as $post ) {
		$content = str_replace( array_keys( $replacements ), array_values( $replacements ), $post->post_content );
		if ( $content !== $post->post_content ) {
			wp_update_post(
				array(
					'ID'           => $post->ID,
					'post_content' => $content,
				)
			);
		}
	}
	update_option( 'elazyo_content_version', '1.0.3' );
}
