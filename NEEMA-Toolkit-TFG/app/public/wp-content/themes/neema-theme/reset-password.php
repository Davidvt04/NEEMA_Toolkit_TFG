<?php
/* Template Name: Reset Password */

if ( is_user_logged_in() ) {
    wp_redirect( home_url() );
    exit;
}

$token = isset( $_GET['token'] ) ? sanitize_text_field( $_GET['token'] ) : '';
$email = isset( $_GET['email'] ) ? sanitize_email( $_GET['email'] ) : '';
$valid_token = false;
$user = null;
$error = '';
$success = false;
if ( ! empty( $token ) && ! empty( $email ) ) {
    $user = get_user_by( 'email', $email );
    
    if ( $user ) {
        $saved_token = get_user_meta( $user->ID, 'password_reset_token', true );
        $expiry = get_user_meta( $user->ID, 'password_reset_expiry', true );
        
        if ( $saved_token === $token && $expiry > time() ) {
            $valid_token = true;
        }
    }
}

if ( isset( $_POST['reset-password-submit'] ) && isset( $_POST['neema_new_password_nonce'] ) ) {
    if ( wp_verify_nonce( $_POST['neema_new_password_nonce'], 'neema-new-password' ) ) {
        
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        $token_post = sanitize_text_field( $_POST['token'] );
        $email_post = sanitize_email( $_POST['email'] );
        
        $user = get_user_by( 'email', $email_post );
        
        if ( $user ) {
            $saved_token = get_user_meta( $user->ID, 'password_reset_token', true );
            $expiry = get_user_meta( $user->ID, 'password_reset_expiry', true );
            
            if ( $saved_token === $token_post && $expiry > time() ) {
                
                if ( empty( $new_password ) || empty( $confirm_password ) ) {
                    $error = 'empty';
                } elseif ( strlen( $new_password ) < 8 ) {
                    $error = 'short';
                } elseif ( $new_password !== $confirm_password ) {
                    $error = 'mismatch';
                } else {
                    wp_set_password( $new_password, $user->ID );
                    delete_user_meta( $user->ID, 'password_reset_token' );
                    delete_user_meta( $user->ID, 'password_reset_expiry' );
                    
                    $success = true;
                }
            } else {
                $error = 'expired';
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
        <h2><?php pll_e('Restablecer contraseña'); ?></h2>

        <?php if ( $success ): ?>
            <div class="login-success">
                <?php echo pll__('¡Contraseña actualizada con éxito!'); ?>
            </div>
            <p class="login-register-link" style="margin-bottom: 0;">
                <a href="<?php echo home_url(pll_current_language() . '/login-' . pll_current_language()); ?>"><?php pll_e('Iniciar sesión'); ?></a>
            </p>

        <?php elseif ( ! $valid_token ): ?>
            <div class="login-error">
                <?php echo pll__('El enlace de recuperación no es válido o ha expirado.'); ?>
            </div>
            <p class="login-register-link" style="margin-bottom: 0;">
                <a href="<?php echo home_url(pll_current_language() . '/recuperar-contrasena-' . pll_current_language()); ?>"><?php pll_e('Solicitar nuevo enlace'); ?></a>
            </p>

        <?php else: ?>

            <?php
            if ( ! empty( $error ) ) {
                switch ( $error ) {
                    case 'empty':
                        echo '<div class="login-error">' . pll__('Por favor, introduce una nueva contraseña.') . '</div>';
                        break;
                    case 'short':
                        echo '<div class="login-error">' . pll__('La contraseña debe tener al menos 8 caracteres.') . '</div>';
                        break;
                    case 'mismatch':
                        echo '<div class="login-error">' . pll__('Las contraseñas no coinciden.') . '</div>';
                        break;
                    case 'expired':
                        echo '<div class="login-error">' . pll__('El enlace ha expirado. Solicita uno nuevo.') . '</div>';
                        break;
                }
            }
            ?>

            <form name="newpasswordform" id="newpasswordform" action="<?php echo esc_url( get_permalink() ); ?>" method="post">
                <label for="new_password"><?php pll_e('Nueva contraseña'); ?></label>
                <div class="password-container">
                    <input 
                        type="text" 
                        name="new_password" 
                        id="new_password" 
                        required 
                        minlength="8"
                        autocomplete="off"
                        class="password-input">
                    <button 
                        type="button" 
                        class="password-toggle-btn"
                        data-target="new_password"
                        aria-label="Mostrar contraseña">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </button>
                </div>

                <label for="confirm_password"><?php pll_e('Confirmar contraseña'); ?></label>
                <div class="password-container">
                    <input 
                        type="text" 
                        name="confirm_password" 
                        id="confirm_password" 
                        required 
                        minlength="8"
                        autocomplete="off"
                        class="password-input">
                    <button 
                        type="button" 
                        class="password-toggle-btn"
                        data-target="confirm_password"
                        aria-label="Mostrar contraseña">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </button>
                </div>

                <input type="hidden" name="token" value="<?php echo esc_attr( $token ); ?>">
                <input type="hidden" name="email" value="<?php echo esc_attr( $email ); ?>">

                <input type="submit" name="reset-password-submit" id="reset-password-submit" value="<?php pll_e('Cambiar contraseña'); ?>">
                <?php wp_nonce_field( 'neema-new-password', 'neema_new_password_nonce' ); ?>
            </form>

        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>