<?php

declare(strict_types=1);

/**
 * =========================
 * GLOBAL MOCKS (ANTES DE TODO)
 * =========================
 */
namespace {
    if (!function_exists('neema_render_recurso')) {
        function neema_render_recurso($post_id)
        {
            $GLOBALS['__test_render_recurso_calls'][] = $post_id;
            echo '<article data-recurso="' . (int) $post_id . '"></article>';
        }
    }
}

/**
 * =========================
 * TESTS
 * =========================
 */
namespace NeemaTheme\Tests\Unit {

    use NeemaTheme\Tests\TestCase;

    // Cargar dependencias del tema DESPUÉS del mock
    require_once __DIR__ . '/../../inc/language-helpers.php';
    require_once __DIR__ . '/../../inc/ajax-buscar-recursos.php';

    final class AjaxBuscarRecursosTest extends TestCase
    {
        protected function setUp(): void
        {
            parent::setUp();

            $_POST = [];

            $GLOBALS['__test_current_user_id'] = 0;
            $GLOBALS['__test_post_meta'] = [];
            $GLOBALS['__test_wp_query_callback'] = null;

            $GLOBALS['__test_render_recurso_calls'] = [];
            $GLOBALS['__test_wp_send_json_success'] = [];
        }

        public function test_neema_buscar_recursos_returns_message_when_no_filters_are_sent(): void
        {
            try {
                neema_buscar_recursos();
                $this->fail('Expected JSON response exception.');
            } catch (\RuntimeException $exception) {
                $this->assertSame('wp_send_json_success called', $exception->getMessage());
            }

            $response = $GLOBALS['__test_wp_send_json_success'][0];

            $this->assertStringContainsString('no-recursos-msg', $response['html']);
            $this->assertStringContainsString(
                'No se especificó ningún criterio de búsqueda.',
                $response['html']
            );
        }

        public function test_neema_buscar_recursos_renders_exact_and_relaxed_results(): void
        {
            $GLOBALS['__test_wp_query_callback'] = static function () {
                static $call = 0;
                $call++;

                if ($call === 1) {
                    return [
                        (object) ['ID' => 101],
                        (object) ['ID' => 102],
                    ];
                }

                return [
                    (object) ['ID' => 103],
                ];
            };

            $_POST = [
                'texto' => 'salud',
                'tipos' => ['tipo-1'],
            ];

            try {
                neema_buscar_recursos();
                $this->fail('Expected JSON response exception.');
            } catch (\RuntimeException $exception) {
                $this->assertSame('wp_send_json_success called', $exception->getMessage());
            }

            $response = $GLOBALS['__test_wp_send_json_success'][0];

            $this->assertStringContainsString('Resultados de búsqueda', $response['html']);
            $this->assertStringContainsString('Resultados aproximados', $response['html']);

            $this->assertSame(
                [101, 102, 103],
                $GLOBALS['__test_render_recurso_calls']
            );
        }

        public function test_neema_pesos_recursos_boosts_title_and_description_matches(): void
        {
            $GLOBALS['__test_post_meta'][55] = [
                '_recurso_titulo_es' => 'Guía de salud pública',
                'descripcion_es' => 'Documento sobre salud y prevención',
            ];

            $match = (object) [
                'weight' => 1,
                'customfield' => true,
                'doc' => 55,
                'term' => 'salud',
            ];

            $result = neema_pesos_recursos($match, null);

            $this->assertSame(9, $result->weight);
        }
    }
}