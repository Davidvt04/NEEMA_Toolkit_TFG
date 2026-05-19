<?php

function neema_force_pending_for_gestor_contenido($data, $postarr) {
    if ($data['post_type'] !== 'recurso') {
        return $data;
    }
    
    $user = wp_get_current_user();
    
    if (isset($_POST['neema_review_action']) && $_POST['neema_review_action'] === 'approve') {
        return $data;
    }
    
    if (in_array('gestor_contenido', (array) $user->roles)) {
        if ($data['post_status'] === 'auto-draft') {
            $data['post_status'] = 'draft';
        }
        elseif ($data['post_status'] === 'publish') {
            $data['post_status'] = 'pending';
            
            if (!empty($postarr['ID'])) {
                $is_version = get_post_meta($postarr['ID'], '_recurso_id_original', true);
                
                if (!$is_version) {
                    $current_version = get_post_meta($postarr['ID'], '_recurso_version', true);
                    $new_version = $current_version ? intval($current_version) + 1 : 1;
                    update_post_meta($postarr['ID'], '_recurso_version', $new_version);
                }
            }
        }
    }
    
    return $data;
}
add_filter('wp_insert_post_data', 'neema_force_pending_for_gestor_contenido', 10, 2);

function neema_intercept_edit_published_resource() {
    if (!is_admin()) {
        return;
    }
    
    global $pagenow;
    if ($pagenow !== 'post.php' || !isset($_GET['action']) || $_GET['action'] !== 'edit' || !isset($_GET['post'])) {
        return;
    }
    
    $post_id = intval($_GET['post']);
    $post = get_post($post_id);
    
    if (!$post || $post->post_type !== 'recurso') {
        return;
    }
    
    $user = wp_get_current_user();
    if (!in_array('gestor_contenido', (array) $user->roles)) {
        return;
    }
    
    if ($post->post_status !== 'publish') {
        return;
    }
    
    $existing_clone = neema_get_draft_version($post_id);
    
    if ($existing_clone) {
        wp_redirect(admin_url('post.php?post=' . $existing_clone->ID . '&action=edit'));
        exit;
    }
    
    $clone_id = neema_create_resource_version($post_id, $user->ID);
    
    if ($clone_id) {
        wp_redirect(admin_url('post.php?post=' . $clone_id . '&action=edit'));
        exit;
    }
}
add_action('admin_init', 'neema_intercept_edit_published_resource');

function neema_get_draft_version($original_id) {
    $args = array(
        'post_type' => 'recurso',
        'post_status' => array('draft', 'pending'),
        'meta_query' => array(
            array(
                'key' => '_recurso_id_original',
                'value' => $original_id,
            )
        ),
        'posts_per_page' => 1,
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        return $query->posts[0];
    }
    
    return false;
}

function neema_create_resource_version($original_id, $user_id) {
    $original = get_post($original_id);
    
    if (!$original) {
        return false;
    }
    
    $original_version = get_post_meta($original_id, '_recurso_version', true);
    $new_version = $original_version ? intval($original_version) + 1 : 1;
    
    $clone_data = array(
        'post_type' => 'recurso',
        'post_title' => $original->post_title,
        'post_content' => $original->post_content,
        'post_excerpt' => $original->post_excerpt,
        'post_status' => 'draft',
        'post_author' => $user_id,
        'post_name' => $original->post_name,
    );
    
    $clone_id = wp_insert_post($clone_data);
    
    if (is_wp_error($clone_id)) {
        return false;
    }
    
    neema_copy_resource_metadata($original_id, $clone_id);
    
    update_post_meta($clone_id, '_recurso_id_original', $original_id);
    update_post_meta($clone_id, '_recurso_version', $new_version);
    
    $thumbnail_id = get_post_thumbnail_id($original_id);
    if ($thumbnail_id) {
        set_post_thumbnail($clone_id, $thumbnail_id);
    }
    
    $taxonomies = get_object_taxonomies('recurso');
    foreach ($taxonomies as $taxonomy) {
        $terms = wp_get_object_terms($original_id, $taxonomy, array('fields' => 'ids'));
        if (!is_wp_error($terms) && !empty($terms)) {
            wp_set_object_terms($clone_id, $terms, $taxonomy);
        }
    }
    
    return $clone_id;
}

