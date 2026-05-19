<?php
/* Template Name: Registro */

if ( is_user_logged_in() ) {
    wp_redirect( home_url() );
    exit;
}

if ( isset($_GET['act']) && $_GET['act'] == 'activate_account' && isset($_GET['user_id']) && isset($_GET['code']) ) {
    $user_id = intval($_GET['user_id']);
    $code = sanitize_text_field($_GET['code']);
    $stored_code = get_user_meta($user_id, 'activation_code', true);
    
    if ( $stored_code && $stored_code === $code ) {
        delete_user_meta($user_id, 'account_status');
        delete_user_meta($user_id, 'activation_code');
        wp_safe_redirect( add_query_arg( 'activated', '1', home_url('/login-' . pll_current_language()) ) );
        exit;
    } else {
        $errors[] = pll__('El código de activación no es válido o ha expirado.');
    }
}


$errors = array();
if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    $recaptcha_token = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
    if ( empty($recaptcha_token) ) {
        $errors[] = pll__('Error de verificación. Por favor, recarga la página e intenta de nuevo.');
    } else {
        $recaptcha_secret = defined('RECAPTCHA_SECRET_KEY') ? RECAPTCHA_SECRET_KEY : '';
        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptcha_response = wp_remote_post($recaptcha_url, array(
            'body' => array(
                'secret' => $recaptcha_secret,
                'response' => $recaptcha_token,
                'remoteip' => $_SERVER['REMOTE_ADDR']
            )
        ));
        
        if ( !is_wp_error($recaptcha_response) ) {
            $recaptcha_data = json_decode(wp_remote_retrieve_body($recaptcha_response));
            if ( !$recaptcha_data->success || $recaptcha_data->score < 0.5 ) {
                $errors[] = pll__('Verificación de seguridad fallida. Si eres humano, inténtalo de nuevo.');
            }
        } else {
            $errors[] = pll__('Error al verificar la seguridad. Por favor, inténtalo de nuevo.');
        }
    }
    
    $nombre_completo   = sanitize_text_field( $_POST['nombre_completo'] );
    $username          = sanitize_user( $_POST['username'] );
    $email = sanitize_email( $_POST['email'] );
    if ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) || !preg_match( '/^[^@]+@[^@]+\.[a-zA-Z]{2,}$/', $email ) ) {
        $errors[] = pll__('Por favor, introduce un correo electrónico válido.');
    }
    $entidad           = sanitize_text_field( $_POST['entidad'] );
    $rol               = sanitize_text_field( $_POST['rol'] );
    $password          = $_POST['password'];
    $confirm_password  = $_POST['confirm_password'];

    if ( empty($nombre_completo) || empty($username) || empty($email) || empty($entidad) || empty($rol) || empty($password) || empty($confirm_password) ) {
        $errors[] = pll__('Por favor, completa todos los campos.');
    }

    if ( username_exists( $username ) ) {
        $errors[] = pll__('El nombre de usuario ya existe.');
    }

    if ( email_exists( $email ) ) {
        $errors[] = pll__('El correo electrónico ya está registrado.');
    }

    if ( $password !== $confirm_password ) {
        $errors[] = pll__('Las contraseñas no coinciden.');
    }
    if ( strlen($password) < 8 
        || !preg_match('/[A-Z]/', $password) 
        || !preg_match('/[a-z]/', $password) 
        || !preg_match('/[0-9]/', $password) 
        || !preg_match('/[\W_]/', $password) ) {
        $errors[] = pll__('La contraseña debe tener al menos 8 caracteres e incluir mayúsculas, minúsculas, números y símbolos.');
    }



    if ( empty($errors) ) {
        $user_id = wp_create_user( $username, $password, $email );
        if ( !is_wp_error($user_id) ) {
            wp_update_user( array(
                'ID'           => $user_id,
                'display_name' => $nombre_completo
            ) );
            update_user_meta( $user_id, 'entidad_proveniente', $entidad );
            update_user_meta( $user_id, 'rol_usuario', $rol );
            $user = new WP_User( $user_id );
            $user->set_role( 'visitante' );
            $activation_code = wp_generate_password(20, false);
            update_user_meta( $user_id, 'account_status', 'awaiting_email_confirmation' );
            update_user_meta( $user_id, 'activation_code', $activation_code );
            $activation_url = add_query_arg( array(
                'act' => 'activate_account',
                'user_id' => $user_id,
                'code' => $activation_code
            ), get_permalink( get_queried_object_id() ) );
            
            $site_name = get_bloginfo('name');
            $site_url = home_url();
            $admin_email = get_option('admin_email');
            ob_start();
            include( get_template_directory() . '/email-templates/activation-email.php' );
            $message = ob_get_clean();
            
            $subject = pll__('Activa tu cuenta');
            $headers = array('Content-Type: text/html; charset=UTF-8');
            wp_mail( $email, $subject, $message, $headers );

            wp_safe_redirect( add_query_arg( 'registro', 'pending', get_permalink( get_queried_object_id() ) ) );
            exit;
        } else {
            $errors[] = pll__('Error al crear la cuenta. Inténtalo de nuevo.');
        }
    }
}
$entidades = new WP_Query(array(
    'post_type'      => 'miembro',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
));
$lista_entidades = array();
if ( $entidades->have_posts() ) {
    while ( $entidades->have_posts() ) {
        $entidades->the_post();
        $lista_entidades[] = get_the_title();
    }
}
wp_reset_postdata();
$lista_entidades[] = pll__('Otra');

