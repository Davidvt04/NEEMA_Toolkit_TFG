<?php

declare(strict_types=1);

namespace {
    if (!function_exists('remove_accents')) {
        function remove_accents($string) {
            return $string;
        }
    }

    if (!function_exists('get_post_meta')) {
        function get_post_meta($post_id, $key, $single = true) {
            return $GLOBALS['__test_post_meta'][$post_id][$key] ?? '';
        }
    }

    if (!function_exists('update_post_meta')) {
        function update_post_meta($post_id, $key, $value) {
            $GLOBALS['__test_update_post_meta_calls'][] = [$post_id, $key, $value];
        }
    }

    if (!function_exists('delete_post_meta')) {
        function delete_post_meta($post_id, $key) {
            $GLOBALS['__test_delete_post_meta_calls'][] = [$post_id, $key];
        }
    }

    if (!function_exists('wp_verify_nonce')) {
        function wp_verify_nonce($nonce, $action) {
            return true;
        }
    }

    if (!function_exists('current_user_can')) {
        function current_user_can($capability, $post_id = null) {
            return $GLOBALS['__test_current_user_can']['edit_post'] ?? true;
        }
    }

    if (!function_exists('wp_nonce_field')) {
        function wp_nonce_field() {
            echo '<input type="hidden" name="neema_member_meta_nonce" value="1">';
        }
    }

    if (!function_exists('esc_attr')) {
        function esc_attr($text) {
            return htmlspecialchars((string)$text, ENT_QUOTES);
        }
    }

    if (!function_exists('esc_textarea')) {
        function esc_textarea($text) {
            return htmlspecialchars((string)$text, ENT_QUOTES);
        }
    }

    if (!function_exists('esc_url')) {
        function esc_url($text) {
            return htmlspecialchars((string)$text, ENT_QUOTES);
        }
    }

    if (!function_exists('esc_url_raw')) {
        function esc_url_raw($text) {
            return $text;
        }
    }

    if (!function_exists('sanitize_text_field')) {
        function sanitize_text_field($text) {
            return trim(strip_tags((string)$text));
        }
    }

    if (!function_exists('sanitize_textarea_field')) {
        function sanitize_textarea_field($text) {
            return trim(strip_tags((string)$text));
        }
    }
}

namespace NeemaTheme\Tests\Unit {

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/cpt-miembros.php';

final class MiembrosCptTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['__test_register_post_type_calls'] = [];
        $GLOBALS['__test_add_meta_box_calls'] = [];
        $GLOBALS['__test_update_post_meta_calls'] = [];
        $GLOBALS['__test_delete_post_meta_calls'] = [];
        $GLOBALS['__test_post_meta'] = [];
        $GLOBALS['__test_current_user_can'] = ['edit_post' => true];

        $_POST = [];
    }

    // Este test comprueba que se registra el CPT de miembros.
    public function test_register_miembros_cpt_is_called(): void
    {
        neema_register_miembros_cpt();

        $this->assertSame(
            'miembro',
            $GLOBALS['__test_register_post_type_calls'][0][0]
        );
    }

    // Este test comprueba que se registran los meta boxes de miembros.
    public function test_add_meta_boxes_are_registered(): void
    {
        neema_add_member_meta_boxes();

        $this->assertNotEmpty($GLOBALS['__test_add_meta_box_calls']);
    }

    // Este test comprueba que el meta box de URL de miembro muestra los campos.
    public function test_member_url_meta_box_renders_fields(): void
    {
        $GLOBALS['__test_post_meta'][10] = [
            '_member_url' => 'https://test.com',
            '_member_description_es' => 'Desc ES',
            '_member_description_en' => 'Desc EN',
            '_member_description_fr' => 'Desc FR',
        ];

        ob_start();
        neema_member_url_meta_box_callback((object)['ID' => 10]);
        $output = ob_get_clean();

        $this->assertStringContainsString('member_url', $output);
        $this->assertStringContainsString('Desc ES', $output);
    }

    // Este test comprueba que el meta box de orden de miembro muestra el valor.
    public function test_member_order_meta_box_renders_value(): void
    {
        $GLOBALS['__test_post_meta'][11] = [
            '_member_order' => '3'
        ];

        ob_start();
        neema_member_order_meta_box_callback((object)['ID' => 11]);
        $output = ob_get_clean();

        $this->assertStringContainsString('3', $output);
        $this->assertStringContainsString('member_order', $output);
    }

    // Este test comprueba que guardar meta de miembro actualiza los campos básicos.
    public function test_save_member_meta_updates_basic_fields(): void
    {
        $_POST = [
            'neema_member_meta_nonce' => '1',
            'member_url' => 'https://site.com',
            'member_description_es' => '<p>ES</p>',
            'member_description_en' => '<p>EN</p>',
            'member_description_fr' => '<p>FR</p>',
            'member_order' => '5'
        ];

        neema_save_member_meta(99);

        $calls = $GLOBALS['__test_update_post_meta_calls'];

        $this->assertContains([99, '_member_url', 'https://site.com'], $calls);
        $this->assertContains([99, '_member_order', 5], $calls);
    }

    // Este test comprueba que guardar meta de miembro guarda las entidades asociadas.
    public function test_save_member_meta_saves_entities(): void
    {
        $_POST = [
            'neema_member_meta_nonce' => '1',
            'member_entities' => [
                [
                    'title' => 'Entidad 1',
                    'url' => 'https://entidad.com',
                    'image' => 'https://img.com/a.jpg',
                    'description_es' => 'Desc',
                    'participants' => [
                        [
                            'title' => 'Participante 1',
                            'url' => 'https://p.com',
                            'image' => '',
                            'description_es' => 'P desc'
                        ]
                    ]
                ]
            ]
        ];

        neema_save_member_meta(100);

        $this->assertNotEmpty($GLOBALS['__test_update_post_meta_calls']);
    }
}
}