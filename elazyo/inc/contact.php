<?php
/**
 * Formulaire « Parlons de votre club ».
 *
 * Le formulaire est envoyé à admin-post.php?action=elazyo_contact :
 * - par le script du site, en JSON (réponse JSON, message affiché sous le bouton) ;
 * - sans JavaScript, en formulaire classique (redirection vers la page « Merci »).
 *
 * Chaque demande est enregistrée dans le menu « Demandes » de l’administration, puis envoyée par
 * e-mail. Si l’e-mail ne part pas (hébergeur sans serveur d’envoi), la demande reste visible
 * dans l’administration : rien n’est perdu.
 *
 * Protections : champ piège, délai minimal de saisie, contrôle de l’origine, 5 envois au plus
 * par adresse IP et par tranche de 10 minutes, validation complète côté serveur.
 * Les demandes sont supprimées automatiquement 3 ans après leur réception
 * (durée annoncée dans la politique de confidentialité).
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;

/**
 * Profils proposés dans le formulaire.
 *
 * @return array<string, string>
 */
function elazyo_contact_profils() {
	return array(
		'president'  => 'Président ou dirigeant',
		'coach'      => 'Coach ou éducateur',
		'tresorerie' => 'Secrétaire ou trésorier',
		'parent'     => 'Parent',
		'benevole'   => 'Bénévole',
		'partenaire' => 'Partenaire, association ou collectivité',
		'autre'      => 'Autre',
	);
}

/**
 * Objets proposés dans le formulaire.
 *
 * @return array<string, string>
 */
function elazyo_contact_objets() {
	return array(
		'club-fondateur' => 'Devenir Club Fondateur',
		'demo'           => 'Demander une démo',
		'question'       => 'Poser une question',
		'partenariat'    => 'Proposer un partenariat',
	);
}

/**
 * Message affiché sous le formulaire après un envoi sans JavaScript.
 *
 * @param string $etat Code reçu dans l’adresse (?demande=…).
 * @return string
 */
function elazyo_contact_message( $etat ) {
	$messages = array(
		'erreur' => 'Votre demande n’a pas pu être envoyée : vérifiez que tous les champs sont remplis, puis réessayez.',
		'limite' => 'Trop de demandes envoyées depuis votre connexion. Réessayez dans quelques minutes.',
	);
	return isset( $messages[ $etat ] ) ? $messages[ $etat ] : '';
}

add_action( 'init', 'elazyo_register_demandes' );
/**
 * Type de contenu « Demandes » : visible seulement dans l’administration, non créable à la main.
 */
