<?php
/**
 * AJAX: Cargar más recursos
 */
add_action('wp_ajax_cargar_mas_recursos', 'neema_cargar_mas_recursos');
add_action('wp_ajax_nopriv_cargar_mas_recursos', 'neema_cargar_mas_recursos');

function neema_cargar_mas_recursos() {
    $categoria = isset( $_POST['categoria'] ) ? sanitize_text_field( wp_unslash( $_POST['categoria'] ) ) : '';
    $modulo = isset( $_POST['modulo'] ) ? sanitize_text_field( wp_unslash( $_POST['modulo'] ) ) : '';
    $offset = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;
    $exclude_ids = isset( $_POST['exclude_ids'] ) ? $_POST['exclude_ids'] : array();
    $current_user_id = get_current_user_id();
    $user_favorite_ids = $current_user_id ? get_recursos_favoritos( $current_user_id ) : array();
    set_query_var( 'user_favorite_ids', $user_favorite_ids );
    $exclude_ids = array();
    if ( isset( $_POST['exclude_ids'] ) && ! empty( $_POST['exclude_ids'] ) ) {
        $raw = $_POST['exclude_ids'];
        $exclude_ids = array_map( 'intval', $raw );
    }

    $total = neema_num_total_recursos( $categoria, $modulo );
    $remaining = max( 0, $total - count( $exclude_ids ) );
    $num_to_load = min( 6, $remaining );

    if ( $num_to_load <= 0 ) {
        wp_send_json_success( array( 'html' => '', 'has_more' => false, 'loaded' => 0 ) );
    }

    $recursos = neema_cargar_recursos( $categoria, $modulo, $num_to_load, $exclude_ids );
    $has_more = ( count( $exclude_ids ) + count( $recursos ) ) < $total;

    if ( $recursos && is_array( $recursos ) && count( $recursos ) > 0 ) {
        ob_start();
        foreach ( $recursos as $recurso ) {
            neema_render_recurso( $recurso->ID );
        }
        $html = ob_get_clean();

        wp_send_json_success( array(
            'html' => $html,
            'has_more' => (bool) $has_more,
            'loaded' => count( $recursos ),
        ) );
    }

    wp_send_json_error();
}
