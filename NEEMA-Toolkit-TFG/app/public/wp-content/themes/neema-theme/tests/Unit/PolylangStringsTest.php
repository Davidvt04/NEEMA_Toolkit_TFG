<?php

declare(strict_types=1);

namespace {
if (!function_exists('pll_register_string')) {
    function pll_register_string($name, $string, $group = '') {
        $GLOBALS['__test_pll_register_string_calls'][] = [$name, $string, $group];
    }
}
}

namespace NeemaTheme\Tests\Unit {

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/polylang-strings.php';

final class PolylangStringsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['__test_pll_register_string_calls'] = [];
    }

    // Este test comprueba que se registran cadenas de varios apartados en Polylang.
    public function test_neema_registrar_cadenas_registers_strings_from_multiple_sections(): void
    {
        neema_registrar_cadenas();

        $calls = $GLOBALS['__test_pll_register_string_calls'];

        $this->assertGreaterThan(150, count($calls));
        $this->assertContains(['Home', 'Home', 'General'], $calls);
        $this->assertContains(['Iniciar Sesión', 'Iniciar Sesión', 'Header'], $calls);
        $this->assertContains(['Volver al inicio', 'Volver al inicio', '404 Page'], $calls);
        $this->assertContains(['Mi Perfil', 'Mi Perfil', 'User Menu'], $calls);
        $this->assertContains(['Propuestas de diseños', 'Propuestas de diseños', 'Resiliencia Alimentaria y Nutricional'], $calls);
    }
}
}
