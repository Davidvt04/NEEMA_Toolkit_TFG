<?php
/**
 * Funciones de utilidad para idiomas
 */

function neema_get_current_lang() {
    if (is_singular('recurso') && isset($_GET['lang']) && in_array($_GET['lang'], ['es', 'en', 'fr'])) {
        return sanitize_text_field($_GET['lang']);
    }
    return pll_current_language();
}

function neema_translate($string) {
    $lang = neema_get_current_lang();
    return pll_translate_string($string, $lang);
}
