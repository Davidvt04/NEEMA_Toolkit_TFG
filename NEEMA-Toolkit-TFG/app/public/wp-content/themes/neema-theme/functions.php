<?php
/**
 * NEEMA Theme Functions
 * Autor: David Valencia Toscano
 * Version: 1.0
 */

// Cargar archivos modulares
require_once get_template_directory() . '/inc/theme-setup.php';
require_once get_template_directory() . '/inc/enqueue-scripts.php';
require_once get_template_directory() . '/inc/polylang-strings.php';
require_once get_template_directory() . '/inc/components.php';
require_once get_template_directory() . '/inc/users.php';
require_once get_template_directory() . '/inc/dashboard-widgets.php';
require_once get_template_directory() . '/inc/language-helpers.php';
require_once get_template_directory() . '/inc/ajax-cargar-mas-recursos.php';
require_once get_template_directory() . '/inc/ajax-buscar-recursos.php';
require_once get_template_directory() . '/inc/ajax-cargar-mas-organismos.php';
require_once get_template_directory() . '/inc/ajax-buscar-organismos.php';
require_once get_template_directory() . '/inc/buscador-helpers.php';
require_once get_template_directory() . '/inc/recursos-favoritos.php';

// Registro de CPTs
require_once get_template_directory() . '/inc/cpt-miembros.php';
require_once get_template_directory() . '/inc/cpt-modulos.php';
require_once get_template_directory() . '/inc/cpt-recursos.php';
require_once get_template_directory() . '/inc/cpt-paises.php';
require_once get_template_directory() . '/inc/cpt-tipo-recurso.php';
require_once get_template_directory() . '/inc/cpt-tematicas.php';
require_once get_template_directory() . '/inc/cpt-regiones.php';
require_once get_template_directory() . '/inc/cpt-guia-introductoria.php';
require_once get_template_directory() . '/inc/cpt-categorias-organismos.php';
require_once get_template_directory() . '/inc/cpt-organismos.php';
require_once get_template_directory() . '/inc/cpt-design.php';

