<?php
/**
 * Email Template: Account Activation
 * 
 * Variables:
 * @var string $activation_url URL de activación de la cuenta
 * @var string $site_name Nombre del sitio
 * @var string $site_url URL del sitio
 * @var string $admin_email Email del administrador
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div style="max-width: 560px;padding: 20px;background: #3D3073;border-radius: 10px;margin: 40px auto;font-family: 'Open Sans', Helvetica, Arial, sans-serif;font-size: 15px;color: #ffffff">
    <!-- Header -->
    <div style="text-align: center;font-weight: bold;font-size: 28px;padding: 15px 0;border-bottom: 3px solid #5A45A0">
        <?php echo esc_html($site_name); ?>
    </div>
    
    <!-- Body -->
    <div style="padding: 30px;line-height: 1.6">
        <h2 style="font-size: 22px;font-weight: 600;text-align: center;margin-bottom: 25px">
            <?php pll_e('¡Gracias por registrarte en NEEMA!'); ?>
        </h2>
        <p style="text-align: center;font-size: 16px;margin-bottom: 30px">
            <?php pll_e('Para activar tu cuenta, por favor haz clic en el botón de abajo:'); ?>
        </p>
        <div style="text-align: center;margin-bottom: 40px">
            <a style="background-color: #f6ebf8;color: #3d3073;padding: 12px 30px;text-decoration: none;border-radius: 5px;font-weight: 600" href="<?php echo esc_url($activation_url); ?>">
                <?php pll_e('Activar mi cuenta'); ?>
            </a>
        </div>
        <p style="text-align: center;font-size: 14px;color: #e0dff4">
            <?php pll_e('¿Necesitas ayuda?'); ?> 
            <a style="color: #ff9f1c;text-decoration: none" href="mailto:<?php echo esc_attr($admin_email); ?>">
                <?php pll_e('Contáctanos'); ?>
            </a>
        </p>
    </div>
    
    <!-- Footer -->
    <div style="text-align: center;font-size: 13px;color: #d1cfe8;padding: 20px 30px">
        <?php pll_e('Gracias'); ?>,<br />
        <?php pll_e('El equipo de NEEMA'); ?> <br />
    </div>
</div>
