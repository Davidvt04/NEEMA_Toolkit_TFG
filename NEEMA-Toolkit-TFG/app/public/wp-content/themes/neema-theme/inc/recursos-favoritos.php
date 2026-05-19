<?php
/**
 * Recursos Favoritos - Funciones relacionadas con los recursos favoritos de los usuarios
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

function crear_tabla_favoritos() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'recursos_favoritos_usuario';
	$charset_collate = $wpdb->get_charset_collate();

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$sql = "CREATE TABLE $table_name (
	  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	  user_id BIGINT(20) UNSIGNED NOT NULL,
	  recurso_id BIGINT(20) UNSIGNED NOT NULL,
	  favorited_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	  PRIMARY KEY  (id),
	  UNIQUE KEY user_recurso (user_id, recurso_id),
	  KEY user_idx (user_id),
	  KEY recurso_idx (recurso_id)
	) $charset_collate;";

	dbDelta( $sql );
}

add_action( 'after_switch_theme', 'crear_tabla_favoritos' );
add_action( 'init', 'neema_verificar_tabla_favoritos' );

function neema_verificar_tabla_favoritos() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'recursos_favoritos_usuario';
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
		crear_tabla_favoritos();
	}
}

function add_recurso_favorito($user_id, $recurso_id) {
    global $wpdb;

    $table = $wpdb->prefix . 'recursos_favoritos_usuario';

    if ( is_recurso_favorito( $user_id, $recurso_id ) ) {
        return false;
    }

    $result = $wpdb->insert(
        $table,
        [
            'user_id' => $user_id,
            'recurso_id' => $recurso_id,
        ],
        ['%d', '%d']
    );

    neema_update_preferencia( $user_id, $recurso_id, true );

    if (function_exists('neema_clear_recursos_cache')) {
        neema_clear_recursos_cache($user_id);
    }

    return ( $result !== false );
}

function remove_recurso_favorito($user_id, $recurso_id) {
    global $wpdb;

    $table = $wpdb->prefix . 'recursos_favoritos_usuario';

    $rows_deleted = $wpdb->delete(
        $table,
        [
            'user_id' => $user_id,
            'recurso_id' => $recurso_id,
        ],
        ['%d', '%d']
    );
    neema_update_preferencia( $user_id, $recurso_id, false );
    if (function_exists('neema_clear_recursos_cache')) {
        neema_clear_recursos_cache($user_id);
    }

    return ( $rows_deleted !== false && $rows_deleted > 0 );
}

function toggle_recurso_favorito($user_id, $recurso_id) {
    if ( is_recurso_favorito( $user_id, $recurso_id ) ) {
        return remove_recurso_favorito( $user_id, $recurso_id );
    } else {
        return add_recurso_favorito( $user_id, $recurso_id );
    }
}

function get_recursos_favoritos($user_id) {
    global $wpdb;

    $table = $wpdb->prefix . 'recursos_favoritos_usuario';

    $results = $wpdb->get_col( $wpdb->prepare(
        "SELECT recurso_id FROM $table WHERE user_id = %d ORDER BY favorited_at DESC",
        $user_id
    ) );

    return $results;
}

function count_recursos_favoritos_usuario($user_id) {
    global $wpdb;

    $table = $wpdb->prefix . 'recursos_favoritos_usuario';

    $count = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE user_id = %d",
        $user_id
    ) );

    return $count;
}

function is_recurso_favorito($user_id, $recurso_id) {
    global $wpdb;

    $table = $wpdb->prefix . 'recursos_favoritos_usuario';

    $result = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE user_id = %d AND recurso_id = %d",
        $user_id,
        $recurso_id
    ) );

    return $result > 0;
}

function count_favoritos_recurso($recurso_id) {
    global $wpdb;

    $table = $wpdb->prefix . 'recursos_favoritos_usuario';

    $count = $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE recurso_id = %d",
        $recurso_id
    ) );

    return $count;
}

add_action( 'wp_ajax_neema_toggle_favorito', 'neema_ajax_toggle_favorito' );

function neema_ajax_toggle_favorito() {
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'neema_favoritos_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Nonce inválido' ) );
    }

    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        wp_send_json_error( array( 'message' => 'Debes iniciar sesión' ) );
    }

    $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
    if ( ! $post_id ) {
        wp_send_json_error( array( 'message' => 'ID de recurso inválido' ) );
    }

    $success = toggle_recurso_favorito( $user_id, $post_id );
    
    if ( $success ) {
        $is_favorited = is_recurso_favorito( $user_id, $post_id );
        wp_send_json_success( array(
            'favorited' => $is_favorited,
            'message'   => $is_favorited ? 'Recurso añadido a favoritos' : 'Recurso eliminado de favoritos'
        ) );
    } else {
        wp_send_json_error( array( 'message' => 'Error al actualizar favorito' ) );
    }
}
add_action( 'wp_enqueue_scripts', 'neema_enqueue_favoritos_script' );

function neema_enqueue_favoritos_script() {
    if ( is_singular( 'modulo' ) || is_singular( 'recurso' ) || is_post_type_archive( 'recurso' ) || is_page_template( 'page-ran.php' ) || is_page_template( 'page-mis-favoritos.php' ) ) {
        wp_enqueue_script(
            'neema-favoritos',
            get_template_directory_uri() . '/js/favoritos.js',
            array( 'jquery' ),
            '1.0.0',
            true
        );

        wp_localize_script( 'neema-favoritos', 'neemaFavoritos', array(
            'ajax_url'   => admin_url( 'admin-ajax.php' ),
            'nonce'      => wp_create_nonce( 'neema_favoritos_nonce' ),
            'is_logged'  => is_user_logged_in(),
            'text_guardar'  => neema_translate('Guardar'),
            'text_guardado' => neema_translate('Guardado')
        ) );
    }
}

function get_link_guardados() {
    $current_lang = neema_get_current_lang();
    if( $current_lang === 'es' ) {
        return home_url( '/guardados/' );
    } else {
        return home_url( '/' . $current_lang . '/guardados-' . $current_lang . '/' );
    }
}
add_action( 'before_delete_post', 'neema_limpiar_favoritos_al_eliminar_recurso' );

function neema_limpiar_favoritos_al_eliminar_recurso( $post_id ) {
    $post = get_post( $post_id );
    if ( ! $post || $post->post_type !== 'recurso' ) {
        return;
    }
    global $wpdb;
    $table = $wpdb->prefix . 'recursos_favoritos_usuario';
    $wpdb->delete(
        $table,
        array( 'recurso_id' => $post_id ),
        array( '%d' )
    );
}
add_action( 'delete_user', 'neema_limpiar_favoritos_al_eliminar_usuario' );

function neema_limpiar_favoritos_al_eliminar_usuario( $user_id ) {
    global $wpdb;
    $table = $wpdb->prefix . 'recursos_favoritos_usuario';
    $wpdb->delete(
        $table,
        array( 'user_id' => $user_id ),
        array( '%d' )
    );
}
