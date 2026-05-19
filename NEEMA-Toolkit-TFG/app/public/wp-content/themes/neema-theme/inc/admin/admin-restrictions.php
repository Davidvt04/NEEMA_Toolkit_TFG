<?php
/**
 * Restricciones de Administración
 * 
 * Gestiona el acceso al panel de administración y la visibilidad de la barra de admin
 */

if (!defined('ABSPATH')) {
    exit;
}

/* ==========================================================
    Ocultar barra de administración para todos los usuarios
   ========================================================== */
add_action('after_setup_theme', function() {
    if (!is_user_logged_in()) return;
    if(current_user_can('administrator')) return;
    show_admin_bar(false);
});


/* ==========================================================
    Redirigir usuarios no administradores fuera del admin
   ========================================================== */
add_action('admin_init', function() {
    if (!is_user_logged_in()) return;

    $user = wp_get_current_user();
    $roles = (array) $user->roles;

    // Si es visitante → fuera del admin
    if (in_array('visitante', $roles) && !wp_doing_ajax()) {
        wp_redirect(home_url());
        exit;
    }
});


/* ==========================================================
    Ocultar elementos de la barra superior para roles limitados
   ========================================================== */
function neema_hide_admin_bar_items($wp_admin_bar) {
    $user = wp_get_current_user();
    if (!in_array('neema_admin', (array) $user->roles) && !in_array('gestor_contenido', (array) $user->roles)) {
        return;
    }
    
    $wp_admin_bar->remove_node('comments');     // Comentarios
    $wp_admin_bar->remove_node('new-content');  // Añadir (+ Nuevo)
    $wp_admin_bar->remove_node('view-posts');   // Ver entradas
    $wp_admin_bar->remove_node('archive');      // Ver entradas (alternativo)
}
add_action('admin_bar_menu', 'neema_hide_admin_bar_items', 999);
