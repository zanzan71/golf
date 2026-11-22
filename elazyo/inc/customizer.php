<?php
/**
 * Textes modifiables dans Apparence > Personnaliser > ELAZYO.
 *
 * Chaque modification se voit en direct dans l’aperçu, avant d’être publiée.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;

add_action( 'customize_register', 'elazyo_customize_register' );
/**
 * Panneau, sections et réglages.
 *
 * @param WP_Customize_Manager $wp_customize Gestionnaire.
 */
function elazyo_customize_register( $wp_customize ) {
	$d = elazyo_defaults();

	$wp_customize->add_panel(
		'elazyo',
		array(
			'title'       => 'ELAZYO',
			'description' => 'Les textes clés de la page d’accueil. Les autres textes sont dans les fichiers du thème (template-parts/home/).',
			'priority'    => 20,
		)
	);

	$sections = array(
		'elazyo_fondateurs' => array( 'Clubs Fondateurs et bandeau', 'Le nombre de places et la date de fin de gratuité sont repris dans le bandeau du haut, le billet et la liste des engagements. Pensez aussi à la question « ELAZYO est-il gratuit ? » dans FAQ.' ),
		'elazyo_accueil'    => array( 'Haut de page', 'Le grand titre s’écrit sur quatre lignes au plus. Le mot indiqué dans « Mot en couleur » est affiché en vert.' ),
		'elazyo_preuve'     => array( 'Club qui utilise ELAZYO', 'Encadré de la section Clubs Fondateurs. Le témoignage ne s’affiche que s’il est rempli. N’indiquez que des chiffres et des propos réels, avec l’accord du club.' ),
		'elazyo_liens'      => array( 'Liens et contact', 'Les demandes envoyées par le formulaire arrivent dans le menu Demandes de l’administration et par e-mail à l’adresse indiquée ici (à défaut, celle de Réglages > Général).' ),
		'elazyo_seo'        => array( 'Référencement', 'Utilisé seulement si aucune extension de référencement (Yoast SEO, Rank Math, SEOPress) n’est active. Sinon, réglez le titre et la description dans l’extension.' ),
	);
	$priority = 10;
	foreach ( $sections as $id => $section ) {
		$wp_customize->add_section(
			$id,
			array(
				'title'       => $section[0],
				'description' => $section[1],
				'panel'       => 'elazyo',
				'priority'    => $priority,
			)
		);
		$priority += 10;
	}

	$fields = array(
		// Clubs Fondateurs.
		array( 'annonce_afficher', 'elazyo_fondateurs', 'checkbox', 'Afficher le bandeau en haut de l’accueil' ),
		array( 'fondateurs_places', 'elazyo_fondateurs', 'number', 'Nombre de places' ),
		array( 'fondateurs_date', 'elazyo_fondateurs', 'text', 'Gratuit jusqu’au', 'Par exemple : 30 juin 2027' ),
		// Haut de page.
		array( 'hero_label', 'elazyo_accueil', 'text', 'Petit titre au-dessus' ),
		array( 'hero_l1', 'elazyo_accueil', 'text', 'Grand titre, ligne 1' ),
		array( 'hero_l2', 'elazyo_accueil', 'text', 'Grand titre, ligne 2' ),
		array( 'hero_l3', 'elazyo_accueil', 'text', 'Grand titre, ligne 3' ),
		array( 'hero_l4', 'elazyo_accueil', 'text', 'Grand titre, ligne 4' ),
		array( 'hero_accent', 'elazyo_accueil', 'text', 'Mot en couleur', 'Doit figurer tel quel dans le titre.' ),
		array( 'hero_lead', 'elazyo_accueil', 'textarea', 'Texte sous le titre' ),
		array( 'hero_cta', 'elazyo_accueil', 'text', 'Bouton principal' ),
		array( 'hero_cta2', 'elazyo_accueil', 'text', 'Bouton secondaire' ),
		array( 'hero_proof1', 'elazyo_accueil', 'text', 'Argument 1' ),
		array( 'hero_proof2', 'elazyo_accueil', 'text', 'Argument 2' ),
		array( 'hero_proof3', 'elazyo_accueil', 'text', 'Argument 3' ),
		// Club qui utilise ELAZYO.
		array( 'preuve_afficher', 'elazyo_preuve', 'checkbox', 'Afficher l’encadré' ),
		array( 'preuve_phrase', 'elazyo_preuve', 'text', 'Phrase d’accroche' ),
		array( 'preuve_v1', 'elazyo_preuve', 'text', 'Chiffre 1' ),
		array( 'preuve_t1', 'elazyo_preuve', 'text', 'Légende du chiffre 1' ),
		array( 'preuve_v2', 'elazyo_preuve', 'text', 'Chiffre 2' ),
		array( 'preuve_t2', 'elazyo_preuve', 'text', 'Légende du chiffre 2' ),
		array( 'preuve_v3', 'elazyo_preuve', 'text', 'Chiffre 3' ),
		array( 'preuve_t3', 'elazyo_preuve', 'text', 'Légende du chiffre 3' ),
		array( 'preuve_source', 'elazyo_preuve', 'text', 'Source des chiffres' ),
		array( 'temoignage_texte', 'elazyo_preuve', 'textarea', 'Témoignage (sans guillemets)' ),
		array( 'temoignage_auteur', 'elazyo_preuve', 'text', 'Auteur du témoignage', 'Prénom Nom, fonction, club' ),
		// Liens et contact.
		array( 'app_url', 'elazyo_liens', 'url', 'Adresse de l’appli' ),
		array( 'contact_email', 'elazyo_liens', 'email', 'E-mail qui reçoit les demandes' ),
		array( 'instagram_url', 'elazyo_liens', 'url', 'Compte Instagram d’ELAZYO', 'Adresse complète. Sert à Google pour relier le site au compte.' ),
		// Référencement.
		array( 'seo_title', 'elazyo_seo', 'text', 'Titre de la page d’accueil dans Google' ),
		array( 'seo_description', 'elazyo_seo', 'textarea', 'Description dans Google (150 caractères environ)' ),
		array( 'seo_google', 'elazyo_seo', 'text', 'Code de validation Google Search Console', 'Méthode « Balise HTML » : collez seulement le code entre content="…".' ),
		array( 'seo_bing', 'elazyo_seo', 'text', 'Code de validation Bing Webmaster Tools', 'Facultatif. Même principe que pour Google.' ),
	);

	$sanitizers = array(
		'checkbox' => 'elazyo_sanitize_checkbox',
		'number'   => 'absint',
		'text'     => 'sanitize_text_field',
		'textarea' => 'sanitize_textarea_field',
		'url'      => 'esc_url_raw',
		'email'    => 'sanitize_email',
	);

	foreach ( $fields as $field ) {
		list( $key, $section, $type, $label ) = $field;
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => isset( $d[ $key ] ) ? $d[ $key ] : '',
				'sanitize_callback' => $sanitizers[ $type ],
				'transport'         => 'refresh',
			)
		);
		$control = array(
			'label'   => $label,
			'section' => $section,
			'type'    => $type,
		);
		if ( isset( $field[4] ) ) {
			$control['description'] = $field[4];
		}
		if ( 'number' === $type ) {
			$control['input_attrs'] = array(
				'min'  => 0,
				'max'  => 99,
				'step' => 1,
			);
		}
		$wp_customize->add_control( $key, $control );
	}
}

/**
 * Case à cocher.
 *
 * @param mixed $value Valeur reçue.
 * @return bool
 */
function elazyo_sanitize_checkbox( $value ) {
	return (bool) $value;
}
