<?php

add_action( 'init', 'neema_handle_profile_form', 20 );

function neema_handle_profile_form() {
    if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) return;
    if ( !isset( $_POST['update_profile'] ) ) return;
    if ( isset( $_POST['delete_account'] ) ) return;
    if ( !is_user_logged_in() ) return;

    $user_id = get_current_user_id();

    // Verificar nonce de seguridad
    if ( !isset( $_POST['profile_nonce'] ) || !wp_verify_nonce( $_POST['profile_nonce'], 'update_user_profile' ) ) {
        set_transient( 'profile_result_' . $user_id, array( 'errors' => array( __( 'Error de seguridad. Por favor, recarga la página e intenta de nuevo.', 'neema-theme' ) ) ), 60 );
        wp_safe_redirect( add_query_arg( 'profile_result', 'error', home_url( strtok( $_SERVER['REQUEST_URI'], '?' ) ) ) );
        exit;
    }

    $current_user    = wp_get_current_user();
    $errors          = array();
    $success         = false;

    $nombre_completo = sanitize_text_field( $_POST['nombre_completo'] );
    $username        = sanitize_user( $_POST['username'] );
    $email           = sanitize_email( $_POST['email'] );
    $entidad         = sanitize_text_field( $_POST['entidad'] );
    $rol             = sanitize_text_field( $_POST['rol'] );

    // Validaciones
    if ( empty( $nombre_completo ) || empty( $username ) || empty( $email ) || empty( $entidad ) || empty( $rol ) ) {
        $errors[] = function_exists( 'pll__' ) ? pll__( 'Por favor, completa todos los campos.' ) : __( 'Por favor, completa todos los campos.', 'neema-theme' );
    }

    if ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) || !preg_match( '/^[^@]+@[^@]+\.[a-zA-Z]{2,}$/', $email ) ) {
        $errors[] = function_exists( 'pll__' ) ? pll__( 'Por favor, introduce un correo electrónico válido.' ) : __( 'Por favor, introduce un correo electrónico válido.', 'neema-theme' );
    }

    if ( $username !== $current_user->user_login ) {
        $errors[] = function_exists( 'pll__' ) ? pll__( 'El nombre de usuario no se puede cambiar una vez creada la cuenta.' ) : __( 'El nombre de usuario no se puede cambiar una vez creada la cuenta.', 'neema-theme' );
    }

    $email_changed = false;
    if ( $email !== $current_user->user_email ) {
        $email_changed = true;
        if ( email_exists( $email ) ) {
            $errors[] = function_exists( 'pll__' ) ? pll__( 'El correo electrónico ya está registrado.' ) : __( 'El correo electrónico ya está registrado.', 'neema-theme' );
        }
    }

    if ( empty( $errors ) ) {
        $update_data = array(
            'ID'           => $user_id,
            'display_name' => $nombre_completo,
        );

        if ( !$email_changed ) {
            $update_data['user_email'] = $email;
        }

        $updated = wp_update_user( $update_data );

        if ( !is_wp_error( $updated ) ) {
            update_user_meta( $user_id, 'entidad_proveniente', $entidad );
            update_user_meta( $user_id, 'rol_usuario', $rol );

            if ( $email_changed ) {
                $verification_token = wp_generate_password( 32, false );
                $expiry             = time() + 3600;
                $current_page_url   = home_url( strtok( $_SERVER['REQUEST_URI'], '?' ) );

                update_user_meta( $user_id, 'pending_email', $email );
                update_user_meta( $user_id, 'email_verification_token', $verification_token );
                update_user_meta( $user_id, 'email_verification_expiry', $expiry );

                $verification_link = add_query_arg( array(
                    'action'  => 'verify_email',
                    'user_id' => $user_id,
                    'token'   => $verification_token,
                ), $current_page_url );

                $user_name   = $nombre_completo;
                $site_name   = get_bloginfo( 'name' );
                $site_url    = home_url();
                $admin_email = get_option( 'admin_email' );

                ob_start();
                include( get_template_directory() . '/email-templates/verify-email.php' );
                $message = ob_get_clean();

                $subject = function_exists( 'pll__' ) ? pll__( 'Verifica tu nuevo correo electrónico' ) : __( 'Verifica tu nuevo correo electrónico', 'neema-theme' );
                $headers = array(
                    'Content-Type: text/html; charset=UTF-8',
                    'From: ' . $site_name . ' <' . $admin_email . '>',
                );

                if ( wp_mail( $email, $subject, $message, $headers ) ) {
                    $success = 'email_verification';
                } else {
                    $errors[] = function_exists( 'pll__' ) ? pll__( 'Error al enviar el correo de verificación. Inténtalo de nuevo.' ) : __( 'Error al enviar el correo de verificación. Inténtalo de nuevo.', 'neema-theme' );
                }
            } else {
                $success = 'profile_updated';
            }
        } else {
            $errors[] = function_exists( 'pll__' ) ? pll__( 'Error al actualizar el perfil. Inténtalo de nuevo.' ) : __( 'Error al actualizar el perfil. Inténtalo de nuevo.', 'neema-theme' );
        }
    }

    // Guardar resultado en transient y redirigir con GET limpio
    $redirect_url = home_url( strtok( $_SERVER['REQUEST_URI'], '?' ) );
    if ( $success ) {
        set_transient( 'profile_result_' . $user_id, array( 'success' => $success ), 60 );
        wp_safe_redirect( add_query_arg( 'profile_result', 'success', $redirect_url ) );
    } else {
        set_transient( 'profile_result_' . $user_id, array( 'errors' => $errors ), 60 );
        wp_safe_redirect( add_query_arg( 'profile_result', 'error', $redirect_url ) );
    }
    exit;
}