function elazyo_register_demandes() {
	register_post_type(
		'elazyo_demande',
		array(
			'labels'          => array(
				'name'          => 'Demandes',
				'singular_name' => 'Demande',
				'menu_name'     => 'Demandes',
				'all_items'     => 'Toutes les demandes',
				'edit_item'     => 'Demande',
				'search_items'  => 'Rechercher une demande',
				'not_found'     => 'Aucune demande pour l’instant',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'show_in_rest'    => false,
			'menu_position'   => 22,
			'menu_icon'       => 'dashicons-email-alt',
			'supports'        => array( 'title' ),
			'capability_type' => 'page',
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);
}

add_action( 'admin_post_nopriv_elazyo_contact', 'elazyo_handle_contact' );
add_action( 'admin_post_elazyo_contact', 'elazyo_handle_contact' );
/**
 * Reçoit une demande.
 */
function elazyo_handle_contact() {
	$content_type = isset( $_SERVER['CONTENT_TYPE'] ) ? strtolower( sanitize_text_field( wp_unslash( $_SERVER['CONTENT_TYPE'] ) ) ) : '';
	$accept       = isset( $_SERVER['HTTP_ACCEPT'] ) ? strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT'] ) ) ) : '';
	$is_json      = false !== strpos( $content_type, 'application/json' );
	$wants_json   = $is_json || false !== strpos( $accept, 'application/json' );

	if ( 'POST' !== ( isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : '' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		elazyo_contact_respond( $wants_json, 405, array( 'error' => 'method_not_allowed' ), 'erreur' );
	}

	// Origine : les envois depuis un autre site sont refusés.
	if ( ! empty( $_SERVER['HTTP_ORIGIN'] ) ) {
		$origin = wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['HTTP_ORIGIN'] ) ), PHP_URL_HOST );
		$home   = wp_parse_url( home_url(), PHP_URL_HOST );
		if ( $origin && $home && strtolower( $origin ) !== strtolower( $home ) ) {
			elazyo_contact_respond( $wants_json, 403, array( 'error' => 'forbidden' ), 'erreur' );
		}
	}

	if ( $is_json ) {
		$raw   = file_get_contents( 'php://input', false, null, 0, 10000 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$input = json_decode( (string) $raw, true );
	} else {
		$input = wp_unslash( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- formulaire public, protégé par champ piège, délai et limite de débit.
	}
	if ( ! is_array( $input ) ) {
		elazyo_contact_respond( $wants_json, 400, array( 'error' => 'bad_request' ), 'erreur' );
	}

	$result = elazyo_contact_validate( $input );

	// Robot : on fait comme si tout s’était bien passé, sans rien enregistrer.
	if ( ! empty( $result['spam'] ) ) {
		elazyo_contact_respond( $wants_json, 200, array( 'ok' => true ), 'ok' );
	}
	if ( ! empty( $result['errors'] ) ) {
		elazyo_contact_respond( $wants_json, 422, array( 'errors' => $result['errors'] ), 'erreur' );
	}

	if ( ! elazyo_contact_rate_ok() ) {
		elazyo_contact_respond( $wants_json, 429, array( 'error' => 'rate_limited' ), 'limite' );
	}

	$data     = $result['data'];
	$profils  = elazyo_contact_profils();
	$objets   = elazyo_contact_objets();
	$post_id  = wp_insert_post(
		array(
			'post_type'   => 'elazyo_demande',
			'post_status' => 'private',
			'post_title'  => $objets[ $data['objet'] ] . ' — ' . $data['nom'],
			'meta_input'  => array(
				'_elazyo_nom'     => $data['nom'],
				'_elazyo_email'   => $data['email'],
				'_elazyo_profil'  => $profils[ $data['profil'] ],
				'_elazyo_club'    => $data['club'],
				'_elazyo_objet'   => $objets[ $data['objet'] ],
				'_elazyo_message' => $data['message'],
			),
		),
		true
	);
	if ( is_wp_error( $post_id ) ) {
		elazyo_contact_respond( $wants_json, 500, array( 'error' => 'server_error' ), 'erreur' );
	}

	elazyo_contact_mail( $data, $profils, $objets, (int) $post_id );

	elazyo_contact_respond( $wants_json, 200, array( 'ok' => true ), 'ok' );
}

/**
 * Valide et nettoie une demande (mêmes règles que le formulaire côté navigateur).
 *
 * @param array $input Données reçues.
 * @return array{data?: array, errors?: array, spam?: bool}
 */
function elazyo_contact_validate( $input ) {
	$get = function ( $key, $max ) use ( $input ) {
		$value = isset( $input[ $key ] ) && is_scalar( $input[ $key ] ) ? (string) $input[ $key ] : '';
		$value = preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value );
		$value = str_replace( array( "\r\n", "\r" ), "\n", (string) $value );
		return mb_substr( trim( $value ), 0, $max );
	};

	if ( '' !== $get( 'site_web', 200 ) ) {
		return array( 'spam' => true );
	}
	// Formulaire rempli en moins de 2,5 s : robot. (Une horloge en avance chez la personne donne
	// un écart négatif : on l’accepte.)
	$t = isset( $input['t'] ) && is_numeric( $input['t'] ) ? (float) $input['t'] : 0;
	if ( $t > 0 ) {
		$elapsed = microtime( true ) * 1000 - $t;
		if ( $elapsed >= 0 && $elapsed < 2500 ) {
			return array( 'spam' => true );
		}
	}

	$consent = isset( $input['consentement'] ) ? $input['consentement'] : false;
	$data    = array(
		'nom'          => sanitize_text_field( $get( 'nom', 80 ) ),
		'email'        => strtolower( sanitize_email( $get( 'email', 254 ) ) ),
		'profil'       => $get( 'profil', 20 ),
		'club'         => sanitize_text_field( $get( 'club', 120 ) ),
		'objet'        => $get( 'objet', 20 ),
		'message'      => sanitize_textarea_field( $get( 'message', 2000 ) ),
		'consentement' => true === $consent || in_array( $consent, array( 'oui', 'on', 'true', '1', 1 ), true ),
	);

	$errors = array();
	if ( mb_strlen( $data['nom'] ) < 2 ) {
		$errors['nom'] = 'Indiquez votre nom et prénom.';
	}
	if ( ! is_email( $data['email'] ) ) {
		$errors['email'] = 'Cette adresse e-mail n’est pas valide.';
	}
	if ( ! array_key_exists( $data['profil'], elazyo_contact_profils() ) ) {
		$errors['profil'] = 'Choisissez votre profil.';
	}
	if ( ! array_key_exists( $data['objet'], elazyo_contact_objets() ) ) {
		$errors['objet'] = 'Choisissez l’objet de votre demande.';
	}
	if ( mb_strlen( $data['message'] ) < 10 ) {
		$errors['message'] = 'Votre message doit compter 10 caractères au moins.';
	}
	if ( ! $data['consentement'] ) {
		$errors['consentement'] = 'Cochez la case pour que nous puissions vous répondre.';
	}

	return $errors ? array( 'errors' => $errors ) : array( 'data' => $data );
}

/**
 * 5 demandes au plus par adresse IP et par tranche de 10 minutes.
 *
 * @return bool
 */
function elazyo_contact_rate_ok() {
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'inconnue';
	$key = 'elazyo_rl_' . md5( $ip . wp_salt( 'nonce' ) );
	$n   = (int) get_transient( $key );
	if ( $n >= 5 ) {
		return false;
	}
	set_transient( $key, $n + 1, 10 * MINUTE_IN_SECONDS );
	return true;
}

/**
 * Envoie la demande par e-mail. Un échec n’empêche pas l’enregistrement.
 *
 * @param array $data    Demande validée.
 * @param array $profils Libellés des profils.
 * @param array $objets  Libellés des objets.
 * @param int   $post_id Demande enregistrée.
 */
function elazyo_contact_mail( $data, $profils, $objets, $post_id ) {
	$to = elazyo_mod( 'contact_email' );
	if ( ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}
	$subject = sprintf( '[Site ELAZYO] Contact — %s — %s', $objets[ $data['objet'] ], $data['nom'] );
	$lines   = array(
		'Nouvelle demande reçue sur le site.',
		'',
		'Nom : ' . $data['nom'],
		'E-mail : ' . $data['email'],
		'Profil : ' . $profils[ $data['profil'] ],
		'Club : ' . ( '' !== $data['club'] ? $data['club'] : '(non indiqué)' ),
		'Objet : ' . $objets[ $data['objet'] ],
		'',
		'Message :',
		$data['message'],
		'',
		'—',
		'Répondez directement à cet e-mail pour écrire à la personne.',
		'Demande enregistrée dans l’administration : ' . admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
	);
	$reply   = str_replace( array( "\n", "\r", '<', '>', '"' ), '', $data['nom'] );
	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $reply . ' <' . $data['email'] . '>',
	);
	$sent    = wp_mail( $to, $subject, implode( "\n", $lines ), $headers );
	update_post_meta( $post_id, '_elazyo_mail', $sent ? 'envoyé' : 'échec' );
}

