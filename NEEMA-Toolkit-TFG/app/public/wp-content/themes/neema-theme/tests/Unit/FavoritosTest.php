<?php

declare(strict_types=1);

namespace NeemaTheme\Tests\Unit;

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/language-helpers.php';
require_once __DIR__ . '/../../inc/recursos/preferencias-recursos.php';
require_once __DIR__ . '/../../inc/recursos-favoritos.php';

final class FakeWpdb
// Este test comprueba que se inserta y actualiza un recurso favorito correctamente.
{
    public string $prefix = 'wp_';
    public string $options = 'wp_options';

    /** @var array<int, array{0:string,1:array}> */
    public array $prepareCalls = [];

    /** @var array<int, array{table:string,data:array,formats:array}> */
    public array $insertCalls = [];

    /** @var array<int, array{table:string,where:array,formats:array}> */
    public array $deleteCalls = [];

    /** @var array<int, mixed> */
    public array $varResults = [];

    /** @var array<int, mixed> */
    public array $colResults = [];

    public function prepare($query, ...$args)
    {
        $this->prepareCalls[] = [$query, $args];

        return $query;
    }

    public function insert($table, $data, $formats)
    {
        $this->insertCalls[] = [
            'table' => $table,
            'data' => $data,
            'formats' => $formats,
        ];

        return 1;
    }

    public function delete($table, $where, $formats)
    {
        $this->deleteCalls[] = [
            'table' => $table,
            'where' => $where,
            'formats' => $formats,
        ];

        return 1;
    }

    public function get_var($query)
    {
        if ($this->varResults === []) {
            return 0;
        }

        return array_shift($this->varResults);
    }

    public function get_col($query)
    {
        if ($this->colResults === []) {
            return [];
        }

        return array_shift($this->colResults);
    }

    public function query($query)
    {
        return 1;
    }
}

