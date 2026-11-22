<?php
/**
 * Banc d’essai : imitation minimale de WordPress pour afficher le thème ELAZYO sans installer
 * WordPress (pas d’accès réseau ici). Sert uniquement aux tests, n’est pas livré avec le thème.
 */

define( 'ABSPATH', __DIR__ . '/' );
define( 'MINUTE_IN_SECONDS', 60 );
define( 'HOUR_IN_SECONDS', 3600 );
define( 'BANC_URL', 'http://127.0.0.1:8090' );
define( 'BANC_THEME', realpath( __DIR__ . '/../elazyo' ) );
define( 'BANC_STATE', '/tmp/banc-wp-state.json' );

$GLOBALS['banc_hooks']   = array();
$GLOBALS['banc_styles']  = array();
$GLOBALS['banc_scripts'] = array();
$GLOBALS['banc_mods']    = array();
$GLOBALS['banc_view']    = array( 'front' => true );
$GLOBALS['banc_posts']   = array();
$GLOBALS['banc_i']       = -1;

function banc_state() {
	return is_file( BANC_STATE ) ? json_decode( file_get_contents( BANC_STATE ), true ) : array( 'posts' => array(), 'mails' => array(), 'transients' => array() );
}
function banc_save( $s ) {
	file_put_contents( BANC_STATE, json_encode( $s, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );
}

// Crochets.
function add_action( $hook, $cb, $prio = 10, $args = 1 ) {
	$GLOBALS['banc_hooks'][ $hook ][ $prio ][] = array( $cb, $args );
	return true;
}
function add_filter( $hook, $cb, $prio = 10, $args = 1 ) {
	return add_action( $hook, $cb, $prio, $args );
}
function remove_action() { return true; }
function do_action( $hook, ...$a ) {
	if ( empty( $GLOBALS['banc_hooks'][ $hook ] ) ) {
		return;
	}
	ksort( $GLOBALS['banc_hooks'][ $hook ] );
	foreach ( $GLOBALS['banc_hooks'][ $hook ] as $cbs ) {
		foreach ( $cbs as $cb ) {
			call_user_func_array( $cb[0], array_slice( $a, 0, $cb[1] ) );
		}
	}
}
function apply_filters( $hook, $value, ...$a ) {
	if ( empty( $GLOBALS['banc_hooks'][ $hook ] ) ) {
		return $value;
	}
	ksort( $GLOBALS['banc_hooks'][ $hook ] );
	foreach ( $GLOBALS['banc_hooks'][ $hook ] as $cbs ) {
		foreach ( $cbs as $cb ) {
			$value = call_user_func_array( $cb[0], array_slice( array_merge( array( $value ), $a ), 0, max( 1, $cb[1] ) ) );
		}
	}
	return $value;
}
function __return_false() { return false; }
function __return_empty_array() { return array(); }

// Thème.
function add_theme_support() {}
function add_editor_style() {}
function add_image_size() {}
function register_post_type() {}
function get_template_directory() { return BANC_THEME; }
function get_theme_file_uri( $p = '' ) { return BANC_URL . '/wp-content/themes/elazyo/' . ltrim( $p, '/' ); }
function get_theme_mod( $k, $d = false ) { return array_key_exists( $k, $GLOBALS['banc_mods'] ) ? $GLOBALS['banc_mods'][ $k ] : $d; }
function trailingslashit( $s ) { return rtrim( $s, '/\\' ) . '/'; }
function wp_enqueue_style( $h, $src = '', $deps = array(), $ver = false ) { $GLOBALS['banc_styles'][ $h ] = $src . ( $ver ? '?ver=' . $ver : '' ); }
function wp_dequeue_style( $h ) { unset( $GLOBALS['banc_styles'][ $h ] ); }
function wp_enqueue_script( $h, $src = '', $deps = array(), $ver = false, $args = array() ) { $GLOBALS['banc_scripts'][ $h ] = $src . ( $ver ? '?ver=' . $ver : '' ); }
function has_site_icon() { return false; }
function get_template_part( $slug ) {
	$f = BANC_THEME . '/' . $slug . '.php';
	if ( is_file( $f ) ) {
		require $f;
	}
}
function get_header() { require BANC_THEME . '/header.php'; }
function get_footer() { require BANC_THEME . '/footer.php'; }
function language_attributes() { echo 'lang="fr-FR"'; }
function bloginfo( $k ) { echo 'charset' === $k ? 'UTF-8' : 'ELAZYO'; }
function body_class( $c = '' ) { echo 'class="' . esc_attr( trim( 'banc ' . $c ) ) . '"'; }
function wp_body_open() {}
function wp_head() {
	do_action( 'wp_enqueue_scripts' );
	$title = apply_filters( 'pre_get_document_title', '' );
	if ( '' === $title ) {
		$title = ( isset( $GLOBALS['banc_view']['title'] ) ? $GLOBALS['banc_view']['title'] . ' | ' : '' ) . 'ELAZYO';
	}
	echo '<title>' . esc_html( $title ) . "</title>\n";
	$hooks = isset( $GLOBALS['banc_hooks']['wp_head'] ) ? $GLOBALS['banc_hooks']['wp_head'] : array();
	ksort( $hooks );
	$printed = false;
	foreach ( $hooks as $prio => $cbs ) {
		if ( $prio >= 8 && ! $printed ) {
			banc_print_assets();
			$printed = true;
		}
		foreach ( $cbs as $cb ) {
			call_user_func( $cb[0] );
		}
	}
	if ( ! $printed ) {
		banc_print_assets();
	}
}
function banc_print_assets() {
	foreach ( apply_filters( 'wp_resource_hints', array(), 'preconnect' ) as $u ) {
		$href = is_array( $u ) ? $u['href'] : $u;
		echo '<link rel="preconnect" href="' . esc_url( $href ) . '"' . ( is_array( $u ) ? ' crossorigin' : '' ) . ">\n";
	}
	foreach ( $GLOBALS['banc_styles'] as $h => $src ) {
		echo '<link rel="stylesheet" id="' . esc_attr( $h ) . '-css" href="' . esc_url( $src ) . "\">\n";
	}
	foreach ( $GLOBALS['banc_scripts'] as $h => $src ) {
		echo '<script src="' . esc_url( $src ) . '" id="' . esc_attr( $h ) . "-js\" defer></script>\n";
	}
}
function wp_footer() { do_action( 'wp_footer' ); }

// Échappement et nettoyage (versions simplifiées).
function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8', false ); }
function esc_attr( $s ) { return esc_html( $s ); }
function esc_url( $s ) { return str_replace( array( '"', "'", ' ' ), array( '%22', '%27', '%20' ), (string) $s ) === '' ? '' : htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8', false ); }
function esc_url_raw( $s ) { return (string) $s; }
function wp_kses_post( $s ) { return (string) $s; }
function wpautop( $s ) { $s = trim( (string) $s ); return '' === $s ? '' : '<p>' . preg_replace( '/\n\s*\n/', "</p>\n<p>", $s ) . "</p>\n"; }
function wp_strip_all_tags( $s ) { return trim( strip_tags( (string) $s ) ); }
function wp_unslash( $v ) { return $v; }
function sanitize_key( $s ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $s ) ); }
function sanitize_text_field( $s ) { return trim( preg_replace( '/[\r\n\t ]+/', ' ', strip_tags( (string) $s ) ) ); }
function sanitize_textarea_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function sanitize_email( $s ) { return preg_replace( '/[^a-zA-Z0-9.@_+\-]/', '', (string) $s ); }
function is_email( $s ) { return (bool) filter_var( $s, FILTER_VALIDATE_EMAIL ); }
function wp_json_encode( $d, $f = 0 ) { return json_encode( $d, $f ); }
function wp_parse_url( $u, $c = -1 ) { return parse_url( $u, $c ); }
function wp_salt() { return 'banc'; }
function wp_date( $f ) { return date( $f ); }
function strip_shortcodes( $s ) { return $s; }
function wp_trim_words( $s, $n = 55, $more = '…' ) { $w = preg_split( '/\s+/', trim( $s ) ); return count( $w ) > $n ? implode( ' ', array_slice( $w, 0, $n ) ) . $more : implode( ' ', $w ); }

