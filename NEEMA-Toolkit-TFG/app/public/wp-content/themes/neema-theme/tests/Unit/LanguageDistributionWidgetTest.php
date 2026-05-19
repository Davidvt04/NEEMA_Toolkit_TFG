<?php

declare(strict_types=1);

namespace NeemaTheme\Tests\Unit;

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/dashboard-widgets/language-distribution-widget.php';

final class LanguageDistributionWidgetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['wpdb'] = new LanguageDistributionWpdbFake();
        $GLOBALS['__test_wp_send_json_success'] = [];
        $_POST = [];
    }

    // Este test comprueba que se detecta el idioma correctamente en la URL.
    public function test_detect_language_from_url_handles_query_and_path_patterns(): void
    {
        $availableLanguages = ['es', 'en', 'fr'];

        $this->assertSame('en', neema_detect_language_from_url('/recurso/test?lang=en', $availableLanguages));
        $this->assertSame('fr', neema_detect_language_from_url('/fr/recursos/', $availableLanguages));
        $this->assertSame('es', neema_detect_language_from_url('/recursos/sin-prefijo', $availableLanguages));
    }

    // Este test comprueba que se obtiene la condición SQL correcta para cada periodo.
    public function test_get_date_condition_for_period_returns_expected_sql(): void
    {
        $this->assertSame('DATE(date) = CURDATE()', neema_get_date_condition_for_period('today'));
        $this->assertSame('date >= DATE_SUB(NOW(), INTERVAL 7 DAY)', neema_get_date_condition_for_period('week'));
        $this->assertSame('date >= DATE_SUB(NOW(), INTERVAL 30 DAY)', neema_get_date_condition_for_period('month'));
        $this->assertSame('date >= DATE_SUB(NOW(), INTERVAL 365 DAY)', neema_get_date_condition_for_period('year'));
    }

    // Este test comprueba que los datos de visitas se agrupan por idioma correctamente.
    public function test_get_language_distribution_data_groups_visits_by_language(): void
    {
        /** @var LanguageDistributionWpdbFake $wpdb */
        $wpdb = $GLOBALS['wpdb'];
        $wpdb->results = [
            (object) ['uri' => '/recurso/uno?lang=en', 'visits' => '3'],
            (object) ['uri' => '/fr/recurso/dos', 'visits' => '5'],
            (object) ['uri' => '/recurso/tres', 'visits' => '2'],
        ];

        $data = neema_get_language_distribution_data('month');

        $this->assertSame(10, $data['total']);
        $this->assertSame(5, $data['languages']['Français']);
        $this->assertSame(3, $data['languages']['English']);
        $this->assertSame(2, $data['languages']['Español']);
        $this->assertSame('Français', $data['most_visited']);
    }

    // Este test comprueba que el widget muestra estados vacío y con datos.
    public function test_widget_content_renders_empty_and_populated_states(): void
    {
        /** @var LanguageDistributionWpdbFake $wpdb */
        $wpdb = $GLOBALS['wpdb'];
        $wpdb->results = [];

        ob_start();
        neema_language_distribution_widget_content();
        $emptyOutput = ob_get_clean();

        $wpdb->results = [
            (object) ['uri' => '/recurso/uno?lang=en', 'visits' => '4'],
        ];

        ob_start();
        neema_language_distribution_widget_content();
        $filledOutput = ob_get_clean();

        $this->assertStringContainsString('No hay datos de visitas disponibles aún.', $emptyOutput);
        $this->assertStringContainsString('neemaLanguageChart', $filledOutput);
        $this->assertStringContainsString('neema-language-period-selector', $filledOutput);
        $this->assertStringContainsString('Idioma Principal', $filledOutput);
    }

    // Este test comprueba que el widget de distribución de idiomas se registra en el dashboard.
    public function test_add_language_distribution_widget_registers_dashboard_widget(): void
    {
        if (!function_exists('wp_statistics_pages')) {
            eval('namespace { function wp_statistics_pages() {} }');
        }

        $GLOBALS['__test_dashboard_widget_calls'] = [];

        neema_add_language_distribution_widget();

        $this->assertCount(1, $GLOBALS['__test_dashboard_widget_calls']);
        $this->assertSame('neema_language_distribution', $GLOBALS['__test_dashboard_widget_calls'][0][0]);
        $this->assertSame('Distribución de Visitas por Idioma', $GLOBALS['__test_dashboard_widget_calls'][0][1]);
    }

    // Este test comprueba que el ajax devuelve correctamente los datos de idioma.
    public function test_ajax_get_language_stats_returns_success_payload(): void
    {
        /** @var LanguageDistributionWpdbFake $wpdb */
        $wpdb = $GLOBALS['wpdb'];
        $wpdb->results = [
            (object) ['uri' => '/recurso/uno?lang=en', 'visits' => '4'],
        ];
        $_POST = [
            'nonce' => wp_create_nonce('neema_language_stats'),
            'period' => 'today',
        ];

        try {
            neema_ajax_get_language_stats();
            $this->fail('Expected wp_send_json_success to be called.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('wp_send_json_success called', $exception->getMessage());
        }

        $this->assertSame(4, $GLOBALS['__test_wp_send_json_success'][0]['total']);
    }
}

final class LanguageDistributionWpdbFake
{
    public string $prefix = 'wp_';

    /** @var array<int, object> */
    public array $results = [];

    public array $prepareCalls = [];

    public function prepare($query, ...$args)
    {
        $this->prepareCalls[] = [$query, $args];

        return $query;
    }

    public function get_results($query)
    {
        return $this->results;
    }
}