final class FavoritosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['__test_user_meta'] = [];
        $GLOBALS['__test_post_meta'] = [];
        $GLOBALS['__test_pll_get_term'] = [];
    }

    // Este test comprueba que se inserta y actualiza un recurso favorito correctamente.
    public function test_add_recurso_favorito_inserts_and_updates_preferences(): void
    {
        $wpdb = new FakeWpdb();
        $wpdb->varResults = [0];
        $GLOBALS['wpdb'] = $wpdb;
        $GLOBALS['__test_user_meta'][7]['preferencias'] = [];
        $GLOBALS['__test_post_meta'][42] = [
            '_recurso_tipo' => [20],
            '_recurso_tematicas' => [30],
            '_recurso_paises' => [],
            '_recurso_regiones' => [],
        ];
        $GLOBALS['__test_pll_get_term'] = ['es' => [20 => 20, 30 => 30]];

        $result = add_recurso_favorito(7, 42);

        $this->assertTrue($result);
        $this->assertCount(1, $wpdb->insertCalls);
        $this->assertSame('wp_recursos_favoritos_usuario', $wpdb->insertCalls[0]['table']);
        $this->assertSame(['user_id' => 7, 'recurso_id' => 42], $wpdb->insertCalls[0]['data']);
        $this->assertIsArray($GLOBALS['__test_user_meta'][7]['preferencias'] ?? null);
    }

    // Este test comprueba que no se inserta un favorito si ya existe.
    public function test_add_recurso_favorito_returns_false_when_it_already_exists(): void
    {
        $wpdb = new FakeWpdb();
        $wpdb->varResults = [1];
        $GLOBALS['wpdb'] = $wpdb;

        $result = add_recurso_favorito(7, 42);

        $this->assertFalse($result);
        $this->assertCount(0, $wpdb->insertCalls);
        $this->assertSame([], $GLOBALS['__test_user_meta']);
    }

    // Este test comprueba que eliminar un favorito borra y actualiza las preferencias.
    public function test_remove_recurso_favorito_deletes_and_updates_preferences(): void
    {
        $wpdb = new FakeWpdb();
        $GLOBALS['wpdb'] = $wpdb;
        $GLOBALS['__test_user_meta'][7]['preferencias'] = [];
        $GLOBALS['__test_post_meta'][42] = [
            '_recurso_tipo' => [20],
            '_recurso_tematicas' => [30],
            '_recurso_paises' => [],
            '_recurso_regiones' => [],
        ];
        $GLOBALS['__test_pll_get_term'] = ['es' => [20 => 20, 30 => 30]];

        $result = remove_recurso_favorito(7, 42);

        $this->assertTrue($result);
        $this->assertCount(1, $wpdb->deleteCalls);
        $this->assertSame('wp_recursos_favoritos_usuario', $wpdb->deleteCalls[0]['table']);
        $this->assertSame(['user_id' => 7, 'recurso_id' => 42], $wpdb->deleteCalls[0]['where']);
        $this->assertIsArray($GLOBALS['__test_user_meta'][7]['preferencias'] ?? null);
    }

    // Este test comprueba que alternar favorito lo añade si no estaba guardado.
    public function test_toggle_recurso_favorito_adds_when_not_saved(): void
    {
        $wpdb = new FakeWpdb();
        $wpdb->varResults = [0];
        $GLOBALS['wpdb'] = $wpdb;
        $GLOBALS['__test_user_meta'][4]['preferencias'] = [];
        $GLOBALS['__test_post_meta'][9] = [
            '_recurso_tipo' => [20],
            '_recurso_tematicas' => [],
            '_recurso_paises' => [],
            '_recurso_regiones' => [],
        ];
        $GLOBALS['__test_pll_get_term'] = ['es' => [20 => 20]];

        $result = toggle_recurso_favorito(4, 9);

        $this->assertTrue($result);
        $this->assertCount(1, $wpdb->insertCalls);
        $this->assertIsArray($GLOBALS['__test_user_meta'][4]['preferencias'] ?? null);
    }

    // Este test comprueba que alternar favorito lo elimina si ya estaba guardado.
    public function test_toggle_recurso_favorito_removes_when_saved(): void
    {
        $wpdb = new FakeWpdb();
        $wpdb->varResults = [1];
        $GLOBALS['wpdb'] = $wpdb;
        $GLOBALS['__test_user_meta'][4]['preferencias'] = [];
        $GLOBALS['__test_post_meta'][9] = [
            '_recurso_tipo' => [20],
            '_recurso_tematicas' => [],
            '_recurso_paises' => [],
            '_recurso_regiones' => [],
        ];
        $GLOBALS['__test_pll_get_term'] = ['es' => [20 => 20]];

        $result = toggle_recurso_favorito(4, 9);

        $this->assertTrue($result);
        $this->assertCount(1, $wpdb->deleteCalls);
        $this->assertIsArray($GLOBALS['__test_user_meta'][4]['preferencias'] ?? null);
    }

    // Este test comprueba que se obtienen los IDs de favoritos en orden reciente.
    public function test_get_recursos_favoritos_returns_ids_in_recent_order(): void
    {
        $wpdb = new FakeWpdb();
        $wpdb->colResults = [[9, 3, 1]];
        $GLOBALS['wpdb'] = $wpdb;

        $result = get_recursos_favoritos(11);

        $this->assertSame([9, 3, 1], $result);
        $this->assertCount(1, $wpdb->prepareCalls);
        $this->assertStringContainsString('ORDER BY favorited_at DESC', $wpdb->prepareCalls[0][0]);
        $this->assertSame([11], $wpdb->prepareCalls[0][1]);
    }

    // Este test comprueba que los helpers de favoritos leen los conteos de la base de datos.
    public function test_count_and_is_favorite_helpers_read_database_counts(): void
    {
        $wpdb = new FakeWpdb();
        $wpdb->varResults = [3, 1, 8];
        $GLOBALS['wpdb'] = $wpdb;

        $this->assertSame(3, count_recursos_favoritos_usuario(11));
        $this->assertTrue(is_recurso_favorito(11, 22));
        $this->assertSame(8, count_favoritos_recurso(22));
    }

    // Este test comprueba que el enlace de guardados usa la ruta según el idioma.
    public function test_get_link_guardados_uses_language_specific_path(): void
    {
        $originalSingular = $GLOBALS['__test_is_singular'] ?? null;
        $originalLanguage = $GLOBALS['__test_pll_current_language'] ?? null;

        $GLOBALS['__test_is_singular'] = false;
        $GLOBALS['__test_pll_current_language'] = 'fr';

        $this->assertSame('http://example.test/fr/guardados-fr/', get_link_guardados());

        $GLOBALS['__test_is_singular'] = $originalSingular;
        $GLOBALS['__test_pll_current_language'] = $originalLanguage;
    }
}
