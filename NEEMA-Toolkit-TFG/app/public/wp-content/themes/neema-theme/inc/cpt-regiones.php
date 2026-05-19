<?php
/* ==========================================================
    Custom Post Type: Regiones
   ========================================================== */
function neema_register_regiones_cpt() {
    $labels = array(
        'name'               => 'Regiones',
        'singular_name'      => 'Region',
        'add_new'            => 'Añadir nueva',
        'add_new_item'       => 'Añadir nueva región',
        'edit_item'          => 'Editar región',
        'new_item'           => 'Nueva región',
        'view_item'          => 'Ver región',
        'search_items'       => 'Buscar regiones',
        'not_found'          => 'No se encontraron regiones',
        'not_found_in_trash' => 'No hay regiones en la papelera',
        'menu_name'          => 'Regiones'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'supports'           => array('title'),
        'menu_icon'          => 'dashicons-location-alt',
        'has_archive'        => false,
        'rewrite'            => array('slug' => 'regiones')
    );

    register_post_type('region', $args);
}
add_action('init', 'neema_register_regiones_cpt');

/* ==========================================================
   Auto-generar Key desde el título en español
   ========================================================== */
function neema_region_auto_generate_key($post_id, $post, $update) {
    if ($post->post_type !== 'region') {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    $spanish_id = $post_id;
    $spanish_title = $post->post_title;
    if (function_exists('pll_get_post') && function_exists('pll_get_post_language')) {
        $current_lang = pll_get_post_language($post_id);
        if ($current_lang !== 'es') {
            $spanish_version = pll_get_post($post_id, 'es');
            if ($spanish_version) {
                $spanish_id = $spanish_version;
                $spanish_post = get_post($spanish_version);
                if ($spanish_post) {
                    $spanish_title = $spanish_post->post_title;
                }
            }
        }
    }
    if (!empty($spanish_title)) {
        $key = remove_accents($spanish_title);
        $key = strtolower($key);
        $key = preg_replace('/[^a-z0-9]+/', '-', $key);
        $key = trim($key, '-');
        update_post_meta($spanish_id, '_region_key', $key);
    }
}
add_action('save_post', 'neema_region_auto_generate_key', 10, 3);

