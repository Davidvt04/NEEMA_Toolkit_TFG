<?php

declare(strict_types=1);

use NeemaTheme\Tests\TestCase;

/**
 * FIX: ruta correcta al archivo real
 */
require_once __DIR__ . '/../../inc/recursos/cpt-recursos-guardado.php';

/**
 * =========================
 * WORDPRESS STUBS
 * =========================
 */

if (!function_exists('update_post_meta')) {
    function update_post_meta($post_id, $key, $value)
    {
        $GLOBALS['__test_post_meta'][$post_id][$key] = $value;
        return true;
    }
}

if (!function_exists('delete_post_meta')) {
    function delete_post_meta($post_id, $key)
    {
        unset($GLOBALS['__test_post_meta'][$post_id][$key]);
    }
}

if (!function_exists('set_transient')) {
    function set_transient($key, $value, $exp)
    {
        $GLOBALS['__test_transients'][$key] = $value;
    }
}

if (!function_exists('wp_safe_redirect')) {
    function wp_safe_redirect($url)
    {
        $GLOBALS['__test_redirect'] = $url;
        throw new \RuntimeException('redirect');
    }
}

if (!function_exists('get_edit_post_link')) {
    function get_edit_post_link($id, $context = 'url')
    {
        return "http://example.com/wp-admin/post.php?post={$id}&action=edit";
    }
}

if (!function_exists('current_user_can')) {
    function current_user_can($cap, $post_id)
    {
        return true;
    }
}

if (!function_exists('has_post_thumbnail')) {
    function has_post_thumbnail($post_id)
    {
        return $GLOBALS['__test_has_thumbnail'][$post_id] ?? false;
    }
}

if (!function_exists('wp_update_post')) {
    function wp_update_post($data, $wp_error = false, $fire = true)
    {
        $GLOBALS['__test_updated_post'] = $data;
        return $data['ID'];
    }
}

if (!function_exists('remove_action')) {
    function remove_action() {}
}

if (!function_exists('add_action')) {
    function add_action() {}
}

if (!function_exists('wp_verify_nonce')) {
    function wp_verify_nonce($nonce, $action)
    {
        return true;
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str)
    {
        return trim($str);
    }
}

/**
 * =========================
 * TESTS
 * =========================
 */
final class CptRecursosGuardadosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $_POST = [];

        $GLOBALS['__test_post_meta'] = [];
        $GLOBALS['__test_transients'] = [];
        $GLOBALS['__test_redirect'] = null;
        $GLOBALS['__test_has_thumbnail'] = [];
        $GLOBALS['__test_updated_post'] = [];
    }

    public function test_save_titulos_success(): void
    {
        $_POST = [
            'neema_recursos_titulos_nonce' => 'ok',
            'recurso_titulo_es' => 'Titulo ES',
            'recurso_titulo_en' => 'Title EN',
            'recurso_titulo_fr' => 'Titre FR',
        ];

        neema_recursos_save_titulos(1);

        $this->assertSame('Titulo ES', $GLOBALS['__test_post_meta'][1]['_recurso_titulo_es']);
        $this->assertSame('Title EN', $GLOBALS['__test_post_meta'][1]['_recurso_titulo_en']);
        $this->assertSame('Titre FR', $GLOBALS['__test_post_meta'][1]['_recurso_titulo_fr']);
    }

    public function test_save_titulos_missing_fields_redirects(): void
    {
        $_POST = [
            'neema_recursos_titulos_nonce' => 'ok',
            'recurso_titulo_es' => '',
            'recurso_titulo_en' => '',
            'recurso_titulo_fr' => '',
        ];

        try {
            neema_recursos_save_titulos(2);
        } catch (\RuntimeException $e) {
            $this->assertSame('redirect', $e->getMessage());
        }

        $this->assertArrayHasKey('neema_recursos_errors_2', $GLOBALS['__test_transients']);
    }
}