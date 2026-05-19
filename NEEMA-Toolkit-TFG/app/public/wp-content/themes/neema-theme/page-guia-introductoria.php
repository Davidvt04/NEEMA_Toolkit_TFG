<?php 
/**
 * Template Name: Guía Introductoria
 * 
 * Página para mostrar la guía introductoria con visor PDF multiidioma
 */

get_header(); 
?>
<?php $home_link = pll_home_url( pll_current_language() ); ?>
<p class="back-home" style="text-align:left; margin-top:1rem;">
    <?php pll_e('Volver a '); ?>
    <a href="<?php echo esc_url( $home_link ); ?>"><?php pll_e('Home'); ?></a>
</p>
<?php

$idioma = function_exists('pll_current_language') ? pll_current_language() : 'es';

$guias = get_posts(array(
    'post_type' => 'guia-intro',
    'posts_per_page' => 1,
    'post_status' => 'publish'
));

if (empty($guias)) {
    $guia = null;
    $guia_id = 0;
} else {
    $guia = $guias[0];
    $guia_id = $guia->ID;
}

$titulo_actual = neema_translate('Guía Introductoria');

function neema_normalizar_archivo($raw_meta) {
    if (empty($raw_meta)) {
        return null;
    }
    if (is_numeric($raw_meta)) {
        $url = wp_get_attachment_url((int) $raw_meta);
        $title = get_the_title((int) $raw_meta);
        return $url ? array('url' => $url, 'title' => $title) : null;
    }
    if (is_array($raw_meta)) {
        if (!empty($raw_meta['url'])) {
            return $raw_meta;
        }
        if (!empty($raw_meta['file'])) {
            return array('url' => $raw_meta['file'], 'title' => basename($raw_meta['file']));
        }
        if (!empty($raw_meta[0])) {
            $url = is_array($raw_meta[0]) ? $raw_meta[0]['url'] : $raw_meta[0];
            return array('url' => $url, 'title' => basename($url));
        }
    }
    if (is_object($raw_meta)) {
        if (!empty($raw_meta->url)) {
            return array('url' => $raw_meta->url, 'title' => $raw_meta->title ?? basename($raw_meta->url));
        }
        if (!empty($raw_meta->ID)) {
            $url = wp_get_attachment_url((int) $raw_meta->ID);
            return $url ? array('url' => $url, 'title' => get_the_title((int) $raw_meta->ID)) : null;
        }
    }
    if (is_string($raw_meta)) {
        return array('url' => trim($raw_meta), 'title' => basename($raw_meta));
    }
    
    return null;
}

function neema_normalizar_video($raw_meta) {
    if (empty($raw_meta)) {
        return null;
    }

    if (is_numeric($raw_meta)) {
        $url = wp_get_attachment_url((int) $raw_meta);
        return $url ? array('url' => $url) : null;
    }

    if (is_array($raw_meta)) {
        if (!empty($raw_meta['url'])) {
            return array('url' => $raw_meta['url']);
        }
        if (!empty($raw_meta['file'])) {
            return array('url' => $raw_meta['file']);
        }
        if (!empty($raw_meta[0])) {
            $url = is_array($raw_meta[0]) ? $raw_meta[0]['url'] : $raw_meta[0];
            return array('url' => $url);
        }
    }

    if (is_object($raw_meta)) {
        if (!empty($raw_meta->url)) {
            return array('url' => $raw_meta->url);
        }
        if (!empty($raw_meta->ID)) {
            $url = wp_get_attachment_url((int) $raw_meta->ID);
            return $url ? array('url' => $url) : null;
        }
    }

    if (is_string($raw_meta)) {
        return array('url' => trim($raw_meta));
    }

    return null;
}

function neema_get_youtube_id($url) {
    if (empty($url)) return '';
    // Soporta youtu.be, youtube.com/watch?v=, youtube.com/embed/
    if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|v\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/', $url, $m)) {
        return $m[1];
    }
    return '';
}
$archivo_es = neema_normalizar_archivo(get_post_meta($guia_id, 'guia_introductoria_en_espanol', true));
$archivo_en = neema_normalizar_archivo(get_post_meta($guia_id, 'guia_introductoria_en_ingles', true));
$archivo_fr = neema_normalizar_archivo(get_post_meta($guia_id, 'guia_introductoria_en_frances', true));
$archivos_disponibles = array();
if ($archivo_es) $archivos_disponibles['es'] = $archivo_es;
if ($archivo_en) $archivos_disponibles['en'] = $archivo_en;
if ($archivo_fr) $archivos_disponibles['fr'] = $archivo_fr;

$idioma_archivo_defecto = isset($archivos_disponibles[$idioma]) ? $idioma : array_key_first($archivos_disponibles);
?>

<main class="guia-introductoria">
    <h1 class="guia-title"><?php echo esc_html($titulo_actual); ?></h1>
    
    <p class="guia-intro-text">
        <?php echo neema_translate('¡Bienvenido/a a NEEMA Toolkit! Esta guía te acompañará paso a paso en el uso de la plataforma. Consúltala siempre que necesites ayuda o tengas alguna duda sobre cómo navegar y aprovechar al máximo todo el material disponible.'); ?>
    </p>

    <?php
        $video_meta_raw = neema_normalizar_video(get_post_meta($guia_id, 'video_introductorio', true));
        $youtube_id = '';
        if ($video_meta_raw && !empty($video_meta_raw['url'])) {
            $youtube_id = neema_get_youtube_id($video_meta_raw['url']);
        }
    ?>

    <?php if (!empty($youtube_id)): ?>
    <div class="guia-video">
        <div class="guia-video-inner">
            <iframe src="https://www.youtube.com/embed/<?php echo esc_attr($youtube_id); ?>?rel=0" title="Introducción NEEMA" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($archivos_disponibles)): ?>

    <div class="guia-file">
            
            <!-- Selector de idioma del archivo -->
            <?php if (count($archivos_disponibles) > 1): ?>
            <div class="guia-file-lang-selector">
                <?php 
                    $lang_labels = array('en' => 'ENG', 'fr' => 'FRA', 'es' => 'ESP');
                    $lang_order = array('en', 'fr', 'es');
                    foreach ($lang_order as $lang): 
                        if (!isset($archivos_disponibles[$lang])) continue;
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
                $is_active = ($lang === $idioma_archivo_defecto);
            ?>
            
            <div class="archivo-container" data-lang="<?php echo $lang; ?>" style="display: <?php echo $is_active ? 'block' : 'none'; ?>;">
                
                <!-- Botón de descarga (siempre disponible) -->
                <div class="guia-file-download">
                    <a href="<?php echo $url; ?>" class="btn-descargar" download>
                        <i class="fas fa-download"></i>
                    </a>
                </div>

                <!-- Visor PDF -->
                <div class="guia-file-viewer">
                    <?php 
                        $viewer_height = wp_is_mobile() ? '500px' : '700px';
                        echo do_shortcode(
                            '[pdfjs-viewer url="' . $url . '" viewer_width="100%" viewer_height="' . $viewer_height . '" download="false" print="false" fullscreen="false"]'
                        ); 
                    ?>
                </div>
            </div>
            
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <div class="guia-no-archivos guia-no-archivos--missing">
            <p><?php echo neema_translate("Vaya... ahora mismo no hay una guía introductoria disponible"); ?></p>
        </div>
    <?php endif; ?>
    <?php get_template_part('template-parts/funding-statement'); ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>

<?php get_footer(); ?>
