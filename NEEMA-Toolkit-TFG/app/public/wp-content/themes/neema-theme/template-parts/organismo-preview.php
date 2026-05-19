<?php
$data = get_query_var('organismo_data');
if (!$data) return;
?>

<article class="organismo-preview">
    
    <?php if (!empty($data['thumbnail'])) : ?>
        <div class="organismo-thumb">
            <?php echo $data['thumbnail']; ?>
        </div>
    <?php endif; ?>

    <div class="organismo-info">
        <h3 class="organismo-preview-title"><?php echo esc_html($data['title']); ?></h3>
        
        <?php if (!empty($data['description'])) : ?>
            <div class="organismo-description">
                <?php echo wp_kses_post($data['description']); ?>
            </div>
        <?php endif; ?>
        
        <div class="organismo-sections">
            <div class="organismo-alcance">
                <h4><?php pll_e('Alcance', 'Servicios de Apoyo'); ?></h4>
                <p>
                    <strong><?php pll_e('Ámbito', 'Servicios de Apoyo'); ?>:</strong> 
                    <?php echo esc_html($data['ambito']); ?>
                </p>
                <?php if (!empty($data['paises'])) : ?>
                    <p>
                        <strong><?php pll_e('País/es', 'Servicios de Apoyo'); ?>:</strong> 
                        <?php echo esc_html($data['paises']); ?>
                    </p>
                <?php endif; ?>
                <?php if (!empty($data['ciudad'])) : ?>
                    <p>
                        <strong><?php pll_e('Ciudad/Localidad', 'Servicios de Apoyo'); ?>:</strong> 
                        <?php echo esc_html($data['ciudad']); ?>
                    </p>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($data['pagina_web']) || !empty($data['correo'])) : ?>
                <div class="organismo-contacto">
                    <h4><?php pll_e('Contacto', 'Servicios de Apoyo'); ?></h4>
                    <?php if (!empty($data['pagina_web'])) : ?>
                        <p>
                            <strong><?php pll_e('Web', 'Servicios de Apoyo'); ?>:</strong> 
                            <a href="<?php echo esc_url($data['pagina_web']); ?>" target="_blank" rel="noopener">
                                <?php echo esc_html($data['pagina_web']); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                    <?php if (!empty($data['correo'])) : ?>
                        <p>
                            <strong><?php pll_e('Email', 'Servicios de Apoyo'); ?>:</strong> 
                            <a href="mailto:<?php echo esc_attr($data['correo']); ?>">
                                <?php echo esc_html($data['correo']); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</article>
