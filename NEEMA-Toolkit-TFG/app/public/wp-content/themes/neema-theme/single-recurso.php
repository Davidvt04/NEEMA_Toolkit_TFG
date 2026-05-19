<?php get_header(); ?>

<?php
$idioma = 'es';
if (isset($_GET['lang']) && in_array($_GET['lang'], ['es', 'en', 'fr'])) {
    $idioma = sanitize_text_field($_GET['lang']);
}
?>

<main class="single-recurso">
    <p class="back">
        <?php echo neema_translate('Volver a '); ?>
            <?php 
            $recurso_id = get_the_ID();
            $modulo_key = get_post_meta($recurso_id, '_recurso_modulo', true);
            $modulo_posts = get_posts(array(
                'post_type'      => 'modulo',
                'meta_query'     => array(
                    array(
                        'key'     => '_modulo_key',
                        'value'   => $modulo_key,
                        'compare' => '='
                    )
                ),
                'posts_per_page' => 1
            ));
            $modulo_es_id = $modulo_posts[0]->ID;
            $modulo_traducido_id = pll_get_post($modulo_es_id, $idioma);
            $modulo_title = get_the_title($modulo_traducido_id);
            $modulo_url = get_permalink($modulo_traducido_id);
            echo '<a href="' . esc_url($modulo_url) . '">';
            echo esc_html($modulo_title);
            echo '</a>';
            ?>
    </p>

    <?php
    if ( have_posts() ) :
        while ( have_posts() ) : the_post();
    ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <h1 class="recurso-title">
            <?php 
                $recurso_id = get_the_ID();
                $titulo = get_post_meta($recurso_id, '_recurso_titulo_' . $idioma, true);
                echo $titulo;
                $current_user_id = get_current_user_id();
                $is_favorited = $current_user_id ? is_recurso_favorito($current_user_id, $recurso_id) : false;
            ?>
        </h1>
         
        <div class="recurso-etiquetas-container">
            <?php
            echo '<p class="recurso-detalles-title">' . neema_translate('Detalles de este recurso:') . '</p>';
            $paises_ids = get_post_meta($recurso_id, '_recurso_paises', true);
            $paises_nombres = array();
            if (!empty($paises_ids) && is_array($paises_ids)) {
                foreach ($paises_ids as $pais_id) {
                    $pais_title = get_the_title($pais_id);
                    if ($pais_title) {
                        $paises_nombres[] = $pais_title;
                    }
                }
            }
            $paises_texto = !empty($paises_nombres) ? implode(' | ', $paises_nombres) : neema_translate('Cualquier país');
            $tipo_key = get_post_meta($recurso_id, '_recurso_tipo', true);
            $tipo_texto = '';
            if (!empty($tipo_key)) {
                $tipo_posts = get_posts(array(
                    'post_type'      => 'tipo-recurso',
                    'meta_query'     => array(
                        array(
                            'key'     => '_tipo_recurso_key',
                            'value'   => $tipo_key,
                            'compare' => '='
                        )
                    ),
                    'posts_per_page' => 1
                ));
                
                if (!empty($tipo_posts)) {
                    $tipo_es_id = $tipo_posts[0]->ID;
                    $tipo_traducido_id = pll_get_post($tipo_es_id, $idioma);
                    if ($tipo_traducido_id) {
                        $tipo_texto = get_the_title($tipo_traducido_id);
                    } else {
                        $tipo_texto = get_the_title($tipo_es_id);
                    }
                }
            }
            $tematicas_keys = get_post_meta($recurso_id, '_recurso_tematicas', true);
            $tematicas_nombres = array();
            if (!empty($tematicas_keys) && is_array($tematicas_keys)) {
                foreach ($tematicas_keys as $tematica_key) {
                    $tematica_posts = get_posts(array(
                        'post_type'      => 'tematica',
                        'meta_query'     => array(
                            array(
                                'key'     => '_tematica_key',
                                'value'   => $tematica_key,
                                'compare' => '='
                            )
                        ),
                        'posts_per_page' => 1
                    ));
                    
                    if (!empty($tematica_posts)) {
                        $tematica_es_id = $tematica_posts[0]->ID;
                        $tematica_traducido_id = pll_get_post($tematica_es_id, $idioma);
                        if ($tematica_traducido_id) {
                            $tematicas_nombres[] = get_the_title($tematica_traducido_id);
                        } else {
                            $tematicas_nombres[] = get_the_title($tematica_es_id);
                        }
                    }
                }
            }
            $tematicas_texto = !empty($tematicas_nombres) ? implode(' | ', $tematicas_nombres) : neema_translate('Sin temáticas');
            $regiones_keys = get_post_meta($recurso_id, '_recurso_regiones', true);
            $regiones_nombres = array();
            if (!empty($regiones_keys) && is_array($regiones_keys)) {
                foreach ($regiones_keys as $region_key) {
                    $region_posts = get_posts(array(
                        'post_type'      => 'region',
                        'meta_query'     => array(
                            array(
                                'key'     => '_region_key',
                                'value'   => $region_key,
                                'compare' => '='
                            )
                        ),
                        'posts_per_page' => 1
                    ));
                    
                    if (!empty($region_posts)) {
                        $region_es_id = $region_posts[0]->ID;
                        $region_traducido_id = pll_get_post($region_es_id, $idioma);
                        if ($region_traducido_id) {
                            $regiones_nombres[] = get_the_title($region_traducido_id);
                        } else {
                            $regiones_nombres[] = get_the_title($region_es_id);
                        }
                    }
                }
            }
            $regiones_texto = !empty($regiones_nombres) ? implode(' | ', $regiones_nombres) : neema_translate('Cualquier región');
            ?>

            <div class="etiqueta-item">
                <span class="etiqueta-valor"><?php echo esc_html($paises_texto); ?></span>
            </div>

            <div class="etiqueta-item">
                <span class="etiqueta-valor"><?php echo esc_html($tipo_texto); ?></span>
            </div>

            <div class="etiqueta-item">
                <span class="etiqueta-valor"><?php echo esc_html($tematicas_texto); ?></span>
            </div>

            <div class="etiqueta-item">
                <span class="etiqueta-valor"><?php echo esc_html($regiones_texto); ?></span>
            </div>
        </div>

        <div class="recurso-content">
            <?php if ( has_post_thumbnail() ) : ?>
            <div class="recurso-image">
                <?php the_post_thumbnail('large'); ?>
                
                <!-- Botón de favorito sobre la imagen -->
                <div class="recurso-bookmark-container">
                    <i class="fa <?php echo $is_favorited ? 'fa-bookmark' : 'fa-bookmark-o'; ?>" 
                       data-post-id="<?php echo esc_attr($recurso_id); ?>" 
                       data-favorited="<?php echo $is_favorited ? '1' : '0'; ?>"></i>
                    <span class="recurso-bookmark-text" 
                          data-post-id="<?php echo esc_attr($recurso_id); ?>" 
                          data-favorited="<?php echo $is_favorited ? '1' : '0'; ?>">
                        <?php echo $is_favorited ? neema_translate('Guardado') : neema_translate('Guardar'); ?>
                    </span>
                </div>
            </div>
            <?php endif; ?>
            <div class="recurso-description">
                <?php 
                    $descripcion = get_field('descripcion_' . $idioma);
                    echo $descripcion;
                ?>
            </div>
        </div>

        <?php
            $archivo_es = get_field('archivo_es');
            $archivo_en = get_field('archivo_en');
            $archivo_fr = get_field('archivo_fr');
            $tiene_archivos = !empty($archivo_es) || !empty($archivo_en) || !empty($archivo_fr);
        ?>

       <?php 
            $archivos_disponibles = array();
            if ($archivo_es) $archivos_disponibles['es'] = $archivo_es;
            if ($archivo_en) $archivos_disponibles['en'] = $archivo_en;
            if ($archivo_fr) $archivos_disponibles['fr'] = $archivo_fr;
            if (!empty($archivos_disponibles)):
                $idioma_archivo_defecto = isset($archivos_disponibles[$idioma]) ? $idioma : array_key_first($archivos_disponibles);
        ?>
        
        <div class="recurso-file">
            
            <!-- Selector de idioma del archivo -->
            <?php if (count($archivos_disponibles) > 1): ?>
            <div class="recurso-file-lang-selector">
                <?php 
                    $lang_labels = array('en' => 'ENG', 'fr' => 'FRA', 'es' => 'ESP');
                    $lang_order = array('en', 'fr', 'es');
                    foreach ($lang_order as $lang): 
                        if (!isset($archivos_disponibles[$lang])) continue;
                        $archivo = $archivos_disponibles[$lang];
                ?>
                    <button class="lang-archivo-btn <?php echo ($lang === $idioma_archivo_defecto) ? 'active' : ''; ?>" 
                            data-lang="<?php echo $lang; ?>">
                        <?php echo $lang_labels[$lang]; ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Contenedores de archivos por idioma -->
            <?php foreach ($archivos_disponibles as $lang => $archivo): 
                $url = esc_url($archivo['url']);
                $titulo = esc_html($archivo['title']);
                $ext = strtolower(pathinfo($url, PATHINFO_EXTENSION));
                $is_active = ($lang === $idioma_archivo_defecto);
            ?>
            
            <div class="archivo-container" data-lang="<?php echo $lang; ?>" style="display: <?php echo $is_active ? 'block' : 'none'; ?>;">
                <?php
                    $es_descargable = get_post_meta($recurso_id, '_recurso_descargable', true);
                ?>
                <?php if ($es_descargable === '1'): ?>
                <div class="recurso-file-download">
                    <?php if ( is_user_logged_in() ): ?>
                        <a href="<?php echo $url; ?>" class="btn-descargar" data-recurso-id="<?php echo $recurso_id; ?>" download>
                            <i class="fas fa-download"></i>
                        </a>
                    <?php else: ?>
                        <a href="#" class="btn-descargar no-login">
                            <i class="fas fa-download"></i> 
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="recurso-file-viewer">
                    <?php
                        $es_visualizable = get_post_meta($recurso_id, '_recurso_visualizable', true);
                        
                        if ($es_visualizable !== '1') {
                            echo '<div class="recurso-generico">';
                            echo '<i class="fa-solid fa-file"></i>';
                            echo '<p class="recurso-generico-title">' . $titulo . '.' . $ext . '</p>';
                            echo '</div>';
                        } elseif ( $ext === 'pdf' ) {
                            $viewer_height = wp_is_mobile() ? '500px' : '700px';
                            echo do_shortcode(
                                '[pdfjs-viewer url="' . $url . '" viewer_width="100%" viewer_height="' . $viewer_height . '" download="false" print="false" fullscreen="false"]'
                            ); 
                        } elseif ( in_array($ext, ['jpg','jpeg','png','gif','webp']) ) {
                            echo '<div class="recurso-img-wrapper">';
                            echo '<img src="' . $url . '" class="recurso-imagen-protegida" oncontextmenu="return false;" draggable="false" />';
                            echo '</div>';
                        } else {
                            echo '<div class="recurso-generico">';
                            echo '<i class="fa-solid fa-file"></i>';
                            echo '<p class="recurso-generico-title">' . $titulo . '.' . $ext . '</p>';
                            echo '</div>';
                        }
                    ?>
                </div>
            </div>
            
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!$tiene_archivos): ?>
        <div style="margin-bottom: 20%;"></div>
        <?php endif; ?>


    </article>

    <?php
        endwhile;
    endif;
    ?>
    <?php get_template_part('template-parts/funding-statement'); ?>
