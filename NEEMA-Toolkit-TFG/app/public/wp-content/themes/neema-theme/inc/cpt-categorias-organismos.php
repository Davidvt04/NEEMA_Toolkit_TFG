<?php
/* ==========================================================
   Custom Post Type: Categorías Organismos
========================================================== */
function neema_register_categorias_organismos_cpt() {
    $labels = array(
        'name'               => 'Categorías Organismos',
        'singular_name'      => 'Categoría Organismo',
        'add_new'            => 'Añadir nueva',
        'add_new_item'       => 'Añadir nueva categoría',
        'edit_item'          => 'Editar categoría',
        'new_item'           => 'Nueva categoría',
        'view_item'          => 'Ver categoría',
        'search_items'       => 'Buscar categorías',
        'not_found'          => 'No se encontraron categorías',
        'not_found_in_trash' => 'No hay categorías en la papelera',
        'menu_name'          => 'Categorías Organismos'
    );

    $args = array(
        'labels'        => $labels,
        'public'        => true,
        'supports'      => array('title', 'thumbnail', 'page-attributes'),
        'menu_icon'     => 'dashicons-networking',
        'has_archive'   => false,
        'rewrite'       => array('slug' => 'categoria-organismo', 'with_front' => false),
    );

    register_post_type('categoria-organismo', $args);
}
add_action('init', 'neema_register_categorias_organismos_cpt');

/* ==========================================================
    Campo personalizado para Categorías Organismos
========================================================== */
function neema_add_categoria_organismo_meta_boxes() {
    add_meta_box(
        'categoria_organismo_description_box',
        'Descripción',
        'neema_categoria_organismo_description_meta_box_callback',
        'categoria-organismo',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'neema_add_categoria_organismo_meta_boxes');

/* ==========================================================
    Callback para el campo de descripción
========================================================== */
function neema_categoria_organismo_description_meta_box_callback($post) {
    $value = get_post_meta($post->ID, '_categoria_organismo_description', true);
    wp_editor($value, 'categoria_organismo_description', array(
        'textarea_name' => 'categoria_organismo_description',
        'media_buttons' => false,
        'textarea_rows' => 6,
        'teeny'         => true,
        'tinymce'       => array(
            'toolbar1' => 'bold,italic,underline,link,unlink,bullist,numlist,forecolor',
            'toolbar2' => '',
        )
    ));
}

/* ==========================================================
    Guardar campo de descripción
========================================================== */
function neema_save_categoria_organismo_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (isset($_POST['categoria_organismo_description'])) {
        update_post_meta($post_id, '_categoria_organismo_description', wp_kses_post($_POST['categoria_organismo_description']));
    }
}
add_action('save_post', 'neema_save_categoria_organismo_meta');

/* ==========================================================
    Mostrar categorías organismos en listado
========================================================== */
function neema_display_categorias_organismos() {
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : '';
    
    $args = array(
        'post_type'      => 'categoria-organismo',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'lang'           => $current_lang,
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        echo '<div class="servicios-container">';
        while ($query->have_posts()) {
            $query->the_post();

            $thumb = get_the_post_thumbnail(get_the_ID(), 'thumbnail');
            $title = get_the_title();
            $permalink = get_permalink();

            echo '<a href="' . esc_url($permalink) . '" class="servicio-link">';
            echo '<div class="servicio">';
            if ($thumb) echo '<div class="servicio-thumb">' . $thumb . '</div>';
            echo '<h3 class="servicio-title">' . esc_html($title) . '</h3>';
            echo '</div>';
            echo '</a>';
        }
        echo '</div>';
        wp_reset_postdata();
    } else {
        echo '<p>' . pll__('No se encontraron categorías.', 'Servicios de Apoyo') . '</p>';
    }
}

?>
