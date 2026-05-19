<?php
/**
 * AJAX Handler: Buscar Organismos
 * Maneja las búsquedas de organismos según ámbito, países y ciudad
 */

add_action('wp_ajax_buscar_organismos', 'neema_buscar_organismos');
add_action('wp_ajax_nopriv_buscar_organismos', 'neema_buscar_organismos');

function neema_buscar_organismos() {
    
    $texto = isset($_POST['buscar_texto']) ? sanitize_text_field($_POST['buscar_texto']) : '';
    $ambitos = isset($_POST['ambito']) && is_array($_POST['ambito']) ? array_map('sanitize_text_field', $_POST['ambito']) : array();
    $paises_ids = isset($_POST['paises']) && is_array($_POST['paises']) ? array_map('intval', $_POST['paises']) : array();
    $ciudad = isset($_POST['ciudad']) ? sanitize_text_field($_POST['ciudad']) : '';
    $categoria_id = isset($_POST['categoria_id']) ? intval($_POST['categoria_id']) : null;
    if (!empty($ciudad)) {
        $ciudad = neema_normalizar_ciudad($ciudad);
    }
    $meta_query = array('relation' => 'AND');
    if (!empty($ambitos)) {
        $meta_query[] = array(
            'key'     => '_organismo_ambito',
            'value'   => $ambitos,
            'compare' => 'IN'
        );
    }
    if (!empty($paises_ids)) {
        $paises_meta_query = array('relation' => 'OR');
        foreach ($paises_ids as $pais_id) {
            $paises_meta_query[] = array(
                'key'     => '_organismo_paises',
                'value'   => sprintf('i:%d;', $pais_id), 
                'compare' => 'LIKE'
            );
        }
        $meta_query[] = $paises_meta_query;
    }
    if (!empty($ciudad) && in_array('Local', $ambitos)) {
        $meta_query[] = array(
            'key'     => '_organismo_ciudad',
            'value'   => $ciudad,
            'compare' => 'LIKE'
        );
    }
    $categoria_meta_query = null;
    $meta_query_non_category = null;
    if ($categoria_id) {
        $meta_query_non_category = $meta_query;
        $categoria_translations = function_exists('pll_get_post_translations') ? pll_get_post_translations($categoria_id) : array($categoria_id);
        $categoria_ids = array_values($categoria_translations);
        $categoria_meta_query = array(
            'key'     => '_organismo_categoria',
            'value'   => $categoria_ids,
            'compare' => 'IN'
        );
        $meta_query[] = $categoria_meta_query;
    }
    $args = array(
        'post_type'      => 'organismo',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'lang'           => '', 
    );

    if (count($meta_query) > 1) {
        $args['meta_query'] = $meta_query;
    }

    if (!empty($texto)) {
        $args['s'] = $texto;
        $args['relevanssi'] = true;
    }


    $num_organismos = 0;
    $no_results_msg = null;
    if(!empty($texto) || !empty($ambitos) || !empty($paises_ids) || !empty($ciudad)){
        $query = new WP_Query($args);
        $organismos = $query->posts;
        $num_organismos = count($organismos);
    }else{
        $no_results_msg = neema_translate('No se especificó ningún criterio de búsqueda.', 'General');
    }
    $organismos_relaxed = array();
    if($num_organismos < 6 && !empty($texto) && ( !empty($ambitos) || !empty($paises_ids) || !empty($ciudad) || $categoria_id ) ) {
        if($num_organismos <6 && !empty($texto)) {
            $args_texto_sin_filtros = array(
                'post_type'      => 'organismo',
                'posts_per_page' => 6 - $num_organismos, 
                'post_status'    => 'publish',
                'lang'           => '', 
                's'              => $texto,
                'relevanssi'     => true,
                'post__not_in'   => wp_list_pluck($organismos, 'ID')
            );
                if ($categoria_meta_query) {
                    $args_texto_sin_filtros['meta_query'] = array(
                        'relation' => 'AND',
                        $categoria_meta_query
                    );
                }
            $query_texto_sin_filtros = new WP_Query($args_texto_sin_filtros);
            $organismos_relaxed = $query_texto_sin_filtros->posts;
            $num_organismos = $num_organismos + count($organismos_relaxed);
        }

        if($num_organismos < 6 && !empty($texto) && ( !empty($ambitos) || !empty($paises_ids) || !empty($ciudad) ) ) {
            $args_sin_texto = $args;
            unset($args_sin_texto['s']);
            unset($args_sin_texto['relevanssi']);
            $args_sin_texto['posts_per_page'] = 6 - $num_organismos; 
                $args_sin_texto['post__not_in'] = wp_list_pluck(array_merge($organismos, $organismos_relaxed), 'ID');
            
            $query_sin_texto = new WP_Query($args_sin_texto);
                $organismos_relaxed = array_merge($organismos_relaxed, $query_sin_texto->posts);
                $num_organismos= $num_organismos + count($query_sin_texto->posts);
        }
        
        if($num_organismos <6 && ( !empty($ambitos) || !empty($paises_ids) || !empty($ciudad) ) ) {
            $args_relaxed = isset($args_sin_texto) ? $args_sin_texto : $args;
            if ($categoria_meta_query && !empty($meta_query_non_category)) {
                $other_filters = array();
                foreach ($meta_query_non_category as $k => $v) {
                    if ($k === 'relation') continue;
                    $other_filters[] = $v;
                }

                $new_meta = array('relation' => 'AND');
                $new_meta[] = $categoria_meta_query;
                if (!empty($other_filters)) {
                    $or_block = array_merge(array('relation' => 'OR'), $other_filters);
                    $new_meta[] = $or_block;
                }
                $args_relaxed['meta_query'] = $new_meta;
            } else {
                if (isset($args_relaxed['meta_query'])) {
                    $args_relaxed['meta_query']['relation'] = 'OR';
                }
            }

            $args_relaxed['posts_per_page'] = 6 - $num_organismos;
            $args_relaxed['post__not_in'] = wp_list_pluck(array_merge($organismos, $organismos_relaxed), 'ID');
            $query_relaxed = new WP_Query($args_relaxed);
            $organismos_or = $query_relaxed->posts;
            $organismos_relaxed = array_merge($organismos_relaxed, $organismos_or);
            $num_organismos = $num_organismos + count($organismos_or);
        }
    }
    if (!empty($organismos) || !empty($organismos_relaxed)) {
        ob_start();
        if (!empty($organismos)) {
            $exact_title = neema_translate('Resultados de búsqueda', 'Servicios de Apoyo');
            echo '<h2 class="organismos-title">' . esc_html($exact_title) . '</h2>';
            foreach ($organismos as $organismo) {
                neema_render_organismo($organismo->ID);
            }
        }
        if (!empty($organismos_relaxed)) {
            $approx_title = neema_translate('Resultados aproximados', 'General');
            echo '<h2 class="organismos-title">' . esc_html($approx_title) . '</h2>';
            foreach ($organismos_relaxed as $organismo) {
                neema_render_organismo($organismo->ID);
            }
        }

        $html = ob_get_clean();
        $total_count = $num_organismos;

        wp_send_json_success(array(
            'html'  => $html,
            'count' => $total_count
        ));
    } else {
        if(!isset($no_results_msg)){
            $no_results_msg = neema_translate('No se encontraron organismos con los criterios seleccionados.', 'Servicios de Apoyo');
        }
        
        wp_send_json_success(array(
            'html'  => '<p class="no-recursos-msg">' . esc_html($no_results_msg) . '</p>',
            'count' => 0
        ));
    }
}



add_filter('relevanssi_match', 'neema_pesos_custom_fields', 10, 2);
function neema_pesos_custom_fields($match, $query) {

    $weight = $match->weight;
    $title_factor = 5; 
    $description_factor = 3;

    $titulos = ['nombre_organismo_es', 'nombre_organismo_en', 'nombre_organismo_fr'];
    $descripciones = ['description_es', 'description_en', 'description_fr'];

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




