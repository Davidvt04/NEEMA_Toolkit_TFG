<?php
/* ==========================================================
   Custom Post Type: Design
========================================================== */
function neema_register_design_cpt() {
    $labels = array(
        'name'               => 'Diseños',
        'singular_name'      => 'Diseño',
        'add_new'            => 'Añadir nuevo',
        'add_new_item'       => 'Añadir nuevo diseño',
        'edit_item'          => 'Editar diseño',
        'new_item'           => 'Nuevo diseño',
        'view_item'          => 'Ver diseño',
        'search_items'       => 'Buscar diseños',
        'not_found'          => 'No se encontraron diseños',
        'not_found_in_trash' => 'No hay diseños en la papelera',
        'menu_name'          => 'Diseños'
    );

    $args = array(
        'labels'        => $labels,
        'public'        => true,
        'supports'      => array('title', 'thumbnail'),
        'menu_icon'     => 'dashicons-admin-customizer',
        'has_archive'   => true,
        'rewrite'       => array('slug' => 'diseños', 'with_front' => false),
    );

    register_post_type('design', $args);
}
add_action('init', 'neema_register_design_cpt');


function neema_display_designs() {
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : '';
    $args = array(
        'post_type'      => 'design',
        'posts_per_page' => -1,
        'lang'           => $current_lang,
    );
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        echo '<div class="designs-container">';
        while ($query->have_posts()) {
            $query->the_post();
            $thumb = get_the_post_thumbnail(get_the_ID(), 'thumbnail');
            $title = get_the_title();
            $archivo = get_field('archivo_asociado');
            $archivo_url = '';
            if ($archivo) {
                if (is_array($archivo) && isset($archivo['url'])) {
                    $archivo_url = $archivo['url'];
                } elseif (is_numeric($archivo)) {
                    $archivo_url = wp_get_attachment_url($archivo);
                } else {
                    $archivo_url = $archivo;
                }
            }
            if ($archivo_url) {
                echo '<a href="' . esc_url($archivo_url) . '" class="design-link" download target="_blank">';
            } else {
                echo '<span class="design-link disabled" title="No hay archivo disponible">';
            }
            echo '<div class="design">';
            if ($thumb) {
                echo '<div class="design-thumb">' . $thumb . '</div>';
            }
            echo '<h3 class="design-title">' . esc_html($title) . '</h3>';
            echo '</div>';
            if ($archivo_url) {
                echo '</a>';
            } else {
                echo '</span>';
            }
        }
        echo '</div>';
        wp_reset_postdata();
    } else {
        echo '<p>' . pll__('No se encontraron propuestas de diseños.', 'Resiliencia Alimentaria y Nutricional') . '</p>';
    }
}

?>
