<?php
/* ==========================================================
    Custom Post Type: Paises
   ========================================================== */
function neema_register_paises_cpt() {
    $labels = array(
        'name'               => 'Países',
        'singular_name'      => 'País',
        'add_new'            => 'Añadir nuevo',
        'add_new_item'       => 'Añadir nuevo país',
        'edit_item'          => 'Editar país',
        'new_item'           => 'Nuevo país',
        'view_item'          => 'Ver país',
        'search_items'       => 'Buscar países',
        'not_found'          => 'No se encontraron países',
        'not_found_in_trash' => 'No hay países en la papelera',
        'menu_name'          => 'Países'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'supports'           => array('title'),
        'menu_icon'          => 'dashicons-admin-site-alt',
        'has_archive'        => false,
        'rewrite'            => array('slug' => 'paises')
    );

    register_post_type('pais', $args);
}
add_action('init', 'neema_register_paises_cpt');

