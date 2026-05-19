<?php
/**
 * Gestión de Usuarios - Archivo Principal
 * 
 * Este archivo coordina la carga de todos los módulos relacionados con la gestión de usuarios,
 * roles y capacidades del sistema Neema.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Cargar módulos de roles
require_once get_template_directory() . '/inc/roles/role-registration.php';
require_once get_template_directory() . '/inc/roles/role-neema-admin.php';
require_once get_template_directory() . '/inc/roles/role-gestor-contenido.php';

// Cargar módulos de administración
require_once get_template_directory() . '/inc/admin/admin-restrictions.php';
require_once get_template_directory() . '/inc/admin/admin-menus.php';

// Cargar módulos de email
require_once get_template_directory() . '/inc/email/password-reset.php';

// Cargar módulos de usuarios
require_once get_template_directory() . '/inc/users/user-verification.php';

// Deshabilitar notificaciones automáticas de cambio de email de WordPress
add_filter('send_email_change_email', '__return_false');
add_filter('send_password_change_email', '__return_false');