// Adresses et requêtes.
function home_url( $p = '' ) { return BANC_URL . ( $p ? $p : '/' ); }
function admin_url( $p = '' ) { return BANC_URL . '/wp-admin/' . $p; }
function add_query_arg( $k, $v, $u ) { return $u . ( false === strpos( $u, '?' ) ? '?' : '&' ) . rawurlencode( $k ) . '=' . rawurlencode( $v ); }
function get_page_by_path( $slug ) { return in_array( $slug, array( 'mentions-legales', 'confidentialite', 'merci', 'actualites' ), true ) ? (object) array( 'ID' => $slug, 'post_name' => $slug ) : null; }
function get_permalink( $p = null ) {
	if ( is_object( $p ) && isset( $p->post_name ) ) {
		return BANC_URL . '/' . $p->post_name . '/';
	}
	if ( 99 === $p ) {
		return BANC_URL . '/actualites/';
	}
	if ( is_string( $p ) ) {
		return BANC_URL . '/' . $p . '/';
	}
	$post = banc_current();
	return $post ? BANC_URL . '/' . $post['slug'] . '/' : BANC_URL . '/';
}
function wp_count_posts() { return (object) array( 'publish' => ! empty( $GLOBALS['banc_view']['news'] ) ? 3 : 0 ); }
function get_option( $k, $d = false ) {
	$o = array( 'page_for_posts' => 99, 'admin_email' => 'admin@exemple.fr', 'elazyo_faq_seeded' => 0 );
	return array_key_exists( $k, $o ) ? $o[ $k ] : $d;
}
function is_front_page() { return ! empty( $GLOBALS['banc_view']['front'] ); }
function is_home() { return ! empty( $GLOBALS['banc_view']['home'] ); }
function is_singular( $t = '' ) { return ! empty( $GLOBALS['banc_view']['singular'] ) && ( '' === $t || $t === $GLOBALS['banc_view']['singular'] ); }
function is_page( $s = '' ) { return is_singular( 'page' ) && ( '' === $s || $s === ( banc_current()['slug'] ?? '' ) ); }
function is_archive() { return false; }
function is_search() { return false; }
function is_404() { return ! empty( $GLOBALS['banc_view']['404'] ); }
function get_posts() { return array(); }
function get_queried_object() { $p = banc_current(); return (object) array( 'post_type' => $GLOBALS['banc_view']['singular'], 'post_content' => $p['content'], 'post_name' => $p['slug'] ); }
function has_excerpt() { return false; }

