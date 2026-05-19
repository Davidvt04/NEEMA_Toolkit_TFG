<?php

declare(strict_types=1);

namespace NeemaTheme\Tests\Unit {

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/enqueue-scripts.php';

final class EnqueueScriptsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['__test_wp_enqueue_style_calls'] = [];
        $GLOBALS['__test_wp_enqueue_script_calls'] = [];
        $GLOBALS['__test_wp_localize_script_calls'] = [];
        $GLOBALS['__test_wp_add_inline_script_calls'] = [];
        $GLOBALS['__test_page_templates'] = [];
        $GLOBALS['__test_is_singular'] = false;
    }

    // Este test comprueba que se encolan los estilos según la plantilla y si es singular.
    public function test_neema_enqueue_styles_covers_template_and_singular_branches(): void
    {
        $GLOBALS['__test_page_templates'] = [
            'page-ran.php',
            'page-perfil.php',
            'page-guia-introductoria.php',
            'page-servicios-apoyo.php',
        ];
        $GLOBALS['__test_is_singular'] = true;

        neema_enqueue_styles();

        $handles = array_column($GLOBALS['__test_wp_enqueue_style_calls'], 0);

        $this->assertContains('neema-style', $handles);
        $this->assertContains('neema-page-ran', $handles);
        $this->assertContains('neema-page-perfil', $handles);
        $this->assertContains('neema-page-guia-introductoria', $handles);
        $this->assertContains('neema-page-servicios-apoyo', $handles);
        $this->assertContains('neema-organismo-preview', $handles);
        $this->assertContains('neema-single-categoria-organismo', $handles);
    }

    // Este test comprueba que se encolan los scripts y se localizan los datos correctamente.
    public function test_neema_enqueue_helpers_enqueue_scripts_and_localize_data(): void
    {
        $GLOBALS['__test_page_templates'] = [
            'page-login.php',
            'page-ran.php',
            'page-servicios-apoyo.php',
        ];
        $GLOBALS['__test_is_singular'] = true;

        neema_enqueue_font_awesome();
        neema_enqueue_js();
        neema_enqueue_cargar_recursos();
        neema_enqueue_cargar_organismos();
        neema_enqueue_buscador_recursos();
        neema_enqueue_buscador_organismos();
        neema_login_scripts();

        $scriptHandles = array_column($GLOBALS['__test_wp_enqueue_script_calls'], 0);
        $styleHandles = array_column($GLOBALS['__test_wp_enqueue_style_calls'], 0);
        $localizeHandles = array_column($GLOBALS['__test_wp_localize_script_calls'], 0);

        $this->assertContains('font-awesome', $styleHandles);
        $this->assertContains('neema-main-js', $scriptHandles);
        $this->assertContains('neema-cargar-recursos', $scriptHandles);
        $this->assertContains('neema-cargar-organismos', $scriptHandles);
        $this->assertContains('neema-buscador-recursos', $scriptHandles);
        $this->assertContains('neema-buscador-organismos', $scriptHandles);
        $this->assertContains('neema-login-js', $scriptHandles);
        $this->assertContains('neema-cargar-recursos', $localizeHandles);
        $this->assertContains('neema-cargar-organismos', $localizeHandles);
        $this->assertContains('neema-buscador-recursos', $localizeHandles);
        $this->assertContains('neema-buscador-organismos', $localizeHandles);

        $buscadorRecursosArgs = $GLOBALS['__test_wp_localize_script_calls'][2][2];
        $this->assertSame('No se han encontrado resultados', $buscadorRecursosArgs['noResultsMessage']);
        $this->assertSame('Cargando...', $buscadorRecursosArgs['loadingMessage']);
    }
}
}
