<?php
/**
 * Keeps the front end identical to the original page: WordPress normally adds
 * the admin bar, emoji script and block/global styles to every page, which
 * would shift or restyle the layout.
 *
 * @package Vangel_Naumovski
 */

add_filter( 'show_admin_bar', '__return_false' );

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
remove_action( 'wp_head', 'wp_print_auto_sizes_contain_css_fix', 1 );
add_filter( 'wp_img_tag_add_auto_sizes', '__return_false' );
add_filter( 'wp_speculation_rules_configuration', '__return_null' );

add_action(
	'wp_enqueue_scripts',
	function () {
		foreach ( array( 'wp-block-library', 'wp-block-library-theme', 'classic-theme-styles', 'global-styles' ) as $handle ) {
			wp_dequeue_style( $handle );
		}
	},
	100
);
