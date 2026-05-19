<?php

declare(strict_types=1);

namespace NeemaTheme\Tests\Unit {

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/cpt-organismos.php';

final class OrganismosCptTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['__test_register_post_type_calls'] = [];
        $GLOBALS['__test_add_meta_box_calls'] = [];
        $GLOBALS['__test_update_post_meta_calls'] = [];
        $GLOBALS['__test_delete_post_meta_calls'] = [];
        $GLOBALS['__test_update_post_calls'] = [];
        $GLOBALS['__test_redirect_calls'] = [];
        $GLOBALS['__test_roles'] = [];
        $GLOBALS['__test_has_post_thumbnail'] = [];
        $GLOBALS['__test_post_status'] = [];
        $GLOBALS['__test_post_types'] = [];
        $GLOBALS['__test_post_meta'] = [];
        $GLOBALS['__test_get_posts_return'] = [];
        $GLOBALS['__test_current_user_can'] = ['edit_post' => true];
        $_POST = [];
    }

    // Este test comprueba que se registran el CPT y los permisos de organismo.
    public function test_register_and_caps_helpers_configure_organismo_support(): void
    {
        neema_register_organismos_cpt();
        neema_add_organismo_caps_to_administrator();

        $this->assertSame('organismo', $GLOBALS['__test_register_post_type_calls'][0][0]);
        $this->assertContains('edit_organismo', $GLOBALS['__test_roles']['administrator']->caps);
        $this->assertContains('publish_organismos', $GLOBALS['__test_roles']['administrator']->caps);
    }

    // Este test comprueba que los meta boxes muestran selects, checkboxes y ciudad.
    public function test_meta_box_callbacks_render_selects_checkboxes_and_city_field(): void
    {
        $GLOBALS['__test_get_posts_return'] = [
            (object) ['ID' => 11, 'post_title' => 'Categoría Uno'],
            (object) ['ID' => 21, 'post_title' => 'País Uno'],
            (object) ['ID' => 22, 'post_title' => 'País Dos'],
        ];
        $GLOBALS['__test_post_meta'][7] = [
            '_organismo_categoria' => 11,
            '_organismo_ambito' => 'Local',
            '_organismo_paises' => [21],
            '_organismo_ciudad' => 'Dakar',
        ];

        ob_start();
        neema_organismo_categoria_meta_box_callback((object) ['ID' => 7]);
        $categoriaOutput = ob_get_clean();

        ob_start();
        neema_organismo_ambito_meta_box_callback((object) ['ID' => 7]);
        $ambitoOutput = ob_get_clean();

        ob_start();
        neema_organismo_paises_meta_box_callback((object) ['ID' => 7]);
        $paisesOutput = ob_get_clean();

        ob_start();
        neema_organismo_ciudad_meta_box_callback((object) ['ID' => 7]);
        $ciudadOutput = ob_get_clean();

        $this->assertStringContainsString('Categoría Uno', $categoriaOutput);
        $this->assertStringContainsString('selected', $categoriaOutput);
        $this->assertStringContainsString('Nacional', $ambitoOutput);
        $this->assertStringContainsString('checked', $paisesOutput);
        $this->assertStringContainsString('ciudad_wrapper', $ciudadOutput);
        $this->assertStringContainsString('Dakar', $ciudadOutput);
    }

    // Este test comprueba que guardar meta actualiza campos y recoge errores de validación.
    public function test_save_meta_updates_fields_and_collects_validation_errors(): void
    {
        $GLOBALS['__test_has_post_thumbnail'][80] = false;
        $GLOBALS['__test_post_status'][80] = 'publish';
        $GLOBALS['__test_post_types'][80] = 'organismo';
        $GLOBALS['__test_post_types'][81] = 'organismo';
        $_POST = [
            'organismo_categoria' => '',
            'organismo_ambito' => 'Local',
            'organismo_paises' => [],
            'organismo_ciudad' => '  saint   louis ',
        ];

        neema_save_organismo_meta(80);

        $this->assertSame([[80, '_organismo_ambito', 'Local'], [80, '_organismo_paises', []], [80, '_organismo_ciudad', 'Saint Louis'], [80, '_neema_organismo_validation_errors', ['La Imagen destacada es obligatoria.', 'La Categoría del Organismo es obligatoria.', 'El campo de países es obligatorio para ámbito Local y Nacional.']]], $GLOBALS['__test_update_post_meta_calls']);
        $this->assertSame([[['ID' => 80, 'post_status' => 'draft'], false, true]], $GLOBALS['__test_update_post_calls']);

        $GLOBALS['__test_has_post_thumbnail'][81] = true;
        $GLOBALS['__test_post_meta'] = [];
        $GLOBALS['__test_update_post_meta_calls'] = [];
        $GLOBALS['__test_delete_post_meta_calls'] = [];
        $_POST = [
            'organismo_categoria' => '11',
            'organismo_ambito' => 'Internacional',
            'organismo_paises' => [21, 22],
            'organismo_ciudad' => 'dakar',
        ];

        neema_save_organismo_meta(81);

        $this->assertSame([[81, '_organismo_categoria', '11'], [81, '_organismo_ambito', 'Internacional'], [81, '_organismo_paises', [21, 22]], [81, '_organismo_ciudad', 'Dakar']], $GLOBALS['__test_update_post_meta_calls']);
        $this->assertSame([[81, '_neema_organismo_validation_errors', '']], $GLOBALS['__test_delete_post_meta_calls']);
    }

    // Este test comprueba que se muestran los avisos de validación de errores.
    public function test_show_errors_outputs_validation_notice(): void
    {
        $GLOBALS['__test_post_meta'][90]['_neema_organismo_validation_errors'] = ['Error uno', 'Error dos'];
        $GLOBALS['post'] = (object) ['ID' => 90, 'post_type' => 'organismo'];

        ob_start();
        neema_organismo_show_errors();
        $output = ob_get_clean();

        $this->assertStringContainsString('Por favor, corrige los siguientes errores', $output);
        $this->assertStringContainsString('Error uno', $output);
        $this->assertStringContainsString('Error dos', $output);
    }
}
}
