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

require_once __DIR__ . '/../../inc/cpt-modulos.php';

final class ModulosCptTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['__test_register_post_type_calls'] = [];
        $GLOBALS['__test_add_meta_box_calls'] = [];
        $GLOBALS['__test_update_post_meta_calls'] = [];
        $GLOBALS['__test_post_meta'] = [];
        $GLOBALS['__test_get_post_return'] = [];
        $GLOBALS['__test_wp_query_callback'] = null;
        $GLOBALS['__test_wp_editor_calls'] = [];
        $GLOBALS['__test_current_user_can'] = ['edit_post' => true];
        $GLOBALS['__test_pll_get_post_language'] = [];
        $GLOBALS['__test_pll_get_post'] = ['es' => []];
        $GLOBALS['__test_the_title'] = [];
        $GLOBALS['__test_query_vars'] = [];
        $_POST = [];
    }

    // Este test comprueba que se registran los meta boxes y helpers del CPT módulos.
    public function test_register_meta_boxes_and_field_helper_cover_basic_setup(): void
    {
        neema_register_modulos_cpt();
        neema_add_modulo_meta_boxes();

        $this->assertSame('modulo', $GLOBALS['__test_register_post_type_calls'][0][0]);
        $this->assertCount(3, $GLOBALS['__test_add_meta_box_calls']);
        $this->assertSame('modulo_description_box', $GLOBALS['__test_add_meta_box_calls'][0][0]);
        $this->assertSame('modulo_skills_box', $GLOBALS['__test_add_meta_box_calls'][2][0]);

        $GLOBALS['__test_post_meta'][44] = ['_modulo_objective' => 'Objetivo'];
        $this->assertSame('Objetivo', neema_get_modulo_field(44, 'modulo_objective'));
    }

    // Este test comprueba que los meta boxes muestran valores y guardan los meta correctamente.
    public function test_meta_box_callbacks_render_values_and_save_updates_meta(): void
    {
        $GLOBALS['__test_post_meta'][55] = [
            '_modulo_description' => 'Descripción del módulo',
            '_modulo_objective' => 'Objetivo del módulo',
            '_modulo_skills' => 'Competencias del módulo',
        ];

        ob_start();
        neema_modulo_description_meta_box_callback((object) ['ID' => 55]);
        $descriptionOutput = ob_get_clean();

        ob_start();
        neema_modulo_objective_meta_box_callback((object) ['ID' => 55]);
        $objectiveOutput = ob_get_clean();

        ob_start();
        neema_modulo_skills_meta_box_callback((object) ['ID' => 55]);
        $skillsOutput = ob_get_clean();

        $this->assertStringContainsString('Descripción del módulo', $descriptionOutput);
        $this->assertStringContainsString('Objetivo del módulo', $objectiveOutput);
        $this->assertStringContainsString('Competencias del módulo', $skillsOutput);

        $_POST = [
            'modulo_description' => '<p>Descripción guardada</p>',
            'modulo_objective' => '<p>Objetivo guardado</p>',
            'modulo_skills' => '<p>Competencias guardadas</p>',
        ];

        neema_save_modulo_meta(55);

        $this->assertSame([
            [55, '_modulo_description', '<p>Descripción guardada</p>'],
            [55, '_modulo_objective', '<p>Objetivo guardado</p>'],
            [55, '_modulo_skills', '<p>Competencias guardadas</p>'],
        ], $GLOBALS['__test_update_post_meta_calls']);
    }

    // Este test comprueba que se muestran los módulos y se genera el slug automáticamente.
    public function test_display_modulos_and_auto_generate_key_cover_list_and_slug(): void
    {
        $GLOBALS['__test_wp_query_callback'] = static function () {
            return [
                (object) ['ID' => 601, 'post_title' => 'Módulo Uno'],
                (object) ['ID' => 602, 'post_title' => 'Módulo Dos'],
            ];
        };

        ob_start();
        neema_display_modulos();
        $output = ob_get_clean();

        $this->assertStringContainsString('modulos-container', $output);
        $this->assertStringContainsString('Módulo Uno', $output);

        $GLOBALS['__test_pll_get_post_language'][77] = 'en';
        $GLOBALS['__test_pll_get_post']['es'][77] = 99;
        $GLOBALS['__test_get_post_return'] = (object) ['ID' => 99, 'post_title' => 'Título del Módulo'];

        neema_modulo_auto_generate_key(77, (object) ['post_type' => 'modulo', 'post_title' => 'Title EN'], true);

        $this->assertSame([[99, '_modulo_key', 'titulo-del-modulo']], $GLOBALS['__test_update_post_meta_calls']);
    }
}
}
