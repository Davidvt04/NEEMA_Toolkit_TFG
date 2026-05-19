<?php
function neema_enqueue_styles() {
    $styles = [
        'neema-style' => '/style.css',
        'neema-header' => '/assets/css/header.css',
        'neema-session-menu' => '/assets/css/session-menu.css',
        'neema-front-page' => '/assets/css/front-page.css',
        'neema-footer' => '/assets/css/footer.css',
        'neema-404' => '/assets/css/404.css',
        'neema-single-modulo' => '/assets/css/single-modulo.css',
        'neema-recurso-preview' => '/assets/css/recurso-preview.css',
        'neema-single-recurso' => '/assets/css/single-recurso.css',
        'neema-page-login' => '/assets/css/page-login.css',
        'neema-buscador' => '/assets/css/buscador.css',
        'neema-page-mis-favoritos' => '/assets/css/page-mis-favoritos.css',
        'neema-funding-statement' => '/assets/css/funding-statement.css',
        'neema-page-resiliencia' => '/assets/css/page-resiliencia.css',
    ];

    foreach ($styles as $handle => $path) {
        wp_enqueue_style(
            $handle,
            get_stylesheet_directory_uri() . $path,
            ($handle === 'neema-style') ? array() : array('neema-style'),
            filemtime(get_stylesheet_directory() . $path)
        );
    }


    if (is_page_template('page-ran.php')) {
        wp_enqueue_style(
            'neema-page-ran',
            get_stylesheet_directory_uri() . '/assets/css/page-ran.css',
            array('neema-style'),
            filemtime(get_stylesheet_directory() . '/assets/css/page-ran.css')
        );
    }

    if (is_page_template('page-perfil.php')) {
        wp_enqueue_style(
            'neema-page-perfil',
            get_stylesheet_directory_uri() . '/assets/css/page-perfil.css',
            array('neema-style'),
            filemtime(get_stylesheet_directory() . '/assets/css/page-perfil.css')
        );
    }

    if (is_page_template('page-guia-introductoria.php')) {
        wp_enqueue_style(
            'neema-page-guia-introductoria',
            get_stylesheet_directory_uri() . '/assets/css/page-guia-introductoria.css',
            array('neema-style'),
            filemtime(get_stylesheet_directory() . '/assets/css/page-guia-introductoria.css')
        );
    }

    if (is_page_template('page-servicios-apoyo.php')) {
        wp_enqueue_style(
            'neema-page-servicios-apoyo',
            get_stylesheet_directory_uri() . '/assets/css/page-servicios-apoyo.css',
            array('neema-style'),
            filemtime(get_stylesheet_directory() . '/assets/css/page-servicios-apoyo.css')
        );
        wp_enqueue_style(
            'neema-organismo-preview',
            get_stylesheet_directory_uri() . '/assets/css/organismo-preview.css',
            array('neema-style'),
            filemtime(get_stylesheet_directory() . '/assets/css/organismo-preview.css')
        );
    }

    if (is_singular('categoria-organismo')) {
        wp_enqueue_style(
            'neema-single-categoria-organismo',
            get_stylesheet_directory_uri() . '/assets/css/single-categoria-organismo.css',
            array('neema-style'),
            filemtime(get_stylesheet_directory() . '/assets/css/single-categoria-organismo.css')
        );
        wp_enqueue_style(
            'neema-organismo-preview',
            get_stylesheet_directory_uri() . '/assets/css/organismo-preview.css',
            array('neema-style'),
            filemtime(get_stylesheet_directory() . '/assets/css/organismo-preview.css')
        );
    }
}
add_action('wp_enqueue_scripts', 'neema_enqueue_styles');

function neema_enqueue_font_awesome() {
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        array(),
        '6.5.1'
    );
}
add_action('wp_enqueue_scripts', 'neema_enqueue_font_awesome');

function neema_enqueue_js() {
    wp_enqueue_script(
        'neema-main-js',
        get_template_directory_uri() . '/js/main.js',
        array(), 
        '1.0',
        true
    );
}

add_action('wp_enqueue_scripts', 'neema_enqueue_js');

function neema_enqueue_cargar_recursos() {
    if ( is_singular('modulo') ) {
        wp_enqueue_script(
            'neema-cargar-recursos',
            get_template_directory_uri() . '/js/cargar-recursos.js',
            array('jquery'),
            '1.1',
            true
        );
        wp_localize_script('neema-cargar-recursos', 'ajaxurl', admin_url('admin-ajax.php'));
    }
}
add_action('wp_enqueue_scripts', 'neema_enqueue_cargar_recursos');

function neema_enqueue_cargar_organismos() {
    if ( is_singular('categoria-organismo') ) {
        wp_enqueue_script(
            'neema-cargar-organismos',
            get_template_directory_uri() . '/js/cargar-organismos.js',
            array('jquery'),
            '1.0',
            true
        );
        wp_localize_script('neema-cargar-organismos', 'ajaxurl', admin_url('admin-ajax.php'));
    }
}
add_action('wp_enqueue_scripts', 'neema_enqueue_cargar_organismos');

function neema_enqueue_buscador_recursos() {
    if ( is_singular('modulo') || is_page_template('page-ran.php') ) {
        $js_path = get_template_directory() . '/js/buscador-recursos.js';
        wp_enqueue_script(
            'neema-buscador-recursos',
            get_template_directory_uri() . '/js/buscador-recursos.js',
            array('jquery'),
            file_exists($js_path) ? filemtime($js_path) : '1.0',
            true
        );
        wp_localize_script('neema-buscador-recursos', 'buscadorAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('buscador_recursos_nonce'),
            'noResultsMessage' => pll__('No se han encontrado resultados', 'Módulo Page'),
            'loadingMessage' => pll__('Cargando...', 'Buscador')
        ));
    }
}
add_action('wp_enqueue_scripts', 'neema_enqueue_buscador_recursos');

function neema_enqueue_buscador_organismos() {
    if ( is_singular('categoria-organismo') || is_page_template('page-servicios-apoyo.php') ) {
        $js_path = get_template_directory() . '/js/buscador-organismos.js';
        wp_enqueue_script(
            'neema-buscador-organismos',
            get_template_directory_uri() . '/js/buscador-organismos.js',
            array('jquery'),
            file_exists($js_path) ? filemtime($js_path) : '1.0',
            true
        );
        wp_localize_script('neema-buscador-organismos', 'buscadorAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('buscador_organismos_nonce'),
            'noResultsMessage' => pll__('No se han encontrado resultados', 'Servicios de Apoyo'),
            'loadingMessage' => pll__('Cargando...', 'Buscador')
        ));
    }
}
add_action('wp_enqueue_scripts', 'neema_enqueue_buscador_organismos');

function neema_login_scripts() {
    if ( is_page_template('page-login.php') || is_page_template('page-registro.php') || is_page_template('reset-password.php') ) {
        wp_enqueue_script(
            'neema-login-js',
            get_template_directory_uri() . '/js/login.js',
            array(),
            '1.1',
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'neema_login_scripts');

function custom_login_failed() {
    $login_page = home_url('/login/');
    wp_redirect($login_page . '?login=failed');
    exit;
}
add_action('wp_login_failed', 'custom_login_failed');

function custom_verify_username_password($user, $username, $password) {
    $login_page = home_url('/login/');
    if ($username == "" || $password == "") {
        wp_redirect($login_page . '?login=empty');
        exit;
    }
}
add_filter('authenticate', 'custom_verify_username_password', 1, 3);