/**
 * Répond au navigateur et s’arrête.
 *
 * @param bool   $json     Réponse JSON (envoi par le script) ou redirection (envoi classique).
 * @param int    $status   Code HTTP.
 * @param array  $body     Corps JSON.
 * @param string $etat     Code pour la redirection : ok, erreur ou limite.
 */
function elazyo_contact_respond( $json, $status, $body, $etat ) {
	if ( $json ) {
		wp_send_json( $body, $status );
	}
	$url = 'ok' === $etat ? elazyo_page_url( 'merci' ) : add_query_arg( 'demande', $etat, home_url( '/' ) ) . '#contact';
	wp_safe_redirect( $url, 303 );
	exit;
}

// ---------------------------------------------------------------------------
// Administration : colonnes de la liste et détail d’une demande.
// ---------------------------------------------------------------------------

add_filter( 'manage_elazyo_demande_posts_columns', 'elazyo_demande_columns' );
/**
 * Colonnes de la liste des demandes.
 *
 * @param array $columns Colonnes.
 * @return array
 */
function elazyo_demande_columns( $columns ) {
	return array(
		'cb'            => $columns['cb'],
		'title'         => 'Demande',
		'elazyo_email'  => 'E-mail',
		'elazyo_profil' => 'Profil',
		'elazyo_club'   => 'Club',
		'elazyo_mail'   => 'E-mail envoyé',
		'date'          => 'Reçue le',
	);
}

