<?php

declare(strict_types=1);

namespace NeemaTheme\Tests\Unit;

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/recursos/preferencias-recursos.php';

final class PreferenciasTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['__test_post_meta'] = [];
        $GLOBALS['__test_pll_get_term'] = [];
        $GLOBALS['__test_user_meta'] = [];
    }

    // Este test comprueba que se devuelven las preferencias por defecto si están vacías.
    public function test_neema_get_preferencias_returns_default_when_empty(): void
    {
        $user_id = 123;
        $GLOBALS['__test_user_meta'][$user_id]['preferencias'] = [];

        $prefs = neema_get_preferencias($user_id);

        $this->assertIsArray($prefs);
        $this->assertArrayHasKey('_recurso_paises', $prefs);
        $this->assertArrayHasKey('_recurso_tipo', $prefs);
        $this->assertArrayHasKey('_recurso_tematicas', $prefs);
        $this->assertArrayHasKey('_recurso_regiones', $prefs);
        $this->assertSame([], $prefs['_recurso_paises']);
    }

    // Este test comprueba que al actualizar preferencias se incrementa y elimina si es cero.
    public function test_neema_update_preferencia_increments_and_prunes_zero(): void
    {
        $user_id = 5;
        $recurso_id = 10;

        $GLOBALS['__test_user_meta'][$user_id]['preferencias'] = [];
        $GLOBALS['__test_post_meta'][$recurso_id] = [
            '_recurso_tipo' => [20],
            '_recurso_tematicas' => [30],
            '_recurso_paises' => [],
            '_recurso_regiones' => [],
        ];
        $GLOBALS['__test_pll_get_term'] = [
            'es' => [20 => 20, 30 => 30],
        ];

        neema_update_preferencia($user_id, $recurso_id, true);

        $captured = $GLOBALS['__test_user_meta'][$user_id]['preferencias'] ?? null;

        $this->assertIsArray($captured);
        $this->assertArrayHasKey('_recurso_tipo', $captured);
        $this->assertArrayHasKey(20, $captured['_recurso_tipo']);
        $this->assertSame(1, $captured['_recurso_tipo'][20]);
    }

    // Este test comprueba que al decrementar preferencias se elimina si llega a cero.
    public function test_neema_update_preferencia_decrements_and_prunes_zero(): void
    {
        $user_id = 5;
        $recurso_id = 10;

        $GLOBALS['__test_user_meta'][$user_id]['preferencias'] = [
            '_recurso_tipo' => [20 => 1],
            '_recurso_tematicas' => [],
            '_recurso_paises' => [],
            '_recurso_regiones' => [],
        ];
        $GLOBALS['__test_post_meta'][$recurso_id] = [
            '_recurso_tipo' => [20],
            '_recurso_tematicas' => [],
            '_recurso_paises' => [],
            '_recurso_regiones' => [],
        ];
        $GLOBALS['__test_pll_get_term'] = [
            'es' => [20 => 20],
        ];

        neema_update_preferencia($user_id, $recurso_id, false);

        $captured = $GLOBALS['__test_user_meta'][$user_id]['preferencias'] ?? null;

        $this->assertIsArray($captured);
        $this->assertArrayNotHasKey(20, $captured['_recurso_tipo']);
    }
}
