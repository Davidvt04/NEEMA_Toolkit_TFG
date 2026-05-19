<?php
/**
 * Email de Restablecimiento de Contraseña
 * 
 * Personaliza el email enviado cuando un usuario solicita restablecer su contraseña
 */

if (!defined('ABSPATH')) {
    exit;
}

/* ==========================================================
    Personalizar email de restablecimiento de contraseña
   ========================================================== */
function neema_custom_password_reset_email($defaults, $key, $user_login, $user_data) {
    $reset_link = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');
    $user_name = $user_data->display_name;
    $site_name = get_bloginfo('name');
    $site_url = home_url();
    $admin_email = get_option('admin_email');
    ob_start();
    include(get_template_directory() . '/email-templates/reset-password-email.php');
    $message = ob_get_clean();
    $defaults['message'] = $message;
    $defaults['subject'] = sprintf('[%s] %s', $site_name, pll__('Restablecer contraseña'));
    $defaults['headers'] = array('Content-Type: text/html; charset=UTF-8');
    
    return $defaults;
}
add_filter('retrieve_password_notification_email', 'neema_custom_password_reset_email', 10, 4);
