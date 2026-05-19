<?php
function neema_set_default_preferencias( $user_id ) {
    $preferencias = array(
        '_recurso_paises' => array(),        
        '_recurso_tipo' => array(),   
        '_recurso_tematicas' => array(),
        '_recurso_regiones' => array(),
    );
    update_user_meta( $user_id, 'preferencias', $preferencias );
}
add_action( 'user_register', 'neema_set_default_preferencias' );

function neema_get_preferencias( $user_id ) {
    $prefs = get_user_meta( $user_id, 'preferencias', true );
    if ( empty( $prefs ) || ! is_array( $prefs ) ) {
        return array(
            '_recurso_paises' => array(),
            '_recurso_tipo' => array(),
            '_recurso_tematicas' => array(),
            '_recurso_regiones' => array(),
        );
    }
    return $prefs;
}

function neema_update_preferencia( $user_id, $recurso_id, $incrementar = true ) {
    /*
    $user_id: ID del usuario
    $recurso_id: Id del recurso que se va a añadir o eliminar de favoritos
    $incrementar: booleano, true para añadir, false para eliminar
    */
    $incremento = $incrementar ? 1 : -1;
    $prefs = neema_get_preferencias( $user_id );
    
    $preferencias = array('_recurso_paises', '_recurso_tipo', '_recurso_tematicas', '_recurso_regiones');
    
    foreach ( $preferencias as $preferencia ) {
        $terms = array();
        
        $terms = (array) get_post_meta( $recurso_id, $preferencia,true);
        foreach ( $terms as $term_id ) {
            if( $preferencia !== '_recurso_paises'){
                $term_es_id = pll_get_term( $term_id, 'es' );
                if ( $term_es_id ) {
                    $term_id = $term_es_id;
                }
            }

            if ( ! isset( $prefs[ $preferencia ][ $term_id ] ) ) {
                $prefs[ $preferencia ][ $term_id ] = 0;
            }

            $prefs[ $preferencia ][ $term_id ] += $incremento;
            if ( $prefs[ $preferencia ][ $term_id ] <= 0 ) {
                unset( $prefs[ $preferencia ][ $term_id ] );
            }
        }
    }

    update_user_meta( $user_id, 'preferencias', $prefs );
}






