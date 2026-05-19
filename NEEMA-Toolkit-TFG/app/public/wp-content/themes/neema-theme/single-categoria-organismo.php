<?php get_header(); ?>

<div class="page-categoria-organismo">
    <p class="back-categoria">
        <?php pll_e('Volver a ', 'General'); ?>
        <a href="<?php echo home_url(pll_current_language() . '/support-' . pll_current_language()); ?>">
            <?php pll_e('Servicios de apoyo', 'Servicios de Apoyo'); ?>
        </a>
    </p>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

        <?php
        function neema_get_categoria_field($post_id, $field) {
            $value = get_post_meta($post_id, '_categoria_organismo_' . $field, true);
            return $value ? $value : '';
        }

        $categoria_id = get_the_ID(); 
        $title       = get_the_title();
        $description = neema_get_categoria_field(get_the_ID(), 'description');
        ?>

        <div class="categoria-header">
            <h1 class="categoria-title"><?php echo esc_html($title); ?></h1>
        </div>

        <div class="categoria-content">

            <?php if ( $description ) : ?>
                <section class="categoria-description">
                    <?php echo wp_kses_post($description); ?>
                </section>
            <?php endif; ?>

        </div>

        <!-- Sección del buscador -->
        <section class="categoria-buscador">
            <h2 class="organismos-title"><?php pll_e('Buscador', 'Servicios de Apoyo'); ?></h2>
            <?php get_template_part('template-parts/buscador-organismos', null, array('categoria_id' => $categoria_id)); ?>
        </section>

        <!-- Sección de organismos -->
        <section class="organismos-section" id="seccion-organismos-categoria">
            <h2 class="organismos-title"><?php pll_e('Organismos', 'Servicios de Apoyo'); ?></h2>
            <div class="organismos-container" id="organismos-lista" data-categoria="<?php echo esc_attr($categoria_id); ?>" data-offset="6">
                <?php
                $categoria_translations = pll_get_post_translations($categoria_id);
                $categoria_ids = array_values($categoria_translations); 
                
                $args = array(
                    'post_type'      => 'organismo',
                    'posts_per_page' => 6,
                    'meta_query'     => array(
                        array(
                            'key'     => '_organismo_categoria',
                            'value'   => $categoria_ids,
                            'compare' => 'IN'
                        )
                    ),
                    'orderby' => 'title',
                    'order'   => 'ASC',
                    'lang'           => ''
                );
                
                $organismos = get_posts($args);
                $args_total = $args;
                $args_total['posts_per_page'] = -1;
                $total_organismos = count(get_posts($args_total));
                
                if (!empty($organismos)) {
                    foreach ($organismos as $organismo) {
                        neema_render_organismo($organismo->ID);
                    }
                } else {
                    echo '<p class="no-recursos-msg">';
                    pll_e('No hay organismos disponibles en esta categoría.', 'Servicios de Apoyo');
                    echo '</p>';
                }
                ?>
            </div>
            <?php if ($total_organismos > 6): ?>
            <button class="btn-cargar-mas-organismos" data-categoria-ids="<?php echo esc_attr(implode(',', $categoria_ids)); ?>" data-target="organismos-lista">
                <i class="fas fa-plus"></i>
            </button>
            <?php endif; ?>
        </section>

        <!-- Sección de resultados de búsqueda: SE MUESTRA solo cuando hay búsqueda -->
        <section class="resultados-busqueda-organismos" id="resultados-organismos-categoria" style="display: none;">
            <div class="organismos-container" id="organismos-resultados"></div>
        </section>

    <?php endwhile; endif; ?>
    <?php get_template_part('template-parts/funding-statement'); ?>
</div>

<?php get_footer(); ?>
