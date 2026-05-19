<?php

declare(strict_types=1);

namespace NeemaTheme\Tests\Unit {

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/theme-setup.php';

final class ThemeSetupTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['__test_add_theme_support_calls'] = [];
    }

    // Este test comprueba que se habilitan las miniaturas de las entradas en el tema.
    public function test_setup_theme_enables_post_thumbnails(): void
    {
        neema_setup_theme();

        $this->assertSame([['post-thumbnails', []]], $GLOBALS['__test_add_theme_support_calls']);
    }
}
}
