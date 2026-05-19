<?php
/* ==========================================================
   Custom Post Type: Organismos
========================================================== */
function neema_register_organismos_cpt() {
    $labels = array(
        'name'               => 'Organismos',
        'singular_name'      => 'Organismo',
        'add_new'            => 'Añadir nuevo',
        'add_new_item'       => 'Añadir nuevo organismo',
        'edit_item'          => 'Editar organismo',
        'new_item'           => 'Nuevo organismo',
        'view_item'          => 'Ver organismo',
        'search_items'       => 'Buscar organismos',
        'not_found'          => 'No se encontraron organismos',
        'not_found_in_trash' => 'No hay organismos en la papelera',
        'menu_name'          => 'Organismos'
    );

    $args = array(
        'labels'        => $labels,
        'public'        => true,
        'supports'      => array('title', 'thumbnail'),
        'menu_icon'     => 'dashicons-universal-access-alt',
        'has_archive'   => false,
        'rewrite'       => array('slug' => 'organismo', 'with_front' => false),
        'capability_type' => 'organismo',
        'capabilities'  => array(
            'edit_post'          => 'edit_organismo',
            'read_post'          => 'read_organismo',
            'delete_post'        => 'delete_organismo',
            'edit_posts'         => 'edit_organismos',
            'edit_others_posts'  => 'edit_others_organismos',
            'publish_posts'      => 'publish_organismos',
            'read_private_posts' => 'read_private_organismos',
            'delete_posts'       => 'delete_organismos',
            'delete_private_posts' => 'delete_private_organismos',
            'delete_published_posts' => 'delete_published_organismos',
            'delete_others_posts' => 'delete_others_organismos',
            'edit_private_posts' => 'edit_private_organismos',
            'edit_published_posts' => 'edit_published_organismos',
        ),
        'map_meta_cap'  => true,
    );

    register_post_type('organismo', $args);
}
add_action('init', 'neema_register_organismos_cpt');

/* ==========================================================
   Asignar capacidades de organismos al rol Administrator
========================================================== */
function neema_add_organismo_caps_to_administrator() {
    $admin = get_role('administrator');
    if ($admin) {
        $admin->add_cap('edit_organismo');
        $admin->add_cap('read_organismo');
        $admin->add_cap('delete_organismo');
        $admin->add_cap('edit_organismos');
        $admin->add_cap('edit_others_organismos');
        $admin->add_cap('publish_organismos');
        $admin->add_cap('read_private_organismos');
        $admin->add_cap('delete_organismos');
        $admin->add_cap('delete_private_organismos');
        $admin->add_cap('delete_published_organismos');
        $admin->add_cap('delete_others_organismos');
        $admin->add_cap('edit_private_organismos');
        $admin->add_cap('edit_published_organismos');
    }
}
add_action('init', 'neema_add_organismo_caps_to_administrator');

/* ==========================================================
   Campos personalizados para Organismos
========================================================== */
function neema_add_organismo_meta_boxes() {
    add_meta_box(
        'organismo_categoria_box',
        'Categoría de Organismo',
        'neema_organismo_categoria_meta_box_callback',
        'organismo',
        'normal',
        'high'
    );
    add_meta_box(
        'organismo_ambito_box',
        'Ámbito de Actuación',
        'neema_organismo_ambito_meta_box_callback',
        'organismo',
        'normal',
        'high'
    );
    add_meta_box(
        'organismo_paises_box',
        'País o Países de Operación',
        'neema_organismo_paises_meta_box_callback',
        'organismo',
        'normal',
        'high'
    );
    add_meta_box(
        'organismo_ciudad_box',
        'Ciudad / Localidad',
        'neema_organismo_ciudad_meta_box_callback',
        'organismo',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'neema_add_organismo_meta_boxes');

/* ==========================================================
   Callbacks para los campos
========================================================== */
function neema_organismo_categoria_meta_box_callback($post) {
    $selected_categoria = get_post_meta($post->ID, '_organismo_categoria', true);
    $categorias = get_posts(array(
        'post_type'      => 'categoria-organismo',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'lang'           => 'es'
    ));
    echo '<select name="organismo_categoria" id="organismo_categoria" style="width: 100%;" required>';
    echo '<option value="">-- Seleccionar Categoría (Obligatorio) --</option>';
    foreach ($categorias as $categoria) {
        $selected = ($selected_categoria == $categoria->ID) ? 'selected' : '';
        echo '<option value="' . esc_attr($categoria->ID) . '" ' . $selected . '>' . esc_html($categoria->post_title) . '</option>';
    }
    echo '</select>';
}

function neema_organismo_ambito_meta_box_callback($post) {
    $selected_ambito = get_post_meta($post->ID, '_organismo_ambito', true);
    $ambitos = array('Local', 'Nacional', 'Internacional');
    
    echo '<select name="organismo_ambito" id="organismo_ambito" style="width: 100%;">';
    echo '<option value="">-- Seleccionar Ámbito --</option>';
    foreach ($ambitos as $ambito) {
        $selected = ($selected_ambito == $ambito) ? 'selected' : '';
        echo '<option value="' . esc_attr($ambito) . '" ' . $selected . '>' . esc_html($ambito) . '</option>';
    }
    echo '</select>';
    
    echo '<p class="description">El campo de países es <strong>opcional</strong> para Internacional y <strong>obligatorio</strong> para Local y Nacional.</p>';
}

function neema_organismo_paises_meta_box_callback($post) {
    $selected_paises = get_post_meta($post->ID, '_organismo_paises', true);
    if (!is_array($selected_paises)) {
        $selected_paises = !empty($selected_paises) ? array($selected_paises) : array();
    }
    $paises = get_posts(array(
        'post_type'      => 'pais',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'lang'           => '' 
    ));

    echo '<div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fff;">';
    foreach ($paises as $pais) {
        $checked = in_array($pais->ID, $selected_paises) ? 'checked' : '';
        echo '<label style="display: block; margin-bottom: 8px;">';
        echo '<input type="checkbox" name="organismo_paises[]" value="' . esc_attr($pais->ID) . '" ' . $checked . '> ';
        echo esc_html($pais->post_title);
        echo '</label>';
    }
    echo '</div>';
    echo '<p class="description">Selecciona uno o varios países donde opera el organismo.</p>';
}

function neema_organismo_ciudad_meta_box_callback($post) {
    $ciudad = get_post_meta($post->ID, '_organismo_ciudad', true);
    $ambito = get_post_meta($post->ID, '_organismo_ambito', true);
    
    $display = ($ambito == 'Local') ? 'block' : 'none';
    
    echo '<div id="ciudad_wrapper" style="display: ' . $display . ';">';
    echo '<input type="text" name="organismo_ciudad" id="organismo_ciudad" value="' . esc_attr($ciudad) . '" style="width: 100%;" placeholder="Ej: Dakar, Saint-Louis">';
    echo '<p class="description">Solo visible cuando el ámbito es Local. Se normalizará automáticamente (Primera Letra En Mayúscula).</p>';
    echo '</div>';
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#organismo_ambito').on('change', function() {
            var ambito = $(this).val();
            if (ambito === 'Local') {
                $('#ciudad_wrapper').slideDown();
            } else {
                $('#ciudad_wrapper').slideUp();
            }
        });
    });
    </script>
    <?php
}

