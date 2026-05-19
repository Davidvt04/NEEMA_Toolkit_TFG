<?php
/* ==========================================================
   Funciones Helper para el Buscador de Recursos
   ========================================================== */

/**
 * Obtiene todos los países del CPT
 */
function neema_get_all_paises() {
    $paises = get_posts(array(
        'post_type'      => 'pais',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'post_status'    => 'publish'
    ));

    $resultado = array();
    
    foreach ($paises as $pais) {
        $key = get_post_meta($pais->ID, '_pais_key', true);
        if (empty($key)) {
            $key = 'pais_' . $pais->ID;
        }
        
        $resultado[] = array(
            'id'    => $pais->ID,
            'key'   => $key,
            'title' => $pais->post_title
        );
    }

    return $resultado;
}

/**
 * Obtiene todos los tipos de recurso del CPT
 */
function neema_get_all_tipos_recurso() {
    $tipos_es = get_posts(array(
        'post_type'      => 'tipo-recurso',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'post_status'    => 'publish',
        'lang'           => 'es'
    ));

    $resultado = array();
    $lang_actual = function_exists('pll_current_language') ? pll_current_language() : 'es';
    
    foreach ($tipos_es as $tipo_es) {
        $key = get_post_meta($tipo_es->ID, '_tipo_recurso_key', true);
        if ($key) {
            $tipo_traducido_id = function_exists('pll_get_post') ? pll_get_post($tipo_es->ID, $lang_actual) : $tipo_es->ID;
            $titulo = $tipo_traducido_id ? get_the_title($tipo_traducido_id) : $tipo_es->post_title;
            
            $resultado[] = array(
                'id'    => $tipo_es->ID,
                'key'   => $key,
                'title' => $titulo
            );
        }
    }

    return $resultado;
}

/**
 * Obtiene todas las temáticas del CPT
 */
function neema_get_all_tematicas() {
    $tematicas_es = get_posts(array(
        'post_type'      => 'tematica',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'post_status'    => 'publish',
        'lang'           => 'es'
    ));

    $resultado = array();
    $lang_actual = function_exists('pll_current_language') ? pll_current_language() : 'es';
    
    foreach ($tematicas_es as $tematica_es) {
        $key = get_post_meta($tematica_es->ID, '_tematica_key', true);
        if ($key) {
            $tematica_traducida_id = function_exists('pll_get_post') ? pll_get_post($tematica_es->ID, $lang_actual) : $tematica_es->ID;
            $titulo = $tematica_traducida_id ? get_the_title($tematica_traducida_id) : $tematica_es->post_title;
            
            $resultado[] = array(
                'id'    => $tematica_es->ID,
                'key'   => $key,
                'title' => $titulo
            );
        }
    }

    return $resultado;
}

/**
 * Obtiene todas las regiones del CPT
 */
function neema_get_all_regiones() {
    $regiones_es = get_posts(array(
        'post_type'      => 'region',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'post_status'    => 'publish',
        'lang'           => 'es'
    ));

    $resultado = array();
    $lang_actual = function_exists('pll_current_language') ? pll_current_language() : 'es';
    
    foreach ($regiones_es as $region_es) {
        $key = get_post_meta($region_es->ID, '_region_key', true);
        if ($key) {
            $region_traducida_id = function_exists('pll_get_post') ? pll_get_post($region_es->ID, $lang_actual) : $region_es->ID;
            $titulo = $region_traducida_id ? get_the_title($region_traducida_id) : $region_es->post_title;
            
            $resultado[] = array(
                'id'    => $region_es->ID,
                'key'   => $key,
                'title' => $titulo
            );
        }
    }

    return $resultado;
}

/**
 * Normaliza el texto de ciudad/localidad
 * Igual que en el CPT de organismo: Primera letra en mayúscula de cada palabra
 */
function neema_normalizar_ciudad($ciudad) {
    if (empty($ciudad)) {
        return '';
    }
    $ciudad = trim($ciudad);
    $ciudad = preg_replace('/\s+/', ' ', $ciudad);
    $ciudad = mb_convert_case($ciudad, MB_CASE_TITLE, 'UTF-8');
    return $ciudad;
}
