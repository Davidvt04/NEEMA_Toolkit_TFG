<?php
/* Template Name: Perfil de Usuario */

if ( !is_user_logged_in() ) {
    wp_redirect( home_url('/login-' . pll_current_language()) );
    exit;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$current_page_url = get_permalink(get_queried_object_id());
$errors = array();
$success = false;

if ( isset($_GET['success']) && $_GET['success'] === 'email_verified' ) {
    $success = 'email_verified';
}

if ( isset($_GET['action']) && $_GET['action'] === 'verify_email' && isset($_GET['user_id']) && isset($_GET['token']) ) {
    $verify_user_id = intval($_GET['user_id']);
    $verify_token = sanitize_text_field($_GET['token']);
    if ( $verify_user_id === $user_id ) {
        $stored_token = get_user_meta($user_id, 'email_verification_token', true);
        $expiry = get_user_meta($user_id, 'email_verification_expiry', true);
        $pending_email = get_user_meta($user_id, 'pending_email', true);
        
        if ( $stored_token && $stored_token === $verify_token && $expiry && time() < $expiry && $pending_email ) {
            if ( !email_exists($pending_email) ) {
                add_filter('send_email_change_email', '__return_false');
                wp_update_user( array(
                    'ID' => $user_id,
                    'user_email' => $pending_email
                ) );
                delete_user_meta($user_id, 'pending_email');
                delete_user_meta($user_id, 'email_verification_token');
                delete_user_meta($user_id, 'email_verification_expiry');
                $success = 'email_verified';
                $current_user = wp_get_current_user();
            } else {
                $errors[] = pll__('El correo electrónico ya está siendo utilizado por otro usuario.');
            }
        } else {
            $errors[] = pll__('El enlace de verificación no es válido o ha expirado.');
        }
    }
}
if ( isset($_POST['delete_account']) ) {
    require_once(ABSPATH.'wp-admin/includes/user.php');
    $admin_id = get_users(array('role' => 'administrator', 'number' => 1))[0]->ID;
    
    if ( wp_delete_user($user_id, $admin_id) ) {
        wp_logout();
        wp_safe_redirect( add_query_arg( 'account', 'deleted', home_url('/login-' . pll_current_language()) ) );
        exit;
    } else {
        $errors[] = pll__('Error al eliminar la cuenta. Inténtalo de nuevo.');
    }
}

if ( isset($_GET['profile_result']) ) {
    $profile_result = get_transient('profile_result_' . $user_id);
    if ( $profile_result ) {
        delete_transient('profile_result_' . $user_id);
        if ( !empty($profile_result['success']) ) {
            $success = $profile_result['success'];
            $current_user = wp_get_current_user();
        }
        if ( !empty($profile_result['errors']) ) {
            $errors = $profile_result['errors'];
        }
    }
}

$nombre_completo = $current_user->display_name;
$username = $current_user->user_login;
$email = $current_user->user_email;
$entidad = get_user_meta( $user_id, 'entidad_proveniente', true );
$rol_usuario = get_user_meta( $user_id, 'rol_usuario', true );
$pending_email = get_user_meta( $user_id, 'pending_email', true );

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

get_header();
?>

<?php $home_link = pll_home_url( pll_current_language() ); ?>
<p class="back-home" style="text-align:left; margin-top:1rem;">
    <?php pll_e('Volver a '); ?>
    <a href="<?php echo esc_url( $home_link ); ?>"><?php pll_e('Home'); ?></a>
</p>

<div class="profile-page">
  <div class="profile-container">
    <h2><?php pll_e('Mi perfil'); ?></h2>

    <?php if ( $success === 'email_verification' ) : ?>
        <div class="login-success">
            <p><?php pll_e('Perfil actualizado. Te hemos enviado un correo de verificación a tu nueva dirección. Por favor, revisa tu bandeja de entrada y haz clic en el enlace para confirmar el cambio.'); ?></p>
        </div>
    <?php elseif ( $success === 'email_verified' ) : ?>
        <div class="login-success">
            <p><?php pll_e('¡Correo electrónico verificado y actualizado correctamente!'); ?></p>
        </div>
    <?php elseif ( $success === 'profile_updated' ) : ?>
        <div class="login-success">
            <p><?php pll_e('Perfil actualizado correctamente.'); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if ( $pending_email && !$success ) : ?>
        <div class="login-error" style="background-color: #fff3cd; color: #856404; border-color: #ffeaa7;">
            <p><?php echo sprintf( pll__('Tienes un cambio de correo pendiente a: %s. Por favor, revisa tu bandeja de entrada para verificarlo.'), '<strong>' . esc_html($pending_email) . '</strong>' ); ?></p>
        </div>
    <?php endif; ?>

    <?php if ( !empty($errors) ) : ?>
        <div class="login-error">
            <?php foreach ($errors as $error) {
                echo '<p>' . esc_html($error) . '</p>';
            } ?>
        </div>
    <?php endif; ?>

    <form method="post" action="" id="profileform">
        <?php wp_nonce_field('update_user_profile', 'profile_nonce'); ?>
        <input type="hidden" name="update_profile" value="1">
        
        <label for="nombre_completo"><?php pll_e('Nombre y apellidos'); ?> (*)</label>
        <input type="text" name="nombre_completo" id="nombre_completo" 
            value="<?php echo esc_attr($nombre_completo); ?>" required>

        <label for="username"><?php pll_e('Nombre de usuario'); ?> (*)</label>
        <input type="text" name="username" id="username" 
            value="<?php echo esc_attr($username); ?>" readonly 
            style="background-color: #f0f0f0; cursor: not-allowed;" 
            title="<?php pll_e('El nombre de usuario no se puede cambiar'); ?>">
        <p style="font-size: 12px; color: #666; margin-top: -10px; margin-bottom: 15px;">
            <?php pll_e('El nombre de usuario no se puede modificar una vez creada la cuenta.'); ?>
        </p>

        <label for="email"><?php pll_e('Correo electrónico'); ?> (*)</label>
        <input type="email" name="email" id="email" 
            value="<?php echo esc_attr($email); ?>" required>

        <label for="entidad"><?php pll_e('Entidad proveniente'); ?> (*)</label>
        <select name="entidad" id="entidad" required>
            <option value=""><?php pll_e('Selecciona una entidad'); ?></option>
            <?php 
            $entidad_guardada = get_user_meta( $user_id, 'entidad_proveniente', true );
            foreach ( $lista_entidades as $entidad_item ) : ?>
                <option value="<?php echo esc_attr($entidad_item); ?>"
                    <?php selected( $entidad_guardada, $entidad_item ); ?>>
                    <?php echo esc_html($entidad_item); ?>
                </option>
            <?php endforeach; ?>
                <option value="Otra" <?php selected( $entidad_guardada, 'Otra' ); ?>>
                    <?php pll_e('Otra'); ?>
                </option>
        </select>

        <label for="rol"><?php pll_e('Rol'); ?> (*)</label>
        <select name="rol" id="rol" required>
            <option value=""><?php pll_e('Selecciona tu rol'); ?></option>
            <option value="Profesor" <?php selected( $rol_usuario, 'Profesor' ); ?>><?php pll_e('Profesor'); ?></option>
            <option value="Estudiante" <?php selected( $rol_usuario, 'Estudiante' ); ?>><?php pll_e('Estudiante'); ?></option>
            <option value="Tomador de decisiones" <?php selected( $rol_usuario, 'Tomador de decisiones' ); ?>><?php pll_e('Tomador de decisiones'); ?></option>
            <option value="Equipo de soporte" <?php selected( $rol_usuario, 'Equipo de soporte' ); ?>><?php pll_e('Equipo de soporte'); ?></option>
            <option value="Organización" <?php selected( $rol_usuario, 'Organización' ); ?>><?php pll_e('Organización'); ?></option>
            <option value="Otro" <?php selected( $rol_usuario, 'Otro' ); ?>><?php pll_e('Otro'); ?></option>
        </select>

        <div class="password-change-info">
            <p>
                <?php pll_e('¿Deseas cambiar tu contraseña?'); ?> 
                <a href="<?php echo home_url('/recuperar-contrasena-' . pll_current_language() . '/'); ?>"><?php pll_e('Cambia tu contraseña aquí'); ?></a>
            </p>
        </div>

        <div class="profile-buttons">
            <input type="submit" value="<?php pll_e('Actualizar perfil'); ?>" class="btn-update-profile">
            <button type="button" class="btn-delete-account" onclick="openDeleteModal()">
                <?php pll_e('Eliminar cuenta'); ?>
            </button>
        </div>
    </form>

  </div>
</div>

<?php get_template_part('template-parts/modal-delete-account'); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        var profilePage = document.querySelector('.profile-page');
        if (!profilePage) return;

        if (document.querySelector('.login-error') || document.querySelector('.login-success')) {
            profilePage.classList.add('has-errors');
        }

        var observer = new MutationObserver(function(mutations) {
            for (var i = 0; i < mutations.length; i++) {
                var m = mutations[i];
                if (m.addedNodes && m.addedNodes.length) {
                    if (document.querySelector('.login-error') || document.querySelector('.login-success')) {
                        profilePage.classList.add('has-errors');
                        return;
                    }
                }
            }
        });

        observer.observe(profilePage, { childList: true, subtree: true });
    } catch (e) {
        console.error(e);
    }
});
</script>

<?php 
get_footer();
?>
