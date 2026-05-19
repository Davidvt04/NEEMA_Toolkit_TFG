<?php
/* ==========================================================
   Custom Post Type: Miembros
   ========================================================== */
function neema_register_miembros_cpt() {
    $labels = array(
        'name'               => 'Miembros',
        'singular_name'      => 'Miembro',
        'add_new'            => 'Añadir nuevo',
        'add_new_item'       => 'Añadir nuevo miembro',
        'edit_item'          => 'Editar miembro',
        'new_item'           => 'Nuevo miembro',
        'view_item'          => 'Ver miembro',
        'search_items'       => 'Buscar miembros',
        'not_found'          => 'No se encontraron miembros',
        'not_found_in_trash' => 'No hay miembros en la papelera',
        'menu_name'          => 'Miembros'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'supports'           => array('title', 'thumbnail'),
        'menu_icon'          => 'dashicons-groups',
        'has_archive'        => false,
        'rewrite'            => array('slug' => 'miembros')
    );

    register_post_type('miembro', $args);
}
add_action('init', 'neema_register_miembros_cpt');


/* ==========================================================
   Meta Boxes para Miembros
   ========================================================== */
function neema_add_member_meta_boxes() {
    add_meta_box(
        'member_url_box',
        'Enlace del miembro',
        'neema_member_url_meta_box_callback',
        'miembro',
        'normal',
        'default'
    );


    add_meta_box(
        'member_order_box',
        'Orden del miembro',
        'neema_member_order_meta_box_callback',
        'miembro',
        'side',
        'default'
    );

    add_meta_box(
        'member_entities_box',
        'Entidades y participantes',
        'neema_member_entities_meta_box_callback',
        'miembro',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'neema_add_member_meta_boxes');

/* ==========================================================
   Callbacks de los campos
   ========================================================== */
function neema_member_url_meta_box_callback($post) {
    $value = get_post_meta($post->ID, '_member_url', true);
    $description_es = get_post_meta($post->ID, '_member_description_es', true);
    $description_en = get_post_meta($post->ID, '_member_description_en', true);
    $description_fr = get_post_meta($post->ID, '_member_description_fr', true);

    wp_nonce_field('neema_member_meta_nonce_action', 'neema_member_meta_nonce');

    echo '<p><strong>Foto del miembro</strong><br>Usa la Imagen destacada de WordPress para la foto del miembro.</p>';
    echo '<label for="member_url">URL del miembro:</label>';
    echo '<input type="url" id="member_url" name="member_url" value="' . esc_attr($value) . '" style="width:100%;margin-top:8px;" placeholder="https://ejemplo.com">';

    echo '<p style="margin-top:14px;"><label for="member_description_es"><strong>Descripción (ES)</strong></label><br>';
    echo '<textarea id="member_description_es" name="member_description_es" rows="3" style="width:100%;" placeholder="Descripción en español">' . esc_textarea($description_es) . '</textarea></p>';

    echo '<p><label for="member_description_en"><strong>Descripción (EN)</strong></label><br>';
    echo '<textarea id="member_description_en" name="member_description_en" rows="3" style="width:100%;" placeholder="Description in English">' . esc_textarea($description_en) . '</textarea></p>';

    echo '<p><label for="member_description_fr"><strong>Descripción (FR)</strong></label><br>';
    echo '<textarea id="member_description_fr" name="member_description_fr" rows="3" style="width:100%;" placeholder="Description en français">' . esc_textarea($description_fr) . '</textarea></p>';
}

function neema_member_order_meta_box_callback($post) {
    $value = get_post_meta($post->ID, '_member_order', true);
    echo '<label for="member_order">Número de orden:</label>';
    echo '<input type="number" id="member_order" name="member_order" value="' . esc_attr($value) . '" style="width:100%;margin-top:8px;" placeholder="Ej: 1">';
}


function neema_member_entities_meta_box_callback($post) {
    wp_enqueue_media();

    $entities = get_post_meta($post->ID, '_member_entities', true);

    if (!is_array($entities)) {
        $entities = array();
    }

    echo '<div id="neema-member-entities-wrapper">';
    echo '<p>Agrega una o varias entidades para este miembro. Cada entidad puede tener uno o varios participantes.</p>';
    echo '<div id="neema-member-entities-list">';

    foreach ($entities as $entity_index => $entity) {
        $entity_title = isset($entity['title']) ? $entity['title'] : '';
        $entity_url = isset($entity['url']) ? $entity['url'] : '';
        $entity_image = isset($entity['image']) ? $entity['image'] : '';
        $entity_description_es = isset($entity['description_es']) ? $entity['description_es'] : '';
        $entity_description_en = isset($entity['description_en']) ? $entity['description_en'] : '';
        $entity_description_fr = isset($entity['description_fr']) ? $entity['description_fr'] : '';
        $participants = (isset($entity['participants']) && is_array($entity['participants'])) ? $entity['participants'] : array();

        echo '<div class="neema-entity-item" data-entity-index="' . esc_attr($entity_index) . '">';
        echo '<h4>Entidad</h4>';
        echo '<p><label>Título de la entidad</label><br>';
        echo '<input type="text" name="member_entities[' . esc_attr($entity_index) . '][title]" value="' . esc_attr($entity_title) . '" style="width:100%;" placeholder="Ej: Universidad X"></p>';
        echo '<p><label>Web de la entidad</label><br>';
        echo '<input type="url" name="member_entities[' . esc_attr($entity_index) . '][url]" value="' . esc_attr($entity_url) . '" style="width:100%;" placeholder="https://entidad.com"></p>';
        echo '<div class="neema-image-field">';
        echo '<p><label>Foto de la entidad</label><br>';
        echo '<input type="hidden" class="neema-image-input" name="member_entities[' . esc_attr($entity_index) . '][image]" value="' . esc_attr($entity_image) . '">';
        echo '<span class="neema-image-preview"' . (empty($entity_image) ? ' style="display:none;"' : '') . '><img src="' . esc_url($entity_image) . '" alt=""></span><br>';
        echo '<button type="button" class="button neema-select-image">Subir/Seleccionar imagen</button> ';
        echo '<button type="button" class="button button-secondary neema-remove-image">Quitar imagen</button></p>';
        echo '</div>';
        echo '<p><label>Descripción entidad (ES)</label><br>';
        echo '<textarea name="member_entities[' . esc_attr($entity_index) . '][description_es]" rows="2" style="width:100%;" placeholder="Descripción en español">' . esc_textarea($entity_description_es) . '</textarea></p>';
        echo '<p><label>Descripción entidad (EN)</label><br>';
        echo '<textarea name="member_entities[' . esc_attr($entity_index) . '][description_en]" rows="2" style="width:100%;" placeholder="Description in English">' . esc_textarea($entity_description_en) . '</textarea></p>';
        echo '<p><label>Descripción entidad (FR)</label><br>';
        echo '<textarea name="member_entities[' . esc_attr($entity_index) . '][description_fr]" rows="2" style="width:100%;" placeholder="Description en français">' . esc_textarea($entity_description_fr) . '</textarea></p>';

        echo '<div class="neema-participants-list">';
        echo '<h5>Participantes</h5>';

        foreach ($participants as $participant_index => $participant) {
            $participant_title = isset($participant['title']) ? $participant['title'] : '';
            $participant_url = isset($participant['url']) ? $participant['url'] : '';
            $participant_image = isset($participant['image']) ? $participant['image'] : '';
            $participant_description_es = isset($participant['description_es']) ? $participant['description_es'] : '';
            $participant_description_en = isset($participant['description_en']) ? $participant['description_en'] : '';
            $participant_description_fr = isset($participant['description_fr']) ? $participant['description_fr'] : '';

            echo '<div class="neema-participant-item">';
            echo '<p><label>Título del participante</label><br>';
            echo '<input type="text" name="member_entities[' . esc_attr($entity_index) . '][participants][' . esc_attr($participant_index) . '][title]" value="' . esc_attr($participant_title) . '" style="width:100%;" placeholder="Ej: Participante 1"></p>';
            echo '<p><label>Web del participante</label><br>';
            echo '<input type="url" name="member_entities[' . esc_attr($entity_index) . '][participants][' . esc_attr($participant_index) . '][url]" value="' . esc_attr($participant_url) . '" style="width:100%;" placeholder="https://participante.com"></p>';
            echo '<div class="neema-image-field">';
            echo '<p><label>Foto del participante</label><br>';
            echo '<input type="hidden" class="neema-image-input" name="member_entities[' . esc_attr($entity_index) . '][participants][' . esc_attr($participant_index) . '][image]" value="' . esc_attr($participant_image) . '">';
            echo '<span class="neema-image-preview"' . (empty($participant_image) ? ' style="display:none;"' : '') . '><img src="' . esc_url($participant_image) . '" alt=""></span><br>';
            echo '<button type="button" class="button neema-select-image">Subir/Seleccionar imagen</button> ';
            echo '<button type="button" class="button button-secondary neema-remove-image">Quitar imagen</button></p>';
            echo '</div>';
            echo '<p><label>Descripción participante (ES)</label><br>';
            echo '<textarea name="member_entities[' . esc_attr($entity_index) . '][participants][' . esc_attr($participant_index) . '][description_es]" rows="2" style="width:100%;" placeholder="Descripción en español">' . esc_textarea($participant_description_es) . '</textarea></p>';
            echo '<p><label>Descripción participante (EN)</label><br>';
            echo '<textarea name="member_entities[' . esc_attr($entity_index) . '][participants][' . esc_attr($participant_index) . '][description_en]" rows="2" style="width:100%;" placeholder="Description in English">' . esc_textarea($participant_description_en) . '</textarea></p>';
            echo '<p><label>Descripción participante (FR)</label><br>';
            echo '<textarea name="member_entities[' . esc_attr($entity_index) . '][participants][' . esc_attr($participant_index) . '][description_fr]" rows="2" style="width:100%;" placeholder="Description en français">' . esc_textarea($participant_description_fr) . '</textarea></p>';
            echo '<p><button type="button" class="button button-secondary neema-remove-participant">Eliminar participante</button></p>';
            echo '</div>';
        }

        echo '</div>';
        echo '<p><button type="button" class="button neema-add-participant">+ Añadir participante</button></p>';
        echo '<p><button type="button" class="button button-secondary neema-remove-entity">Eliminar entidad</button></p>';
        echo '</div>';
    }

    echo '</div>';
    echo '<p><button type="button" class="button button-primary" id="neema-add-entity">+ Añadir entidad</button></p>';
    echo '</div>';

    echo '<template id="neema-entity-template">';
    echo '<div class="neema-entity-item" data-entity-index="__ENTITY_INDEX__">';
    echo '<h4>Entidad</h4>';
    echo '<p><label>Título de la entidad</label><br>';
    echo '<input type="text" name="member_entities[__ENTITY_INDEX__][title]" style="width:100%;" placeholder="Ej: Universidad X"></p>';
    echo '<p><label>Web de la entidad</label><br>';
    echo '<input type="url" name="member_entities[__ENTITY_INDEX__][url]" style="width:100%;" placeholder="https://entidad.com"></p>';
    echo '<div class="neema-image-field">';
    echo '<p><label>Foto de la entidad</label><br>';
    echo '<input type="hidden" class="neema-image-input" name="member_entities[__ENTITY_INDEX__][image]">';
    echo '<span class="neema-image-preview" style="display:none;"><img src="" alt=""></span><br>';
    echo '<button type="button" class="button neema-select-image">Subir/Seleccionar imagen</button> ';
    echo '<button type="button" class="button button-secondary neema-remove-image">Quitar imagen</button></p>';
    echo '</div>';
    echo '<p><label>Descripción entidad (ES)</label><br>';
    echo '<textarea name="member_entities[__ENTITY_INDEX__][description_es]" rows="2" style="width:100%;" placeholder="Descripción en español"></textarea></p>';
    echo '<p><label>Descripción entidad (EN)</label><br>';
    echo '<textarea name="member_entities[__ENTITY_INDEX__][description_en]" rows="2" style="width:100%;" placeholder="Description in English"></textarea></p>';
    echo '<p><label>Descripción entidad (FR)</label><br>';
    echo '<textarea name="member_entities[__ENTITY_INDEX__][description_fr]" rows="2" style="width:100%;" placeholder="Description en français"></textarea></p>';
    echo '<div class="neema-participants-list">';
    echo '<h5>Participantes</h5>';
    echo '</div>';
    echo '<p><button type="button" class="button neema-add-participant">+ Añadir participante</button></p>';
    echo '<p><button type="button" class="button button-secondary neema-remove-entity">Eliminar entidad</button></p>';
    echo '</div>';
    echo '</template>';

    echo '<template id="neema-participant-template">';
    echo '<div class="neema-participant-item">';
    echo '<p><label>Título del participante</label><br>';
    echo '<input type="text" name="member_entities[__ENTITY_INDEX__][participants][__PARTICIPANT_INDEX__][title]" style="width:100%;" placeholder="Ej: Participante 1"></p>';
    echo '<p><label>Web del participante</label><br>';
    echo '<input type="url" name="member_entities[__ENTITY_INDEX__][participants][__PARTICIPANT_INDEX__][url]" style="width:100%;" placeholder="https://participante.com"></p>';
    echo '<div class="neema-image-field">';
    echo '<p><label>Foto del participante</label><br>';
    echo '<input type="hidden" class="neema-image-input" name="member_entities[__ENTITY_INDEX__][participants][__PARTICIPANT_INDEX__][image]">';
    echo '<span class="neema-image-preview" style="display:none;"><img src="" alt=""></span><br>';
    echo '<button type="button" class="button neema-select-image">Subir/Seleccionar imagen</button> ';
    echo '<button type="button" class="button button-secondary neema-remove-image">Quitar imagen</button></p>';
    echo '</div>';
    echo '<p><label>Descripción participante (ES)</label><br>';
    echo '<textarea name="member_entities[__ENTITY_INDEX__][participants][__PARTICIPANT_INDEX__][description_es]" rows="2" style="width:100%;" placeholder="Descripción en español"></textarea></p>';
    echo '<p><label>Descripción participante (EN)</label><br>';
    echo '<textarea name="member_entities[__ENTITY_INDEX__][participants][__PARTICIPANT_INDEX__][description_en]" rows="2" style="width:100%;" placeholder="Description in English"></textarea></p>';
    echo '<p><label>Descripción participante (FR)</label><br>';
    echo '<textarea name="member_entities[__ENTITY_INDEX__][participants][__PARTICIPANT_INDEX__][description_fr]" rows="2" style="width:100%;" placeholder="Description en français"></textarea></p>';
    echo '<p><button type="button" class="button button-secondary neema-remove-participant">Eliminar participante</button></p>';
    echo '</div>';
    echo '</template>';

    echo '<style>
        #neema-member-entities-wrapper .neema-entity-item {
            border: 1px solid #dcdcde;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 12px;
            background: #fff;
        }
        #neema-member-entities-wrapper .neema-participant-item {
            border: 1px dashed #dcdcde;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 10px;
            background: #fcfcfc;
        }
        #neema-member-entities-wrapper h4,
        #neema-member-entities-wrapper h5 {
            margin-top: 0;
            margin-bottom: 10px;
        }
        #neema-member-entities-wrapper .neema-image-preview {
            display: inline-block;
            margin-bottom: 8px;
        }
        #neema-member-entities-wrapper .neema-image-preview img {
            max-width: 120px;
            max-height: 120px;
            border: 1px solid #dcdcde;
            border-radius: 4px;
            padding: 2px;
            background: #fff;
        }
    </style>';

    echo '<script>
        (function() {
            const wrapper = document.getElementById("neema-member-entities-wrapper");
            if (!wrapper) {
                return;
            }

            const entitiesList = document.getElementById("neema-member-entities-list");
            const addEntityButton = document.getElementById("neema-add-entity");
            const entityTemplate = document.getElementById("neema-entity-template");
            const participantTemplate = document.getElementById("neema-participant-template");

            const existingEntityItems = entitiesList.querySelectorAll(".neema-entity-item");
            let entityIndex = existingEntityItems.length;

            addEntityButton.addEventListener("click", function() {
                let html = entityTemplate.innerHTML.replace(/__ENTITY_INDEX__/g, entityIndex);
                entitiesList.insertAdjacentHTML("beforeend", html);
                entityIndex++;
            });

            wrapper.addEventListener("click", function(event) {
                const addParticipantButton = event.target.closest(".neema-add-participant");
                if (addParticipantButton) {
                    const entityItem = addParticipantButton.closest(".neema-entity-item");
                    const participantsList = entityItem.querySelector(".neema-participants-list");
                    const currentEntityIndex = entityItem.getAttribute("data-entity-index");
                    const participantIndex = participantsList.querySelectorAll(".neema-participant-item").length;

                    let html = participantTemplate.innerHTML
                        .replace(/__ENTITY_INDEX__/g, currentEntityIndex)
                        .replace(/__PARTICIPANT_INDEX__/g, participantIndex);

                    participantsList.insertAdjacentHTML("beforeend", html);
                    return;
                }

                const removeParticipantButton = event.target.closest(".neema-remove-participant");
                if (removeParticipantButton) {
                    const participantItem = removeParticipantButton.closest(".neema-participant-item");
                    if (participantItem) {
                        participantItem.remove();
                    }
                    return;
                }

                const removeEntityButton = event.target.closest(".neema-remove-entity");
                if (removeEntityButton) {
                    const entityItem = removeEntityButton.closest(".neema-entity-item");
                    if (entityItem) {
                        entityItem.remove();
                    }
                    return;
                }

                const selectImageButton = event.target.closest(".neema-select-image");
                if (selectImageButton) {
                    const imageField = selectImageButton.closest(".neema-image-field");
                    if (!imageField || typeof wp === "undefined" || !wp.media) {
                        return;
                    }

                    const imageInput = imageField.querySelector(".neema-image-input");
                    const imagePreview = imageField.querySelector(".neema-image-preview");
                    const imageTag = imagePreview ? imagePreview.querySelector("img") : null;

                    const frame = wp.media({
                        title: "Seleccionar imagen",
                        button: { text: "Usar esta imagen" },
                        multiple: false
                    });

                    frame.on("select", function() {
                        const attachment = frame.state().get("selection").first().toJSON();
                        const imageUrl = attachment.url || "";

                        if (imageInput) {
                            imageInput.value = imageUrl;
                        }

                        if (imagePreview && imageTag && imageUrl) {
                            imageTag.src = imageUrl;
                            imagePreview.style.display = "inline-block";
                        }
                    });

                    frame.open();
                    return;
                }

                const removeImageButton = event.target.closest(".neema-remove-image");
                if (removeImageButton) {
                    const imageField = removeImageButton.closest(".neema-image-field");
                    if (!imageField) {
                        return;
                    }

                    const imageInput = imageField.querySelector(".neema-image-input");
                    const imagePreview = imageField.querySelector(".neema-image-preview");
                    const imageTag = imagePreview ? imagePreview.querySelector("img") : null;

                    if (imageInput) {
                        imageInput.value = "";
                    }

                    if (imageTag) {
                        imageTag.src = "";
                    }

                    if (imagePreview) {
                        imagePreview.style.display = "none";
                    }
                }
            });
        })();
    </script>';
}

