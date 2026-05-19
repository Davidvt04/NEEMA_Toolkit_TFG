<?php

declare(strict_types=1);

namespace NeemaTheme\Tests\Unit {

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/dashboard-widgets/top-recursos-widget.php';

final class TopRecursosWidgetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['wpdb'] = new TopRecursosWpdbFake();
        $GLOBALS['__test_wp_send_json_success'] = [];
        $GLOBALS['__test_wp_send_json_error'] = [];
        $GLOBALS['__test_update_post_meta_calls'] = [];
        $GLOBALS['__test_post_types'] = [];
        $GLOBALS['__test_post_meta'] = [];
        $GLOBALS['__test_current_user_id'] = 0;
        $_POST = [];
    }

    // Este test comprueba que la lista de recursos muestra los rangos y etiquetas correctamente.
    public function test_render_top_recursos_list_renders_rank_and_label(): void
    {
        ob_start();
        neema_render_top_recursos_list([
            ['id' => 11, 'title' => 'Recurso Uno', 'count' => 1200],
            ['id' => 22, 'title' => 'Recurso Dos', 'count' => 300],
            ['id' => 33, 'title' => 'Recurso Tres', 'count' => 90],
        ], 'downloads');
        $output = ob_get_clean();

        $this->assertStringContainsString('gold', $output);
        $this->assertStringContainsString('silver', $output);
        $this->assertStringContainsString('bronze', $output);
        $this->assertStringContainsString('descargas', $output);
        $this->assertStringContainsString('Recurso Uno', $output);
    }

    // Este test comprueba que la lista de recursos muestra el estado vacío si no hay datos.
    public function test_render_top_recursos_list_shows_empty_state(): void
    {
        ob_start();
        neema_render_top_recursos_list([]);
        $output = ob_get_clean();

        $this->assertStringContainsString('No hay datos disponibles todavía.', $output);
    }

    // Este test comprueba que el widget muestra controles y la lista de recursos.
    public function test_top_recursos_widget_content_renders_controls_and_list_markup(): void
    {
        /** @var TopRecursosWpdbFake $wpdb */
        $wpdb = $GLOBALS['wpdb'];
        $wpdb->results = [
            (object) ['post_id' => 10, 'post_title' => 'Recurso Uno', 'total_visits' => '8'],
            (object) ['post_id' => 20, 'post_title' => 'Recurso Dos', 'total_visits' => '5'],
        ];

        ob_start();
        neema_top_recursos_widget_content();
        $output = ob_get_clean();

        $this->assertStringContainsString('neemaTopRecursosList', $output);
        $this->assertStringContainsString('neema-metric-btn', $output);
        $this->assertStringContainsString('Más Visitados', $output);
        $this->assertStringContainsString('Recurso Uno', $output);
    }

    // Este test comprueba que el widget de top recursos se registra en el dashboard.
    public function test_add_top_recursos_widget_registers_dashboard_widget(): void
    {
        $GLOBALS['__test_dashboard_widget_calls'] = [];

        neema_add_top_recursos_widget();

        $this->assertCount(1, $GLOBALS['__test_dashboard_widget_calls']);
        $this->assertSame('neema_top_recursos', $GLOBALS['__test_dashboard_widget_calls'][0][0]);
        $this->assertSame('Top 10 Recursos', $GLOBALS['__test_dashboard_widget_calls'][0][1]);
    }

    // Este test comprueba que se obtienen los recursos más visitados y se filtra por autor.
    public function test_get_top_recursos_by_visits_maps_rows_and_author_filter(): void
    {
        /** @var TopRecursosWpdbFake $wpdb */
        $wpdb = $GLOBALS['wpdb'];
        $wpdb->results = [
            (object) ['post_id' => 10, 'post_title' => 'Visitado Uno', 'total_visits' => '8'],
            (object) ['post_id' => 20, 'post_title' => 'Visitado Dos', 'total_visits' => '5'],
        ];

        $result = neema_get_top_recursos_by_visits(2, 99);

        $this->assertSame([
            ['id' => 10, 'title' => 'Visitado Uno', 'count' => 8],
            ['id' => 20, 'title' => 'Visitado Dos', 'count' => 5],
        ], $result);
        $this->assertNotEmpty($wpdb->prepareCalls);
        $this->assertStringContainsString('post_author = %d', $wpdb->prepareCalls[0]['query']);
    }

    // Este test comprueba que se obtienen los recursos más guardados correctamente.
    public function test_get_top_recursos_by_favorites_maps_rows(): void
    {
        /** @var TopRecursosWpdbFake $wpdb */
        $wpdb = $GLOBALS['wpdb'];
        $wpdb->results = [
            (object) ['post_id' => 30, 'post_title' => 'Guardado Uno', 'total_favorites' => '12'],
        ];

        $result = neema_get_top_recursos_by_favorites(5);

        $this->assertSame([
            ['id' => 30, 'title' => 'Guardado Uno', 'count' => 12],
        ], $result);
    }

    // Este test comprueba que se devuelve vacío si no hay recursos descargados.
    public function test_get_top_recursos_by_downloads_returns_empty_when_no_rows(): void
    {
        /** @var TopRecursosWpdbFake $wpdb */
        $wpdb = $GLOBALS['wpdb'];
        $wpdb->results = [];

        $this->assertSame([], neema_get_top_recursos_by_downloads(5));
    }

    // Este test comprueba que el ajax usa el scope y la métrica correctamente.
    public function test_ajax_get_top_recursos_uses_scope_and_metric(): void
    {
        /** @var TopRecursosWpdbFake $wpdb */
        $wpdb = $GLOBALS['wpdb'];
        $wpdb->results = [
            (object) ['post_id' => 40, 'post_title' => 'Mi Recurso', 'total_favorites' => '3'],
        ];
        $GLOBALS['__test_current_user_id'] = 21;
        $_POST = [
            'nonce' => wp_create_nonce('neema_top_recursos'),
            'metric' => 'favorites',
            'scope' => 'mine',
        ];

        try {
            neema_ajax_get_top_recursos();
            $this->fail('Expected wp_send_json_success to be called.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('wp_send_json_success called', $exception->getMessage());
        }

        $this->assertNotEmpty($GLOBALS['__test_wp_send_json_success']);
        $this->assertStringContainsString('Mi Recurso', $GLOBALS['__test_wp_send_json_success'][0]);
        $this->assertNotEmpty($wpdb->prepareCalls);
    }

    public function test_ajax_track_download_errors_for_invalid_recurso(): void
    {
        $_POST = [
            'nonce' => wp_create_nonce('neema_track_download'),
            'recurso_id' => 0,
        ];

        try {
            neema_ajax_track_download();
            $this->fail('Expected wp_send_json_error to be called.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('wp_send_json_error called', $exception->getMessage());
        }

        $this->assertNotEmpty($GLOBALS['__test_wp_send_json_error']);
        $this->assertSame('Recurso no válido', $GLOBALS['__test_wp_send_json_error'][0]);
    }

    public function test_ajax_track_download_increments_counter_for_recurso(): void
    {
        $resourceId = 77;
        $GLOBALS['__test_post_types'][$resourceId] = 'recurso';
        $GLOBALS['__test_post_meta'][$resourceId]['numDescargas'] = '4';
        $_POST = [
            'nonce' => wp_create_nonce('neema_track_download'),
            'recurso_id' => $resourceId,
        ];

        try {
            neema_ajax_track_download();
            $this->fail('Expected wp_send_json_success to be called.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('wp_send_json_success called', $exception->getMessage());
        }

        $this->assertSame([[77, 'numDescargas', 5]], $GLOBALS['__test_update_post_meta_calls']);
        $this->assertSame(5, $GLOBALS['__test_wp_send_json_success'][0]['count']);
    }
}

final class TopRecursosWpdbFake
{
    public string $prefix = 'wp_';
    public string $posts = 'wp_posts';
    public string $postmeta = 'wp_postmeta';

    /** @var array<int, array{query:string,args:array}> */
    public array $prepareCalls = [];

    /** @var array<int, object> */
    public array $results = [];

    public function prepare($query, ...$args)
    {
        $this->prepareCalls[] = [
            'query' => $query,
            'args' => $args,
        ];

        return $query;
    }

    public function get_results($query)
    {
        return $this->results;
    }

    public function get_var($query)
    {
        return 0;
    }

    public function insert($table, $data, $formats)
    {
        return 1;
    }

    public function delete($table, $where, $formats)
    {
        return 1;
    }
}

}
