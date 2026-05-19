<?php

declare(strict_types=1);

namespace NeemaTheme\Tests\Unit;

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/dashboard-widgets/top-recursos-widget.php';
require_once __DIR__ . '/../../inc/dashboard-widgets/top-gestores-widget.php';
require_once __DIR__ . '/../../inc/dashboard-widgets/users-distribution-widget.php';
require_once __DIR__ . '/../../inc/dashboard-widgets/recursos-caracteristicas-widget.php';

final class DashboardStatsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['__test_get_users_return'] = [];
        $GLOBALS['__test_user_meta'] = [];
        $GLOBALS['__test_post_meta'] = [];
        $GLOBALS['__test_pages_by_path'] = [];
        $GLOBALS['__test_get_posts_return'] = [];
        $GLOBALS['__test_get_posts_callback'] = null;
        $GLOBALS['__test_the_title'] = [];
        $GLOBALS['__test_get_post_return'] = [];
        $GLOBALS['__test_wp_query_posts'] = [];
        $GLOBALS['wpdb'] = null;
    }

    // Este test comprueba que la lista de recursos muestra el estado vacío si no hay datos.
    public function test_neema_render_top_recursos_list_outputs_empty_state(): void
    {
        ob_start();
        neema_render_top_recursos_list([]);
        $output = ob_get_clean();

        $this->assertStringContainsString('No hay datos disponibles todavía.', $output);
    }

    // Este test comprueba que la lista de recursos muestra rangos y etiquetas correctamente.
    public function test_neema_render_top_recursos_list_outputs_rank_and_labels(): void
    {
        ob_start();
        neema_render_top_recursos_list([
            ['id' => 1, 'title' => 'Recurso Uno', 'count' => 12],
            ['id' => 2, 'title' => 'Recurso Dos', 'count' => 8],
        ], 'downloads');
        $output = ob_get_clean();

        $this->assertStringContainsString('gold', $output);
        $this->assertStringContainsString('descargas', $output);
        $this->assertStringContainsString('Recurso Uno', $output);
    }

    // Este test comprueba que la lista de gestores muestra estados vacío y con ranking.
    public function test_neema_render_top_gestores_list_outputs_empty_and_ranked_states(): void
    {
        ob_start();
        neema_render_top_gestores_list([]);
        $emptyOutput = ob_get_clean();

        ob_start();
        neema_render_top_gestores_list([
            ['id' => 1, 'name' => 'Gestor Uno', 'count' => 15, 'recursos_count' => 3],
            ['id' => 2, 'name' => 'Gestor Dos', 'count' => 11, 'recursos_count' => 1],
            ['id' => 3, 'name' => 'Gestor Tres', 'count' => 9, 'recursos_count' => 2],
        ], 'visits');
        $rankedOutput = ob_get_clean();

        $this->assertStringContainsString('No hay datos disponibles todavía.', $emptyOutput);
        $this->assertStringContainsString('gold', $rankedOutput);
        $this->assertStringContainsString('silver', $rankedOutput);
        $this->assertStringContainsString('bronze', $rankedOutput);
        $this->assertStringContainsString('visitas', $rankedOutput);
    }

    // Este test comprueba que se obtienen los recursos más visitados desde la base de datos.
    public function test_neema_get_top_recursos_by_visits_maps_database_rows(): void
    {
        $GLOBALS['wpdb'] = new class {
            public string $prefix = 'wp_';
            public string $posts = 'wp_posts';

            public function prepare($query, ...$args)
            {
                return $query;
            }

            public function get_results($query)
            {
                return [
                    (object) ['post_id' => 11, 'post_title' => 'Recurso Uno', 'total_visits' => '7'],
                    (object) ['post_id' => 22, 'post_title' => 'Recurso Dos', 'total_visits' => '4'],
                ];
            }
        };

        $result = neema_get_top_recursos_by_visits(2);

        $this->assertSame([
            ['id' => 11, 'title' => 'Recurso Uno', 'count' => 7],
            ['id' => 22, 'title' => 'Recurso Dos', 'count' => 4],
        ], $result);
    }

    // Este test comprueba que se obtienen los recursos más guardados desde la base de datos.
    public function test_neema_get_top_recursos_by_favorites_maps_database_rows(): void
    {
        $GLOBALS['wpdb'] = new class {
            public string $prefix = 'wp_';
            public string $posts = 'wp_posts';

            public function prepare($query, ...$args)
            {
                return $query;
            }

            public function get_results($query)
            {
                return [
                    (object) ['post_id' => 33, 'post_title' => 'Favorito Uno', 'total_favorites' => '9'],
                ];
            }
        };

        $result = neema_get_top_recursos_by_favorites(1);

        $this->assertSame([
            ['id' => 33, 'title' => 'Favorito Uno', 'count' => 9],
        ], $result);
    }

    // Este test comprueba que se devuelve vacío si no hay recursos descargados.
    public function test_neema_get_top_recursos_by_downloads_returns_empty_when_no_rows(): void
    {
        $GLOBALS['wpdb'] = new class {
            public string $prefix = 'wp_';
            public string $posts = 'wp_posts';
            public string $postmeta = 'wp_postmeta';

            public function prepare($query, ...$args)
            {
                return $query;
            }

            public function get_results($query)
            {
                return [];
            }
        };

        $this->assertSame([], neema_get_top_recursos_by_downloads(5));
    }

    // Este test comprueba que se agregan correctamente las visitas, descargas y favoritos de gestores.
    public function test_neema_get_top_gestores_by_visit_download_and_favorite_aggregates(): void
    {
        $GLOBALS['__test_get_users_return'] = [
            (object) ['ID' => 10, 'display_name' => 'Gestor Uno'],
            (object) ['ID' => 20, 'display_name' => 'Gestor Dos'],
        ];
        $GLOBALS['__test_get_posts_callback'] = static function (array $args) {
            if (($args['author'] ?? null) === 10) {
                return [111, 112];
            }

            if (($args['author'] ?? null) === 20) {
                return [221];
            }

            return [];
        };
        $GLOBALS['__test_get_post_return'] = [
            111 => (object) ['ID' => 111, 'post_name' => 'recurso-111'],
            112 => (object) ['ID' => 112, 'post_name' => 'recurso-112'],
            221 => (object) ['ID' => 221, 'post_name' => 'recurso-221'],
        ];
        $GLOBALS['__test_post_meta'] = [
            111 => ['numDescargas' => '5'],
            112 => ['numDescargas' => '4'],
            221 => ['numDescargas' => '3'],
        ];
        $GLOBALS['wpdb'] = new class {
            public string $prefix = 'wp_';
            public string $posts = 'wp_posts';
            public string $postmeta = 'wp_postmeta';
            public array $varResults = [6, 2, 1];

            public function prepare($query, ...$args)
            {
                return $query;
            }

            public function get_var($query)
            {
                return array_shift($this->varResults);
            }

            public function get_results($query)
            {
                return [];
            }
        };

        $this->assertSame([
            ['id' => 10, 'name' => 'Gestor Uno', 'count' => 8, 'recursos_count' => 2],
            ['id' => 20, 'name' => 'Gestor Dos', 'count' => 1, 'recursos_count' => 1],
        ], neema_get_top_gestores_by_visits(5));

        $GLOBALS['wpdb'] = new class {
            public string $prefix = 'wp_';
            public string $posts = 'wp_posts';
            public string $postmeta = 'wp_postmeta';

            public function prepare($query, ...$args)
            {
                return $query;
            }

            public function get_var($query)
            {
                if (str_contains($query, '111')) {
                    return 7;
                }

                if (str_contains($query, '112')) {
                    return 1;
                }

                return 0;
            }

            public function get_results($query)
            {
                return [];
            }
        };

        $this->assertSame([
            ['id' => 10, 'name' => 'Gestor Uno', 'count' => 9, 'recursos_count' => 2],
            ['id' => 20, 'name' => 'Gestor Dos', 'count' => 3, 'recursos_count' => 1],
        ], neema_get_top_gestores_by_downloads(5));

        $GLOBALS['wpdb'] = new class {
            public string $prefix = 'wp_';
            public string $posts = 'wp_posts';
            public string $postmeta = 'wp_postmeta';
            public array $varResults = [4, 2, 1];

            public function prepare($query, ...$args)
            {
                return $query;
            }

            public function get_var($query)
            {
                return array_shift($this->varResults);
            }

            public function get_results($query)
            {
                return [];
            }
        };

        $this->assertSame([
            ['id' => 10, 'name' => 'Gestor Uno', 'count' => 6, 'recursos_count' => 2],
            ['id' => 20, 'name' => 'Gestor Dos', 'count' => 1, 'recursos_count' => 1],
        ], neema_get_top_gestores_by_favorites(5));
    }

    // Este test comprueba que se cuentan todas las dimensiones de usuarios correctamente.
    public function test_neema_get_users_distribution_data_counts_all_dimensions(): void
    {
        $GLOBALS['__test_get_users_return'] = [
            (object) ['ID' => 1, 'roles' => ['administrator']],
            (object) ['ID' => 2, 'roles' => ['subscriber']],
            (object) ['ID' => 3, 'roles' => []],
        ];
        $GLOBALS['__test_user_meta'] = [
            1 => ['rol_usuario' => 'gestor', 'entidad_proveniente' => 'Entidad A'],
            2 => ['rol_usuario' => '', 'entidad_proveniente' => 'Entidad A'],
            3 => ['rol_usuario' => 'gestor', 'entidad_proveniente' => ''],
        ];

        $result = neema_get_users_distribution_data();

        $this->assertSame(3, $result['total']);
        $this->assertSame(2, $result['rol']['gestor']);
        $this->assertSame(1, $result['rol']['Sin especificar']);
        $this->assertSame(2, $result['entidad']['Entidad A']);
        $this->assertSame(1, $result['entidad']['Sin especificar']);
        $this->assertSame(1, $result['wordpress_role']['Administrador']);
        $this->assertSame(1, $result['wordpress_role']['Suscriptor']);
        $this->assertSame(1, $result['wordpress_role']['Sin Rol']);
    }

    // Este test comprueba que el archivo de widgets se puede requerir varias veces sin error.
    public function test_dashboard_widgets_loader_file_can_be_required_again_safely(): void
    {
        require __DIR__ . '/../../inc/dashboard-widgets.php';

        $this->assertTrue(true);
    }

    // Este test comprueba que se agregan correctamente los recursos populares por características.
    public function test_neema_get_recursos_caracteristicas_data_aggregates_popular_resources(): void
    {
        $GLOBALS['wpdb'] = new class {
            public string $prefix = 'wp_';
            public string $posts = 'wp_posts';
            public string $postmeta = 'wp_postmeta';

            public function prepare($query, ...$args)
            {
                return $query;
            }

            public function get_results($query)
            {
                return [(object) ['post_id' => 100, 'post_title' => 'Recurso 100', 'total_visits' => '10']];
            }
        };
        $GLOBALS['__test_post_meta'] = [
            100 => [
                '_recurso_modulo' => 'mod-1',
                '_recurso_categoria' => 'Contextual',
                '_recurso_tipo' => 'tipo-1',
                '_recurso_paises' => [1],
                '_recurso_regiones' => ['africa'],
                '_recurso_tematicas' => ['agricultura'],
            ],
        ];
        $GLOBALS['__test_pages_by_path'] = [
            'mod-1' => (object) ['ID' => 501, 'post_title' => 'M1 Módulo principal'],
        ];
        $GLOBALS['__test_get_posts_return'] = [
            (object) ['ID' => 601, 'post_title' => 'Tipo Uno'],
        ];
        $GLOBALS['__test_get_post_return'] = [
            1 => (object) ['ID' => 1, 'post_title' => 'País Uno'],
        ];

        $result = neema_get_recursos_caracteristicas_data();

        $this->assertSame(['M1'], $result['modulos']['labels']);
        $this->assertSame([10], $result['modulos']['values']);
        $this->assertSame(['Contextual'], $result['categorias']['labels']);
        $this->assertSame(['Tipo Uno'], $result['tipos']['labels']);
        $this->assertSame(['País Uno'], $result['paises']['labels']);
        $this->assertSame(['África'], $result['regiones']['labels']);
        $this->assertSame(['Agricultura'], $result['tematicas']['labels']);
        $this->assertSame(100, $result['_debug'][0]['id']);
    }
}
