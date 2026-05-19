<?php

use PHPUnit\Framework\TestCase;

class RoleRegistrationTest extends TestCase
{
    private array $roles_mock = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->roles_mock = [];

        // Mock remove_role
        if (!function_exists('remove_role')) {
            function remove_role($role) {}
        }

        // Mock add_role (IMPORTANTE: usa propiedad del test)
        $self = $this;

        if (!function_exists('add_role')) {
            function add_role($role, $display_name, $capabilities = [])
            {
                global $roles_mock;

                if (!isset($roles_mock)) {
                    $roles_mock = [];
                }

                $roles_mock[$role] = [
                    'name' => $display_name,
                    'capabilities' => $capabilities,
                ];

                return (object) $roles_mock[$role];
            }
        }

        // Mock WP functions
        if (!function_exists('wp_safe_redirect')) {
            function wp_safe_redirect($url) { return $url; }
        }

        if (!function_exists('home_url')) {
            function home_url() { return 'http://example.com'; }
        }

        // IMPORTANTE: no usar add_action automático
        if (!function_exists('add_action')) {
            function add_action($hook, $callback, $priority = 10) {}
        }

        global $roles_mock;
        $roles_mock = [];

        require_once __DIR__ . '/../../inc/roles/role-registration.php';

        // EJECUCIÓN MANUAL (CLAVE DEL FIX)
        neema_register_custom_roles();
    }

    // Este test comprueba que los roles personalizados se registran correctamente.
    public function test_roles_are_registered()
    {
        global $roles_mock;

        $this->assertIsArray($roles_mock);

        $this->assertArrayHasKey('visitante', $roles_mock);
        $this->assertArrayHasKey('gestor_contenido', $roles_mock);
        $this->assertArrayHasKey('neema_admin', $roles_mock);
    }

    // Este test comprueba que el rol visitante tiene el permiso de lectura.
    public function test_visitante_has_read_cap()
    {
        global $roles_mock;

        $this->assertTrue($roles_mock['visitante']['capabilities']['read']);
    }

    // Este test comprueba que el rol gestor_contenido puede subir archivos.
    public function test_gestor_contenido_has_upload_cap()
    {
        global $roles_mock;

        $this->assertTrue($roles_mock['gestor_contenido']['capabilities']['upload_files']);
    }
}