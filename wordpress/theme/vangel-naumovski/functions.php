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

/**
 * Contact form: each message is saved as a private "Message" that editors
 * read under Messages in the dashboard (they cannot be created there by hand).
 */
add_action(
	'init',
	function () {
		register_post_type(
			'vn_message',
			array(
				'labels'          => array(
					'name'          => 'Messages',
					'singular_name' => 'Message',
				),
				'public'          => false,
				'show_ui'         => true,
				'menu_icon'       => 'dashicons-email',
				'supports'        => array( 'title', 'editor', 'custom-fields' ),
				'capability_type' => 'post',
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'    => true,
			)
		);
	}
);

function vn_contact_submit() {
	if ( ! check_ajax_referer( 'vn_contact', 'nonce', false ) ) {
		wp_send_json_error( array( 'code' => 'nonce' ), 403 );
	}
	// hidden field that only bots fill in: answer as if it worked, store nothing
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success();
	}
	$name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$subject = sanitize_key( wp_unslash( $_POST['subject'] ?? 'general' ) );
	$message = sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) );
	$lang    = sanitize_key( wp_unslash( $_POST['lang'] ?? '' ) );
	if ( '' === $name || ! is_email( $email ) || mb_strlen( $message ) < 10 || empty( $_POST['consent'] ) ) {
		wp_send_json_error( array( 'code' => 'invalid' ), 422 );
	}
	$id = wp_insert_post(
		array(
			'post_type'    => 'vn_message',
			'post_status'  => 'private',
			'post_title'   => mb_substr( $name . ' (' . $subject . ')', 0, 200 ),
			'post_content' => mb_substr( $message, 0, 5000 ),
			'meta_input'   => array(
				'email'   => $email,
				'subject' => $subject,
				'lang'    => $lang,
			),
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		wp_send_json_error( array( 'code' => 'save' ), 500 );
	}
	wp_send_json_success();
}
add_action( 'wp_ajax_vn_contact', 'vn_contact_submit' );
add_action( 'wp_ajax_nopriv_vn_contact', 'vn_contact_submit' );
