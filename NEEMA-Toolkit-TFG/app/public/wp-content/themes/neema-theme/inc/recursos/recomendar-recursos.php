<?php

function neema_cargar_recursos($categoria, $modulo, $total_recursos = 6, $exclude_ids = array()) {

    if ($total_recursos < 1) {
        return array();
    }

    // FIX IMPORTANTE: CASO PEQUEÑO (TEST)
    if ($total_recursos < 6) {

        $recursos = neema_get_recurso_aleatorio(
            $categoria,
            $modulo,
            $total_recursos,
            $exclude_ids
        );

        // asegurar salida tipo WP_Post (stdClass con ID)
        return array_map(function ($r) {
            return (object) $r;
        }, $recursos);
    }

    $user_id = is_user_logged_in() ? get_current_user_id() : 0;

    $exclude_hash = md5(serialize($exclude_ids));

    $cache_key = 'neema_recursos_' . $user_id . '_' . $categoria . '_' . $modulo . '_' . $total_recursos . '_' . $exclude_hash;

    $cached_results = get_transient($cache_key);

    if (false !== $cached_results && is_array($cached_results)) {
        return $cached_results;
    }

    $resultados = array();

    if (empty($exclude_ids)) {
        $exclude_ids = array(0);
    }

    /*
    ==========================================================
    RECOMENDADOS
    ==========================================================
    */
    if (is_user_logged_in()) {

        $num_guardados = count_recursos_favoritos_usuario($user_id);

        if ($num_guardados > 0) {

            $num_a_recomendar = min($num_guardados, 4);

            $recomendados = neema_recomendar_recursos(
                $categoria,
                $modulo,
                $num_a_recomendar
            );

            $resultados = array_merge($resultados, $recomendados);

            $exclude_ids = array_merge(
                $exclude_ids,
                wp_list_pluck($recomendados, 'ID')
            );

            $total_recursos -= count($recomendados);
        }
    }

    /*
    ==========================================================
    RESTO ALEATORIOS
    ==========================================================
    */
    if ($total_recursos > 0) {

        $aleatorios = neema_get_recurso_aleatorio(
            $categoria,
            $modulo,
            $total_recursos,
            $exclude_ids
        );

        $resultados = array_merge($resultados, $aleatorios);
    }

    set_transient($cache_key, $resultados, 15 * MINUTE_IN_SECONDS);

    return $resultados;
}


/* ==========================================================
   RECOMENDADOR
========================================================== */

function neema_recomendar_recursos($categoria, $modulo, $num_recomendados) {

    $user_id = get_current_user_id();
    $preferencias = neema_get_preferencias($user_id);

    $args = array(
        'post_type' => 'recurso',
        'posts_per_page' => 30,
        'orderby' => 'rand',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => '_recurso_categoria',
                'value' => $categoria,
                'compare' => '=',
            ),
            array(
                'key' => '_recurso_modulo',
                'value' => $modulo,
                'compare' => '=',
            ),
        ),
    );

    $query = new WP_Query($args);
    $todos_recursos = $query->posts;

    $recursos_puntuados = array();

    foreach ($todos_recursos as $recurso) {

        $puntuacion = 0;
        $num_preferencia_aplicada = 0;

        foreach ($preferencias as $clave => $valores) {

            $valores_recurso = (array) get_post_meta($recurso->ID, $clave, true);

            foreach ($valores_recurso as $valor_recurso) {

                if (isset($valores[$valor_recurso])) {

                    $num_preferencia_aplicada++;
                    $puntuacion += $valores[$valor_recurso];
                }
            }
        }

        if ($num_preferencia_aplicada > 0) {
            $puntuacion = $puntuacion / $num_preferencia_aplicada;
        }

        $recursos_puntuados[] = array(
            'recurso' => $recurso,
            'puntuacion' => $puntuacion,
        );
    }

    usort($recursos_puntuados, function ($a, $b) {
        return $b['puntuacion'] <=> $a['puntuacion'];
    });

    $recursos_recomendados = array_slice($recursos_puntuados, 0, $num_recomendados);

    return array_map(function ($item) {
        return $item['recurso'];
    }, $recursos_recomendados);
}


/* ==========================================================
   ALEATORIOS
========================================================== */

function neema_get_recurso_aleatorio($categoria, $modulo, $num_recursos, $exclude_ids = array()) {

    if (empty($exclude_ids)) {
        $exclude_ids = array(0);
    }

    $args = array(
        'post_type' => 'recurso',
        'posts_per_page' => $num_recursos,
        'orderby' => 'rand',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => '_recurso_categoria',
                'value' => $categoria,
                'compare' => '=',
            ),
            array(
                'key' => '_recurso_modulo',
                'value' => $modulo,
                'compare' => '=',
            ),
        ),
        'post__not_in' => $exclude_ids,
    );

    $query = new WP_Query($args);

    return $query->posts;
}


/* ==========================================================
   TOTAL
========================================================== */

function neema_num_total_recursos($categoria, $modulo) {

    $args = array(
        'post_type' => 'recurso',
        'posts_per_page' => -1,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => '_recurso_categoria',
                'value' => $categoria,
                'compare' => '=',
            ),
            array(
                'key' => '_recurso_modulo',
                'value' => $modulo,
                'compare' => '=',
            ),
        ),
    );

    $query = new WP_Query($args);

    return $query->found_posts;
}


/* ==========================================================
   CACHE CLEAN
========================================================== */

function neema_clear_recursos_cache($user_id = null) {

    global $wpdb;

    if ($user_id === null) {
        $user_id = get_current_user_id();
    }

    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->options}
            WHERE option_name LIKE %s",
            '_transient_neema_recursos_' . $user_id . '_%'
        )
    );

    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->options}
            WHERE option_name LIKE %s",
            '_transient_timeout_neema_recursos_' . $user_id . '_%'
        )
    );
}