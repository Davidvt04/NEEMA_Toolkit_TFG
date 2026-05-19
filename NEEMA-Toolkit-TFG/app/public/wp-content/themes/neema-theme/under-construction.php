<?php
/* Template Name: En Construcción */
get_header();
?>

<style>
	:root{--neema-primary:#3D3073;--neema-blue:#3D3073;--neema-light:#f6ebf8;--neema-text:#222}
	.under-construction{min-height:60vh;display:flex;align-items:center;justify-content:center;padding:4rem 1rem;text-align:center;background:linear-gradient(180deg,var(--neema-light),#ffffff);color: #3D3073;font-family:system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial}
	.under-construction .card{max-width:720px;width:100%;padding:2rem;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.06);background:#fff;border:1px solid rgba(61,48,115,0.08)}
	.under-construction .icon{width:96px;height:96px;margin:0 auto 0.75rem;display:block;fill:var(--neema-primary)}
	.under-construction .fa-wrench{display:block;margin:0 auto .6rem;font-size:72px;color:var(--neema-primary);line-height:1}
	.under-construction h1{font-size:2rem;margin:0 0 .5rem;color:var(--neema-primary)}
	.under-construction p{margin:.5rem 0;color:#555}
	.under-construction .meta{margin-top:1rem;font-size:.875rem;color:#666}
	.under-construction a.button{display:inline-block;margin-top:1rem;padding:.6rem 1rem;border-radius:8px;background:var(--neema-blue);color:#fff;text-decoration:none;font-weight:600;border:2px solid #3D3073;transition:background-color .2s,color .2s,opacity .2s}
	.under-construction a.button:hover{background:var(--neema-light);color:var(--neema-blue);border:2px solid var(--neema-blue);opacity:1}
	@media (max-width:600px){.under-construction{padding:3rem 1rem}.under-construction h1{font-size:1.5rem}}
</style>

<?php
$current_url = esc_url( ( ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ) ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

?>

<main class="under-construction" role="main">
	<div class="card" aria-labelledby="uc-title">
        <i class="fa-solid fa-wrench"></i>
		<h1 id="uc-title"><?php echo pll__('Sitio en construcción'); ?></h1>

		<p><?php echo sprintf( pll__('La página a la que has accedido (%s) está todavía en desarrollo.'), '<strong>' . esc_html( $current_url ) . '</strong>' ); ?></p>

		<a class="button" href="<?php echo esc_url( home_url() ); ?>"><?php echo pll__('Volver al inicio'); ?></a>

	</div>
</main>

<?php
get_footer();
?>