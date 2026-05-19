<?php
/**
 * Custom Post Type: Guía Introductoria
 * 
 * Gestiona la guía introductoria en los 3 idiomas (ES, EN, FR)
 * 
 * @package Neema
 */

/* ==========================================================
    REGISTRO DEL CPT
   ========================================================== */
function neema_register_guia_introductoria() {
    $labels = array(
        'name'                  => 'Guía Introductoria',
        'singular_name'         => 'Guía',
        'menu_name'             => 'Guía Introductoria',
        'add_new'               => 'Añadir nueva',
        'add_new_item'          => 'Añadir nueva guía',
        'edit_item'             => 'Editar guía',
        'new_item'              => 'Nueva guía',
        'view_item'             => 'Ver guía',
        'search_items'          => 'Buscar guías',
        'not_found'             => 'No se encontraron guías',
        'not_found_in_trash'    => 'No hay guías en la papelera'
    );

    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'has_archive'           => false,
        'show_in_menu'          => true,
        'menu_icon'             => 'dashicons-superhero',
        'menu_position'         => 25,
        'supports'              => array('title'),
        'show_in_rest'          => true,
        'rewrite'               => array('slug' => 'guia-introductoria'),
    );

    register_post_type('guia-intro', $args);
}
add_action('init', 'neema_register_guia_introductoria');

/* ==========================================================
    META BOXES
   ========================================================== */
function neema_add_guia_meta_boxes() {
    add_meta_box(
        'guia_archivos',
        'Archivos PDF de la Guía (3 idiomas) - OBLIGATORIOS',
        'neema_guia_archivos_callback',
        'guia-intro',
        'normal',
        'high'
    );
}

/* ==========================================================
    COLUMNAS PERSONALIZADAS EN EL ADMIN
   ========================================================== */
function neema_guia_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = 'Título Interno';
    $new_columns['date'] = $columns['date'];
    return $new_columns;
}
add_filter('manage_guia-intro_posts_columns', 'neema_guia_columns');
/* ==========================================================
    LIMITAR A UNA SOLA GUÍA
   ========================================================== */
function neema_limitar_una_guia() {
    global $pagenow;
    $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
    if ($pagenow == 'post-new.php' && $post_type == 'guia-intro') {
        $guias_existentes = get_posts(array(
            'post_type' => 'guia-intro',
            'posts_per_page' => 1,
            'post_status' => array('publish', 'draft', 'pending', 'private')
        ));
        
        if (!empty($guias_existentes)) {
            wp_redirect(admin_url('post.php?post=' . $guias_existentes[0]->ID . '&action=edit&message=guia_existe'));
            exit;
        }
    }
}
add_action('admin_init', 'neema_limitar_una_guia');

function neema_guia_existe_notice() {
    if (isset($_GET['message']) && $_GET['message'] == 'guia_existe') {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><strong>ℹ️ Información:</strong> Solo puede existir una guía. Actualiza la guía existente en lugar de crear una nueva.</p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'neema_guia_existe_notice');

/* ==========================================================
    OCULTAR BOTÓN "AÑADIR NUEVA" SI YA EXISTE UNA GUÍA
   ========================================================== */
function neema_ocultar_boton_nueva_guia() {
    global $pagenow;
    $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
    
    if (($pagenow == 'edit.php' && $post_type == 'guia-intro')) {
        $guias_existentes = get_posts(array(
            'post_type' => 'guia-intro',
            'posts_per_page' => 1,
            'post_status' => array('publish', 'draft', 'pending', 'private')
        ));
        
        if (!empty($guias_existentes)) {
            echo '<style>.page-title-action { display: none !important; }</style>';
        }
    }
}
add_action('admin_head', 'neema_ocultar_boton_nueva_guia');
