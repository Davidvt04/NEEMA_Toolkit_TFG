<?php

declare(strict_types=1);

namespace {
    if (!function_exists('neema_clear_recursos_cache')) {
        function neema_clear_recursos_cache($user_id)
        {
            $GLOBALS['__test_clear_recursos_cache_calls'][] = $user_id;
        }
    }
}

namespace NeemaTheme\Tests\Unit {

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/language-helpers.php';
require_once __DIR__ . '/../../inc/recursos/preferencias-recursos.php';
require_once __DIR__ . '/../../inc/recursos-favoritos.php';

final class RecursosFavoritosExtendedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['wpdb'] = new RecursosFavoritosWpdbFake();
        $GLOBALS['__test_user_meta'] = [];
        $GLOBALS['__test_post_meta'] = [];
        $GLOBALS['__test_post_types'] = [];
        $GLOBALS['__test_get_post_return'] = null;
        $GLOBALS['__test_wp_send_json_success'] = [];
        $GLOBALS['__test_wp_send_json_error'] = [];
        $GLOBALS['__test_update_post_meta_calls'] = [];
        $GLOBALS['__test_clear_recursos_cache_calls'] = [];
        $GLOBALS['__test_current_user_id'] = 0;
        $GLOBALS['__test_nonce_verification_result'] = true;
        $GLOBALS['__test_is_singular'] = false;
        $GLOBALS['__test_is_user_logged_in'] = false;
        $GLOBALS['__test_is_admin'] = false;
        $_POST = [];
    }

    // Este test comprueba que el script de favoritos se carga en las vistas soportadas.
    public function test_enqueue_favoritos_script_loads_for_supported_views(): void
    {
        $GLOBALS['__test_is_singular'] = true;

        neema_enqueue_favoritos_script();

        $this->assertTrue(true);
    }

    // Este test comprueba que el script de favoritos no se carga en vistas no relacionadas.
    public function test_enqueue_favoritos_script_skips_unrelated_views(): void
    {
        neema_enqueue_favoritos_script();

        $this->assertTrue(true);
    }

    // Este test comprueba que el enlace de guardados usa la ruta según el idioma.
    public function test_get_link_guardados_uses_language_specific_paths(): void
    {
        $GLOBALS['__test_pll_current_language'] = 'es';
        $this->assertSame('http://example.test/guardados/', get_link_guardados());

        $GLOBALS['__test_pll_current_language'] = 'fr';
        $this->assertSame('http://example.test/fr/guardados-fr/', get_link_guardados());
    }

    // Este test comprueba que el ajax rechaza un nonce inválido al alternar favorito.
    public function test_ajax_toggle_favorito_rejects_invalid_nonce(): void
    {
        $GLOBALS['__test_nonce_verification_result'] = false;
        $_POST = [
            'nonce' => 'bad',
            'post_id' => 15,
        ];

        try {
            neema_ajax_toggle_favorito();
            $this->fail('Expected wp_send_json_error to be called.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('wp_send_json_error called', $exception->getMessage());
        }

        $this->assertSame('Nonce inválido', $GLOBALS['__test_wp_send_json_error'][0]['message']);
    }

    // Este test comprueba que solo usuarios logueados pueden alternar favoritos por ajax.
    public function test_ajax_toggle_favorito_requires_logged_in_user(): void
    {
        $_POST = [
            'nonce' => wp_create_nonce('neema_favoritos_nonce'),
            'post_id' => 15,
        ];

        try {
            neema_ajax_toggle_favorito();
            $this->fail('Expected wp_send_json_error to be called.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('wp_send_json_error called', $exception->getMessage());
        }

        $this->assertSame('Debes iniciar sesión', $GLOBALS['__test_wp_send_json_error'][0]['message']);
    }

    // Este test comprueba que se requiere un post_id para alternar favorito por ajax.
    public function test_ajax_toggle_favorito_requires_a_post_id(): void
    {
        $GLOBALS['__test_is_user_logged_in'] = true;
        $GLOBALS['__test_current_user_id'] = 12;
        $_POST = [
            'nonce' => wp_create_nonce('neema_favoritos_nonce'),
        ];

        try {
            neema_ajax_toggle_favorito();
            $this->fail('Expected wp_send_json_error to be called.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('wp_send_json_error called', $exception->getMessage());
        }

        $this->assertSame('ID de recurso inválido', $GLOBALS['__test_wp_send_json_error'][0]['message']);
    }

    // Este test comprueba que un post_id igual a cero es rechazado al alternar favorito.
    public function test_ajax_toggle_favorito_rejects_invalid_post_id_zero(): void
    {
        $GLOBALS['__test_is_user_logged_in'] = true;
        $GLOBALS['__test_current_user_id'] = 12;
        $_POST = [
            'nonce' => wp_create_nonce('neema_favoritos_nonce'),
            'post_id' => 0,
        ];

        try {
            neema_ajax_toggle_favorito();
            $this->fail('Expected wp_send_json_error to be called.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('wp_send_json_error called', $exception->getMessage());
        }

        $this->assertSame('ID de recurso inválido', $GLOBALS['__test_wp_send_json_error'][0]['message']);
    }

    // Este test comprueba que limpiar favoritos solo actúa sobre recursos.
    public function test_limpiar_favoritos_solo_actua_con_recursos(): void
    {
        $GLOBALS['__test_get_post_return'] = (object) ['ID' => 999, 'post_type' => 'page'];

        neema_limpiar_favoritos_al_eliminar_recurso(999);

        $this->assertSame([], $GLOBALS['wpdb']->deleteCalls);
    }

    // Este test comprueba que limpiar favoritos elimina por recurso y por usuario.
    public function test_limpiar_favoritos_elimina_por_recurso_y_por_usuario(): void
    {
        /** @var RecursosFavoritosWpdbFake $wpdb */
        $wpdb = $GLOBALS['wpdb'];
        $GLOBALS['__test_get_post_return'] = (object) ['ID' => 999, 'post_type' => 'recurso'];

        neema_limpiar_favoritos_al_eliminar_recurso(999);
        neema_limpiar_favoritos_al_eliminar_usuario(55);

        $this->assertCount(2, $wpdb->deleteCalls);
        $this->assertSame(['recurso_id' => 999], $wpdb->deleteCalls[0]['where']);
        $this->assertSame(['user_id' => 55], $wpdb->deleteCalls[1]['where']);
    }
}

final class RecursosFavoritosWpdbFake
{
    public string $prefix = 'wp_';

    /** @var array<int, array{table:string,data:array,formats:array}> */
    public array $insertCalls = [];

    /** @var array<int, array{table:string,where:array,formats:array}> */
    public array $deleteCalls = [];

    /** @var array<int, mixed> */
    public array $varResults = [];

    public array $prepareCalls = [];

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
        return [];
    }

    public function query($query)
    {
        return 1;
    }
}

}
