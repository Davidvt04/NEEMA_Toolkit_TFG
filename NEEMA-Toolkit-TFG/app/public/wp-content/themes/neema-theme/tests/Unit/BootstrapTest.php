<?php

declare(strict_types=1);

namespace NeemaTheme\Tests\Unit;

use NeemaTheme\Tests\TestCase;

final class BootstrapTest extends TestCase
{
    public function test_bootstrap_loads_theme_functions(): void
    {
        require_once __DIR__ . '/../../inc/recursos/preferencias-recursos.php';

        $this->assertTrue(function_exists('neema_get_preferencias'));
    }
}