function neema_copy_resource_metadata($from_id, $to_id) {
    global $wpdb;
    
    $meta_data = $wpdb->get_results($wpdb->prepare(
        "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d",
        $from_id
    ));
    
    if ($meta_data) {
        foreach ($meta_data as $meta) {
            if (substr($meta->meta_key, 0, 1) !== '_' || 
                strpos($meta->meta_key, '_recurso_') === 0 ||
                strpos($meta->meta_key, '_descripcion_') === 0 ||
                strpos($meta->meta_key, '_archivo_') === 0) {
                
                if (!in_array($meta->meta_key, array('_recurso_id_original', '_recurso_version', '_recurso_motivo_rechazo'))) {
                    add_post_meta($to_id, $meta->meta_key, maybe_unserialize($meta->meta_value));
                }
            }
        }
    }
}

function neema_recursos_add_review_meta_box() {
    $user = wp_get_current_user();
    
    if (in_array('neema_admin', (array) $user->roles) || in_array('administrator', (array) $user->roles)) {
        add_meta_box(
            'neema_recursos_review',
            'Revisión del Recurso',
            'neema_recursos_review_callback',
            'recurso',
            'side',
            'high'
        );
    }
    
    add_meta_box(
        'neema_recursos_version',
        'Información de Versión',
        'neema_recursos_version_callback',
        'recurso',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'neema_recursos_add_review_meta_box');

function neema_recursos_version_callback($post) {
    $version = get_post_meta($post->ID, '_recurso_version', true);
    $original_id = get_post_meta($post->ID, '_recurso_id_original', true);
    
    ?>
    <div style="margin: 10px 0;">
        <p><strong>Versión:</strong> <?php echo $version ? intval($version) : 0; ?></p>
        
        <?php if ($original_id): ?>
            <?php
            $original = get_post($original_id);
            if ($original && $original->post_status === 'publish') {
                ?>
                <p><strong>Versión de:</strong><br>
                    <a href="<?php echo get_permalink($original_id); ?>" target="_blank" style="color: #5cb85c;">
                        <?php echo esc_html($original->post_title); ?> ↗
                    </a>
                </p>
                <p style="color: #5cb85c; font-size: 12px; margin-top: -5px;">El recurso original está publicado</p>
                <?php
            } else {
                ?>
                <p><strong>Versión de:</strong><br>
                    <span style="color: #999;"><?php echo $original ? esc_html($original->post_title) : 'Recurso #' . $original_id; ?></span>
                </p>
                <p style="color: #999; font-size: 12px; margin-top: -5px;">El recurso original no está disponible</p>
                <?php
            }
            ?>
        <?php else: ?>
            <p style="color: #666; font-size: 12px;">Este es el recurso original</p>
        <?php endif; ?>
    </div>
    <?php
}

function neema_recursos_review_callback($post) {
    wp_nonce_field('neema_recursos_review_nonce', 'neema_recursos_review_nonce');
    
    $motivo_rechazo = get_post_meta($post->ID, '_recurso_motivo_rechazo', true);
    $estado_actual = $post->post_status;
    
    ?>
    <div style="margin: 10px 0;">
        <p><strong>Estado actual:</strong> 
            <?php 
            if ($estado_actual === 'pending') {
                echo '<span style="color: #f0ad4e;">En revisión</span>';
            } elseif ($estado_actual === 'publish') {
                echo '<span style="color: #5cb85c;">Publicado</span>';
            } elseif ($estado_actual === 'draft') {
                echo '<span style="color: #999;">Borrador</span>';
            }
            ?>
        </p>
        
        <?php if ($estado_actual === 'pending'): ?>
            <div style="background: #fff3cd; padding: 10px; border-left: 3px solid #f0ad4e; margin: 10px 0;">
                <p style="margin: 0;"><strong>Este recurso está esperando revisión</strong></p>
            </div>
            
            <p><strong>Acciones de revisión:</strong></p>
            
            <button type="button" class="button button-primary" id="neema-approve-resource" style="width: 100%; margin-bottom: 5px;">
                Aprobar y Publicar
            </button>
            
            <button type="button" class="button" id="neema-reject-resource" style="width: 100%; background: #dc3545; color: white; border-color: #dc3545;">
                Rechazar Recurso
            </button>
            
            <div id="neema-reject-reason" style="display: none; margin-top: 10px;">
                <label for="recurso_motivo_rechazo"><strong>Motivo del rechazo:</strong></label>
                <textarea id="recurso_motivo_rechazo" name="recurso_motivo_rechazo" rows="4" style="width: 100%; margin-top: 5px;" placeholder="Explica el motivo del rechazo..."></textarea>
                <button type="button" class="button button-primary" id="neema-confirm-reject" style="width: 100%; margin-top: 5px; background: #dc3545; border-color: #dc3545;">
                    Confirmar Rechazo
                </button>
                <button type="button" class="button" id="neema-cancel-reject" style="width: 100%; margin-top: 5px;">
                    Cancelar
                </button>
            </div>
            
            <input type="hidden" id="neema-review-action" name="neema_review_action" value="">
        <?php endif; ?>
        
        <?php if (!empty($motivo_rechazo) && $estado_actual === 'draft'): ?>
            <div style="background: #f8d7da; padding: 10px; border-left: 3px solid #dc3545; margin: 10px 0;">
                <p style="margin: 0 0 5px 0;"><strong>Este recurso fue rechazado</strong></p>
                <p style="margin: 0; font-size: 12px;"><strong>Motivo:</strong> <?php echo esc_html($motivo_rechazo); ?></p>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('#neema-reject-resource').on('click', function() {
            $('#neema-reject-reason').slideDown();
        });
        
        $('#neema-cancel-reject').on('click', function() {
            $('#neema-reject-reason').slideUp();
            $('#recurso_motivo_rechazo').val('');
        });
        
        $('#neema-confirm-reject').on('click', function() {
            var motivo = $('#recurso_motivo_rechazo').val().trim();
            if (motivo === '') {
                alert('Por favor, indica el motivo del rechazo.');
                return;
            }
            
            if (!confirm('¿Estás seguro de que quieres rechazar este recurso?')) {
                return;
            }
            
            $('#neema-review-action').val('reject');
            
            $('#post_status').val('draft');
            $('#hidden_post_status').val('draft');
            
            $('form#post').submit();
        });
        
        $('#neema-approve-resource').on('click', function() {
            if (!confirm('¿Estás seguro de que quieres aprobar y publicar este recurso?')) {
                return;
            }
            
            $('#neema-review-action').val('approve');
            
            $('#post_status').val('publish');
            $('#hidden_post_status').val('publish');
            $('#original_post_status').val('publish');
            
            $('form#post').submit();
        });
    });
    </script>
    <?php
}

