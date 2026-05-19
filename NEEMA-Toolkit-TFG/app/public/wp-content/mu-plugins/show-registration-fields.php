<?php
/*
Plugin Name: Mostrar campos de registro NEEMA en perfil admin
Description: Muestra todos los campos/metadatos del usuario en la página de edición del usuario en el administrador.
Version: 1.0
Author: David Valencia Toscano
*/

defined('ABSPATH') || exit;

add_action('show_user_profile', 'mt_show_all_registration_fields');
add_action('edit_user_profile', 'mt_show_all_registration_fields');

function mt_show_all_registration_fields($user) {
    if (!current_user_can('edit_user', $user->ID)) return;
    
    echo '<h2>Campos de registro</h2>';
    echo '<table class="form-table"><tbody>';

    $custom_role = '';

    $v = get_user_meta($user->ID,'rol_usuario', true);
    if ($v !== '' && $v !== false) { $custom_role = $v; }
    
    echo '<tr><th>Rol</th><td>' . esc_html(is_array($custom_role) ? print_r($custom_role, true) : $custom_role) . '</td></tr>';

    $entidad_value = '';
    $v = get_user_meta($user->ID, 'entidad_proveniente', true);
    if ($v !== '' && $v !== false) { $entidad_value = $v; }
    echo '<tr><th>Entidad proveniente</th><td>' . esc_html(is_array($entidad_value) ? print_r($entidad_value, true) : $entidad_value) . '</td></tr>';

    $pref_raw = get_user_meta($user->ID, 'preferencias', true);

    echo '<tr><th>Preferencias</th><td>';
    if ($pref_raw === '' || $pref_raw === false) {
        echo '—';
    } else {
        $prefs = (array) $pref_raw;
        $label_map = array(
            '_recurso_paises' => 'Países',
            '_recurso_tipo' => 'Tipo de recurso',
            '_recurso_tematicas' => 'Temáticas',
            '_recurso_regiones' => 'Regiones',
            'recurso_paises' => 'Países',
            'paises' => 'Países',
        );

        echo '<div style="margin:0">';
        foreach ($prefs as $pkey => $pval) {
            $title = isset($label_map[$pkey]) ? $label_map[$pkey] : $pkey;
            echo '<div style="margin-bottom:8px">';
            echo '<strong>' . esc_html($title) . '</strong>';

            $items = (array) $pval;
            arsort($items);
            echo '<ul style="margin:4px 0 0 18px;padding:0">';
                    foreach ($items as $ik => $iv) {
                        if (trim((string) $ik) === '') {
                            continue;
                        }
                        
                        $display_key = $ik;
                        if ($pkey == '_recurso_paises') {
                            $resolved = mt_resolve_country_name((int) $ik);
                            if ($resolved) $display_key = $resolved;
                        }
                                  
                        echo '<li style="list-style:disc;margin:2px 0">' . esc_html($display_key) . ': <span>' . esc_html((string) $iv) . '</span></li>';
            }
            echo '</ul>';

            echo '</div>';
        }
        echo '</div>';
    }
    echo '</td></tr>';

    echo '</tbody></table>';
}

    function mt_resolve_country_name($id) {
        $id = (int) $id;

        $post = get_post($id);
        if ($post && !empty($post->post_title)) {
            return $post->post_title;
        }
    }