/* ==========================================================
   Guardar Meta Datos
   ========================================================== */
function neema_save_member_meta($post_id) {
    if (!isset($_POST['neema_member_meta_nonce']) || !wp_verify_nonce($_POST['neema_member_meta_nonce'], 'neema_member_meta_nonce_action')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (array_key_exists('member_url', $_POST)) {
        update_post_meta($post_id, '_member_url', esc_url_raw($_POST['member_url']));
    }

    if (array_key_exists('member_description_es', $_POST)) {
        update_post_meta($post_id, '_member_description_es', sanitize_textarea_field($_POST['member_description_es']));
    }

    if (array_key_exists('member_description_en', $_POST)) {
        update_post_meta($post_id, '_member_description_en', sanitize_textarea_field($_POST['member_description_en']));
    }

    if (array_key_exists('member_description_fr', $_POST)) {
        update_post_meta($post_id, '_member_description_fr', sanitize_textarea_field($_POST['member_description_fr']));
    }

    if (array_key_exists('member_order', $_POST)) {
        update_post_meta($post_id, '_member_order', intval($_POST['member_order']));
    }

    if (array_key_exists('member_entities', $_POST) && is_array($_POST['member_entities'])) {
        $sanitized_entities = array();

        foreach ($_POST['member_entities'] as $entity) {
            if (!is_array($entity)) {
                continue;
            }

            $entity_title = isset($entity['title']) ? sanitize_text_field($entity['title']) : '';
            $entity_url = isset($entity['url']) ? esc_url_raw($entity['url']) : '';
            $entity_image = isset($entity['image']) ? esc_url_raw($entity['image']) : '';
            $entity_description_es = isset($entity['description_es']) ? sanitize_textarea_field($entity['description_es']) : '';
            $entity_description_en = isset($entity['description_en']) ? sanitize_textarea_field($entity['description_en']) : '';
            $entity_description_fr = isset($entity['description_fr']) ? sanitize_textarea_field($entity['description_fr']) : '';
            $participants_raw = (isset($entity['participants']) && is_array($entity['participants'])) ? $entity['participants'] : array();
            $sanitized_participants = array();

            foreach ($participants_raw as $participant) {
                if (!is_array($participant)) {
                    continue;
                }

                $participant_title = isset($participant['title']) ? sanitize_text_field($participant['title']) : '';
                $participant_url = isset($participant['url']) ? esc_url_raw($participant['url']) : '';
                $participant_image = isset($participant['image']) ? esc_url_raw($participant['image']) : '';
                $participant_description_es = isset($participant['description_es']) ? sanitize_textarea_field($participant['description_es']) : '';
                $participant_description_en = isset($participant['description_en']) ? sanitize_textarea_field($participant['description_en']) : '';
                $participant_description_fr = isset($participant['description_fr']) ? sanitize_textarea_field($participant['description_fr']) : '';

                if ($participant_title !== '' || $participant_url !== '' || $participant_image !== '' || $participant_description_es !== '' || $participant_description_en !== '' || $participant_description_fr !== '') {
                    $sanitized_participants[] = array(
                        'title' => $participant_title,
                        'url' => $participant_url,
                        'image' => $participant_image,
                        'description_es' => $participant_description_es,
                        'description_en' => $participant_description_en,
                        'description_fr' => $participant_description_fr,
                    );
                }
            }

            if ($entity_title !== '' || $entity_url !== '' || $entity_image !== '' || $entity_description_es !== '' || $entity_description_en !== '' || $entity_description_fr !== '' || !empty($sanitized_participants)) {
                $sanitized_entities[] = array(
                    'title' => $entity_title,
                    'url' => $entity_url,
                    'image' => $entity_image,
                    'description_es' => $entity_description_es,
                    'description_en' => $entity_description_en,
                    'description_fr' => $entity_description_fr,
                    'participants' => $sanitized_participants,
                );
            }
        }

        if (!empty($sanitized_entities)) {
            update_post_meta($post_id, '_member_entities', $sanitized_entities);
        } else {
            delete_post_meta($post_id, '_member_entities');
        }
    } else {
        delete_post_meta($post_id, '_member_entities');
    }
}
add_action('save_post_miembro', 'neema_save_member_meta');