function neema_recursos_process_review($post_id, $post, $update) {
    if (!isset($_POST['neema_recursos_review_nonce']) || 
        !wp_verify_nonce($_POST['neema_recursos_review_nonce'], 'neema_recursos_review_nonce')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!$update) {
        return;
    }

    $user = wp_get_current_user();
    if (!in_array('neema_admin', (array) $user->roles) && !in_array('administrator', (array) $user->roles)) {
        return;
    }

    if (isset($_POST['neema_review_action']) && !empty($_POST['neema_review_action'])) {
        $action = sanitize_text_field($_POST['neema_review_action']);
        
        if ($action === 'approve') {
            delete_post_meta($post_id, '_recurso_motivo_rechazo');
            remove_action('save_post_recurso', 'neema_recursos_process_review', 20);
            remove_filter('wp_insert_post_data', 'neema_force_pending_for_gestor_contenido', 10);

            if ($post->post_status !== 'publish') {
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_status' => 'publish'
                ), true, false);
            }
            neema_delete_previous_versions($post_id);
            $author_id = $post->post_author;
            do_action('neema_send_approval_notification', $author_id, $post_id);
            set_transient('neema_redirect_after_approval_' . get_current_user_id(), true, 60);

            add_action('save_post_recurso', 'neema_recursos_process_review', 20, 3);
        } elseif ($action === 'reject') {
            $motivo = isset($_POST['recurso_motivo_rechazo']) ? sanitize_textarea_field($_POST['recurso_motivo_rechazo']) : '';
            
            if (!empty($motivo)) {
                update_post_meta($post_id, '_recurso_motivo_rechazo', $motivo);
                $author_id = $post->post_author;
                do_action('neema_send_rejection_notification', $author_id, $post_id, $motivo);
            }
        }
    }
}
add_action('save_post_recurso', 'neema_recursos_process_review', 20, 3);
function neema_redirect_after_approval($location) {
    $user_id = get_current_user_id();
    if (get_transient('neema_redirect_after_approval_' . $user_id)) {
        delete_transient('neema_redirect_after_approval_' . $user_id);
        return admin_url('edit.php?post_type=recurso');
    }
    
    return $location;
}
add_filter('redirect_post_location', 'neema_redirect_after_approval');

