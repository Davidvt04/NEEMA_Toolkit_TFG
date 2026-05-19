<?php

declare(strict_types=1);

namespace NeemaTheme\Tests\Unit;

use NeemaTheme\Tests\TestCase;

// Define global WP_Query at runtime so the theme code can instantiate it in tests.
if (!class_exists('WP_Query')) {
    eval('namespace { class WP_Query { public $posts = []; public function __construct($args = null) { if (isset($GLOBALS["__wp_query_posts"])) { $this->posts = $GLOBALS["__wp_query_posts"]; } } } }');
}

// Minimal global implementation of wp_list_pluck used by the recommendation logic.
if (!function_exists('wp_list_pluck')) {
    eval('namespace { function wp_list_pluck($list, $field) { $out = []; foreach ((array) $list as $item) { if (is_array($item) && array_key_exists($field, $item)) { $out[] = $item[$field]; continue; } if (is_object($item) && isset($item->{$field})) { $out[] = $item->{$field}; continue; } $out[] = null; } return $out; } }');
}

final class RecomendarRecursosTest extends TestCase
{
    // Este test comprueba que los recursos se puntúan y ordenan según las preferencias.
    public function test_neema_recomendar_recursos_scores_and_sorts(): void
    {
        $categoria = 'cat';
        $modulo = 'mod';
        $num = 2;

        // Prepare two fake post objects
        $post1 = (object) ['ID' => 1, 'post_title' => 'A'];
        $post2 = (object) ['ID' => 2, 'post_title' => 'B'];
        $GLOBALS['__test_wp_query_posts'] = [$post1, $post2];

        $GLOBALS['__test_current_user_id'] = 11;
        $GLOBALS['__test_user_meta'] = [
            11 => [
                'preferencias' => [
                    '_recurso_tipo' => [20 => 5],
                    '_recurso_tematicas' => [30 => 3],
                    '_recurso_paises' => [],
                    '_recurso_regiones' => [],
                ],
            ],
        ];
        $GLOBALS['__test_post_meta'] = [
            1 => [
                '_recurso_tipo' => [20],
                '_recurso_tematicas' => [],
                '_recurso_paises' => [],
                '_recurso_regiones' => [],
            ],
            2 => [
                '_recurso_tipo' => [],
                '_recurso_tematicas' => [30],
                '_recurso_paises' => [],
                '_recurso_regiones' => [],
            ],
        ];
        $GLOBALS['__test_pll_get_term'] = [
            'es' => [20 => 20, 30 => 30],
        ];
        $GLOBALS['__test_update_meta_cache_calls'] = [];

        require_once __DIR__ . '/../../inc/recursos/preferencias-recursos.php';
        require_once __DIR__ . '/../../inc/recursos/recomendar-recursos.php';

        $result = neema_recomendar_recursos($categoria, $modulo, $num);

        $this->assertCount(2, $result);
        // post1 should have higher score (5) than post2 (3)
        $this->assertEquals(1, $result[0]->ID);
        $this->assertEquals(2, $result[1]->ID);
    }

    // Este test comprueba que se devuelve un array vacío si no hay posts para recomendar.
    public function test_neema_recomendar_recursos_returns_empty_when_no_posts(): void
    {
        $categoria = 'x';
        $modulo = 'y';
        $num = 3;

        $GLOBALS['__test_wp_query_posts'] = [];

        $GLOBALS['__test_current_user_id'] = 0;
        $GLOBALS['__test_user_meta'] = [0 => ['preferencias' => []]];
        $GLOBALS['__test_post_meta'] = [];
        $GLOBALS['__test_update_meta_cache_calls'] = [];

        require_once __DIR__ . '/../../inc/recursos/preferencias-recursos.php';
        require_once __DIR__ . '/../../inc/recursos/recomendar-recursos.php';

        $result = neema_recomendar_recursos($categoria, $modulo, $num);

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }
}
