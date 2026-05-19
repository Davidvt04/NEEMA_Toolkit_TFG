<?php
get_header();
?>

<main class="page-404">
  <section class="error-content">
    <h1>¡Ups!</h1>
    <h2><?php pll_e("Parece que esta página no existe."); ?></h2>
    <p><?php pll_e("Es posible que el enlace no exista o que la página haya sido movida."); ?></p>
    <a href="<?php echo home_url(); ?>" class="btn-volver"><?php pll_e("Volver al inicio"); ?></a>
  </section>
</main>

<?php
get_footer();
?>