function neema_delete_previous_versions($approved_post_id) {
    $original_id = get_post_meta($approved_post_id, '_recurso_id_original', true);
    
    if ($original_id) {
        $clone = get_post($approved_post_id);
        $original = get_post($original_id);
        
        if ($clone && $original) {
            wp_update_post(array(
                'ID' => $original_id,
                'post_title' => $clone->post_title,
                'post_content' => $clone->post_content,
                'post_excerpt' => $clone->post_excerpt,
                'post_author' => $clone->post_author,
                'post_status' => 'publish',
                'post_date' => $original->post_date, 
                'post_date_gmt' => $original->post_date_gmt,
            ), true, false);
            
            neema_copy_all_metadata($approved_post_id, $original_id);
            $clone_version = get_post_meta($approved_post_id, '_recurso_version', true);
            update_post_meta($original_id, '_recurso_version', $clone_version);
            $clone_thumbnail_id = get_post_thumbnail_id($approved_post_id);
            if ($clone_thumbnail_id) {
                set_post_thumbnail($original_id, $clone_thumbnail_id);
            } else {
                delete_post_thumbnail($original_id);
            }
            $taxonomies = get_object_taxonomies('recurso');
            foreach ($taxonomies as $taxonomy) {
                $terms = wp_get_object_terms($approved_post_id, $taxonomy, array('fields' => 'ids'));
                if (!is_wp_error($terms)) {
                    wp_set_object_terms($original_id, $terms, $taxonomy);
                }
            }
            delete_post_meta($original_id, '_recurso_motivo_rechazo');
            delete_post_meta($original_id, '_recurso_id_original');
            wp_delete_post($approved_post_id, true);
        }
        
    } else {
        $version = neema_get_draft_version($approved_post_id);
        
        if ($version) {
            wp_delete_post($version->ID, true);
        }
    }
}

function neema_copy_all_metadata($from_id, $to_id) {
    global $wpdb;
    $meta_data = $wpdb->get_results($wpdb->prepare(
        "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d",
        $from_id
    ));
    
    if ($meta_data) {
        foreach ($meta_data as $meta) {
            if (!in_array($meta->meta_key, array('_recurso_id_original', '_recurso_version', '_recurso_motivo_rechazo', '_edit_last', '_edit_lock', '_thumbnail_id'))
                && strpos($meta->meta_key, '_wp_') !== 0) {
                
                delete_post_meta($to_id, $meta->meta_key);
                
                $value = maybe_unserialize($meta->meta_value);
                update_post_meta($to_id, $meta->meta_key, $value);
            }
        }
    }
}

add_action('neema_send_rejection_notification', 'neema_notify_author_rejection', 10, 3);

add_action('neema_send_approval_notification', 'neema_notify_author_approval', 10, 2);

function neema_notify_author_rejection($author_id, $post_id, $motivo) {
    $author = get_userdata($author_id);
    $post = get_post($post_id);
    
    if (!$author || !$post) {
        return;
    }
    
    $autor_nombre = $author->display_name;
    $recurso_titulo = $post->post_title;
    $motivo_rechazo = $motivo;
    $recurso_editar_url = admin_url('post.php?post=' . $post_id . '&action=edit');
    $fecha_rechazo = date_i18n('d/m/Y H:i', current_time('timestamp'));
    
    ob_start();
    include(get_template_directory() . '/email-templates/recurso-rechazado.php');
    $html_message = ob_get_clean();
    
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: NEEMA Toolkit <noreply@neematoolkit.com>'
    );
    $to = $author->user_email;
    $subject = 'Rechazado: Tu recurso '.  $recurso_titulo .' requiere cambios - NEEMA Toolkit';
    
    wp_mail($to, $subject, $html_message, $headers);
}

function neema_notify_author_approval($author_id, $post_id) {
    $author = get_userdata($author_id);
    $post = get_post($post_id);
    
    if (!$author || !$post) {
        return;
    }
    
    $autor_nombre = $author->display_name;
    $recurso_titulo = $post->post_title;
    $recurso_url = get_permalink($post_id);
    $fecha_aprobacion = date_i18n('d/m/Y H:i', current_time('timestamp'));
    
    ob_start();
    include(get_template_directory() . '/email-templates/recurso-aprobado.php');
    $html_message = ob_get_clean();
    
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: NEEMA Toolkit <noreply@neematoolkit.com>'
    );
    
    $to = $author->user_email;
    $subject = 'Aprobado: Tu recurso '.  $recurso_titulo .' ha sido aprobado - NEEMA Toolkit';
    
    wp_mail($to, $subject, $html_message, $headers);
}

