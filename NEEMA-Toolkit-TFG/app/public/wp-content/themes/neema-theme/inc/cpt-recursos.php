<?php
/**
 * Custom Post Type: Recursos - Archivo Principal
 * 
 * Este archivo coordina todos los módulos relacionados con el CPT Recursos
 * 
 * @package Neema
 */

/* ==========================================================
    Cargar módulos
   ========================================================== */

// Registro del CPT y capacidades
require_once get_template_directory() . '/inc/recursos/cpt-recursos-registro.php';

// Definición de meta boxes
require_once get_template_directory() . '/inc/recursos/cpt-recursos-metaboxes.php';

// Callbacks de meta boxes
require_once get_template_directory() . '/inc/recursos/cpt-recursos-callbacks.php';

// Funciones de guardado
require_once get_template_directory() . '/inc/recursos/cpt-recursos-guardado.php';

// Validación y errores
require_once get_template_directory() . '/inc/recursos/cpt-recursos-validacion.php';

// Revisión de recursos
require_once get_template_directory() . '/inc/recursos/revision-recursos.php';

// Preferencias de usuario para recursos
require_once get_template_directory() . '/inc/recursos/preferencias-recursos.php';

// Recomendar recursos
require_once get_template_directory() . '/inc/recursos/recomendar-recursos.php';