get_header();
?>

<?php $home_link = pll_home_url( pll_current_language() ); ?>
<p class="back-home" style="text-align:left; margin-top:1rem;">
    <?php pll_e('Volver a '); ?>
    <a href="<?php echo esc_url( $home_link ); ?>"><?php pll_e('Home'); ?></a>
</p>

<div class="register-page">
  <div class="register-container">
    <h2><?php pll_e('Registro de usuario'); ?></h2>

    <?php if ( !empty($errors) ) : ?>
        <div class="login-error">
            <?php foreach ($errors as $error) {
                echo '<p>' . esc_html($error) . '</p>';
            } ?>
        </div>
    <?php endif; ?>

    <form method="post" id="registerform">
        <label for="nombre_completo"><?php pll_e('Nombre y apellidos'); ?> (*)</label>
        <input type="text" name="nombre_completo" id="nombre_completo" 
            value="<?php echo isset($_POST['nombre_completo']) ? esc_attr($_POST['nombre_completo']) : ''; ?>" required>

        <label for="username"><?php pll_e('Nombre de usuario'); ?> (*)</label>
        <input type="text" name="username" id="username" 
            value="<?php echo isset($_POST['username']) ? esc_attr($_POST['username']) : ''; ?>" required>

        <label for="email"><?php pll_e('Correo electrónico'); ?> (*)</label>
        <input type="email" name="email" id="email" 
            value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>" required>

        <label for="entidad"><?php pll_e('Entidad proveniente'); ?> (*)</label>
        <select name="entidad" id="entidad" required>
            <option value=""><?php pll_e('Selecciona una entidad'); ?></option>
            <?php foreach ( $lista_entidades as $entidad ) : ?>
                <option value="<?php echo esc_attr($entidad); ?>"
                    <?php selected( isset($_POST['entidad']) ? $_POST['entidad'] : '', $entidad ); ?>>
                    <?php echo esc_html($entidad); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="rol"><?php pll_e('Rol'); ?> (*)</label>
        <select name="rol" id="rol" required>
            <option value=""><?php pll_e('Selecciona tu rol'); ?></option>
            <option value="Profesor" <?php selected( isset($_POST['rol']) ? $_POST['rol'] : '', 'Profesor' ); ?>><?php pll_e('Profesor'); ?></option>
            <option value="Estudiante" <?php selected( isset($_POST['rol']) ? $_POST['rol'] : '', 'Estudiante' ); ?>><?php pll_e('Estudiante'); ?></option>
            <option value="Tomador de decisiones" <?php selected( isset($_POST['rol']) ? $_POST['rol'] : '', 'Tomador de decisiones' ); ?>><?php pll_e('Tomador de decisiones'); ?></option>
            <option value="Equipo de soporte" <?php selected( isset($_POST['rol']) ? $_POST['rol'] : '', 'Equipo de soporte' ); ?>><?php pll_e('Equipo de soporte'); ?></option>
            <option value="Organización" <?php selected( isset($_POST['rol']) ? $_POST['rol'] : '', 'Organización' ); ?>><?php pll_e('Organización'); ?></option>
            <option value="Otro" <?php selected( isset($_POST['rol']) ? $_POST['rol'] : '', 'Otro' ); ?>><?php pll_e('Otro'); ?></option>
        </select>

        <label for="password"><?php pll_e('Contraseña'); ?> (*)</label>
        <p class="password-info"><?php pll_e('La contraseña debe tener al menos 8 caracteres e incluir mayúsculas, minúsculas, números y símbolos.'); ?></p>
        <div class="password-container">
            <input 
                type="text" 
                name="password" 
                id="password" 
                required 
                autocomplete="off"
                class="password-input">
            <button 
                type="button" 
                class="password-toggle-btn"
                data-target="password"
                aria-label="Mostrar contraseña">
                <i class="fa fa-eye" aria-hidden="true"></i>
            </button>
        </div>

        <label for="confirm_password"><?php pll_e('Confirmar contraseña'); ?> (*)</label>
        <div class="password-container">
            <input 
                type="text" 
                name="confirm_password" 
                id="confirm_password" 
                required 
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

        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
        <input type="submit" value="<?php pll_e('Registrarse'); ?>" id="submit-btn">
    </form>


    <p class="login-register-link">
        <?php pll_e('¿Ya tienes una cuenta?'); ?> 
        <a href="<?php echo home_url(pll_current_language() . '/login-' . pll_current_language()); ?>"><?php pll_e('Inicia sesión aquí'); ?></a>
    </p>
  </div>
