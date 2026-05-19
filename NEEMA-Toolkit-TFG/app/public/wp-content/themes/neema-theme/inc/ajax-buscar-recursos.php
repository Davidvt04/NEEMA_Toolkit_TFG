<?php
/**
 * AJAX: Buscar recursos con filtros
 */
require_once __DIR__ . '/ajax-recurso-helpers.php';

add_action('wp_ajax_buscar_recursos', 'neema_buscar_recursos');
add_action('wp_ajax_nopriv_buscar_recursos', 'neema_buscar_recursos');

function neema_buscar_recursos() {
    $texto = isset($_POST['texto']) ? sanitize_text_field($_POST['texto']) : '';
    $paises = isset($_POST['paises']) ? array_map('sanitize_text_field', $_POST['paises']) : array();
    $tipos = isset($_POST['tipos']) ? array_map('sanitize_text_field', $_POST['tipos']) : array();
    $tematicas = isset($_POST['tematicas']) ? array_map('sanitize_text_field', $_POST['tematicas']) : array();
    $regiones = isset($_POST['regiones']) ? array_map('sanitize_text_field', $_POST['regiones']) : array();
    $categorias_seleccionadas = isset($_POST['categorias']) ? array_map('sanitize_text_field', $_POST['categorias']) : array();
    $modulo_key = isset($_POST['modulo_key']) ? sanitize_text_field($_POST['modulo_key']) : '';
    $lang = isset($_POST['lang']) ? sanitize_text_field($_POST['lang']) : 'es';
    if (empty($texto) && empty($paises) && empty($tipos) && empty($tematicas) && empty($regiones) && empty($categorias_seleccionadas)) {
        $no_results_msg = neema_translate('No se especificó ningún criterio de búsqueda.', 'General');
        wp_send_json_success(array(
            'html' => '<p class="no-recursos-msg">' . esc_html($no_results_msg) . '</p>'
        ));
    }
    if (function_exists('pll_current_language')) {
        global $polylang;
        if ($polylang) {
            $polylang->curlang = $polylang->model->get_language($lang);
        }
    }

    $current_user_id = get_current_user_id();
    $user_favorite_ids = $current_user_id ? get_recursos_favoritos($current_user_id) : array();
    set_query_var('user_favorite_ids', $user_favorite_ids);
    $meta_query_base = array('relation' => 'AND');
    if (!empty($modulo_key)) {
        $meta_query_base[] = array(
            'key' => '_recurso_modulo',
            'value' => $modulo_key,
            'compare' => '='
        );
    }
    $meta_query = $meta_query_base;
    if (!empty($categorias_seleccionadas)) {
        $meta_query[] = array(
            'key' => '_recurso_categoria',
            'value' => $categorias_seleccionadas,
            'compare' => 'IN'
        );
    }
    if (!empty($tipos)) {
        $meta_query[] = array(
            'key' => '_recurso_tipo',
            'value' => $tipos,
            'compare' => 'IN'
        );
    }
    $args = array(
        'post_type' => 'recurso',
        'posts_per_page' => 20, 
        'meta_query' => $meta_query,
        'post_status' => 'publish'
    );
    if (!empty($texto)) {
        $args['s'] = $texto;
        $args['relevanssi'] = true;
    }
    $query = new WP_Query($args);
    $recursos_found = $query->posts;
    if (!empty($paises)) {
        $recursos_found = neema_filter_by_paises($recursos_found, $paises);
    }
    if (!empty($tematicas)) {
        $recursos_found = neema_filter_by_tematicas($recursos_found, $tematicas);
    }
    if (!empty($regiones)) {
        $recursos_found = neema_filter_by_regiones($recursos_found, $regiones);
    }
    $recursos_exact = array_values($recursos_found);
    $num_recursos = count($recursos_exact);
    $recursos_relaxed = array();
    if ($num_recursos < 3) {
        $meta_query_relaxed = array('relation' => 'AND');
        if (!empty($modulo_key)) {
            $meta_query_relaxed[] = array(
                'key' => '_recurso_modulo',
                'value' => $modulo_key,
                'compare' => '='
            );
        }
        if ($num_recursos > 0 && !empty($categorias_seleccionadas)) {
            $meta_query_relaxed[] = array(
                'key' => '_recurso_categoria',
                'value' => $categorias_seleccionadas,
                'compare' => 'IN'
            );
        }
        if (!empty($tipos)) {
            $meta_query_relaxed[] = array(
                'key' => '_recurso_tipo',
                'value' => $tipos,
                'compare' => 'IN'
            );
        }
        
        $args_relaxed = array(
            'post_type' => 'recurso',
            'posts_per_page' => 20,
            'post_status' => 'publish',
            'meta_query' => $meta_query_relaxed,
            'post__not_in' => wp_list_pluck($recursos_exact, 'ID')
        );
        
        if (!empty($texto)) {
            $args_relaxed['s'] = $texto;
            $args_relaxed['relevanssi'] = true;
        }
        
        $q_relaxed = new WP_Query($args_relaxed);
        $recursos_relaxed_found = $q_relaxed->posts;
        if (!empty($paises) || !empty($tematicas) || !empty($regiones)) {
            $recursos_relaxed_found = array_filter($recursos_relaxed_found, function($recurso) use ($paises, $tematicas, $regiones) {
                if (!empty($paises)) {
                    $pais_ids = array_map(function($pais) {
                        return intval(str_replace('pais_', '', $pais));
                    }, $paises);
                    $recurso_paises = get_post_meta($recurso->ID, '_recurso_paises', true);
                    if (is_array($recurso_paises) && !empty(array_intersect($pais_ids, $recurso_paises))) {
                        return true;
                    }
                }
                
                if (!empty($tematicas)) {
                    $recurso_tematicas = get_post_meta($recurso->ID, '_recurso_tematicas', true);
                    if (is_array($recurso_tematicas) && !empty(array_intersect($tematicas, $recurso_tematicas))) {
                        return true;
                    }
                }
                
                if (!empty($regiones)) {
                    $recurso_regiones = get_post_meta($recurso->ID, '_recurso_regiones', true);
                    if (is_array($recurso_regiones) && !empty(array_intersect($regiones, $recurso_regiones))) {
                        return true;
                    }
                }
                
                return false;
            });
        }
        
        $recursos_relaxed = array_values($recursos_relaxed_found);
    }

    ob_start();
    if (!empty($recursos_exact)) {
        $exact_title = neema_translate('Resultados de búsqueda', 'Recursos');
        echo '<h2 class="recursos-title">' . esc_html($exact_title) . '</h2>';
        echo '<div class="recursos-grid">';
        $count = 0;
        foreach ($recursos_exact as $r) {
            if ($count >= 6) break;
            neema_render_recurso($r->ID);
            $count++;
        }
        echo '</div>';
    }
    if (!empty($recursos_relaxed)) {
        $approx_title = neema_translate('Resultados aproximados', 'General');
        echo '<h2 class="recursos-title">' . esc_html($approx_title) . '</h2>';
        echo '<div class="recursos-grid">';
        $count = 0;
        foreach ($recursos_relaxed as $r) {
            if ($count >= 6) break;
            neema_render_recurso($r->ID);
            $count++;
        }
        echo '</div>';
    }
    $html_final = ob_get_clean();

    if (empty($html_final)) {
        $html_final = '<p class="no-recursos-msg">' . esc_html(neema_translate('No se encontraron recursos con los criterios seleccionados.', 'Recursos')) . '</p>';
    }

    wp_send_json_success(array('html' => $html_final));
}

add_filter('relevanssi_match', 'neema_pesos_recursos', 10, 2);
function neema_pesos_recursos($match, $query) {
    $weight = isset($match->weight) ? $match->weight : 0;
    $title_factor = 5;
    $description_factor = 3;

    $titulos = ['_recurso_titulo_es', '_recurso_titulo_en', '_recurso_titulo_fr'];
    $descripciones = ['descripcion_es', 'descripcion_en', 'descripcion_fr'];

    if (isset($match->customfield) && $match->customfield) {
        if (isset($match->doc) && !empty($match->term)) {
            $post_id = (int) $match->doc;
            $term = $match->term;

            foreach ($titulos as $campo_titulo) {
                $titulo_post = get_post_meta($post_id, $campo_titulo, true);
                if ($titulo_post && stripos($titulo_post, $term) !== false) {
                    $weight += $title_factor;
                    break;
                }
            }

            foreach ($descripciones as $campo_descripcion) {
                $descripcion_post = get_post_meta($post_id, $campo_descripcion, true);
                if ($descripcion_post && stripos($descripcion_post, $term) !== false) {
                    $weight += $description_factor;
                    break;
                }
            }
        }
    }
    $match->weight = $weight;
    return $match;
}
