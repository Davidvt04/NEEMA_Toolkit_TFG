<?php

declare(strict_types=1);

namespace {
    if (!function_exists('current_user_can')) {
        function current_user_can($capability)
        {
            $currentUser = $GLOBALS['__test_current_user'] ?? (object) ['roles' => []];

            return in_array('edit_users', (array) ($currentUser->roles ?? []), true);
        }
    }

    if (!function_exists('wp_die')) {
        function wp_die($message)
        {
            throw new \RuntimeException((string) $message);
        }
    }

    if (!function_exists('wp_nonce_url')) {
        function wp_nonce_url($url, $action = -1)
        {
            return $url . '&_wpnonce=nonce';
        }
    }

    if (!function_exists('get_current_screen')) {
        function get_current_screen()
        {
            return $GLOBALS['__test_current_screen'] ?? (object) ['id' => ''];
        }
    }

    if (!function_exists('selected')) {
        function selected($selected, $current, $echo = true)
        {
            $value = ((string) $selected === (string) $current) ? ' selected="selected"' : '';

            if ($echo) {
                echo $value;
            }

            return $value;
        }
    }

    if (!function_exists('add_query_arg')) {
        function add_query_arg($key, $value = null, $url = null)
        {
            if (is_array($key)) {
                $query = http_build_query($key);
                return rtrim((string) $url, '?') . '?' . $query;
            }

            $separator = (str_contains((string) $url, '?')) ? '&' : '?';

            return (string) $url . $separator . rawurlencode((string) $key) . '=' . rawurlencode((string) $value);
        }
    }

    if (!function_exists('get_bloginfo')) {
        function get_bloginfo($show = '')
        {
            return 'NEEMA';
        }
    }

    if (!function_exists('get_option')) {
        function get_option($option, $default = false)
        {
            return 'admin@example.test';
        }
    }

    if (!function_exists('wp_generate_password')) {
        function wp_generate_password($length = 12, $special_chars = true, $extra_special_chars = false)
        {
            return str_repeat('a', $length);
        }
    }

    if (!function_exists('esc_url')) {
        function esc_url($url)
        {
            return $url;
        }
    }

    if (!function_exists('esc_attr')) {
        function esc_attr($text)
        {
            return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
        }
    }

    if (!function_exists('pll_e')) {
        function pll_e($text)
        {
            echo $text;
        }
    }
}

namespace NeemaTheme\Tests\Unit {

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/users/user-verification.php';

final class UserVerificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['__test_user_meta'] = [];
        $GLOBALS['__test_redirect_calls'] = [];
        $GLOBALS['__test_current_user'] = (object) ['ID' => 1, 'roles' => ['edit_users']];
        $GLOBALS['__test_current_screen'] = (object) ['id' => 'users'];
        $GLOBALS['__test_is_admin'] = true;
        $_GET = [];
        $GLOBALS['pagenow'] = 'users.php';
    }

    // Este test comprueba que se añaden y ordenan las columnas de estado de cuenta.
    public function test_add_and_sortable_columns_include_account_status(): void
    {
        $columns = neema_add_user_status_column(['username' => 'Usuario']);
        $sortable = neema_make_status_column_sortable([]);

        $this->assertSame('Estado de verificación', $columns['account_status']);
        $this->assertSame('account_status', $sortable['account_status']);
    }

    // Este test comprueba que la columna de estado muestra los valores verificado y pendiente.
    public function test_show_user_status_column_renders_verified_and_pending_states(): void
    {
        $GLOBALS['__test_user_meta'][10]['account_status'] = 'awaiting_email_confirmation';
        $pending = neema_show_user_status_column('', 'account_status', 10);

        $GLOBALS['__test_user_meta'][11]['account_status'] = 'verified';
        $verified = neema_show_user_status_column('', 'account_status', 11);

        $this->assertStringContainsString('No verificado', $pending);
        $this->assertStringContainsString('Reenviar email', $pending);
        $this->assertStringContainsString('Verificado', $verified);
    }

    // Este test comprueba que el filtro de estado de usuario se muestra en la pantalla de usuarios.
    public function test_add_user_status_filter_outputs_select_on_users_screen(): void
    {
        $_GET['user_status'] = 'verified';

        ob_start();
        neema_add_user_status_filter();
        $output = ob_get_clean();

        $this->assertStringContainsString('user_status_filter', $output);
        $this->assertStringContainsString('selected="selected"', $output);
    }

    // Este test comprueba que el filtrado por estado de usuario genera las meta queries correctas.
    public function test_filter_users_by_status_builds_expected_meta_queries(): void
    {
        $query = new FakeUserQuery();
        $_GET['user_status'] = 'awaiting_email_confirmation';

        neema_filter_users_by_status($query);

        $this->assertSame([
            [
                'meta_query' => [
                    [
                        'key' => 'account_status',
                        'value' => 'awaiting_email_confirmation',
                        'compare' => '=',
                    ],
                ],
            ],
        ], $query->sets);

        $query = new FakeUserQuery();
        $_GET['user_status'] = 'verified';

        neema_filter_users_by_status($query);

        $this->assertSame('OR', $query->sets[0]['meta_query']['relation']);
    }

    // Este test comprueba que se lanza un error si el nonce es inválido al reenviar activación.
    public function test_handle_resend_activation_triggers_die_for_invalid_nonce(): void
    {
        $GLOBALS['__test_nonce_verification_result'] = false;
        $_GET = [
            'action' => 'resend_activation',
            'user_id' => 99,
            '_wpnonce' => 'bad',
        ];

        try {
            neema_handle_resend_activation();
            $this->fail('Expected wp_die to be called.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Error de seguridad.', $exception->getMessage());
        }
    }

    // Este test comprueba que los avisos de reenvío muestran los mensajes esperados.
    public function test_show_resend_notices_outputs_expected_messages(): void
    {
        $_GET = ['resend_success' => '1'];
        ob_start();
        neema_show_resend_notices();
        $success = ob_get_clean();

        $_GET = ['resend_error' => '2'];
        ob_start();
        neema_show_resend_notices();
        $error = ob_get_clean();

        $this->assertStringContainsString('reenviado correctamente', $success);
        $this->assertStringContainsString('Error al enviar el email', $error);
    }
}

final class FakeUserQuery
{
    public array $sets = [];

    public function set($key, $value): void
    {
        $this->sets[] = [$key => $value];
    }
}

}
