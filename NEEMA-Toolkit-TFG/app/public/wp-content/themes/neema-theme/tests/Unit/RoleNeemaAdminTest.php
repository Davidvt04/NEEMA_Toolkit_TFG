<?php
/**
 * Configuración del Rol Administrador funcional NEEMA
 */

if (!defined('ABSPATH')) {
    exit;
}

/* ==========================================================
    CORE: setup del rol NEEMA ADMIN
    ========================================================== */
function neema_setup_neema_admin_role() {
    $neema_admin = get_role('neema_admin');

    if (!$neema_admin) {
        return;
    }

    $caps = array(

        // Recursos
        'edit_recurso',
        'read_recurso',
        'delete_recurso',
        'edit_recursos',
        'edit_others_recursos',
        'publish_recursos',
        'read_private_recursos',
        'delete_recursos',
        'delete_private_recursos',
        'delete_published_recursos',
        'delete_others_recursos',
        'edit_private_recursos',
        'edit_published_recursos',

        // Organismos
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

        // Posts base
        'edit_posts',
        'edit_others_posts',
        'publish_posts',
        'read_private_posts',
        'delete_posts',
        'delete_private_posts',
        'delete_published_posts',
        'delete_others_posts',
        'edit_private_posts',
        'edit_published_posts',

        // Polylang / sistema
        'manage_options',
        'manage_translations',
    );

    foreach ($caps as $cap) {
        $neema_admin->add_cap($cap);
    }
}

/* Hook normal WordPress */
add_action('init', 'neema_setup_neema_admin_role');

/* ==========================================================
    POLYLANG PERMISSIONS
    ========================================================== */
function neema_admin_polylang_permissions($allcaps, $caps, $args, $user) {

    if (!isset($user->roles) || !in_array('neema_admin', (array)$user->roles)) {
        return $allcaps;
    }

    $allcaps['manage_options'] = true;
    $allcaps['pll_manage_languages'] = true;
    $allcaps['pll_manage_strings'] = true;
    $allcaps['manage_translations'] = true;

    return $allcaps;
}
add_filter('user_has_cap', 'neema_admin_polylang_permissions', 10, 4);

/* ==========================================================
    EDIT USERS override
    ========================================================== */
function neema_admin_can_edit_all_users($allcaps, $caps, $args, $user) {

    if (!isset($user->roles) || !in_array('neema_admin', (array)$user->roles)) {
        return $allcaps;
    }

    if (
        in_array('edit_users', $caps) ||
        in_array('promote_users', $caps) ||
        in_array('edit_user', $caps)
    ) {
        $allcaps['edit_users'] = true;
        $allcaps['promote_users'] = true;
        $allcaps['edit_user'] = true;
    }

    return $allcaps;
}

add_filter('user_has_cap', 'neema_admin_can_edit_all_users', 10, 4);

/* ==========================================================
    HELPERS PARA TESTING
    ========================================================== */
/**
 * Permite ejecutar setup manual en tests
 */
function neema_setup_neema_admin_role_for_tests() {
    neema_setup_neema_admin_role();
}