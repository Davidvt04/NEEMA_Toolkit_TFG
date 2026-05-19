<?php
/* ==========================================================
    Custom Post Type: Tipos de recursos
   ========================================================== */
function neema_register_tipos_recursos_cpt() {
    $labels = array(
        'name'               => 'Tipos de recursos',
        'singular_name'      => 'Tipo de recurso',
        'add_new'            => 'Añadir nuevo',
        'add_new_item'       => 'Añadir nuevo tipo de recurso',
        'edit_item'          => 'Editar tipo de recurso',
        'new_item'           => 'Nuevo tipo de recurso',
        'view_item'          => 'Ver tipo de recurso',
        'search_items'       => 'Buscar tipos de recursos',
        'not_found'          => 'No se encontraron tipos de recursos',
        'not_found_in_trash' => 'No hay tipos de recursos en la papelera',
        'menu_name'          => 'Tipos de recursos'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'supports'           => array('title', 'thumbnail'),
        'menu_icon'          => 'dashicons-category',
        'has_archive'        => false,
        'rewrite'            => array('slug' => 'tipos-recursos')
    );

    register_post_type('tipo-recurso', $args);
}
add_action('init', 'neema_register_tipos_recursos_cpt');

/* ==========================================================
   Auto-generar Key desde el título en español
   ========================================================== */
function neema_tipo_recurso_auto_generate_key($post_id, $post, $update) {
    if ($post->post_type !== 'tipo-recurso') {
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
        update_post_meta($spanish_id, '_tipo_recurso_key', $key);
    }
}
add_action('save_post', 'neema_tipo_recurso_auto_generate_key', 10, 3);

