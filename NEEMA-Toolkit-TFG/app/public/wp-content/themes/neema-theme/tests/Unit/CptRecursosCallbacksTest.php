<?php

declare(strict_types=1);

namespace {
if (!function_exists('pll_get_post')) {
    function pll_get_post($post_id, $lang = '') {
        return $GLOBALS['__test_pll_get_post_map'][$post_id][$lang] ?? $post_id;
    }
}
}

namespace NeemaTheme\Tests\Unit {

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/recursos/cpt-recursos-callbacks.php';

final class CptRecursosCallbacksTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['__test_post_meta'] = [];
        $GLOBALS['__test_get_posts_return'] = [];
        $GLOBALS['__test_pll_get_post_map'] = [];
        $GLOBALS['__test_wp_editor_calls'] = [];
        $GLOBALS['__test_user_locale'] = 'es_ES';
        $GLOBALS['__test_current_user_id'] = 12;
    }

    public function test_neema_recursos_titulos_callback_renders_saved_titles(): void
    {
        $GLOBALS['__test_post_meta'][99] = [
            '_recurso_titulo_es' => 'Título ES',
            '_recurso_titulo_en' => 'Title EN',
            '_recurso_titulo_fr' => 'Titre FR',
        ];

        ob_start();
        neema_recursos_titulos_callback((object) ['ID' => 99]);
        $output = ob_get_clean();

        $this->assertStringContainsString('Título ES', $output);
        $this->assertStringContainsString('Title EN', $output);
        $this->assertStringContainsString('Titre FR', $output);
        $this->assertStringContainsString('neema_recursos_titulos_nonce', $output);
    }

    public function test_neema_recursos_relaciones_callback_renders_relations_and_translations(): void
    {
        $posts = [
            (object) ['ID' => 201, 'post_title' => 'País Uno'],
            (object) ['ID' => 202, 'post_title' => 'Tipo Uno'],
            (object) ['ID' => 203, 'post_title' => 'Temática Uno'],
            (object) ['ID' => 204, 'post_title' => 'Región Uno'],
            (object) ['ID' => 205, 'post_title' => 'Módulo Uno'],
        ];
        $GLOBALS['__test_get_posts_return'] = $posts;
        $GLOBALS['__test_user_locale'] = 'fr_FR';

        foreach ($posts as $post) {
            $GLOBALS['__test_pll_get_post']['es'][$post->ID] = $post->ID + 1000;
        }
        foreach ([1201, 1202, 1203, 1204, 1205] as $translatedId) {
            $GLOBALS['__test_post_meta'][$translatedId] = [
                '_tipo_recurso_key' => 'tipo-uno',
                '_tematica_key' => 'tematica-uno',
                '_region_key' => 'region-uno',
                '_modulo_key' => 'modulo-uno',
            ];
        }

        $GLOBALS['__test_post_meta'][55] = [
            '_recurso_paises' => 'sin-arreglo',
            '_recurso_tipo' => 'tipo-uno',
            '_recurso_tematicas' => 'sin-arreglo',
            '_recurso_regiones' => 'sin-arreglo',
            '_recurso_modulo' => 'modulo-uno',
            '_recurso_categoria' => 'Contextual',
            '_recurso_descargable' => '1',
            '_recurso_visualizable' => '0',
        ];

        ob_start();
        neema_recursos_relaciones_callback((object) ['ID' => 55]);
        $output = ob_get_clean();

        $this->assertStringContainsString('País Uno', $output);
        $this->assertStringContainsString('Tipo Uno', $output);
        $this->assertStringContainsString('Contextuel', $output);
        $this->assertStringContainsString('checked="checked"', $output);
        $this->assertStringContainsString('selected="selected"', $output);
    }

    public function test_neema_recursos_ia_callback_renders_action_box(): void
    {
        ob_start();
        neema_recursos_ia_callback((object) ['ID' => 10]);
        $output = ob_get_clean();

        $this->assertStringContainsString('Autocompletar con IA', $output);
        $this->assertStringContainsString('neema-ia-error', $output);
    }
}
}
