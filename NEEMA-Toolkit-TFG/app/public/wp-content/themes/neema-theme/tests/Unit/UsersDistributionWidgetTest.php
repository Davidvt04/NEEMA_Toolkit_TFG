<?php

declare(strict_types=1);

namespace NeemaTheme\Tests\Unit;

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/dashboard-widgets/users-distribution-widget.php';

final class UsersDistributionWidgetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['__test_get_users_return'] = [];
        $GLOBALS['__test_user_meta'] = [];
        $GLOBALS['__test_dashboard_widget_calls'] = [];
    }

    // Este test comprueba que se cuentan correctamente los roles, entidades y roles de WordPress.
    public function test_get_users_distribution_data_counts_roles_entities_and_wp_roles(): void
    {
        $GLOBALS['__test_get_users_return'] = [
            (object) ['ID' => 1, 'roles' => ['administrator']],
            (object) ['ID' => 2, 'roles' => ['visitante']],
            (object) ['ID' => 3, 'roles' => ['neema_admin']],
        ];
        $GLOBALS['__test_user_meta'] = [
            1 => ['rol_usuario' => 'Gestor', 'entidad_proveniente' => 'Ministerio'],
            2 => ['rol_usuario' => '', 'entidad_proveniente' => ''],
            3 => ['rol_usuario' => 'Gestor', 'entidad_proveniente' => 'Ministerio'],
        ];

        $data = neema_get_users_distribution_data();

        $this->assertSame(3, $data['total']);
        $this->assertSame(2, $data['rol']['Gestor']);
        $this->assertSame(1, $data['rol']['Sin especificar']);
        $this->assertSame(2, $data['entidad']['Ministerio']);
        $this->assertSame(1, $data['entidad']['Sin especificar']);
        $this->assertSame(1, $data['wordpress_role']['Administrador']);
        $this->assertSame(1, $data['wordpress_role']['Visitante']);
        $this->assertSame(1, $data['wordpress_role']['Administrador funcional NEEMA']);
    }

    // Este test comprueba que el contenido del widget muestra controles, resumen y el gráfico.
    public function test_widget_content_renders_controls_summary_and_chart_placeholder(): void
    {
        $GLOBALS['__test_get_users_return'] = [
            (object) ['ID' => 1, 'roles' => ['administrator']],
        ];
        $GLOBALS['__test_user_meta'] = [
            1 => ['rol_usuario' => 'Gestor', 'entidad_proveniente' => 'Ministerio'],
        ];

        ob_start();
        neema_users_distribution_widget_content();
        $output = ob_get_clean();

        $this->assertStringContainsString('neemaUsersChart', $output);
        $this->assertStringContainsString('neema-chart-controls', $output);
        $this->assertStringContainsString('Total Usuarios', $output);
        $this->assertStringContainsString('Roles Diferentes', $output);
        $this->assertStringContainsString('Entidades', $output);
    }

    // Este test comprueba que el widget de distribución de usuarios se registra en el dashboard.
    public function test_add_users_distribution_widget_registers_dashboard_widget(): void
    {
        neema_add_users_distribution_widget();

        $this->assertCount(1, $GLOBALS['__test_dashboard_widget_calls']);
        $this->assertSame('neema_users_distribution', $GLOBALS['__test_dashboard_widget_calls'][0][0]);
        $this->assertSame('Distribución de Usuarios', $GLOBALS['__test_dashboard_widget_calls'][0][1]);
    }
}
