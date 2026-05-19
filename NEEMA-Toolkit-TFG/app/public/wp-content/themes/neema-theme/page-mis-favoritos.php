<?php
/*
Template Name: Mis Favoritos
*/
get_header();
?>
<?php $home_link = pll_home_url( pll_current_language() ); ?>
<p class="back-home" style="text-align:left; margin-top:1rem;">
    <?php pll_e('Volver a '); ?>
    <a href="<?php echo esc_url( $home_link ); ?>"><?php pll_e('Home'); ?></a>
</p>
<?php
$idioma = 'es'; 
if (isset($_GET['lang']) && in_array($_GET['lang'], ['es', 'en', 'fr'])) {
    $idioma = sanitize_text_field($_GET['lang']);
}

function render_favoritos_empty_state() {
    ?>
    <div class="favoritos-empty">
        <div class="favoritos-empty-icon">
            <i class="fa fa-bookmark-o"></i>
        </div>
        <h3><?php echo neema_translate('Aún no has guardado ningún recurso'); ?></h3>
        <p class="favoritos-empty-subtitle"><?php echo neema_translate('Guarda tus recursos favoritos para acceder a ellos rápidamente'); ?></p>
        <a href="<?php echo home_url( pll_current_language() . '/fnr-' . pll_current_language() ); ?>" class="btn-explorar">
            <i class="fa fa-compass"></i> <?php echo neema_translate('¡Explorar recursos!'); ?>
        </a>
    </div>
    <?php
}
?>

<main class="page-mis-favoritos">
    <div class="recursos-container">
        <h2><?php echo neema_translate('Mis Recursos Guardados'); ?></h2>

        <?php
        $current_user_id = get_current_user_id();
        
        if ( ! $current_user_id ) :
            ?>
            <div class="favoritos-not-logged">
                <p><?php echo neema_translate('Debes iniciar sesión para ver tus recursos guardados.'); ?></p>
                <a href="<?php echo wp_login_url( get_permalink() ); ?>" class="btn-login">
                    <?php echo neema_translate('Iniciar sesión'); ?>
                </a>
            </div>
            <?php
        else :
            $user_favorite_ids = get_recursos_favoritos( $current_user_id );
            
            if ( empty( $user_favorite_ids ) ) :
                render_favoritos_empty_state();
            else :
                set_query_var( 'user_favorite_ids', $user_favorite_ids );
                $recursos_query = new WP_Query( array(
                    'post_type'      => 'recurso',
                    'post__in'       => $user_favorite_ids,
                    'posts_per_page' => -1,
                    'orderby'        => 'post__in',
                    'lang'           => ''
                ) );
                
                if ( $recursos_query->have_posts() ) :
                    ?>
                    <div class="recursos-grid">
                        <?php
                        while ( $recursos_query->have_posts() ) : $recursos_query->the_post();
                            neema_render_recurso( get_the_ID() );
                        endwhile;
                        wp_reset_postdata();
                        ?>
                    </div>
                    <?php
                else :
                    render_favoritos_empty_state();
                endif;
            endif;
        endif;
        ?>
    </div>
</main>

<?php 
if ( $current_user_id ) {
    get_template_part('template-parts/modal-login', null, array(
        'modal_id' => 'modal-login-favoritos',
        'title' => 'Acceso restringido',
        'message' => 'Debes iniciar sesión para guardar recursos favoritos.',
        'use_polylang' => true
    )); 
}
?>

<?php get_footer(); ?>
