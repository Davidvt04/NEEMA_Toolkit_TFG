<?php
/**
 * Template part: Modal de login
 * 
 * @param string $modal_id      ID del modal (default: 'modal-login')
 * @param string $title         Título del modal (default: 'Acceso restringido')
 * @param string $message       Mensaje del modal
 * @param bool   $use_polylang  Si usar pll_e() o neema_translate() (default: true)
 */

$modal_id = isset($args['modal_id']) ? $args['modal_id'] : 'modal-login';
$title = isset($args['title']) ? $args['title'] : 'Acceso restringido';
$message = isset($args['message']) ? $args['message'] : '';
$use_polylang = isset($args['use_polylang']) ? $args['use_polylang'] : true;
?>

<!-- Modal de advertencia -->
<div id="<?php echo esc_attr($modal_id); ?>" class="modal-overlay">
    <div class="modal-content">
        <h2>
            <?php 
            if ($use_polylang) {
                pll_e($title); 
            } else {
                echo neema_translate($title);
            }
            ?>
        </h2>
        <p>
            <?php 
            if ($message) {
                if ($use_polylang) {
                    pll_e($message);
                } else {
                    echo neema_translate($message);
                }
            }
            ?>
        </p>
        <a href="<?php echo wp_login_url( get_permalink() ); ?>" class="btn-login">
            <?php 
            if ($use_polylang) {
                pll_e('Iniciar sesión'); 
            } else {
                echo neema_translate('Iniciar sesión');
            }
            ?>
        </a>
        <button class="btn-cerrar">
            <?php 
            if ($use_polylang) {
                pll_e('Cerrar'); 
            } else {
                echo neema_translate('Cerrar');
            }
            ?>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('<?php echo esc_js($modal_id); ?>');
    if (!modal) return;
    
    const btnCerrar = modal.querySelector('.btn-cerrar');
    
    if (btnCerrar) {
        btnCerrar.addEventListener('click', function() {
            modal.classList.remove('active');
        });
    }
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });
});
</script>
