<?php

declare(strict_types=1);

namespace NeemaTheme\Tests\Unit;

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/language-helpers.php';

final class LanguageHelpersTest extends TestCase
{
    // Este test comprueba que se prefiere el parámetro lang de la query si es válido.
    public function test_neema_get_current_lang_prefers_valid_lang_query_param(): void
    {
        $originalGet = $_GET;
        $originalSingular = $GLOBALS['__test_is_singular'] ?? null;
        $_GET['lang'] = 'fr';
        $GLOBALS['__test_is_singular'] = true;

        $this->assertSame('fr', neema_get_current_lang());

        $_GET = $originalGet;
        $GLOBALS['__test_is_singular'] = $originalSingular;
    }

    // Este test comprueba que si no hay lang en la query, se usa el idioma de Polylang.
    public function test_neema_get_current_lang_falls_back_to_polylang(): void
    {
        $originalGet = $_GET;
        $originalSingular = $GLOBALS['__test_is_singular'] ?? null;
        $originalLanguage = $GLOBALS['__test_pll_current_language'] ?? null;
        $_GET = [];
        $GLOBALS['__test_is_singular'] = false;
        $GLOBALS['__test_pll_current_language'] = 'es';

        $this->assertSame('es', neema_get_current_lang());

        $_GET = $originalGet;
        $GLOBALS['__test_is_singular'] = $originalSingular;
        $GLOBALS['__test_pll_current_language'] = $originalLanguage;
    }

    // Este test comprueba que la función de traducción usa Polylang correctamente.
    public function test_neema_translate_uses_polylang_translation(): void
    {
        $originalGet = $_GET;
        $originalSingular = $GLOBALS['__test_is_singular'] ?? null;
        $originalLanguage = $GLOBALS['__test_pll_current_language'] ?? null;
        $originalTranslations = $GLOBALS['__test_pll_translate_string'] ?? null;
        $_GET = [];
        $GLOBALS['__test_is_singular'] = false;
        $GLOBALS['__test_pll_current_language'] = 'en';
        $GLOBALS['__test_pll_translate_string'] = [
            'en' => [
                'Guardar' => 'Save',
            ],
        ];

        $this->assertSame('Save', neema_translate('Guardar'));

        $_GET = $originalGet;
        $GLOBALS['__test_is_singular'] = $originalSingular;
        $GLOBALS['__test_pll_current_language'] = $originalLanguage;
        $GLOBALS['__test_pll_translate_string'] = $originalTranslations;
    }
}
