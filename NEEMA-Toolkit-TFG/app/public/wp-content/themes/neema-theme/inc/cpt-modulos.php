<?php
/* ==========================================================
   Custom Post Type: Módulos
========================================================== */
function neema_register_modulos_cpt() {
    $labels = array(
        'name'               => 'Módulos',
        'singular_name'      => 'Módulo',
        'add_new'            => 'Añadir nuevo',
        'add_new_item'       => 'Añadir nuevo módulo',
        'edit_item'          => 'Editar módulo',
        'new_item'           => 'Nuevo módulo',
        'view_item'          => 'Ver módulo',
        'search_items'       => 'Buscar módulos',
        'not_found'          => 'No se encontraron módulos',
        'not_found_in_trash' => 'No hay módulos en la papelera',
        'menu_name'          => 'Módulos'
    );

    $args = array(
        'labels'        => $labels,
        'public'        => true,
        'supports'      => array('title', 'thumbnail', 'page-attributes'),
        'menu_icon'     => 'dashicons-welcome-learn-more',
        'has_archive'   => true,
        'rewrite'       => array('slug' => 'modulos', 'with_front' => false),
    );

    register_post_type('modulo', $args);
}
add_action('init', 'neema_register_modulos_cpt');

/* ==========================================================
   Campos personalizados para Módulos
========================================================== */
function neema_add_modulo_meta_boxes() {
    add_meta_box(
        'modulo_description_box',
        'Descripción',
        'neema_modulo_description_meta_box_callback',
        'modulo',
        'normal',
        'default'
    );
    add_meta_box(
        'modulo_objective_box',
        'Objetivo',
        'neema_modulo_objective_meta_box_callback',
        'modulo',
        'normal',
        'default'
    );
    add_meta_box(
        'modulo_skills_box',
        'Competencias a adquirir',
        'neema_modulo_skills_meta_box_callback',
        'modulo',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'neema_add_modulo_meta_boxes');

/* ==========================================================
   Callbacks para los campos
========================================================== */
function neema_modulo_description_meta_box_callback($post) {
    $value = get_post_meta($post->ID, '_modulo_description', true);
    wp_editor($value, 'modulo_description', array(
        'textarea_name' => 'modulo_description',
        'media_buttons' => false,
        'textarea_rows' => 6,
        'teeny'         => true,
        'tinymce'       => array(
            'toolbar1' => 'bold,italic,underline,link,unlink,bullist,numlist,forecolor',
            'toolbar2' => '',
        )
    ));
}
function neema_modulo_objective_meta_box_callback($post) {
    $value = get_post_meta($post->ID, '_modulo_objective', true);
    wp_editor($value, 'modulo_objective', array(
        'textarea_name' => 'modulo_objective',
        'media_buttons' => false,
        'textarea_rows' => 4,
        'teeny'         => true,
        'tinymce'       => array(
            'toolbar1' => 'bold,italic,underline,link,unlink,bullist,numlist,forecolor',
            'toolbar2' => '',
        )
    ));
}
function neema_modulo_skills_meta_box_callback($post) {
    $value = get_post_meta($post->ID, '_modulo_skills', true);
    wp_editor($value, 'modulo_skills', array(
        'textarea_name' => 'modulo_skills',
        'media_buttons' => false,
        'textarea_rows' => 4,
        'teeny'         => true,
        'tinymce'       => array(
            'toolbar1' => 'bold,italic,underline,link,unlink,bullist,numlist,forecolor',
            'toolbar2' => '',
        )
    ));
}
/* ==========================================================
   Guardar campos
========================================================== */
function neema_save_modulo_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (isset($_POST['modulo_description'])) {
        update_post_meta($post_id, '_modulo_description', wp_kses_post($_POST['modulo_description']));
    }
    if (isset($_POST['modulo_objective'])) {
        update_post_meta($post_id, '_modulo_objective', wp_kses_post($_POST['modulo_objective']));
    }
    if (isset($_POST['modulo_skills'])) {
        update_post_meta($post_id, '_modulo_skills', wp_kses_post($_POST['modulo_skills']));
    }
}
add_action('save_post', 'neema_save_modulo_meta');


/* ==========================================================
    Función para mostrar campo según idioma activo
    (Polylang gestiona la traducción del post)
========================================================== */
function neema_get_modulo_field($post_id, $field) {
    return get_post_meta($post_id, '_' . $field, true);
}

/* ==========================================================
    Mostrar módulos en listado
========================================================== */
function neema_display_modulos() {
    $args = array(
        'post_type'      => 'modulo',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC'
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        echo '<div class="modulos-container">';
        while ($query->have_posts()) {
            $query->the_post();

            $thumb = get_the_post_thumbnail(get_the_ID(), 'thumbnail');
            $title = get_the_title();
            $permalink = get_permalink();

            echo '<a href="' . esc_url($permalink) . '" class="modulo-link">';
            echo '<div class="modulo">';
            if ($thumb) echo '<div class="modulo-thumb">' . $thumb . '</div>';
            echo '<h3 class="modulo-title">' . esc_html($title) . '</h3>';
            echo '</div>';
            echo '</a>';
        }
        echo '</div>';
        wp_reset_postdata();
    } else {
        echo '<p>No hay módulos creados.</p>';
    }
}


/* ==========================================================
   Auto-generar Key desde el título en español
   ========================================================== */
function neema_modulo_auto_generate_key($post_id, $post, $update) {
    if ($post->post_type !== 'modulo') {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    $spanish_id = $post_id;
    $spanish_title = $post->post_title;
    if (function_exists('pll_get_post') && function_exists('pll_get_post_language')) {
        $current_lang = pll_get_post_language($post_id);
        if ($current_lang !== 'es') {
            $spanish_version = pll_get_post($post_id, 'es');
            if ($spanish_version) {
                $spanish_id = $spanish_version;
                $spanish_post = get_post($spanish_version);
                if ($spanish_post) {
                    $spanish_title = $spanish_post->post_title;
                }
            }
        }
    }
    if (!empty($spanish_title)) {
        $key = remove_accents($spanish_title);
        $key = strtolower($key);
        $key = preg_replace('/[^a-z0-9]+/', '-', $key);
        $key = trim($key, '-');
        update_post_meta($spanish_id, '_modulo_key', $key);
    }
}
add_action('save_post', 'neema_modulo_auto_generate_key', 10, 3);



?>

