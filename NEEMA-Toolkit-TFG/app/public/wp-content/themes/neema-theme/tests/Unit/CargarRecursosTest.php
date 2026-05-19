<?php

declare(strict_types=1);

namespace NeemaTheme\Tests\Unit;

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/recursos/preferencias-recursos.php';
require_once __DIR__ . '/../../inc/recursos-favoritos.php';
require_once __DIR__ . '/../../inc/recursos/recomendar-recursos.php';

final class CargarRecursosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['__test_current_user_id'] = 7;
        $GLOBALS['__test_is_user_logged_in'] = false;
        $GLOBALS['__test_transients'] = [];
        $GLOBALS['__test_set_transient_calls'] = [];
        $GLOBALS['__test_wp_query_posts'] = [];
        $GLOBALS['__test_user_meta'] = [];
        $GLOBALS['__test_post_meta'] = [];
        $GLOBALS['__test_pll_get_term'] = [];
        $GLOBALS['__test_update_user_meta_calls'] = [];
    }

    public function test_neema_cargar_recursos_returns_random_only_when_total_is_small(): void
    {
        $GLOBALS['__test_wp_query_posts'] = [
            (object) ['ID' => 9],
            (object) ['ID' => 10],
        ];

        $result = neema_cargar_recursos('cat', 'mod', 4, [11]);

        $this->assertCount(2, $result);
        $this->assertSame([9, 10], array_map(static fn($item) => $item->ID, $result));
        $this->assertSame([], $GLOBALS['__test_set_transient_calls']);
    }

    public function test_neema_cargar_recursos_returns_cached_results_without_querying(): void
    {
        $cacheKey = $this->buildCacheKey('cat', 'mod', 6, [1, 2], false, 0);
        $GLOBALS['__test_transients'][$cacheKey] = [(object) ['ID' => 99]];

        $result = neema_cargar_recursos('cat', 'mod', 6, [1, 2]);

        $this->assertCount(1, $result);
        $this->assertSame(99, $result[0]->ID);
        $this->assertSame([], $GLOBALS['__test_set_transient_calls']);
    }

    public function test_neema_cargar_recursos_builds_full_list_for_logged_in_users(): void
    {
        $GLOBALS['__test_is_user_logged_in'] = true;
        $GLOBALS['__test_current_user_id'] = 7;
        $GLOBALS['__test_user_meta'][7]['preferencias'] = [
            '_recurso_tipo' => [20 => 5],
            '_recurso_tematicas' => [30 => 3],
            '_recurso_paises' => [],
            '_recurso_regiones' => [],
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
            3 => [
                '_recurso_tipo' => [],
                '_recurso_tematicas' => [],
                '_recurso_paises' => [],
                '_recurso_regiones' => [],
            ],
            4 => [
                '_recurso_tipo' => [],
                '_recurso_tematicas' => [],
                '_recurso_paises' => [],
                '_recurso_regiones' => [],
            ],
            5 => [
                '_recurso_tipo' => [],
                '_recurso_tematicas' => [],
                '_recurso_paises' => [],
                '_recurso_regiones' => [],
            ],
            6 => [
                '_recurso_tipo' => [],
                '_recurso_tematicas' => [],
                '_recurso_paises' => [],
                '_recurso_regiones' => [],
            ],
        ];
        $GLOBALS['__test_pll_get_term'] = ['es' => [20 => 20, 30 => 30]];
        $GLOBALS['__test_wp_query_posts'] = [
            (object) ['ID' => 1],
            (object) ['ID' => 2],
            (object) ['ID' => 3],
            (object) ['ID' => 4],
            (object) ['ID' => 5],
            (object) ['ID' => 6],
        ];
        $GLOBALS['wpdb'] = new class {
            public string $prefix = 'wp_';
            public array $counts = [1 => 10, 2 => 8, 3 => 5, 4 => 1, 5 => 0, 6 => 0];
            public array $userCounts = [7 => 2];

            public function prepare($query, ...$args)
            {
                return vsprintf($query, $args);
            }

            public function get_var($query)
            {
                if (preg_match('/recurso_id = (\d+)/', $query, $matches)) {
                    return $this->counts[(int) $matches[1]] ?? 0;
                }

                if (preg_match('/user_id = (\d+)/', $query, $matches)) {
                    return $this->userCounts[(int) $matches[1]] ?? 0;
                }

                return 0;
            }
        };

        $result = neema_cargar_recursos('cat', 'mod', 6, []);

        $this->assertCount(6, $result);
        $this->assertSame([1, 2, 3, 4, 5, 6], array_map(static fn($item) => $item->ID, $result));
        $this->assertNotEmpty($GLOBALS['__test_set_transient_calls']);
    }

    private function buildCacheKey(string $categoria, string $modulo, int $totalRecursos, array $excludeIds, bool $loggedIn, int $userId): string
    {
        $effectiveUserId = $loggedIn ? $userId : 0;
        return 'neema_recursos_' . $effectiveUserId . '_' . $categoria . '_' . $modulo . '_' . $totalRecursos . '_' . md5(serialize($excludeIds));
    }
}
