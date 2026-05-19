<?php
/*
Template Name: Servicios de Apoyo
*/
get_header();
?>

<?php $home_link = pll_home_url( pll_current_language() ); ?>
<p class="back-home" style="text-align:left; margin-top:1rem;">
    <?php pll_e('Volver a '); ?>
    <a href="<?php echo esc_url( $home_link ); ?>"><?php pll_e('Home'); ?></a>
</p>
  
<main class="page-servicios-apoyo" style="position: relative;">    
    <section class="servicios-intro">
        <h1><?php pll_e('Cómo tener acceso a servicios que puedan ofrecer apoyo.', 'Servicios de Apoyo'); ?></h1>
        <p><?php pll_e('En esta sección encontrarás diferentes organismos que ofrecen apoyo técnico, institucional y operativo en el ámbito de la resiliencia alimentaria y nutricional. Selecciona el tipo de organización que mejor se adapte a tu situación para acceder a más información.', 'Servicios de Apoyo'); ?></p>
    </section>
    
    <!-- Sección del buscador -->
    <section class="servicios-buscador">
        <h2><?php pll_e('Buscador', 'Servicios de Apoyo'); ?></h2>
        <?php get_template_part('template-parts/buscador-organismos', null, array('categoria_id' => null)); ?>
    </section>
    
    <!-- Sección de servicios de apoyo: SE OCULTA cuando hay búsqueda activa -->
    <section class="servicios-lista" id="seccion-categorias">
        <h2><?php pll_e('Servicios de apoyo', 'Servicios de Apoyo'); ?></h2>
        <div class="lista-container">
            <?php neema_display_categorias_organismos(); ?>
        </div>
    </section>

    <!-- Sección de resultados: SE MUESTRA solo cuando hay búsqueda -->
    <section class="resultados-busqueda-organismos" id="resultados-organismos" style="display: none;">
        <div class="organismos-container" id="organismos-resultados"></div>
    </section>
    <?php get_template_part('template-parts/funding-statement'); ?>
</main>

<?php get_footer(); ?>
