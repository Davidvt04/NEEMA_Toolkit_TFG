<?php
/* Template Name: Forgot Password */

$success = false;
$error = '';
$current_user_email = '';

if ( is_user_logged_in() ) {
    $current_user = wp_get_current_user();
    $current_user_email = $current_user->user_email;
}

if ( isset( $_POST['reset-submit'] ) && isset( $_POST['neema_reset_nonce'] ) ) {
    if ( wp_verify_nonce( $_POST['neema_reset_nonce'], 'neema-reset-password' ) ) {
        
        $email = sanitize_email( $_POST['user_email'] );
        
        if ( empty( $email ) ) {
            $error = 'empty';
        } elseif ( ! is_email( $email ) ) {
            $error = 'invalid';
        } else {
            $user = get_user_by( 'email', $email );
            
            if ( $user ) {
                $token = wp_generate_password( 32, false );
                $expiry = time() + 3600; // 1 hora de validez
                update_user_meta( $user->ID, 'password_reset_token', $token );
                update_user_meta( $user->ID, 'password_reset_expiry', $expiry );
                $reset_link = home_url( pll_current_language() . '/restablecer-contrasena-' . pll_current_language() . '/?token=' . $token . '&email=' . urlencode( $email ) );
                $user_name = $user->display_name;
                $site_name = get_bloginfo('name');
                $site_url = home_url();
                $admin_email = get_option('admin_email');
                ob_start();
                include( get_template_directory() . '/email-templates/reset-password-email.php' );
                $message = ob_get_clean();
                $subject = pll__('Recuperación de contraseña');
                $headers = array(
                    'Content-Type: text/html; charset=UTF-8',
                    'From: ' . $site_name . ' <' . $admin_email . '>'
                );
                
                if ( wp_mail( $email, $subject, $message, $headers ) ) {
                    $success = true;
                } else {
                    $error = 'email_failed';
                }
            } else {
                $success = true;
            }
        }
    }
}

get_header();
?>

<?php $home_link = pll_home_url( pll_current_language() ); ?>
<p class="back-home" style="text-align:left; margin-top:1rem;">
    <?php pll_e('Volver a '); ?>
    <a href="<?php echo esc_url( $home_link ); ?>"><?php pll_e('Home'); ?></a>
</p>

<div class="login-page">
    <div class="login-container">
        <h2><?php pll_e('Recuperar contraseña'); ?></h2>

        <?php if ( $success ): ?>
            <div class="login-success">
                <?php echo pll__('Si el correo electrónico está registrado, recibirás un enlace para restablecer tu contraseña.'); ?>
            </div>
            <p class="login-register-link" style="margin-bottom: 0;">
                <a href="<?php echo home_url(pll_current_language() . '/login-' . pll_current_language()); ?>"><?php pll_e('Volver al inicio de sesión'); ?></a>
            </p>
        <?php else: ?>

            <?php
            if ( ! empty( $error ) ) {
                switch ( $error ) {
                    case 'empty':
                        echo '<div class="login-error">' . pll__('Por favor, introduce tu correo electrónico.') . '</div>';
                        break;
                    case 'invalid':
                        echo '<div class="login-error">' . pll__('Por favor, introduce un correo electrónico válido.') . '</div>';
                        break;
                    case 'email_failed':
                        echo '<div class="login-error">' . pll__('Error al enviar el correo. Inténtalo de nuevo.') . '</div>';
                        break;
                }
            }
            ?>

            <p style="margin-top: 0;"><?php pll_e('Introduce tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.'); ?></p>

            <form name="resetform" id="resetform" action="<?php echo esc_url( get_permalink() ); ?>" method="post">
                <label for="user_email"><?php pll_e('Correo electrónico'); ?></label>
                <input type="email" name="user_email" id="user_email" value="<?php echo esc_attr($current_user_email); ?>" required>

                <input type="submit" name="reset-submit" id="reset-submit" value="<?php pll_e('Enviar enlace'); ?>">
                <?php wp_nonce_field( 'neema-reset-password', 'neema_reset_nonce' ); ?>
            </form>

            <p class="login-register-link" style="margin-bottom: 0;">
                <?php if ( is_user_logged_in() ): ?>
                    <a href="<?php echo home_url('/perfil-' . pll_current_language() . '/'); ?>"><?php pll_e('Volver al perfil'); ?></a>
                <?php else: ?>
                    <a href="<?php echo home_url(pll_current_language() . '/login-' . pll_current_language()); ?>"><?php pll_e('Volver al inicio de sesión'); ?></a>
                <?php endif; ?>
            </p>

        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>