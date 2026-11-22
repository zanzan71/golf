<?php
/**
 * Début de chaque page.
 *
 * @package ELAZYO
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( elazyo_mod( 'annonce_afficher' ) ? '' : 'sans-bandeau' ); ?>>
<?php wp_body_open(); ?>
<?php get_template_part( 'template-parts/sprite' ); ?>

<a class="skip-link" href="#contenu">Aller au contenu</a>

<?php get_template_part( is_front_page() ? 'template-parts/header-home' : 'template-parts/header-simple' ); ?>
