<?php
/**
 * Configuración del Rol Administrador funcional NEEMA
 * 
 * Gestiona las capacidades, permisos y configuraciones específicas del rol Administrador funcional NEEMA
 */

if (!defined('ABSPATH')) {
    exit;
}

function neema_setup_neema_admin_role() {
    $neema_admin = get_role('neema_admin');
    if ($neema_admin) {
        $neema_admin->add_cap('edit_recurso');
        $neema_admin->add_cap('read_recurso');
        $neema_admin->add_cap('delete_recurso');
        $neema_admin->add_cap('edit_recursos');
        $neema_admin->add_cap('edit_others_recursos');
        $neema_admin->add_cap('publish_recursos');
        $neema_admin->add_cap('read_private_recursos');
        $neema_admin->add_cap('delete_recursos');
        $neema_admin->add_cap('delete_private_recursos');
        $neema_admin->add_cap('delete_published_recursos');
        $neema_admin->add_cap('delete_others_recursos');
        $neema_admin->add_cap('edit_private_recursos');
        $neema_admin->add_cap('edit_published_recursos'); 
        $neema_admin->add_cap('edit_organismo');
        $neema_admin->add_cap('read_organismo');
        $neema_admin->add_cap('delete_organismo');
        $neema_admin->add_cap('edit_organismos');
        $neema_admin->add_cap('edit_others_organismos');
        $neema_admin->add_cap('publish_organismos');
        $neema_admin->add_cap('read_private_organismos');
        $neema_admin->add_cap('delete_organismos');
        $neema_admin->add_cap('delete_private_organismos');
        $neema_admin->add_cap('delete_published_organismos');
        $neema_admin->add_cap('delete_others_organismos');
        $neema_admin->add_cap('edit_private_organismos');
        $neema_admin->add_cap('edit_published_organismos');
        $neema_admin->add_cap('edit_posts');
        $neema_admin->add_cap('edit_others_posts');
        $neema_admin->add_cap('publish_posts');
        $neema_admin->add_cap('read_private_posts');
        $neema_admin->add_cap('delete_posts');
        $neema_admin->add_cap('delete_private_posts');
        $neema_admin->add_cap('delete_published_posts');
        $neema_admin->add_cap('delete_others_posts');
        $neema_admin->add_cap('edit_private_posts');
        $neema_admin->add_cap('edit_published_posts');
        $neema_admin->add_cap('manage_options');
        $neema_admin->add_cap('manage_translations');
    }
}
add_action('init', 'neema_setup_neema_admin_role');

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

function neema_admin_can_edit_all_users($allcaps, $caps, $args, $user) {
    if (!isset($user->roles) || !in_array('neema_admin', (array)$user->roles)) {
        return $allcaps;
    }
    
    if (in_array('edit_users', $caps) || in_array('promote_users', $caps) || in_array('edit_user', $caps)) {
        $allcaps['edit_users'] = true;
        $allcaps['promote_users'] = true;
        $allcaps['edit_user'] = true;
    }
    
    return $allcaps;
}
add_filter('user_has_cap', 'neema_admin_can_edit_all_users', 10, 4);