function neema_recursos_show_rejection_notice() {
    global $post, $pagenow;
    
    if (!in_array($pagenow, array('post.php', 'post-new.php'))) {
        return;
    }
    
    if (!$post || $post->post_type !== 'recurso') {
        return;
    }
    
    $user = wp_get_current_user();
    
    if ($post->post_author != $user->ID) {
        return;
    }
    
    $motivo_rechazo = get_post_meta($post->ID, '_recurso_motivo_rechazo', true);
    
    if (!empty($motivo_rechazo) && $post->post_status === 'draft') {
        ?>
        <div class="notice notice-error">
            <p><strong>Este recurso ha sido rechazado por el equipo de revisión.</strong></p>
            <p><strong>Motivo:</strong> <?php echo esc_html($motivo_rechazo); ?></p>
            <p>Por favor, realiza las correcciones necesarias y vuelve a enviar el recurso para revisión.</p>
        </div>
        <?php
    } elseif ($post->post_status === 'pending') {
        ?>
        <div class="notice notice-warning">
            <p><strong>Este recurso está en revisión.</strong></p>
            <p>El equipo de administradores revisará tu recurso pronto. Recibirás una notificación cuando sea aprobado o si requiere cambios.</p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'neema_recursos_show_rejection_notice');
function neema_recursos_add_status_column($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['version'] = 'Versión';
            $new_columns['review_status'] = 'Estado';
            $new_columns['author'] = 'Autor';
        }
    }
    
    return $new_columns;
}
add_filter('manage_recurso_posts_columns', 'neema_recursos_add_status_column');

function neema_recursos_status_column_content($column, $post_id) {
    if ($column === 'version') {
        $version = get_post_meta($post_id, '_recurso_version', true);
        $original_id = get_post_meta($post_id, '_recurso_id_original', true);
        
        echo '<strong>v' . ($version ? intval($version) : 0) . '</strong>';
        
        if ($original_id) {
            $original = get_post($original_id);
            if ($original && $original->post_status === 'publish') {
                echo '<br><small style="color: #5cb85c;">De: <a href="' . get_permalink($original_id) . '" target="_blank">' . esc_html($original->post_title) . ' ↗</a></small>';
            } else {
                echo '<br><small style="color: #999;">De: ' . ($original ? esc_html($original->post_title) : 'Recurso #' . $original_id) . '</small>';
            }
        }
    }
    
    if ($column === 'review_status') {
        $post = get_post($post_id);
        $motivo_rechazo = get_post_meta($post_id, '_recurso_motivo_rechazo', true);
        
        if ($post->post_status === 'pending') {
            echo '<span style="color: #f0ad4e; font-weight: bold;">En revisión</span>';
        } elseif ($post->post_status === 'publish') {
            echo '<span style="color: #5cb85c; font-weight: bold;">Publicado</span>';
        } elseif ($post->post_status === 'draft' && !empty($motivo_rechazo)) {
            echo '<span style="color: #dc3545; font-weight: bold;">Rechazado</span>';
            echo '<br><small style="color: #666;">' . esc_html(wp_trim_words($motivo_rechazo, 8)) . '</small>';
        } else {
            echo '<span style="color: #999;">Borrador</span>';
        }
    }
    
    if ($column === 'author') {
        $post = get_post($post_id);
        $author_id = $post->post_author;
        $author = get_userdata($author_id);
        
        if ($author) {
            $author_roles = $author->roles;
            $role_label = '';
            
            if (in_array('administrator', $author_roles)) {
                $role_label = '<span style="color: #d63638;">Admin</span>';
            } elseif (in_array('neema_admin', $author_roles)) {
                $role_label = '<span style="color: #00a0d2;">Administrador funcional NEEMA</span>';
            } elseif (in_array('gestor_contenido', $author_roles)) {
                $role_label = '<span style="color: #46b450;">Gestor</span>';
            }
            
            echo '<strong>' . esc_html($author->display_name) . '</strong>';
            if ($role_label) {
                echo '<br><small>' . $role_label . '</small>';
            }
        }
    }
}
add_action('manage_recurso_posts_custom_column', 'neema_recursos_status_column_content', 10, 2);

function neema_recursos_sortable_columns($columns) {
    $columns['version'] = 'version';
    $columns['review_status'] = 'review_status';
    $columns['author'] = 'author';
    return $columns;
}
add_filter('manage_edit-recurso_sortable_columns', 'neema_recursos_sortable_columns');

