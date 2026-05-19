<?php

declare(strict_types=1);

namespace NeemaTheme\Tests\Unit;

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/buscador-helpers.php';

final class BuscadorHelpersTest extends TestCase
{
    public function test_neema_normalizar_ciudad_trims_and_title_cases_text(): void
    {
        $this->assertSame('Buenos Aires', neema_normalizar_ciudad('  buenos    aires  '));
    }

    public function test_neema_get_all_paises_maps_posts_and_uses_fallback_key(): void
    {
        $paises = [
            (object) ['ID' => 10, 'post_title' => 'Argentina'],
            (object) ['ID' => 20, 'post_title' => 'Chile'],
        ];

        $GLOBALS['__test_get_posts_return'] = $paises;
        $GLOBALS['__test_post_meta'] = [
            10 => ['_pais_key' => 'ar'],
            20 => ['_pais_key' => ''],
        ];

        $this->assertSame([
            ['id' => 10, 'key' => 'ar', 'title' => 'Argentina'],
            ['id' => 20, 'key' => 'pais_20', 'title' => 'Chile'],
        ], neema_get_all_paises());
    }
}
