<?php
/**
 * Validación y mensajes de error para el CPT Recursos
 * 
 * @package Neema
 */

/* ==========================================================
   ️ Mostrar errores de validación
   ========================================================== */

/**
 * Muestra los errores de validación en el admin
 */
function neema_recursos_show_errors() {
    global $post;
    
    if (!$post || $post->post_type !== 'recurso') {
        return;
    }
    if (isset($_GET['neema_error']) && $_GET['neema_error'] == '1') {
        $errors = get_transient('neema_recursos_errors_' . $post->ID);
        if ($errors) {
            echo '<div class="notice notice-error is-dismissible"><ul>';
            foreach ($errors as $error) {
                echo '<li><strong>' . esc_html($error) . '</strong></li>';
            }
            echo '</ul></div>';
            delete_transient('neema_recursos_errors_' . $post->ID);
        }
    }
    $relation_errors = get_post_meta($post->ID, '_neema_validation_errors', true);
    if ($relation_errors && is_array($relation_errors)) {
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p><strong>Por favor, corrige los siguientes errores:</strong></p>';
        echo '<ul>';
        foreach ($relation_errors as $error) {
            echo '<li>' . esc_html($error) . '</li>';
        }
        echo '</ul></div>';
    }
}
add_action('admin_notices', 'neema_recursos_show_errors');
