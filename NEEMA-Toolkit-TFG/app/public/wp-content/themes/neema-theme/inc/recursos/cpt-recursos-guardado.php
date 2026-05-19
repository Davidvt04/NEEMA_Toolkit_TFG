<?php
/**
 * Funciones de guardado para el CPT Recursos
 * 
 * @package Neema
 */

/* ==========================================================
    Guardar Títulos Multiidioma
   ========================================================== */

/**
 * Guarda los títulos en múltiples idiomas
 */
function neema_recursos_save_titulos($post_id) {
    if (!isset($_POST['neema_recursos_titulos_nonce']) || 
        !wp_verify_nonce($_POST['neema_recursos_titulos_nonce'], 'neema_recursos_titulos_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    $errors = array();
    
    if (empty($_POST['recurso_titulo_es'])) {
        $errors[] = 'El título en Español es obligatorio.';
    }
    if (empty($_POST['recurso_titulo_en'])) {
        $errors[] = 'El título en Inglés es obligatorio.';
    }
    if (empty($_POST['recurso_titulo_fr'])) {
        $errors[] = 'El título en Francés es obligatorio.';
    }

    if (!empty($errors)) {
        set_transient('neema_recursos_errors_' . $post_id, $errors, 45);
        wp_safe_redirect(add_query_arg('neema_error', '1', get_edit_post_link($post_id, 'url')));
        exit;
    }
    update_post_meta($post_id, '_recurso_titulo_es', sanitize_text_field($_POST['recurso_titulo_es']));
    update_post_meta($post_id, '_recurso_titulo_en', sanitize_text_field($_POST['recurso_titulo_en']));
    update_post_meta($post_id, '_recurso_titulo_fr', sanitize_text_field($_POST['recurso_titulo_fr']));
}
add_action('save_post_recurso', 'neema_recursos_save_titulos');

/* ==========================================================
    Guardar Relaciones del Recurso
   ========================================================== */

/**
 * Guarda las relaciones y metadatos del recurso
 */
function neema_recursos_save_relaciones($post_id) {
    if (!isset($_POST['neema_recursos_relaciones_nonce']) || 
        !wp_verify_nonce($_POST['neema_recursos_relaciones_nonce'], 'neema_recursos_relaciones_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    $errors = array();
    
    $post_title = isset($_POST['post_title']) ? trim($_POST['post_title']) : '';
    if (empty($post_title)) {
        $errors[] = 'El Título es obligatorio.';
    }
    if (!has_post_thumbnail($post_id)) {
        $errors[] = 'La Imagen destacada es obligatoria.';
    }
    if (empty($_POST['recurso_tipo'])) {
        $errors[] = 'El Tipo de Recurso es obligatorio.';
    }
    if (empty($_POST['recurso_modulo'])) {
        $errors[] = 'El Módulo es obligatorio.';
    }
    if (empty($_POST['recurso_categoria'])) {
        $errors[] = 'La Categoría es obligatoria.';
    }
    
    if (!empty($errors)) {
        if (isset($_POST['recurso_paises'])) {
            $paises = array_map('intval', $_POST['recurso_paises']);
            update_post_meta($post_id, '_recurso_paises', $paises);
        }
        
        if (isset($_POST['recurso_tipo']) && !empty($_POST['recurso_tipo'])) {
            update_post_meta($post_id, '_recurso_tipo', sanitize_text_field($_POST['recurso_tipo']));
        }
        
        if (isset($_POST['recurso_tematicas'])) {
            $tematicas_keys = array_map('sanitize_text_field', $_POST['recurso_tematicas']);
            update_post_meta($post_id, '_recurso_tematicas', $tematicas_keys);
        }
        
        if (isset($_POST['recurso_regiones'])) {
            $regiones_keys = array_map('sanitize_text_field', $_POST['recurso_regiones']);
            update_post_meta($post_id, '_recurso_regiones', $regiones_keys);
        }
        
        if (isset($_POST['recurso_modulo']) && !empty($_POST['recurso_modulo'])) {
            update_post_meta($post_id, '_recurso_modulo', sanitize_text_field($_POST['recurso_modulo']));
        }
        
        if (isset($_POST['recurso_categoria']) && !empty($_POST['recurso_categoria'])) {
            update_post_meta($post_id, '_recurso_categoria', sanitize_text_field($_POST['recurso_categoria']));
        }
        
        if (isset($_POST['recurso_descargable']) && $_POST['recurso_descargable'] == '1') {
            update_post_meta($post_id, '_recurso_descargable', '1');
        } else {
            update_post_meta($post_id, '_recurso_descargable', '0');
        }
        
        if (isset($_POST['recurso_visualizable']) && $_POST['recurso_visualizable'] == '1') {
            update_post_meta($post_id, '_recurso_visualizable', '1');
        } else {
            update_post_meta($post_id, '_recurso_visualizable', '0');
        }
        set_transient('neema_recursos_relaciones_errors_' . $post_id, $errors, 45);
        update_post_meta($post_id, '_neema_validation_errors', $errors);
        remove_action('save_post_recurso', 'neema_recursos_save_relaciones');
        wp_update_post(array(
            'ID' => $post_id,
            'post_status' => 'draft'
        ), true, false);
        add_action('save_post_recurso', 'neema_recursos_save_relaciones');
        
        return;
    } else {
        delete_post_meta($post_id, '_neema_validation_errors');
    }
    if (isset($_POST['recurso_paises'])) {
        $paises = array_map('intval', $_POST['recurso_paises']);
        update_post_meta($post_id, '_recurso_paises', $paises);
    } else {
        delete_post_meta($post_id, '_recurso_paises');
    }
    if (isset($_POST['recurso_tipo']) && !empty($_POST['recurso_tipo'])) {
        update_post_meta($post_id, '_recurso_tipo', sanitize_text_field($_POST['recurso_tipo']));
    } else {
        delete_post_meta($post_id, '_recurso_tipo');
    }
    if (isset($_POST['recurso_tematicas'])) {
        $tematicas_keys = array_map('sanitize_text_field', $_POST['recurso_tematicas']);
        update_post_meta($post_id, '_recurso_tematicas', $tematicas_keys);
    } else {
        delete_post_meta($post_id, '_recurso_tematicas');
    }
    if (isset($_POST['recurso_regiones'])) {
        $regiones_keys = array_map('sanitize_text_field', $_POST['recurso_regiones']);
        update_post_meta($post_id, '_recurso_regiones', $regiones_keys);
    } else {
        delete_post_meta($post_id, '_recurso_regiones');
    }
    if (isset($_POST['recurso_modulo']) && !empty($_POST['recurso_modulo'])) {
        update_post_meta($post_id, '_recurso_modulo', sanitize_text_field($_POST['recurso_modulo']));
    } else {
        delete_post_meta($post_id, '_recurso_modulo');
    }
    if (isset($_POST['recurso_categoria']) && !empty($_POST['recurso_categoria'])) {
        update_post_meta($post_id, '_recurso_categoria', sanitize_text_field($_POST['recurso_categoria']));
    } else {
        delete_post_meta($post_id, '_recurso_categoria');
    }
    if (isset($_POST['recurso_descargable']) && $_POST['recurso_descargable'] == '1') {
        update_post_meta($post_id, '_recurso_descargable', '1');
    } else {
        update_post_meta($post_id, '_recurso_descargable', '0');
    }
    if (isset($_POST['recurso_visualizable']) && $_POST['recurso_visualizable'] == '1') {
        update_post_meta($post_id, '_recurso_visualizable', '1');
    } else {
        update_post_meta($post_id, '_recurso_visualizable', '0');
    }
}
add_action('save_post_recurso', 'neema_recursos_save_relaciones');
