<?php
/* ==========================================================
    Widgets personalizados para el Dashboard
   
    Este archivo carga todos los widgets desde la carpeta dashboard-widgets/   
   ========================================================== */

if (!defined('ABSPATH')) exit;
$dashboard_widgets_dir = get_template_directory() . '/inc/dashboard-widgets/';
$widget_files = array(
    'remove-default-widgets.php',
    'users-distribution-widget.php',
    'language-distribution-widget.php',
    'top-recursos-widget.php',
    'top-gestores-widget.php',
    'recursos-caracteristicas-widget.php',
);
foreach ($widget_files as $widget_file) {
    $file_path = $dashboard_widgets_dir . $widget_file;
    if (file_exists($file_path)) {
        require_once $file_path;
    }
}

