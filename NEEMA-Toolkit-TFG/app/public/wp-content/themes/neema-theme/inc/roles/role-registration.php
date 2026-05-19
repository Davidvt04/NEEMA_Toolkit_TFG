<?php
/**
 * Registro de Roles Personalizados
 * 
 * Define los roles personalizados del sistema: Visitante, Gestor de Contenido y Administrador funcional NEEMA
 */

if (!defined('ABSPATH')) {
    exit;
}

function neema_register_custom_roles() {
    remove_role('subscriber');
    remove_role('contributor');
    remove_role('author');
    remove_role('editor');

    add_role(
        'visitante',
        'Visitante',
        array(
            'read' => true,
        )
    );
    add_role(
        'gestor_contenido',
        'Gestor de Contenido',
        array(
            'read' => true,
            'edit_posts' => true,
            'edit_pages' => true,
            'edit_published_posts' => true,
            'publish_posts' => true,
            'upload_files' => true,
            'edit_recurso' => true,
            'read_recurso' => true,
            'delete_recurso' => true,
            'edit_recursos' => true,
            'edit_published_recursos' => true,
            'read_private_recursos' => true,
            'delete_recursos' => true,
            'delete_private_recursos' => true,
        )
    );

    add_role(
        'neema_admin',
        'Administrador funcional NEEMA',
        array(
            'read' => true,
            'upload_files' => true,
            'edit_users' => true,
            'list_users' => true,
            'create_users' => true,
            'delete_users' => true,
            'promote_users' => true,
            'remove_users' => true,
        )
    );
}
add_action('init', 'neema_register_custom_roles');


function neema_update_role_name() {
    global $wp_roles;
    if (!isset($wp_roles)) {
        $wp_roles = new WP_Roles();
    }
    
    if (isset($wp_roles->roles['neema_admin'])) {
        $wp_roles->roles['neema_admin']['name'] = 'Administrador funcional NEEMA';
        update_option($wp_roles->role_key, $wp_roles->roles);
    }
}
add_action('init', 'neema_update_role_name', 11);


function neema_redirect_after_registration() {
    if ( isset($_GET['registro']) && $_GET['registro'] === 'ok' ) {
        wp_safe_redirect( home_url() );
        exit;
    }
}
add_action('template_redirect', 'neema_redirect_after_registration');
