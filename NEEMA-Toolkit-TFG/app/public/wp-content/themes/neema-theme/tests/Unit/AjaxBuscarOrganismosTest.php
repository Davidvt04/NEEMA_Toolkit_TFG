<?php

declare(strict_types=1);

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/language-helpers.php';
require_once __DIR__ . '/../../inc/buscador-helpers.php';
require_once __DIR__ . '/../../inc/ajax-buscar-organismos.php';

/**
 * Mock render de organismo para tests
 */
if (!function_exists('neema_render_organismo')) {
    function neema_render_organismo($post_id)
    {
        $GLOBALS['__test_render_organismo_calls'][] = $post_id;
        echo '<article data-organismo="' . (int) $post_id . '"></article>';
    }
}

final class AjaxBuscarOrganismosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $_POST = [];

        $GLOBALS['__test_wp_query_callback'] = null;
        $GLOBALS['__test_render_organismo_calls'] = [];
        $GLOBALS['__test_wp_send_json_success'] = [];
        $GLOBALS['__test_post_meta'] = [];
    }

    public function test_neema_buscar_organismos_returns_message_when_no_filters_are_sent(): void
    {
        try {
            neema_buscar_organismos();
            $this->fail('Expected JSON response exception.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('wp_send_json_success called', $exception->getMessage());
        }

        $response = $GLOBALS['__test_wp_send_json_success'][0];

        $this->assertStringContainsString('no-recursos-msg', $response['html']);
        $this->assertSame(0, $response['count']);
    }

    public function test_neema_buscar_organismos_renders_exact_and_relaxed_results_with_count(): void
    {
        $GLOBALS['__test_wp_query_callback'] = static function ($args) {
            static $call = 0;
            $call++;

            return match ($call) {
                1 => [
                    (object) ['ID' => 201],
                    (object) ['ID' => 202],
                ],
                2 => [
                    (object) ['ID' => 203],
                ],
                3 => [
                    (object) ['ID' => 204],
                    (object) ['ID' => 205],
                ],
                default => [
                    (object) ['ID' => 206],
                ],
            };
        };

        $_POST = [
            'buscar_texto' => 'salud',
            'ambito' => ['Local'],
            'paises' => [34],
            'ciudad' => 'Madrid',
            'categoria_id' => 12,
        ];

        try {
            neema_buscar_organismos();
            $this->fail('Expected JSON response exception.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('wp_send_json_success called', $exception->getMessage());
        }

        $response = $GLOBALS['__test_wp_send_json_success'][0];

        $this->assertSame(6, $response['count']);
        $this->assertStringContainsString('Resultados de búsqueda', $response['html']);
        $this->assertStringContainsString('Resultados aproximados', $response['html']);

        $this->assertSame(
            [201, 202, 203, 204, 205, 206],
            $GLOBALS['__test_render_organismo_calls']
        );
    }

    public function test_neema_pesos_custom_fields_boosts_title_and_description_matches(): void
    {
        $GLOBALS['__test_post_meta'][77] = [
            'nombre_organismo_es' => 'Servicio de salud regional',
            'description_es' => 'Entidad vinculada a salud comunitaria',
        ];

        $match = (object) [
            'weight' => 2,
            'customfield' => true,
            'doc' => 77,
            'term' => 'salud',
        ];

        $result = neema_pesos_custom_fields($match, null);

        $this->assertSame(10, $result->weight);
    }
}