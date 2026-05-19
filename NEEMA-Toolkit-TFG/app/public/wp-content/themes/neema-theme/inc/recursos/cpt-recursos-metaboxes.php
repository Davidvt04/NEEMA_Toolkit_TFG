<?php
/**
 * Definición de Meta Boxes para el CPT Recursos
 * 
 * @package Neema
 */

/* ==========================================================
    Registro de Meta Boxes
   ========================================================== */

/**
 * Añadir meta boxes al CPT Recursos
 */
function neema_recursos_add_meta_boxes() {
    add_meta_box(
        'neema_recursos_titulos',
        'Títulos en otros idiomas',
        'neema_recursos_titulos_callback',
        'recurso',
        'normal',
        'high'
    );
    
    add_meta_box(
        'neema_recursos_relaciones',
        'Relaciones del Recurso',
        'neema_recursos_relaciones_callback',
        'recurso',
        'normal',
        'high'
    );
    
}
add_action('add_meta_boxes', 'neema_recursos_add_meta_boxes');