function neema_recursos_orderby_and_filter($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    
    if ($query->get('post_type') !== 'recurso') {
        return;
    }
    $orderby = $query->get('orderby');
    if ('review_status' === $orderby) {
        $query->set('orderby', 'post_status');
        $query->set('order', $query->get('order') ? $query->get('order') : 'ASC');
    }
    $user = wp_get_current_user();
    if (in_array('gestor_contenido', (array) $user->roles)) {
        $current_status = isset($_GET['post_status']) ? $_GET['post_status'] : '';
        if ($current_status === '' || $current_status === 'all') {
            $query->set('post__in', neema_get_visible_recursos_for_gestor($user->ID));
        } elseif ($current_status !== 'publish') {
            $query->set('author', $user->ID);
        }
    }
}
add_action('pre_get_posts', 'neema_recursos_orderby_and_filter');

function neema_recursos_add_pending_filter() {
    global $typenow;
    
    if ($typenow === 'recurso') {
        $user = wp_get_current_user();
        if (in_array('neema_admin', (array) $user->roles) || in_array('administrator', (array) $user->roles)) {
            $pending_count = wp_count_posts('recurso')->pending;
            
            if ($pending_count > 0) {
                $current_status = isset($_GET['post_status']) ? $_GET['post_status'] : '';
                ?>
                <div class="notice notice-info" style="margin: 10px 0; padding: 10px;">
                    <p style="margin: 0;">
                        <strong>Hay <?php echo $pending_count; ?> recurso(s) esperando revisión.</strong>
                        <?php if ($current_status !== 'pending'): ?>
                            <a href="<?php echo admin_url('edit.php?post_type=recurso&post_status=pending'); ?>" class="button button-small">
                                Ver recursos en revisión
                            </a>
                        <?php endif; ?>
                    </p>
                </div>
                <?php
            }
        }
    }
}
add_action('admin_notices', 'neema_recursos_add_pending_filter');

function neema_get_visible_recursos_for_gestor($user_id) {
    global $wpdb;
    $own_recursos = $wpdb->get_col($wpdb->prepare(
        "SELECT ID FROM $wpdb->posts 
        WHERE post_type = 'recurso' 
        AND post_author = %d",
        $user_id
    ));
    $published_recursos = $wpdb->get_col($wpdb->prepare(
        "SELECT ID FROM $wpdb->posts 
        WHERE post_type = 'recurso' 
        AND post_status = 'publish'
        AND post_author != %d",
        $user_id
    ));
    $visible_recursos = array_merge($own_recursos, $published_recursos);
    return empty($visible_recursos) ? array(0) : $visible_recursos;
}

function neema_control_edit_permissions($caps, $cap, $user_id, $args) {
    if ($cap !== 'edit_post') {
        return $caps;
    }
    if (empty($args[0])) {
        return $caps;
    }
    
    $post = get_post($args[0]);
    if (!$post || $post->post_type !== 'recurso') {
        return $caps;
    }
    
    $user = get_userdata($user_id);
    if (!in_array('gestor_contenido', (array) $user->roles)) {
        return $caps;
    }
    if ($post->post_author != $user_id) {
        $caps[] = 'do_not_allow';
    }
    
    return $caps;
}
add_filter('map_meta_cap', 'neema_control_edit_permissions', 10, 4);

function neema_prevent_delete_published_recursos($caps, $cap, $user_id, $args) {
    if ($cap !== 'delete_post' && $cap !== 'delete_recurso') {
        return $caps;
    }
    if (empty($args[0])) {
        return $caps;
    }
    
    $post = get_post($args[0]);
    if (!$post || $post->post_type !== 'recurso') {
        return $caps;
    }
    
    $user = get_userdata($user_id);
    if (!in_array('gestor_contenido', (array) $user->roles)) {
        return $caps;
    }
    if ($post->post_author != $user_id) {
        return array('do_not_allow');
    }
    
    if ($post->post_status === 'publish') {
        return array('do_not_allow');
    }
    
    return $caps;
}
add_filter('map_meta_cap', 'neema_prevent_delete_published_recursos', 11, 4);

function neema_init_resource_version($post_id, $post, $update) {
    if ($update) {
        return;
    }
    if ($post->post_type !== 'recurso') {
        return;
    }
    $existing_version = get_post_meta($post_id, '_recurso_version', true);
    if (!$existing_version) {
        update_post_meta($post_id, '_recurso_version', 0);
    }
}
add_action('save_post_recurso', 'neema_init_resource_version', 5, 3);