</div>

<script src="https://www.google.com/recaptcha/api.js?render=<?php echo defined('RECAPTCHA_SITE_KEY') ? RECAPTCHA_SITE_KEY : ''; ?>"></script>
<script>
const form = document.getElementById('registerform');
if (form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        grecaptcha.ready(function() {
            grecaptcha.execute('<?php echo defined('RECAPTCHA_SITE_KEY') ? RECAPTCHA_SITE_KEY : ''; ?>', {action: 'register'}).then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
                form.submit();
            });
        });
    });
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        var registerPage = document.querySelector('.register-page');
        if (!registerPage) return;
        if (document.querySelector('.login-error')) {
            registerPage.classList.add('has-errors');
        }
        var observer = new MutationObserver(function(mutations) {
            for (var i = 0; i < mutations.length; i++) {
                var m = mutations[i];
                if (m.addedNodes && m.addedNodes.length) {
                    if (document.querySelector('.login-error')) {
                        registerPage.classList.add('has-errors');
                        return;
                    }
                }
            }
        });

        observer.observe(registerPage, { childList: true, subtree: true });
    } catch (e) {
        console.error(e);
    }
});
</script>

<?php 
get_footer();
?>

<?php if ( isset($_GET['registro']) && $_GET['registro'] === 'pending' ) : ?>
    <!-- Modal de confirmación de registro -->
    <div id="pendingModal" class="modal-overlay" style="display: flex;">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fa fa-envelope" aria-hidden="true"></i>
            </div>
            <h3><?php pll_e('¡Registro exitoso!'); ?></h3>
            <p><?php pll_e('Te hemos enviado un correo electrónico con un enlace para activar tu cuenta.'); ?></p>
            <p><?php pll_e('Por favor, revisa tu bandeja de entrada (y la carpeta de spam) y haz clic en el enlace de activación.'); ?></p>
            <button class="btn-close-modal" onclick="closeModal()"><?php pll_e('Entendido'); ?></button>
        </div>
    </div>
    
    <script>
        const modal = document.getElementById('pendingModal');
        if (modal) {
            document.body.appendChild(modal);
            document.body.style.overflow = 'hidden';
        }
        
        window.closeModal = function() {
            const modalEl = document.getElementById('pendingModal');
            if (modalEl) {
                modalEl.remove();
                document.body.style.overflow = '';
            }
            window.location.href = '<?php echo home_url("/" . pll_current_language() . "/login-" . pll_current_language()); ?>';
        }
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    window.closeModal();
                }
            });
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' || e.key === 'Esc') {
                const modalEl = document.getElementById('pendingModal');
                if (modalEl) {
                    window.closeModal();
                }
            }
        });
    </script>
<?php endif; ?>
