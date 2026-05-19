<?php
/*
Template Name: Resiliencia Alimentaria y Nutricional
*/

get_header();
?>

<?php $home_link = pll_home_url( pll_current_language() ); ?>
<p class="back-home" style="text-align:left; margin-top:1rem;">
    <?php pll_e('Volver a '); ?>
    <a href="<?php echo esc_url( $home_link ); ?>"><?php pll_e('Home'); ?></a>
</p>
  
<main class="page-resiliencia" style="position: relative;">    
    <section class="resiliencia-intro">
        <h1><?php pll_e('Cómo obtener Resiliencia Alimentaria y Nutricional', 'Resiliencia Alimentaria y Nutricional'); ?></h1>
        <p><?php pll_e('En esta sección encontrarás herramientas de apoyo para el diseño de cursos, entrenamientos, proyectos de cooperación... vinculadas a la resiliencia alimentaria y nutricional.', 'Resiliencia Alimentaria y Nutricional'); ?></p>
    </section>
    
    <section class="resiliencia-lista" id="seccion-designs">
        <h2><?php pll_e('Propuestas de diseños', 'Resiliencia Alimentaria y Nutricional'); ?></h2>
        <div class="lista-container">
            <?php neema_display_designs(); ?>
        </div>
    </section>

    <?php get_template_part('template-parts/funding-statement'); ?>
</main>

<?php get_footer(); ?>
