<?php
/*
Template Name: Programas RAN
*/
get_header();
?>

<?php $home_link = pll_home_url( pll_current_language() ); ?>
<p class="back-home" style="text-align:left; margin-top:1rem;">
    <?php pll_e('Volver a '); ?>
    <a href="<?php echo esc_url( $home_link ); ?>"><?php pll_e('Home'); ?></a>
</p>
  
<main class="page-programas" style="position: relative;">    
    <section class="programas-intro">
        <h1><?php pll_e('Resumen de los programas de Resiliencia Alimentaria y Nutricional', 'Programas RAN'); ?></h1>
        <p><?php pll_e('Como parte del Proyecto NEEMA, liderado por la Universidad de Sevilla ...', 'Programas RAN'); ?></p>
    </section>
    <section class="programas-buscador">
        <h2><?php pll_e('Buscador', 'Programas RAN'); ?></h2>
        <?php get_template_part('template-parts/buscador-recursos', null, array('modulo_key' => null)); ?>
    </section>
    
    <section class="programas-lista" id="seccion-modulos">
        <h2><?php pll_e('Módulos', 'Programas RAN'); ?></h2>
        <div class="lista-container">
            <?php neema_display_modulos(); ?>
        </div>
    </section>

    <section class="resultados-busqueda" id="resultados-recursos" style="display: none;">
        <div id="resultados-container"></div>
    </section>
    <?php get_template_part('template-parts/funding-statement'); ?>
</main>

<?php 
get_template_part('template-parts/modal-login', null, array(
    'modal_id' => 'modal-login-favoritos',
    'title' => 'Acceso restringido',
    'message' => 'Debes iniciar sesión para guardar recursos favoritos.',
    'use_polylang' => true
)); 
?>

<?php get_footer(); ?>
