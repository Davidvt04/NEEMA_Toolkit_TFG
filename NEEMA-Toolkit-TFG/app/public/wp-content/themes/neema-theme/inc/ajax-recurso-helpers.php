<?php
/**
 * Helpers compartidos para búsquedas y filtros de recursos
 */

/**
 * Construye filtro para campos serializados (temáticas, regiones)
 */
function neema_build_serialized_filter($meta_key, $values) {
    if (empty($values)) return null;
    
    $query = array('relation' => 'OR');
    foreach ($values as $value) {
        $query[] = array(
            'key' => $meta_key,
            'value' => sprintf('"%s"', $value),
            'compare' => 'LIKE'
        );
    }
    return $query;
}

/**
 * Construye filtro para países
 */
function neema_build_paises_filter($paises) {
    if (empty($paises)) return null;
    $pais_ids = array_map(function($pais) {
        return intval(str_replace('pais_', '', $pais));
    }, $paises);
    $paises_query = array('relation' => 'OR');
    foreach ($pais_ids as $pais_id) {
        $paises_query[] = array(
            'key' => '_recurso_paises',
            'value' => sprintf('i:%d;', $pais_id),
            'compare' => 'LIKE'
        );
    }
    return $paises_query;
}

/**
 * Filtra recursos por texto libre en campos multiidioma
 */
function neema_filter_by_text($recursos, $texto) {
    if (empty($texto) || empty($recursos)) return $recursos;
    
    $texto_lower = strtolower($texto);
    $campos_busqueda = [
        '_recurso_titulo_es', '_recurso_titulo_en', '_recurso_titulo_fr',
        'descripcion_es', 'descripcion_en', 'descripcion_fr'
    ];
    
    return array_filter($recursos, function($recurso) use ($texto_lower, $campos_busqueda) {
        foreach ($campos_busqueda as $campo) {
            $valor = strtolower(get_post_meta($recurso->ID, $campo, true));
            if (strpos($valor, $texto_lower) !== false) {
                return true;
            }
        }
        return false;
    });
}

/**
 * Filtra recursos por regiones en PHP (más rápido que LIKE en SQL)
 */
function neema_filter_by_regiones($recursos, $regiones) {
    if (empty($regiones) || empty($recursos)) return $recursos;
    
    return array_filter($recursos, function($recurso) use ($regiones) {
        $recurso_regiones = get_post_meta($recurso->ID, '_recurso_regiones', true);
        if (!is_array($recurso_regiones)) return false;
        return !empty(array_intersect($regiones, $recurso_regiones));
    });
}

/**
 * Filtra recursos por temáticas en PHP (más rápido que LIKE en SQL)
 */
function neema_filter_by_tematicas($recursos, $tematicas) {
    if (empty($tematicas) || empty($recursos)) return $recursos;
    
    return array_filter($recursos, function($recurso) use ($tematicas) {
        $recurso_tematicas = get_post_meta($recurso->ID, '_recurso_tematicas', true);
        if (!is_array($recurso_tematicas)) return false;
        return !empty(array_intersect($tematicas, $recurso_tematicas));
    });
}

/**
 * Filtra recursos por países en PHP (más rápido que LIKE en SQL)
 */
function neema_filter_by_paises($recursos, $paises) {
    if (empty($paises) || empty($recursos)) return $recursos;
    $pais_ids = array_map(function($pais) {
        return intval(str_replace('pais_', '', $pais));
    }, $paises);
    
    return array_filter($recursos, function($recurso) use ($pais_ids) {
        $recurso_paises = get_post_meta($recurso->ID, '_recurso_paises', true);
        if (!is_array($recurso_paises)) return false;
        return !empty(array_intersect($pais_ids, $recurso_paises));
    });
}
