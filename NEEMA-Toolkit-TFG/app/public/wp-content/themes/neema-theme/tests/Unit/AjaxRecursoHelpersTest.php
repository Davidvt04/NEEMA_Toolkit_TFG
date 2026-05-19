<?php

declare(strict_types=1);

namespace NeemaTheme\Tests\Unit;

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/ajax-recurso-helpers.php';

final class AjaxRecursoHelpersTest extends TestCase
{
    public function test_neema_build_serialized_filter_returns_null_for_empty_values(): void
    {
        $this->assertNull(neema_build_serialized_filter('_recurso_tematicas', []));
    }

    public function test_neema_build_serialized_filter_builds_or_query(): void
    {
        $result = neema_build_serialized_filter('_recurso_tematicas', [3, 7]);

        $this->assertSame('OR', $result['relation']);
        $this->assertCount(3, $result);
        $this->assertSame('_recurso_tematicas', $result[0]['key']);
        $this->assertSame('LIKE', $result[0]['compare']);
        $this->assertSame('"3"', $result[0]['value']);
        $this->assertSame('"7"', $result[1]['value']);
    }

    public function test_neema_build_paises_filter_normalizes_country_ids(): void
    {
        $result = neema_build_paises_filter(['pais_4', 'pais_12']);

        $this->assertSame('OR', $result['relation']);
        $this->assertCount(3, $result);
        $this->assertSame('_recurso_paises', $result[0]['key']);
        $this->assertSame('i:4;', $result[0]['value']);
        $this->assertSame('i:12;', $result[1]['value']);
    }

    public function test_neema_filter_by_text_filters_matching_resources(): void
    {
        $resources = [
            (object) ['ID' => 1],
            (object) ['ID' => 2],
            (object) ['ID' => 3],
        ];

        $GLOBALS['__test_post_meta'] = [
            1 => ['_recurso_titulo_es' => 'Guia de apoyo', '_recurso_titulo_en' => '', '_recurso_titulo_fr' => '', 'descripcion_es' => '', 'descripcion_en' => '', 'descripcion_fr' => ''],
            2 => ['_recurso_titulo_es' => '', '_recurso_titulo_en' => 'Training', '_recurso_titulo_fr' => '', 'descripcion_es' => '', 'descripcion_en' => '', 'descripcion_fr' => ''],
            3 => ['_recurso_titulo_es' => '', '_recurso_titulo_en' => '', '_recurso_titulo_fr' => '', 'descripcion_es' => '', 'descripcion_en' => 'Contains guide words', 'descripcion_fr' => ''],
        ];

        $result = neema_filter_by_text($resources, 'guide');

        $this->assertCount(1, $result);
        $this->assertArrayHasKey(2, $result);
    }

    public function test_neema_filter_by_regiones_filters_non_arrays_and_matches_values(): void
    {
        $resources = [
            (object) ['ID' => 1],
            (object) ['ID' => 2],
            (object) ['ID' => 3],
        ];

        $GLOBALS['__test_post_meta'] = [
            1 => ['_recurso_regiones' => [10, 11]],
            2 => ['_recurso_regiones' => 'not-an-array'],
            3 => ['_recurso_regiones' => [99]],
        ];

        $result = neema_filter_by_regiones($resources, [11, 50]);

        $this->assertCount(1, $result);
        $this->assertSame(1, array_values($result)[0]->ID);
    }

    public function test_neema_filter_by_tematicas_filters_matches(): void
    {
        $resources = [
            (object) ['ID' => 1],
            (object) ['ID' => 2],
        ];

        $GLOBALS['__test_post_meta'] = [
            1 => ['_recurso_tematicas' => [100]],
            2 => ['_recurso_tematicas' => [200]],
        ];

        $result = neema_filter_by_tematicas($resources, [200]);

        $this->assertCount(1, $result);
        $this->assertSame(2, array_values($result)[0]->ID);
    }

    public function test_neema_filter_by_paises_filters_matches(): void
    {
        $resources = [
            (object) ['ID' => 1],
            (object) ['ID' => 2],
        ];

        $GLOBALS['__test_post_meta'] = [
            1 => ['_recurso_paises' => [4]],
            2 => ['_recurso_paises' => [8]],
        ];

        $result = neema_filter_by_paises($resources, ['pais_4']);

        $this->assertCount(1, $result);
        $this->assertSame(1, array_values($result)[0]->ID);
    }
}
