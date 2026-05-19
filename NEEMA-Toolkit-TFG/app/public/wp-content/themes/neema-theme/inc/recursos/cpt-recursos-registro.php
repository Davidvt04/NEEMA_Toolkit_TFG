<?php
/**
 * Registro del Custom Post Type: Recursos
 * 
 * @package Neema
 */

/* ==========================================================
    Registro del CPT Recursos
   ========================================================== */

function neema_register_recursos_cpt() {
    $labels = array(
        'name'               => 'Recursos',
        'singular_name'      => 'Recurso',
        'add_new'            => 'Añadir nuevo',
        'add_new_item'       => 'Añadir nuevo recurso',
        'edit_item'          => 'Editar recurso',
        'new_item'           => 'Nuevo recurso',
        'all_items'          => 'Todos los recursos',
        'view_item'          => 'Ver recurso',
        'search_items'       => 'Buscar recursos',
        'not_found'          => 'No se encontraron recursos',
        'not_found_in_trash' => 'No hay recursos en la papelera',
        'menu_name'          => 'Recursos'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'menu_icon'          => 'dashicons-media-document',
        'supports'           => array('title', 'thumbnail'),
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'recursos'),
        'show_in_rest'       => true,
        'capability_type'    => array('recurso', 'recursos'),
        'map_meta_cap'       => true,
    );

    register_post_type('recurso', $args);
}
add_action('init', 'neema_register_recursos_cpt');

/* ==========================================================
    Capacidades del CPT
   ========================================================== */

/**
 * Asegurar que gestor_contenido tiene capacidades para eliminar recursos no publicados
 */
function neema_ensure_gestor_delete_capabilities() {
    $role = get_role('gestor_contenido');
    
    if ($role) {
        $role->add_cap('delete_recurso');
        $role->add_cap('delete_recursos');
    }
}
add_action('init', 'neema_ensure_gestor_delete_capabilities', 11);