</main>

<?php 
get_template_part('template-parts/modal-login', null, array(
    'modal_id' => 'modal-login',
    'title' => 'Acceso restringido',
    'message' => 'Debes iniciar sesión para descargar este recurso.',
    'use_polylang' => false
)); 
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const langBtns = document.querySelectorAll('.lang-btn');
    langBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const btnText = this.textContent.trim();
            let targetLang = '';
            if (btnText === 'ESP') targetLang = 'es';
            else if (btnText === 'ENG') targetLang = 'en';
            else if (btnText === 'FRA') targetLang = 'fr';
            
            if (targetLang) {
                const baseUrl = window.location.origin + window.location.pathname;
                window.location.href = baseUrl + '?lang=' + targetLang;
            }
        });
    });
    const langArchivoBtns = document.querySelectorAll('.lang-archivo-btn');
    const archivoContainers = document.querySelectorAll('.archivo-container');

    langArchivoBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetLang = this.getAttribute('data-lang');
            langArchivoBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            archivoContainers.forEach(container => {
                if (container.getAttribute('data-lang') === targetLang) {
                    container.style.display = 'block';
                } else {
                    container.style.display = 'none';
                }
            });
        });
    });
    document.querySelectorAll('.recurso-imagen-protegida').forEach(img => {
        img.addEventListener('dragstart', function(e) {
            e.preventDefault();
            return false;
        });
        img.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
        img.addEventListener('selectstart', function(e) {
            e.preventDefault();
            return false;
        });
    });
    document.querySelectorAll('.btn-descargar[data-recurso-id]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const recursoId = this.getAttribute('data-recurso-id');
            if (recursoId) {
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'neema_track_download',
                        recurso_id: recursoId,
                        nonce: '<?php echo wp_create_nonce('neema_track_download'); ?>'
                    })
                }).catch(err => {});
            }
        });
    });
});
</script>

<?php get_footer(); ?>
