<?php
/**
 * Configuración del Rol Gestor de Contenido
 * 
 * Gestiona las capacidades y permisos específicos del rol Gestor de Contenido
 */

if (!defined('ABSPATH')) {
    exit;
}

/* ==========================================================
   Configurar capacidades del rol Gestor de Contenido
   ========================================================== */
function neema_setup_gestor_contenido_role() {
    $role = get_role('gestor_contenido');
    
    if (!$role) {
        return;
    }
    $caps_to_remove = [
        'edit_posts',
        'edit_others_posts',
        'publish_posts',
        'delete_posts',
        'manage_options',
        'manage_categories',
        'upload_files',
    ];

    foreach ($caps_to_remove as $cap) {
        $role->remove_cap($cap);
    }
    $caps_to_add = [
        'read',
        'edit_recurso',
        'read_recurso',
        'delete_recurso',
        'edit_recursos',
        'publish_recursos',
        'read_private_recursos',
        'edit_published_recursos',
        'delete_published_recursos',
        'upload_files',
    ];

    foreach ($caps_to_add as $cap) {
        $role->add_cap($cap);
    }
    $organismo_caps = [
        'edit_organismo',
        'read_organismo',
        'delete_organismo',
        'edit_organismos',
        'edit_others_organismos',
        'publish_organismos',
        'read_private_organismos',
        'delete_organismos',
        'delete_private_organismos',
        'delete_published_organismos',
        'delete_others_organismos',
        'edit_private_organismos',
        'edit_published_organismos',
    ];

    foreach ($organismo_caps as $cap) {
        $role->add_cap($cap);
    }
}
add_action('init', 'neema_setup_gestor_contenido_role', 20);

/* ==========================================================
   Permitir acceso a WP Statistics para Gestor de Contenido
   ========================================================== */
function neema_gestor_wp_statistics_capability($allcaps, $caps, $args, $user) {
    if (!isset($user->roles) || !in_array('gestor_contenido', (array)$user->roles)) {
        return $allcaps;
    }

    if (!is_admin()) {
        return $allcaps;
    }

    $current_page  = isset($_GET['page'])               ? sanitize_text_field($_GET['page'])                : '';
    $script_name   = isset($_SERVER['SCRIPT_NAME'])     ? $_SERVER['SCRIPT_NAME']                           : '';
    $ajax_action   = isset($_POST['action'])            ? sanitize_text_field($_POST['action'])              : '';
    $ajax_action   = $ajax_action ?: (isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '');

    if (
        strpos($script_name, 'index.php') !== false ||
        strpos($current_page, 'wps_') === 0 ||
        strpos($ajax_action, 'wp_statistics_') === 0
    ) {
        $allcaps['manage_options'] = true;
    }

    return $allcaps;
}
add_filter('user_has_cap', 'neema_gestor_wp_statistics_capability', 10, 4);
