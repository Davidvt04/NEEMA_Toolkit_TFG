<?php
/**
 * AJAX: Cargar más organismos
 */
add_action('wp_ajax_cargar_mas_organismos', 'neema_cargar_mas_organismos');
add_action('wp_ajax_nopriv_cargar_mas_organismos', 'neema_cargar_mas_organismos');

function neema_cargar_mas_organismos() {
    $categoria_ids_string = sanitize_text_field($_POST['categoria_ids']);
    $categoria_ids = array_map('intval', explode(',', $categoria_ids_string));
    $offset = intval($_POST['offset']);
    
    $args = array(
        'post_type'      => 'organismo',
        'posts_per_page' => 6,
        'offset'         => $offset,
        'meta_query'     => array(
            array(
                'key'     => '_organismo_categoria',
                'value'   => $categoria_ids,
                'compare' => 'IN'
            )
        ),
        'orderby' => 'title',
        'order'   => 'ASC',
        'lang'    => ''
    );
    
    $organismos = get_posts($args);
    
    if ($organismos) {
        ob_start();
        foreach($organismos as $organismo) {
            neema_render_organismo($organismo->ID);
        }
        $html = ob_get_clean();
        $args_total = $args;
        $args_total['posts_per_page'] = -1;
        $args_total['fields'] = 'ids';
        unset($args_total['offset']);
        $total = count(get_posts($args_total));
        
        wp_send_json_success(array(
            'html' => $html,
            'has_more' => ($offset + 6) < $total
        ));
    } else {
        wp_send_json_error();
    }
}
