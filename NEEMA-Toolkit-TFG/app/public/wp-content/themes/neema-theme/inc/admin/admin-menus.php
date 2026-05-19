<?php
/**
 * Gestión de Menús del Administrador
 * 
 * Oculta menús del panel de administración según el rol del usuario
 */

if (!defined('ABSPATH')) {
    exit;
}

function neema_hide_menus_for_neema_admin() {
    $user = wp_get_current_user();
    if (!in_array('neema_admin', (array) $user->roles)) return;

    remove_menu_page('edit.php');                   // Entradas
    remove_menu_page('edit.php?post_type=page');    // Páginas
    remove_menu_page('edit-comments.php');          // Comentarios
    remove_menu_page('themes.php');                 // Apariencia
    remove_menu_page('plugins.php');                // Plugins
    remove_menu_page('tools.php');                  // Herramientas
    remove_menu_page('options-general.php');        // Ajustes
    remove_menu_page('edit.php?post_type=acf-field-group');  // ACF
    remove_menu_page('wp-mail-smtp');               // WP Mail SMTP
    remove_menu_page('wpforms-overview');           // WPForms
}
add_action('admin_menu', 'neema_hide_menus_for_neema_admin', 999);

function neema_hide_menus_for_gestor() {
    $user = wp_get_current_user();
    if (!in_array('gestor_contenido', (array) $user->roles)) return;

    // Mantener acceso al Dashboard (index.php) para ver widgets
    remove_menu_page('upload.php');                 // Medios
    remove_menu_page('edit.php');                   // Entradas
    remove_menu_page('edit.php?post_type=page');    // Páginas
    remove_menu_page('themes.php');                 // Apariencia
    remove_menu_page('plugins.php');                // Plugins
    remove_menu_page('users.php');                  // Usuarios
    remove_menu_page('tools.php');                  // Herramientas
    remove_menu_page('options-general.php');        // Ajustes
    
    // Ocultar plugins específicos
    remove_menu_page('wpforms-overview');           // WPForms
    remove_menu_page('edit.php?post_type=acf-field-group');  // ACF
    remove_menu_page('mlang');                      // Polylang (Idiomas)
    remove_menu_page('wp-mail-smtp');               // WP Mail SMTP
}
add_action('admin_menu', 'neema_hide_menus_for_gestor', 999);
