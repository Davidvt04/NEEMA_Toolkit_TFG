<?php

declare(strict_types=1);

namespace NeemaTheme\Tests\Unit {

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/cpt-design.php';
require_once __DIR__ . '/../../inc/cpt-categorias-organismos.php';
require_once __DIR__ . '/../../inc/cpt-guia-introductoria.php';

final class MiscCptCoverageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['__test_register_post_type_calls'] = [];
        $GLOBALS['__test_add_meta_box_calls'] = [];
        $GLOBALS['__test_update_post_meta_calls'] = [];
        $GLOBALS['__test_delete_post_meta_calls'] = [];
        $GLOBALS['__test_get_posts_return'] = [];
        $GLOBALS['__test_post_meta'] = [];
        $GLOBALS['__test_wp_query_callback'] = null;
        $GLOBALS['__test_query_vars'] = [];
        $GLOBALS['__test_pll_current_language'] = 'es';
        $GLOBALS['__test_current_user_can'] = ['edit_post' => true];
        $_GET = [];
        $_POST = [];
        $GLOBALS['pagenow'] = 'edit.php';
    }

    // Este test comprueba que se muestran los diseños y organismos en el listado público.
    public function test_design_and_organismo_lists_render_public_content(): void
    {
        $GLOBALS['__test_wp_query_callback'] = static function ($args) {
            if (($args['post_type'] ?? '') === 'design') {
                return [(object) ['ID' => 301, 'post_title' => 'Diseño A']];
            }

            if (($args['post_type'] ?? '') === 'categoria-organismo') {
                return [(object) ['ID' => 401, 'post_title' => 'Organismo A']];
            }

            return [];
        };

        ob_start();
        neema_display_designs();
        $designOutput = ob_get_clean();

        ob_start();
        neema_display_categorias_organismos();
        $organismoOutput = ob_get_clean();

        $this->assertStringContainsString('Diseño A', $designOutput);
        $this->assertStringContainsString('Organismo A', $organismoOutput);
    }

    // Este test comprueba que los helpers de guía y documento cubren avisos y visibilidad.
    public function test_guia_and_documento_helpers_cover_admin_notices_and_visibility(): void
    {
        neema_register_guia_introductoria();
        neema_add_guia_meta_boxes();

        $this->assertSame('guia-intro', $GLOBALS['__test_register_post_type_calls'][0][0]);
        $this->assertCount(1, $GLOBALS['__test_add_meta_box_calls']);

        $this->assertSame(['cb' => 'cb', 'title' => 'Título Interno', 'date' => 'date'], neema_guia_columns(['cb' => 'cb', 'title' => 'title', 'date' => 'date']));

        $_GET['message'] = 'guia_existe';
        ob_start();
        neema_guia_existe_notice();
        $noticeOutput = ob_get_clean();
        $this->assertStringContainsString('Solo puede existir una guía', $noticeOutput);

        $GLOBALS['__test_get_posts_return'] = [];
        neema_limitar_una_guia();
        neema_ocultar_boton_nueva_guia();

        $GLOBALS['__test_get_posts_return'] = [(object) ['ID' => 999]];
        $GLOBALS['pagenow'] = 'edit.php';
        $_GET['post_type'] = 'guia-intro';
        ob_start();
        neema_ocultar_boton_nueva_guia();
        $guiaStyle = ob_get_clean();

        $this->assertStringContainsString('display: none', $guiaStyle);
    }

    // Este test comprueba que los callbacks de categoría de organismo muestran y guardan la descripción.
    public function test_categoria_organismo_callbacks_render_and_save_description(): void
    {
        $GLOBALS['__test_post_meta'][88] = [
            '_categoria_organismo_description' => 'Descripción guardada',
        ];
        $GLOBALS['__test_get_posts_return'] = [
            (object) ['ID' => 501, 'post_title' => 'Categoría Uno'],
        ];

        ob_start();
        neema_categoria_organismo_description_meta_box_callback((object) ['ID' => 88]);
        $callbackOutput = ob_get_clean();

        $this->assertStringContainsString('Descripción guardada', $callbackOutput);

        $_POST['categoria_organismo_description'] = '<p>Nueva descripción</p>';
        neema_save_categoria_organismo_meta(88);

        $this->assertSame([[88, '_categoria_organismo_description', '<p>Nueva descripción</p>']], $GLOBALS['__test_update_post_meta_calls']);

        $GLOBALS['__test_post_meta'][99]['_neema_organismo_validation_errors'] = ['Error'];
        $GLOBALS['__test_post_meta'][99]['_categoria_organismo_description'] = 'Pendiente';
        $GLOBALS['__test_post_meta'][99]['_organismo_categoria'] = 501;
        $GLOBALS['__test_post_meta'][99]['_organismo_ambito'] = 'Local';
        $GLOBALS['__test_post_meta'][99]['_organismo_paises'] = [1];
        $GLOBALS['__test_post_meta'][99]['_organismo_ciudad'] = 'Dakar';
        $GLOBALS['__test_wp_query_callback'] = static function () {
            return [(object) ['ID' => 501, 'post_title' => 'Categoría Uno']];
        };

        ob_start();
        neema_display_categorias_organismos();
        $displayOutput = ob_get_clean();

        $this->assertStringContainsString('servicios-container', $displayOutput);
        $this->assertStringContainsString('Categoría Uno', $displayOutput);
    }
}
}
