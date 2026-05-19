<?php get_header(); ?>


<div class="page-module">
    <p class="back-modulo">
        <?php pll_e('Volver a ', 'General'); ?>
            <a href="<?php echo home_url(pll_current_language() . '/fnr-' . pll_current_language()); ?>">
            <?php pll_e('Resiliencia Alimentaria y Nutricional', 'General'); ?>
        </a>
    </p>

    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

        <?php
        $lang = function_exists('pll_current_language') ? pll_current_language() : 'es';
        $lang = substr($lang, 0, 2);

        function neema_get_field($post_id, $field) {
            $value = get_post_meta($post_id, '_' . $field, true);
            return $value ? $value : '';
        }

        $title       = get_the_title();
        $description = neema_get_field(get_the_ID(), 'modulo_description');
        $objective   = neema_get_field(get_the_ID(), 'modulo_objective');
        $skills      = neema_get_field(get_the_ID(), 'modulo_skills');
        ?>

        <div class="module-header">
            <h1 class="module-title"><?php echo esc_html($title); ?></h1>
        </div>

        <div class="module-content">

            <?php if ( $description ) : ?>
                <section class="module-description">
                    <?php echo wp_kses_post($description); ?>
                </section>
            <?php endif; ?>

            <div class="module-side">

                <?php if ( $objective ) : ?>
                    <section class="module-objective">
                        <h2><?php pll_e('Objetivo'); ?></h2>
                        <?php echo wp_kses_post($objective); ?>
                    </section>
                <?php endif; ?>

                <?php if ( $skills ) : ?>
                    <section class="module-skills">
                        <h2><?php pll_e('Competencias a adquirir'); ?></h2>
                        <?php echo wp_kses_post($skills); ?>
                    </section>
                <?php endif; ?>

            </div>

        </div>
        
        <?php
        $spanish_id = pll_get_post(get_the_ID(), 'es');
        $modulo_key = get_post_meta($spanish_id, '_modulo_key', true);
        $current_user_id = get_current_user_id(); 
        $user_favorite_ids = $current_user_id ? get_recursos_favoritos( $current_user_id ) : array();
        set_query_var( 'user_favorite_ids', $user_favorite_ids );
        ?>
        
        <div class="buscador-container">
            <h2><?php pll_e('Buscador'); ?></h2>
            <?php get_template_part('template-parts/buscador-recursos', null, array('modulo_key' => $modulo_key)); ?>
        </div>
        
        <!-- Recursos por defecto: SE OCULTAN cuando hay búsqueda activa -->
        <div class="recursos-container" id="recursos-default">
            <h2><?php pll_e('Recursos'); ?></h2>

            <!-- Contextuales -->
            <h3 class="recursos-container-subtitle"><?php pll_e('Contextuales'); ?></h3>
            <div class="recursos-grid" id="recursos-contextuales" data-categoria="Contextual" data-modulo="<?php echo esc_attr($modulo_key); ?>" data-offset="6">
                <?php 
                    $recursos_contextuales = neema_cargar_recursos('Contextual', $modulo_key);
                    
                    $total_contextuales = neema_num_total_recursos('Contextual', $modulo_key);
                    
                    if ($recursos_contextuales) {
                        foreach($recursos_contextuales as $recurso) {
                            neema_render_recurso($recurso->ID);
                        }
                    } else {
                        echo '<p class="no-recursos-msg">';
                        pll_e('No se han encontrado resultados', 'Módulo Page');
                        echo '</p>';
                    }
                ?>
            </div>
            <?php if ($total_contextuales > 6): ?>
            <button class="btn-cargar-mas" data-categoria="Contextual" data-target="recursos-contextuales">
                <i class="fas fa-plus"></i>
            </button>
            <?php endif; ?>

            <!-- Formativos -->
            <h3 class="recursos-container-subtitle"><?php pll_e('Formativos'); ?></h3>
            <div class="recursos-grid" id="recursos-formativos" data-categoria="Formativo" data-modulo="<?php echo esc_attr($modulo_key); ?>" data-offset="6">
                <?php 
                    $recursos_formativos = neema_cargar_recursos('Formativo', $modulo_key);
                    
                    $total_formativos = neema_num_total_recursos('Formativo', $modulo_key);
                    
                    if ($recursos_formativos) {
                        foreach($recursos_formativos as $recurso) {
                            neema_render_recurso($recurso->ID);
                        }
                    } else {
                        echo '<p class="no-recursos-msg">';
                        pll_e('No se han encontrado resultados', 'Módulo Page');
                        echo '</p>';
                    }
                ?>
            </div>
            <?php if ($total_formativos > 6): ?>
            <button class="btn-cargar-mas" data-categoria="Formativo" data-target="recursos-formativos">
                <i class="fas fa-plus"></i>
            </button>
            <?php endif; ?>

            <!-- Metodológicos -->
            <h3 class="recursos-container-subtitle"><?php pll_e('Metodológicos'); ?></h3>
            <div class="recursos-grid" id="recursos-metodologicos" data-categoria="Metodológico" data-modulo="<?php echo esc_attr($modulo_key); ?>" data-offset="6">
                <?php 
                    $recursos_metodologicos = neema_cargar_recursos('Metodológico', $modulo_key);
                    
                    $total_metodologicos = neema_num_total_recursos('Metodológico', $modulo_key);
                    
                    if ($recursos_metodologicos) {
                        foreach($recursos_metodologicos as $recurso) {
                            neema_render_recurso($recurso->ID);
                        }
                    } else {
                        echo '<p class="no-recursos-msg">';
                        pll_e('No se han encontrado resultados', 'Módulo Page');
                        echo '</p>';
                    }
                ?>
            </div>
            <?php if ($total_metodologicos > 6): ?>
            <button class="btn-cargar-mas" data-categoria="Metodológico" data-target="recursos-metodologicos">
                <i class="fas fa-plus"></i>
            </button>
            <?php endif; ?>

            <!-- Procedimentales -->
            <h3 class="recursos-container-subtitle"><?php pll_e('Procedimentales'); ?></h3>
            <div class="recursos-grid" id="recursos-procedimentales" data-categoria="Procedimental" data-modulo="<?php echo esc_attr($modulo_key); ?>" data-offset="6">
                <?php 
                    $recursos_procedimentales = neema_cargar_recursos('Procedimental', $modulo_key);
                    
                    $total_procedimentales = neema_num_total_recursos('Procedimental', $modulo_key);
                    
                    if ($recursos_procedimentales) {
                        foreach($recursos_procedimentales as $recurso) {
                            neema_render_recurso($recurso->ID);
                        }
                    } else {
                        echo '<p class="no-recursos-msg">';
                        pll_e('No se han encontrado resultados', 'Módulo Page');
                        echo '</p>';
                    }
                ?>
            </div>
            <?php if ($total_procedimentales > 6): ?>
            <button class="btn-cargar-mas" data-categoria="Procedimental" data-target="recursos-procedimentales">
                <i class="fas fa-plus"></i>
            </button>
            <?php endif; ?>

        </div>

        <!-- Resultados de búsqueda: SE MUESTRAN solo cuando hay búsqueda -->
        <div class="recursos-container" id="recursos-filtrados" style="display: none;">
            <div id="busqueda-resultados"></div>
        </div>

    <?php endwhile; endif; ?>
    <?php get_template_part('template-parts/funding-statement'); ?>
</div>

<?php 
get_template_part('template-parts/modal-login', null, array(
    'modal_id' => 'modal-login-favoritos',
    'title' => 'Acceso restringido',
    'message' => 'Debes iniciar sesión para guardar recursos favoritos.',
    'use_polylang' => true
)); 
?>

<?php get_footer(); ?>
