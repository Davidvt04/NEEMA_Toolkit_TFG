<?php

declare(strict_types=1);

namespace NeemaTheme\Tests\Unit;

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/admin/admin-restrictions.php';

final class AdminRestrictionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['__test_current_user'] = (object) ['ID' => 1, 'roles' => []];
    }

    public function test_hide_admin_bar_items_does_nothing_for_unprivileged_roles(): void
    {
        $bar = new FakeAdminBar();
        $GLOBALS['__test_current_user'] = (object) ['ID' => 1, 'roles' => ['subscriber']];

        neema_hide_admin_bar_items($bar);

        $this->assertSame([], $bar->removed);
    }

    public function test_hide_admin_bar_items_removes_expected_nodes_for_managers(): void
    {
        $bar = new FakeAdminBar();
        $GLOBALS['__test_current_user'] = (object) ['ID' => 1, 'roles' => ['gestor_contenido']];

        neema_hide_admin_bar_items($bar);

        $this->assertSame(['comments', 'new-content', 'view-posts', 'archive'], $bar->removed);
    }
}

final class FakeAdminBar
{
    public array $removed = [];

    public function remove_node($id): void
    {
        $this->removed[] = $id;
    }
}
