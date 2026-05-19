<?php
function neema_setup_theme() {
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'neema_setup_theme');