add_action( 'manage_elazyo_demande_posts_custom_column', 'elazyo_demande_column', 10, 2 );
/**
 * Contenu des colonnes.
 *
 * @param string $column  Colonne.
 * @param int    $post_id Demande.
 */
function elazyo_demande_column( $column, $post_id ) {
	$map = array(
		'elazyo_email'  => '_elazyo_email',
		'elazyo_profil' => '_elazyo_profil',
		'elazyo_club'   => '_elazyo_club',
		'elazyo_mail'   => '_elazyo_mail',
	);
	if ( ! isset( $map[ $column ] ) ) {
		return;
	}
	$value = (string) get_post_meta( $post_id, $map[ $column ], true );
	if ( 'elazyo_email' === $column && $value ) {
		printf( '<a href="mailto:%1$s">%2$s</a>', esc_attr( $value ), esc_html( $value ) );
		return;
	}
	echo esc_html( '' !== $value ? $value : '—' );
}

add_action( 'add_meta_boxes_elazyo_demande', 'elazyo_demande_metabox' );
/**
 * Détail d’une demande.
 */
function elazyo_demande_metabox() {
	add_meta_box( 'elazyo_demande_detail', 'Détail de la demande', 'elazyo_demande_detail', 'elazyo_demande', 'normal', 'high' );
}

/**
 * Affiche le détail.
 *
 * @param WP_Post $post Demande.
 */
function elazyo_demande_detail( $post ) {
	$fields = array(
		'_elazyo_nom'    => 'Nom',
		'_elazyo_email'  => 'E-mail',
		'_elazyo_profil' => 'Profil',
		'_elazyo_club'   => 'Club',
		'_elazyo_objet'  => 'Objet',
		'_elazyo_mail'   => 'E-mail de notification',
	);
	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( $fields as $key => $label ) {
		$value = (string) get_post_meta( $post->ID, $key, true );
		if ( '_elazyo_email' === $key && $value ) {
			$value_html = sprintf( '<a href="mailto:%1$s">%2$s</a>', esc_attr( $value ), esc_html( $value ) );
		} else {
			$value_html = esc_html( '' !== $value ? $value : '—' );
		}
		printf( '<tr><th scope="row">%s</th><td>%s</td></tr>', esc_html( $label ), $value_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus.
	}
	echo '</tbody></table>';
	echo '<h3>Message</h3><div style="white-space:pre-wrap;max-width:72ch;font-size:14px;line-height:1.6">' . esc_html( (string) get_post_meta( $post->ID, '_elazyo_message', true ) ) . '</div>';
	echo '<p class="description">Les demandes sont supprimées automatiquement 3 ans après leur réception, comme l’annonce la politique de confidentialité.</p>';
}

// ---------------------------------------------------------------------------
// Suppression automatique après 3 ans.
// ---------------------------------------------------------------------------

/**
 * Programme le nettoyage quotidien.
 */
function elazyo_schedule_cleanup() {
	if ( ! wp_next_scheduled( 'elazyo_cleanup_demandes' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'elazyo_cleanup_demandes' );
	}
}
add_action( 'init', 'elazyo_schedule_cleanup' );

add_action( 'elazyo_cleanup_demandes', 'elazyo_cleanup_demandes' );
/**
 * Supprime définitivement les demandes reçues il y a plus de 3 ans.
 */
function elazyo_cleanup_demandes() {
	$old = get_posts(
		array(
			'post_type'      => 'elazyo_demande',
			'post_status'    => 'any',
			'posts_per_page' => 200,
			'fields'         => 'ids',
			'date_query'     => array( array( 'before' => '3 years ago' ) ),
		)
	);
	foreach ( $old as $id ) {
		wp_delete_post( $id, true );
	}
}

add_action( 'switch_theme', 'elazyo_unschedule_cleanup' );
/**
 * Arrête le nettoyage si le thème est désactivé.
 */
function elazyo_unschedule_cleanup() {
	wp_clear_scheduled_hook( 'elazyo_cleanup_demandes' );
}