/* ==========================================================
   Guardar campos
========================================================== */
function neema_save_organismo_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (get_post_type($post_id) !== 'organismo') return;
    $errors = array();
    if (!has_post_thumbnail($post_id)) {
        $errors[] = 'La Imagen destacada es obligatoria.';
    }
    if (isset($_POST['organismo_categoria']) && !empty($_POST['organismo_categoria'])) {
        update_post_meta($post_id, '_organismo_categoria', sanitize_text_field($_POST['organismo_categoria']));
    } else {
        $errors[] = 'La Categoría del Organismo es obligatoria.';
    }
    if (isset($_POST['organismo_ambito'])) {
        $ambito = sanitize_text_field($_POST['organismo_ambito']);
        update_post_meta($post_id, '_organismo_ambito', $ambito);
        if (($ambito == 'Local' || $ambito == 'Nacional') && empty($_POST['organismo_paises'])) {
            $errors[] = 'El campo de países es obligatorio para ámbito Local y Nacional.';
        }
    }
    if (isset($_POST['organismo_paises']) && is_array($_POST['organismo_paises'])) {
        $paises = array_map('intval', $_POST['organismo_paises']);
        update_post_meta($post_id, '_organismo_paises', $paises);
    } else {
        update_post_meta($post_id, '_organismo_paises', array());
    }
    if (isset($_POST['organismo_ciudad'])) {
        $ciudad = sanitize_text_field($_POST['organismo_ciudad']);
        $ciudad = trim($ciudad);
        $ciudad = preg_replace('/\s+/', ' ', $ciudad); 
        $ciudad = mb_convert_case($ciudad, MB_CASE_TITLE, 'UTF-8');
        update_post_meta($post_id, '_organismo_ciudad', $ciudad);
    }
    if (!empty($errors)) {
        update_post_meta($post_id, '_neema_organismo_validation_errors', $errors);
        if (get_post_status($post_id) == 'publish') {
            wp_update_post(array(
                'ID'          => $post_id,
                'post_status' => 'draft'
            ));
        }
    } else {
        delete_post_meta($post_id, '_neema_organismo_validation_errors');
    }
}
add_action('save_post', 'neema_save_organismo_meta');

/* ==========================================================
   Mostrar errores de validación
========================================================== */
function neema_organismo_show_errors() {
    global $post;
    
    if (!$post || $post->post_type !== 'organismo') {
        return;
    }
    
    $errors = get_post_meta($post->ID, '_neema_organismo_validation_errors', true);
    if ($errors && is_array($errors)) {
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p><strong>Por favor, corrige los siguientes errores:</strong></p>';
        echo '<ul>';
        foreach ($errors as $error) {
            echo '<li>' . esc_html($error) . '</li>';
        }
        echo '</ul></div>';
    }
}
add_action('admin_notices', 'neema_organismo_show_errors');

?>
