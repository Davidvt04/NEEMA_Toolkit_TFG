<?php
/**
 * Media Button
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

// priority is 12 since default button is 10.
add_action( 'media_buttons', 'pdfjs_media_button', 12 );

/**
 * Include the media button
 */
function pdfjs_media_button() {
	if ( ! current_user_can( 'upload_files' ) ) {
		return;
	}
	echo '<a href="#" class="button js-insert-pdfjs" aria-label="' . esc_attr__( 'Add PDF to content', 'pdfjs-viewer-shortcode' ) . '">' . esc_html__( 'Add PDF', 'pdfjs-viewer-shortcode' ) . '</a>';
}

add_action( 'wp_enqueue_media', 'include_pdfjs_media_button_js_file' );

/**
 * Include the media button JS button in the classic editor.
 */
function include_pdfjs_media_button_js_file() {

	// Use cached options to avoid repeated DB queries.
	$pdfjs_array = pdfjs_get_options();

	if ( function_exists( 'use_block_editor_for_post' ) ) {
		if ( use_block_editor_for_post( get_post() ) !== 1 ) {
			wp_enqueue_script( 'media_button', plugin_dir_url( __DIR__ ) . 'pdfjs-media-button.js', array( 'jquery' ), PDFJS_PLUGIN_VERSION, true );
			wp_localize_script( 'media_button', 'pdfjs_options', $pdfjs_array );
		}
	} else {
		wp_enqueue_script( 'media_button', plugin_dir_url( __DIR__ ) . 'pdfjs-media-button.js', array( 'jquery' ), PDFJS_PLUGIN_VERSION, true );
		wp_localize_script( 'media_button', 'pdfjs_options', $pdfjs_array );
	}
}
