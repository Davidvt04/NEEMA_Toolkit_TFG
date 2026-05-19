<?php

declare(strict_types=1);

namespace NeemaTheme\Tests;

use Brain\Monkey;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
        $GLOBALS['__test_wp_query_callback'] = null;
        $GLOBALS['__test_current_post'] = null;
        $GLOBALS['__test_current_post_id'] = 0;
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}