<?php
/**
 * Limpieza de widgets por defecto del Dashboard
 * Para usuarios con rol neema_admin y gestor_contenido
 */

if (!defined('ABSPATH')) exit;

function neema_remove_dashboard_widgets() {
    $user = wp_get_current_user();
    $allowed_roles = array('neema_admin', 'gestor_contenido');
    
    if (!array_intersect($allowed_roles, (array) $user->roles)) return;
    
    global $wp_meta_boxes;
    
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');      
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');       
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');      
    remove_meta_box('dashboard_primary', 'dashboard', 'side');          
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');    
    
    remove_meta_box('wpforms_reports_widget_lite', 'dashboard', 'normal');  
    remove_meta_box('wp_mail_smtp_reports_widget_lite', 'dashboard', 'normal'); 
    remove_meta_box('wp_mail_smtp_widget_report', 'dashboard', 'normal');  
}
add_action('wp_dashboard_setup', 'neema_remove_dashboard_widgets', 999);
