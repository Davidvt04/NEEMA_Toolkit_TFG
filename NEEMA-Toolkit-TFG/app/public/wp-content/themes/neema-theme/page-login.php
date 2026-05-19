<?php
/* Template Name: Login */

if ( is_user_logged_in() ) {
    wp_redirect( home_url() );
    exit;
}

if ( isset( $_POST['wp-submit'] ) && isset( $_POST['neema_login_nonce'] ) ) {
    if ( wp_verify_nonce( $_POST['neema_login_nonce'], 'neema-login' ) ) {
        $username = sanitize_text_field( $_POST['log'] );
        $password = $_POST['pwd'];
        $remember = isset( $_POST['rememberme'] );
        if ( empty( $username ) || empty( $password ) ) {
            $login_error = 'empty';
        } else {
            $user = get_user_by( 'login', $username );
            if ( ! $user ) {
                $user = get_user_by( 'email', $username );
            }
            if ( $user && wp_check_password( $password, $user->data->user_pass, $user->ID ) ) {
                $account_status = get_user_meta( $user->ID, 'account_status', true );
                if ( empty($account_status) || $account_status !== 'awaiting_email_confirmation' ) {
                    wp_clear_auth_cookie();
                    wp_set_auth_cookie( $user->ID, $remember );
                    wp_set_current_user( $user->ID );
                    wp_safe_redirect( home_url() );
                    exit;
                } else {
                    $login_error = 'not_activated';
                }
            } else {
                $login_error = 'failed';
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
        <h2><?php pll_e('Iniciar sesión'); ?></h2>

        <?php
        if ( isset( $login_error ) || isset( $_GET['login'] ) || isset( $_GET['activated'] ) ) {

            if ( isset( $_GET['activated'] ) && $_GET['activated'] === '1' ) {
                echo '<div class="login-success">' . pll__('¡Cuenta activada con éxito! Ya puedes iniciar sesión.') . '</div>';
            }
            
            $error_type = isset( $login_error ) ? $login_error : $_GET['login'];

            switch ($error_type) {
                case 'failed':
                    echo '<div class="login-error">' . pll__('Usuario o contraseña incorrectos.') . '</div>';
                    break;
                case 'empty':
                    echo '<div class="login-error">' . pll__('Por favor, introduce usuario y contraseña.') . '</div>';
                    break;
                case 'not_activated':
                    echo '<div class="login-error">' . pll__('Tu cuenta aún no ha sido activada. Revisa tu correo electrónico.') . '</div>';
                    break;
                case 'false':
                    echo '<div class="login-error">' . pll__('Has cerrado sesión correctamente.') . '</div>';
                    break;
            }
        }
        ?>

        <form name="loginform" id="loginform" action="<?php echo esc_url( get_permalink() ); ?>" method="post">
            <label for="user_login"><?php pll_e('Usuario o correo electrónico'); ?></label>
            <input type="text" name="log" id="user_login" required>

            <label for="user_pass"><?php pll_e('Contraseña'); ?></label>
            <div class="password-container">
                <input 
                    type="text" 
                    name="pwd" 
                    id="user_pass" 
                    required 
                    autocomplete="off"
                    class="password-input">
                <button 
                    type="button" 
                    class="password-toggle-btn"
                    data-target="user_pass"
                    aria-label="Mostrar contraseña">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                </button>
            </div>

            <div class="login-remember">
                <label>
                    <input name="rememberme" type="checkbox" id="rememberme" value="forever">
                    <?php pll_e('Recuérdame'); ?>
                </label>
            </div>

            <input type="submit" name="wp-submit" id="wp-submit" value="<?php pll_e('Acceder'); ?>">
            <?php wp_nonce_field( 'neema-login', 'neema_login_nonce' ); ?>
        </form>

        <p class="login-register-link">
            <?php pll_e('¿No tienes cuenta?'); ?>
            <a href="<?php echo home_url(pll_current_language() . '/sign-up-' . pll_current_language()); ?>"><?php pll_e('Regístrate aquí'); ?></a>
        </p>
        <p class="login-forgot-link">
            <a href="<?php echo home_url(pll_current_language() . '/recuperar-contrasena-' . pll_current_language()); ?>"><?php pll_e('¿Olvidaste tu contraseña?'); ?></a>
        </p>
    </div>
</div>

<?php get_footer(); ?>
