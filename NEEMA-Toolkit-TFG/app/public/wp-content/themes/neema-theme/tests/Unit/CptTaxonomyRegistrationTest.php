<?php

declare(strict_types=1);

namespace {
if (!function_exists('remove_accents')) {
    function remove_accents($string) {
        return strtr($string, [
            'Á' => 'A',
            'É' => 'E',
            'Í' => 'I',
            'Ó' => 'O',
            'Ú' => 'U',
            'Ü' => 'U',
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ü' => 'u',
            'Ñ' => 'N',
            'ñ' => 'n',
        ]);
    }
}

if (!function_exists('pll_get_post_language')) {
    function pll_get_post_language($post_id) {
        return $GLOBALS['__test_pll_get_post_language'][$post_id] ?? 'es';
    }
}
}

namespace NeemaTheme\Tests\Unit {

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/cpt-tipo-recurso.php';
require_once __DIR__ . '/../../inc/cpt-tematicas.php';
require_once __DIR__ . '/../../inc/cpt-regiones.php';
require_once __DIR__ . '/../../inc/cpt-paises.php';
require_once __DIR__ . '/../../inc/cpt-design.php';

final class CptTaxonomyRegistrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['__test_register_post_type_calls'] = [];
        $GLOBALS['__test_update_post_meta_calls'] = [];
        $GLOBALS['__test_get_post_return'] = null;
        $GLOBALS['__test_current_user_can'] = ['edit_post' => true];
        $GLOBALS['__test_pll_get_post_language'] = [];
        $GLOBALS['__test_pll_current_language'] = 'es';
        $GLOBALS['__test_wp_query_callback'] = null;
        $GLOBALS['__test_current_post'] = null;
        $GLOBALS['__test_current_post_id'] = 0;
    }

    // Este test comprueba que se registran los tipos de post esperados.
    public function test_register_cpts_uses_expected_post_types(): void
    {
        neema_register_tipos_recursos_cpt();
        neema_register_tematicas_cpt();
        neema_register_regiones_cpt();
        neema_register_paises_cpt();
        neema_register_design_cpt();

        $postTypes = array_column($GLOBALS['__test_register_post_type_calls'], 0);

        $this->assertContains('tipo-recurso', $postTypes);
        $this->assertContains('tematica', $postTypes);
        $this->assertContains('region', $postTypes);
        $this->assertContains('pais', $postTypes);
        $this->assertContains('design', $postTypes);
    }

    // Este test comprueba que los callbacks generan slugs para la versión en español.
    public function test_auto_generate_key_callbacks_store_slugs_for_spanish_versions(): void
    {
        $GLOBALS['__test_pll_get_post_language'][21] = 'en';
        $GLOBALS['__test_pll_get_post_language'][31] = 'en';
        $GLOBALS['__test_pll_get_post_language'][41] = 'en';
        $GLOBALS['__test_pll_get_post']['es'][21] = 99;
        $GLOBALS['__test_pll_get_post']['es'][31] = 99;
        $GLOBALS['__test_pll_get_post']['es'][41] = 99;
        $GLOBALS['__test_get_post_return'] = (object) ['ID' => 99, 'post_title' => 'Título Español'];

        neema_tipo_recurso_auto_generate_key(21, (object) ['post_type' => 'tipo-recurso', 'post_title' => 'Title EN'], true);
        neema_tematica_auto_generate_key(31, (object) ['post_type' => 'tematica', 'post_title' => 'Topic EN'], true);
        neema_region_auto_generate_key(41, (object) ['post_type' => 'region', 'post_title' => 'Region EN'], true);

        $metaCalls = $GLOBALS['__test_update_post_meta_calls'];

        $this->assertTrue(in_array([99, '_tipo_recurso_key', 'titulo-espanol'], $metaCalls, true));
        $this->assertTrue(in_array([99, '_tematica_key', 'titulo-espanol'], $metaCalls, true));
        $this->assertTrue(in_array([99, '_region_key', 'titulo-espanol'], $metaCalls, true));
    }

    // Este test comprueba que se muestra la lista de diseños y el estado vacío.
    public function test_display_designs_renders_list_and_empty_state(): void
    {
        $GLOBALS['__test_wp_query_callback'] = static function () {
            return [
                (object) ['ID' => 501, 'post_title' => 'Diseño Uno'],
                (object) ['ID' => 502, 'post_title' => 'Diseño Dos'],
            ];
        };

        ob_start();
        neema_display_designs();
        $withPosts = ob_get_clean();

        $this->assertStringContainsString('designs-container', $withPosts);
        $this->assertStringContainsString('Diseño Uno', $withPosts);
        $this->assertStringContainsString('Diseño Dos', $withPosts);

        $GLOBALS['__test_wp_query_callback'] = static function () {
            return [];
        };

        ob_start();
        neema_display_designs();
        $emptyOutput = ob_get_clean();

        $this->assertStringContainsString('No se encontraron propuestas de diseños.', $emptyOutput);
    }
}
}
