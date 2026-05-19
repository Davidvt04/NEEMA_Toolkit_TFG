<?php
/* ==========================================================
   Componente: Recurso Preview
   ========================================================== */
function neema_render_recurso($recurso_id) {
    if ( ! $recurso_id ) return;
    $idioma = function_exists('pll_current_language') ? pll_current_language() : 'es';
    $titulo = get_post_meta($recurso_id, '_recurso_titulo_' . $idioma, true);
    if (empty($titulo)) {
        $titulo = get_post_meta($recurso_id, '_recurso_titulo_es', true);
    }
    $tipo_key = get_post_meta($recurso_id, '_recurso_tipo', true);
    $tipo_icon = '';
    if ( $tipo_key ) {
        $tipo_posts = get_posts(array(
            'post_type'      => 'tipo-recurso',
            'meta_query'     => array(
                array(
                    'key'     => '_tipo_recurso_key',
                    'value'   => $tipo_key,
                    'compare' => '='
                )
            ),
            'posts_per_page' => 1,
            'lang'           => ''
        ));
        if ( !empty($tipo_posts) ) {
            $tipo_icon = get_the_post_thumbnail($tipo_posts[0]->ID, 'thumbnail');
        }
    }
    $languages_list= [];
    $archivo_en = get_field('archivo_en', $recurso_id);
    if( $archivo_en ) {
        $languages_list[] = 'ENG';
    }
    $archivo_fr = get_field('archivo_fr', $recurso_id);
    if( $archivo_fr ) {
        $languages_list[] = 'FRA';
    }
    $archivo_es = get_field('archivo_es', $recurso_id);
    if( $archivo_es ) {
        $languages_list[] = 'ESP';
    }
    if( empty($languages_list) ) {
        $languages_list[] = 'ENG';
        $languages_list[] = 'FRA';
        $languages_list[] = 'ESP';
    }
    $data = array(
        'title'     => $titulo,
        'thumbnail' => get_the_post_thumbnail($recurso_id, 'medium'),
        'link'      => get_permalink($recurso_id),
        'tipo_icon'  => $tipo_icon,
        'languages' => $languages_list
    );
    $fav_ids = get_query_var( 'user_favorite_ids'); 
    $is_fav = in_array( $recurso_id, $fav_ids );
    $data['id'] = $recurso_id; 
    $data['favorited'] = $is_fav;

    set_query_var('recurso_data', $data);
    get_template_part('template-parts/recurso', 'preview');
}

/* ==========================================================
   Componente: Organismo Preview
   ========================================================== */
function neema_render_organismo($organismo_id) {
    if (!$organismo_id) return;
    $idioma = function_exists('pll_current_language') ? pll_current_language() : 'es';
    $titulo = get_field('nombre_organismo_' . $idioma, $organismo_id);
    $descripcion = get_field('description_' . $idioma, $organismo_id);
    $ambito = get_post_meta($organismo_id, '_organismo_ambito', true);
    $paises_ids = get_post_meta($organismo_id, '_organismo_paises', true);
    $ciudad = get_post_meta($organismo_id, '_organismo_ciudad', true);
    $paises_nombres = array();
    if (is_array($paises_ids) && !empty($paises_ids)) {
        foreach ($paises_ids as $pais_id) {
            $pais_post = get_post($pais_id);
            if ($pais_post) {
                $paises_nombres[] = $pais_post->post_title;
            }
        }
    }
    $paises_string = !empty($paises_nombres) ? implode(', ', $paises_nombres) : '';
    $correo = get_field('correo_electronico', $organismo_id);
    $pagina_web = get_field('pagina_web', $organismo_id);
    $correo = $correo ? $correo : '';
    $pagina_web = $pagina_web ? $pagina_web : '';
    $data = array(
        'title'       => $titulo,
        'thumbnail'   => get_the_post_thumbnail($organismo_id, 'medium'),
        'description' => $descripcion,
        'ambito'      => $ambito,
        'paises'      => $paises_string,
        'ciudad'      => $ciudad,
        'correo'      => $correo,
        'pagina_web'  => $pagina_web,
    );
    set_query_var('organismo_data', $data);
    get_template_part('template-parts/organismo', 'preview');
}
