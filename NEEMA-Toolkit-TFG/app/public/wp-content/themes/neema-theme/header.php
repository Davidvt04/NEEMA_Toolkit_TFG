<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NEEMA Toolkit</title>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="site-header">
  <div class="header-top">

    <!-- Fila superior en móvil -->
    <div class="header-left">
      <div class="header-logo">
        <a href="<?php echo home_url(); ?>">
          <img src="<?php echo get_theme_file_uri('assets/images/logo_toolkit.png'); ?>" alt="NEEMA Toolkit Logo">
        </a>
      </div>

      <div class="header-button-mobile">
        <?php if ( is_user_logged_in() ) : 
          $current_user = wp_get_current_user(); ?>
          <button class="btn-login user-menu-trigger" type="button">
            <?php echo esc_html( sprintf($current_user->display_name ) ); ?>
            <span class="arrow">▼</span>
          </button>
          <?php get_template_part('template-parts/session-menu'); ?>
        <?php else : ?>
          <a href="<?php echo home_url(neema_get_current_lang() . '/login-' . neema_get_current_lang()); ?>" class="btn-login">
            <i class="fa-regular fa-user"></i> <?php echo neema_translate('Iniciar Sesión'); ?>
          </a>
        <?php endif; ?>
      </div>
    </div>

    <div class="header-title">
      <h1>NEEMA Toolkit</h1>
    </div>

    <!-- Botón de login / usuario en escritorio -->
    <div class="header-button desktop-only">
      <?php if ( is_user_logged_in() ) : 
        $current_user = wp_get_current_user(); ?>
        <button class="btn-login user-menu-trigger" type="button">
          <?php echo esc_html( sprintf($current_user->display_name ) ); ?>
          <span class="arrow">▼</span>
        </button>
        <?php get_template_part('template-parts/session-menu'); ?>
      <?php else : ?>
        <a href="<?php echo home_url(neema_get_current_lang() . '/login-' . neema_get_current_lang()); ?>" class="btn-login">
          <i class="fa-regular fa-user"></i> <?php echo neema_translate('Iniciar Sesión'); ?>
        </a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Menú -->
  <nav class="header-nav">
    <div class="nav-button">
      <a href="<?php echo home_url(neema_get_current_lang() . '/guide-' . neema_get_current_lang()); ?>" class="btn-menu"><?php echo neema_translate('Guía Introductoria'); ?></a>
    </div>
    <div class="nav-button">
      <a href="<?php echo home_url(neema_get_current_lang() . '/fnr-' . neema_get_current_lang()); ?>" class="btn-menu"><?php echo neema_translate('Programas de RAN'); ?></a>
    </div>
    <div class="nav-button">
      <a href="<?php echo home_url(neema_get_current_lang() . '/assistance-' . neema_get_current_lang()); ?>" class="btn-menu"><?php echo neema_translate('Resiliencia Alimentaria y Nutricional'); ?></a>
    </div>
    <div class="nav-button">
      <a href="<?php echo home_url(neema_get_current_lang() . '/support-' . neema_get_current_lang()); ?>" class="btn-menu"><?php echo neema_translate('Servicios de apoyo'); ?></a>
    </div>
  </nav>
</header>

<section class="header-hero">
  <div class="header-triangle1"></div>
  <div class="header-triangle2">
    <?php if (function_exists('pll_the_languages')): ?>
      <div class="lang-switcher">
        <?php
        $current_lang_display = neema_get_current_lang();
        $languages = pll_the_languages(array('raw' => 1));
        $order = ['en', 'fr', 'es'];
        foreach ($order as $slug) {
          if (isset($languages[$slug])) {
            $lang = $languages[$slug];
            $active_class = ($slug === $current_lang_display) ? 'active' : '';
            switch ($lang['slug']) {
              case 'es': $label = 'ESP'; break;
              case 'en': $label = 'ENG'; break;
              case 'fr': $label = 'FRA'; break;
              default: $label = strtoupper($lang['slug']);
            }

            echo '<a href="' . esc_url($lang['url']) . '" class="lang-btn ' . $active_class . '">' . $label . '</a>';
          }
        }
        ?>
      </div>
    <?php endif; ?>
  </div>
</section>
