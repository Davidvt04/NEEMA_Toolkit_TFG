<?php

declare(strict_types=1);

namespace NeemaTheme\Tests\Unit;

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/dashboard-widgets/top-recursos-widget.php';
require_once __DIR__ . '/../../inc/dashboard-widgets/users-distribution-widget.php';
require_once __DIR__ . '/../../inc/dashboard-widgets/recursos-caracteristicas-widget.php';
require_once __DIR__ . '/../../inc/dashboard-widgets/language-distribution-widget.php';
require_once __DIR__ . '/../../inc/dashboard-widgets/top-gestores-widget.php';

final class DashboardWidgetsRegistrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['__test_dashboard_widget_calls'] = [];
    }

    // Este test comprueba que los helpers de registro añaden los widgets al dashboard.
    public function test_widget_registration_helpers_call_wp_add_dashboard_widget(): void
    {
        neema_add_top_recursos_widget();
        neema_add_users_distribution_widget();
        neema_add_recursos_caracteristicas_widget();
        neema_add_language_distribution_widget();
        neema_add_top_gestores_widget();

        $this->assertCount(4, $GLOBALS['__test_dashboard_widget_calls']);
        $this->assertSame('neema_top_recursos', $GLOBALS['__test_dashboard_widget_calls'][0][0]);
        $this->assertSame('neema_users_distribution', $GLOBALS['__test_dashboard_widget_calls'][1][0]);
        $this->assertSame('neema_recursos_caracteristicas', $GLOBALS['__test_dashboard_widget_calls'][2][0]);
        $this->assertSame('neema_top_gestores', $GLOBALS['__test_dashboard_widget_calls'][3][0]);
    }
}