// Boucle.
function banc_current() { $i = $GLOBALS['banc_i']; return $i >= 0 && isset( $GLOBALS['banc_posts'][ $i ] ) ? $GLOBALS['banc_posts'][ $i ] : ( $GLOBALS['banc_posts'][0] ?? null ); }
function have_posts() { return $GLOBALS['banc_i'] + 1 < count( $GLOBALS['banc_posts'] ); }
function the_post() { $GLOBALS['banc_i']++; }
function the_title() { echo esc_html( banc_current()['title'] ); }
function get_the_title( $p = null ) { return banc_current()['title']; }
function the_permalink() { echo esc_url( get_permalink() ); }
function the_content() { echo banc_current()['content']; }
function get_the_date( $f = '' ) { return 'c' === $f ? '2026-11-20T10:00:00+01:00' : '20 novembre 2026'; }
function has_post_thumbnail() { return ! empty( banc_current()['thumb'] ); }
function the_post_thumbnail() { echo '<img src="' . esc_url( banc_current()['thumb'] ) . '" width="720" height="405" alt="">'; }
function get_the_post_thumbnail_url() { return banc_current()['thumb']; }
function get_the_excerpt() { return wp_trim_words( strip_tags( banc_current()['content'] ), 26 ); }
function the_posts_pagination() { echo '<nav class="navigation pagination"><div class="nav-links"><span class="page-numbers current">1</span><a class="page-numbers" href="#">2</a><a class="next page-numbers" href="#">Articles plus anciens</a></div></nav>'; }
function get_the_archive_title() { return ''; }
function get_the_archive_description() { return ''; }
function get_search_query() { return ''; }

// Enregistrement, e-mail, transients, réponses (formulaire).
function wp_insert_post( $a ) { $s = banc_state(); $a['ID'] = count( $s['posts'] ) + 1; $s['posts'][] = $a; banc_save( $s ); return $a['ID']; }
function is_wp_error( $x ) { return false; }
function update_post_meta( $id, $k, $v ) { $s = banc_state(); $s['posts'][ $id - 1 ]['meta_input'][ $k ] = $v; banc_save( $s ); }
function wp_mail( $to, $subject, $body, $headers ) { $s = banc_state(); $s['mails'][] = compact( 'to', 'subject', 'body', 'headers' ); banc_save( $s ); return true; }
function get_transient( $k ) { $s = banc_state(); return $s['transients'][ $k ] ?? false; }
function set_transient( $k, $v ) { $s = banc_state(); $s['transients'][ $k ] = $v; banc_save( $s ); }
function wp_next_scheduled() { return true; }
function wp_schedule_event() {}
function wp_send_json( $body, $status = 200 ) { http_response_code( $status ); header( 'Content-Type: application/json; charset=UTF-8' ); echo json_encode( $body, JSON_UNESCAPED_UNICODE ); exit; }
function wp_safe_redirect( $u, $s = 302 ) { header( 'Location: ' . $u, true, $s ); }
