<?php
/**
 * Plugin Name: Fix Media Library Delete Error
 * Description: Fixes the "Error al borrar el adjunto" message when deleting media files from the library
 * Version: 1.0.0
 * Author: Custom Fix
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue custom JavaScript to fix the media deletion error
 */
function fix_media_delete_error_script() {
	// Only load on media library pages
	$screen = get_current_screen();
	if ( ! $screen || ( $screen->id !== 'upload' && $screen->base !== 'post' ) ) {
		return;
	}
	
	wp_enqueue_script(
		'fix-media-delete-error',
		plugins_url( 'fix-media-delete-error.js', __FILE__ ),
		array( 'media-views' ),
		'1.0.0',
		true
	);
}
add_action( 'admin_enqueue_scripts', 'fix_media_delete_error_script', 100 );
