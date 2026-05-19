<?php
/**
 * Gestión de Verificación de Usuarios
 * 
 * Gestiona usuarios no confirmados en el panel de administración:
 * - Columnas de estado
 * - Filtros de verificación
 * - Reenvío de emails de activación
 */

if (!defined('ABSPATH')) {
    exit;
}

function neema_add_user_status_column($columns) {
    $columns['account_status'] = 'Estado de verificación';
    return $columns;
}
add_filter('manage_users_columns', 'neema_add_user_status_column');

function neema_show_user_status_column($value, $column_name, $user_id) {
    if ($column_name == 'account_status') {
        $status = get_user_meta($user_id, 'account_status', true);
        
        if ($status === 'awaiting_email_confirmation') {
            $value = '<span style="color: #d63638; font-weight: 600;">No verificado</span><br>';
            $value .= '<a href="' . wp_nonce_url(admin_url('users.php?action=resend_activation&user_id=' . $user_id), 'resend_activation_' . $user_id) . '" class="button button-small" style="margin-top: 5px;">Reenviar email</a>';
        } else {
            $value = '<span style="color: #00a32a; font-weight: 600;">Verificado</span>';
        }
    }
    return $value;
}
add_filter('manage_users_custom_column', 'neema_show_user_status_column', 10, 3);


function neema_make_status_column_sortable($columns) {
    $columns['account_status'] = 'account_status';
    return $columns;
}
add_filter('manage_users_sortable_columns', 'neema_make_status_column_sortable');


function neema_add_user_status_filter() {
    if (!is_admin()) {
        return;
    }
    
    $screen = get_current_screen();
    if ($screen->id !== 'users') {
        return;
    }
    
    $selected = isset($_GET['user_status']) ? $_GET['user_status'] : '';
    ?>
    <label for="user_status_filter" class="screen-reader-text">Filtrar por estado</label>
    <select name="user_status" id="user_status_filter">
        <option value="">Todos los estados</option>
        <option value="awaiting_email_confirmation" <?php selected($selected, 'awaiting_email_confirmation'); ?>>No verificados</option>
        <option value="verified" <?php selected($selected, 'verified'); ?>>Verificados</option>
    </select>
    <?php
}
add_action('restrict_manage_users', 'neema_add_user_status_filter');


function neema_filter_users_by_status($query) {
    global $pagenow;
    
    if (is_admin() && $pagenow == 'users.php' && isset($_GET['user_status']) && !empty($_GET['user_status'])) {
        $status = sanitize_text_field($_GET['user_status']);
        
        if ($status === 'awaiting_email_confirmation') {
            $query->set('meta_query', array(
                array(
                    'key' => 'account_status',
                    'value' => 'awaiting_email_confirmation',
                    'compare' => '='
                )
            ));
        } elseif ($status === 'verified') {
            $query->set('meta_query', array(
                'relation' => 'OR',
                array(
                    'key' => 'account_status',
                    'compare' => 'NOT EXISTS'
                ),
                array(
                    'key' => 'account_status',
                    'value' => 'awaiting_email_confirmation',
                    'compare' => '!='
                )
            ));
        }
    }
}
add_action('pre_get_users', 'neema_filter_users_by_status');

function neema_handle_resend_activation() {
    if (!isset($_GET['action']) || $_GET['action'] !== 'resend_activation') {
        return;
    }
    
    if (!isset($_GET['user_id']) || !current_user_can('edit_users')) {
        wp_die('No tienes permisos para realizar esta acción.');
    }
    
    $user_id = intval($_GET['user_id']);
    
    if (!wp_verify_nonce($_GET['_wpnonce'], 'resend_activation_' . $user_id)) {
        wp_die('Error de seguridad.');
    }
    
    $user_data = get_userdata($user_id);
    if (!$user_data) {
        wp_die('Usuario no encontrado.');
    }
    
    $status = get_user_meta($user_id, 'account_status', true);
    if ($status !== 'awaiting_email_confirmation') {
        wp_redirect(add_query_arg('resend_error', '1', admin_url('users.php')));
        exit;
    }
    
    $activation_code = get_user_meta($user_id, 'activation_code', true);
    if (!$activation_code) {
        $activation_code = wp_generate_password(20, false);
        update_user_meta($user_id, 'activation_code', $activation_code);
    }
    
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : 'es';
    $activation_url = add_query_arg(array(
        'act' => 'activate_account',
        'user_id' => $user_id,
        'code' => $activation_code
    ), home_url('/sign-up-' . $current_lang . '/'));
    
    $site_name = get_bloginfo('name');
    $site_url = home_url();
    $admin_email = get_option('admin_email');
    
    ob_start();
    include(get_template_directory() . '/email-templates/activation-email.php');
    $message = ob_get_clean();
    
    $subject = sprintf('[%s] %s', $site_name, 'Activa tu cuenta');
    $headers = array('Content-Type: text/html; charset=UTF-8');
    
    if (wp_mail($user_data->user_email, $subject, $message, $headers)) {
        wp_redirect(add_query_arg('resend_success', '1', admin_url('users.php')));
    } else {
        wp_redirect(add_query_arg('resend_error', '2', admin_url('users.php')));
    }
    exit;
}
add_action('admin_init', 'neema_handle_resend_activation');

function neema_show_resend_notices() {
    if (isset($_GET['resend_success']) && $_GET['resend_success'] == '1') {
        echo '<div class="notice notice-success is-dismissible"><p><strong>Email de activación reenviado correctamente.</strong></p></div>';
    }
    
    if (isset($_GET['resend_error'])) {
        if ($_GET['resend_error'] == '1') {
            echo '<div class="notice notice-error is-dismissible"><p><strong>Este usuario ya está verificado.</strong></p></div>';
        } elseif ($_GET['resend_error'] == '2') {
            echo '<div class="notice notice-error is-dismissible"><p><strong>Error al enviar el email. Inténtalo de nuevo.</strong></p></div>';
        }
    }
}
add_action('admin_notices', 'neema_show_resend_notices');
