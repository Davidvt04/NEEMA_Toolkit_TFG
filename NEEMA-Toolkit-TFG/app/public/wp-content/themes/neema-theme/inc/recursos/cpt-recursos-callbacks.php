<?php
/**
 * Callbacks de Meta Boxes para el CPT Recursos
 * 
 * @package Neema
 */

/**
 * Renderiza el meta box de títulos en múltiples idiomas
 */
function neema_recursos_titulos_callback($post) {
    wp_nonce_field('neema_recursos_titulos_nonce', 'neema_recursos_titulos_nonce');
    
    $titulo_es = get_post_meta($post->ID, '_recurso_titulo_es', true);
    $titulo_en = get_post_meta($post->ID, '_recurso_titulo_en', true);
    $titulo_fr = get_post_meta($post->ID, '_recurso_titulo_fr', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label for="recurso_titulo_es">Título en Español <span style="color:red;">*</span></label></th>
            <td>
                <input type="text" id="recurso_titulo_es" name="recurso_titulo_es" 
                       value="<?php echo esc_attr($titulo_es); ?>" class="widefat" required />
            </td>
        </tr>
        <tr>
            <th><label for="recurso_titulo_en">Título en Inglés <span style="color:red;">*</span></label></th>
            <td>
                <input type="text" id="recurso_titulo_en" name="recurso_titulo_en" 
                       value="<?php echo esc_attr($titulo_en); ?>" class="widefat" required />
            </td>
        </tr>
        <tr>
            <th><label for="recurso_titulo_fr">Título en Francés <span style="color:red;">*</span></label></th>
            <td>
                <input type="text" id="recurso_titulo_fr" name="recurso_titulo_fr" 
                       value="<?php echo esc_attr($titulo_fr); ?>" class="widefat" required />
            </td>
        </tr>
    </table>
    <p style="color:#666;"><em>* Campos obligatorios</em></p>
    <?php
}

/**
 * Renderiza el meta box de relaciones del recurso
 */
function neema_recursos_relaciones_callback($post) {
    wp_nonce_field('neema_recursos_relaciones_nonce', 'neema_recursos_relaciones_nonce');
    $paises_seleccionados = get_post_meta($post->ID, '_recurso_paises', true);
    $tipo_seleccionado = get_post_meta($post->ID, '_recurso_tipo', true);
    $tematicas_seleccionadas = get_post_meta($post->ID, '_recurso_tematicas', true);
    $regiones_seleccionadas = get_post_meta($post->ID, '_recurso_regiones', true);
    $modulo_seleccionado = get_post_meta($post->ID, '_recurso_modulo', true);
    $categoria_seleccionada = get_post_meta($post->ID, '_recurso_categoria', true);
    $es_descargable = get_post_meta($post->ID, '_recurso_descargable', true);
    $es_visualizable = get_post_meta($post->ID, '_recurso_visualizable', true);
    $current_user_id = get_current_user_id();
    $locale = get_user_locale($current_user_id);
    $current_lang = substr($locale, 0, 2);
    $categorias = array(
        'Contextual' => array(
            'es' => 'Contextual',
            'en' => 'Contextual',
            'fr' => 'Contextuel'
        ),
        'Formativo' => array(
            'es' => 'Formativo',
            'en' => 'Formative',
            'fr' => 'Formatif'
        ),
        'Metodológico' => array(
            'es' => 'Metodológico',
            'en' => 'Methodological',
            'fr' => 'Méthodologique'
        ),
        'Procedimental' => array(
            'es' => 'Procedimental',
            'en' => 'Procedural',
            'fr' => 'Procédural'
        )
    );
    $paises = get_posts(array(
        'post_type' => 'pais',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'suppress_filters' => false,
    ));
    $tipos = get_posts(array(
        'post_type' => 'tipo-recurso',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'suppress_filters' => false,
        'lang' => $current_lang,
    ));
    $tematicas = get_posts(array(
        'post_type' => 'tematica',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'suppress_filters' => false,
        'lang' => $current_lang,
    ));
    $regiones = get_posts(array(
        'post_type' => 'region',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'suppress_filters' => false,
        'lang' => $current_lang,
    ));
    $modulos = get_posts(array(
        'post_type' => 'modulo',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'suppress_filters' => false,
        'lang' => $current_lang,
    ));
    if (!is_array($paises_seleccionados)) $paises_seleccionados = array();
    if (!is_array($tematicas_seleccionadas)) $tematicas_seleccionadas = array();
    if (!is_array($regiones_seleccionadas)) $regiones_seleccionadas = array();
    ?>
    <table class="form-table">
        <!-- País (Múltiple) -->
        <tr>
            <th><label for="recurso_paises"> País(es)</label></th>
            <td>
                <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fff;">
                    <?php foreach ($paises as $pais): ?>
                        <label style="display: block; margin-bottom: 8px;">
                            <input type="checkbox" name="recurso_paises[]" value="<?php echo $pais->ID; ?>" <?php echo in_array($pais->ID, $paises_seleccionados) ? 'checked' : ''; ?> />
                            <?php echo esc_html($pais->post_title); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <p class="description">Selecciona uno o varios países (opcional).</p>
            </td>
        </tr>
        
        <!-- Tipo de Recurso (Una opción) -->
        <tr>
            <th><label for="recurso_tipo"> Tipo de Recurso <span style="color:red;">*</span></label></th>
            <td>
                <select id="recurso_tipo" name="recurso_tipo" class="widefat" required>
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($tipos as $tipo): 
                        $spanish_id = pll_get_post($tipo->ID, 'es');
                        $tipo_key = get_post_meta($spanish_id, '_tipo_recurso_key', true);
                        ?>
                        <option value="<?php echo $tipo_key; ?>" <?php selected($tipo_seleccionado, $tipo_key); ?>>
                            <?php echo esc_html($tipo->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        
        <!-- Temática (Múltiple) -->
        <tr>
            <th><label for="recurso_tematicas"> Temática(s)</label></th>
            <td>
                <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fff;">
                    <?php foreach ($tematicas as $tematica): 
                        $spanish_id = pll_get_post($tematica->ID, 'es');
                        $tematica_key = get_post_meta($spanish_id, '_tematica_key', true);
                        ?>
                        <label style="display: block; margin-bottom: 8px;">
                            <input type="checkbox" name="recurso_tematicas[]" value="<?php echo $tematica_key; ?>" <?php echo in_array($tematica_key, $tematicas_seleccionadas) ? 'checked' : ''; ?> />
                            <?php echo esc_html($tematica->post_title); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <p class="description">Selecciona una o varias temáticas (opcional).</p>
            </td>
        </tr>
        
        <!-- Región (Múltiple) -->
        <tr>
            <th><label for="recurso_regiones">️ Región(es)</label></th>
            <td>
                <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fff;">
                    <?php foreach ($regiones as $region): 
                        $spanish_id = pll_get_post($region->ID, 'es');
                        $region_key = get_post_meta($spanish_id, '_region_key', true);
                    ?>
                        <label style="display: block; margin-bottom: 8px;">
                            <input type="checkbox" name="recurso_regiones[]" value="<?php echo esc_attr($region_key); ?>" <?php echo in_array($region_key, $regiones_seleccionadas) ? 'checked' : ''; ?> />
                            <?php echo esc_html($region->post_title); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <p class="description">Selecciona una o varias regiones (opcional).</p>
            </td>
        </tr>
        
        <!-- Módulo (Una opción) -->
        <tr>
            <th><label for="recurso_modulo"> Módulo <span style="color:red;">*</span></label></th>
            <td>
                <select id="recurso_modulo" name="recurso_modulo" class="widefat" required>
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($modulos as $modulo): 
                        $spanish_id = pll_get_post($modulo->ID, 'es');
                        $modulo_key = get_post_meta($spanish_id, '_modulo_key', true);
                        ?>
                        <option value="<?php echo esc_attr($modulo_key); ?>" <?php selected($modulo_seleccionado, $modulo_key); ?>>
                            <?php echo esc_html($modulo->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        
        <!-- Categoría (Una opción) -->
        <tr>
            <th><label for="recurso_categoria"> Categoría <span style="color:red;">*</span></label></th>
            <td>
                <select id="recurso_categoria" name="recurso_categoria" class="widefat" required>
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($categorias as $valor_es => $traducciones): ?>
                        <option value="<?php echo esc_attr($valor_es); ?>" <?php selected($categoria_seleccionada, $valor_es); ?>>
                            <?php echo esc_html($traducciones[$current_lang] ?? $traducciones['es']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        
        <!-- Descargable -->
        <tr>
            <th><label for="recurso_descargable"> Opciones de acceso</label></th>
            <td>
                <label style="display: block; margin-bottom: 8px;">
                    <input type="checkbox" id="recurso_descargable" name="recurso_descargable" value="1" <?php checked($es_descargable, '1'); ?> />
                    Recurso descargable
                </label>
                <label style="display: block;">
                    <input type="checkbox" id="recurso_visualizable" name="recurso_visualizable" value="1" <?php checked($es_visualizable, '1'); ?> />
                    Recurso visualizable
                </label>
            </td>
        </tr>
    </table>
    <?php
}

/* ==========================================================
    Callback: Autogenerar con IA
   ========================================================== */

/**
 * Renderiza el meta box de autogeneración con IA
 */
function neema_recursos_ia_callback($post) {
    ?>
    <div style="padding: 10px;">
        <p style="margin-top: 0; color: #666; font-size: 13px;">
            Autocompleta los campos del recurso utilizando inteligencia artificial.
        </p>
        <button type="button" id="neema-autogenerar-ia" class="button button-primary button-large" style="width: 100%; height: 40px; font-size: 14px;">
            Autocompletar con IA
        </button>
        <div id="neema-ia-error" style="margin-top: 15px; padding: 10px; background: #fcf3f3; border-left: 4px solid #dc3232; display: none; color: #dc3232;">
            <strong>Error:</strong>
            <span id="neema-ia-error-mensaje"></span>
        </div>
    </div>
    <?php
}
