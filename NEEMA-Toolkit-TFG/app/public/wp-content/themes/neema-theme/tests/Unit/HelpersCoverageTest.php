<?php

declare(strict_types=1);

namespace NeemaTheme\Tests\Unit {

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/buscador-helpers.php';
require_once __DIR__ . '/../../inc/ajax-recurso-helpers.php';
require_once __DIR__ . '/../../inc/language-helpers.php';

final class HelpersCoverageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['__test_post_meta'] = [];
        $GLOBALS['__test_get_posts_return'] = [];
        $GLOBALS['__test_pll_current_language'] = 'fr';
        $GLOBALS['__test_pll_get_post'] = ['fr' => []];
        $GLOBALS['__test_the_title'] = [];
        $GLOBALS['__test_is_singular'] = false;
        $_GET = [];
    }

    // Este test comprueba que los helpers devuelven ítems traducidos y claves alternativas.
    public function test_get_all_helpers_return_translated_items_and_fallback_keys(): void
    {
        $posts = [
            (object) ['ID' => 1, 'post_title' => 'Pais Uno'],
            (object) ['ID' => 2, 'post_title' => 'Tipo Dos'],
            (object) ['ID' => 3, 'post_title' => 'Tematica Tres'],
        ];
        $GLOBALS['__test_get_posts_return'] = $posts;
        $GLOBALS['__test_post_meta'][1]['_tipo_recurso_key'] = 'tipo-uno';
        $GLOBALS['__test_post_meta'][1]['_tematica_key'] = 'tematica-uno';
        $GLOBALS['__test_post_meta'][1]['_region_key'] = 'region-uno';
        $GLOBALS['__test_post_meta'][1]['_pais_key'] = '';
        $GLOBALS['__test_post_meta'][2]['_tipo_recurso_key'] = 'tipo-dos';
        $GLOBALS['__test_post_meta'][2]['_tematica_key'] = 'tematica-dos';
        $GLOBALS['__test_post_meta'][2]['_region_key'] = 'region-dos';
        $GLOBALS['__test_post_meta'][2]['_pais_key'] = 'pais_2';
        $GLOBALS['__test_post_meta'][3]['_tipo_recurso_key'] = 'tipo-tres';
        $GLOBALS['__test_post_meta'][3]['_tematica_key'] = 'tematica-tres';
        $GLOBALS['__test_post_meta'][3]['_region_key'] = 'region-tres';
        $GLOBALS['__test_post_meta'][3]['_pais_key'] = 'pais_3';
        $GLOBALS['__test_the_title'][1001] = 'Pays Un';
        $GLOBALS['__test_the_title'][1002] = 'Thématique Dos';
        $GLOBALS['__test_the_title'][1003] = 'Thématique Trois';
        $GLOBALS['__test_pll_get_post']['fr'][1] = 1001;
        $GLOBALS['__test_pll_get_post']['fr'][2] = 1002;
        $GLOBALS['__test_pll_get_post']['fr'][3] = 1003;

        $paises = neema_get_all_paises();
        $tipos = neema_get_all_tipos_recurso();
        $tematicas = neema_get_all_tematicas();
        $regiones = neema_get_all_regiones();

        $this->assertSame('pais_1', $paises[0]['key']);
        $this->assertSame('Pays Un', $tipos[0]['title']);
        $this->assertSame('Thématique Dos', $tematicas[1]['title']);
        $this->assertSame('region-tres', $regiones[2]['key']);
    }

    // Este test comprueba que los filtros de texto y meta funcionan con coincidencias y vacíos.
    public function test_text_and_meta_filters_cover_match_and_empty_paths(): void
    {
        $recurso1 = (object) ['ID' => 11];
        $recurso2 = (object) ['ID' => 12];
        $GLOBALS['__test_post_meta'][11] = [
            '_recurso_titulo_es' => 'Toolkit NEEMA',
            '_recurso_titulo_en' => 'NEEMA Toolkit',
            '_recurso_titulo_fr' => 'Boite NEEMA',
            'descripcion_es' => 'Texto de apoyo',
            'descripcion_en' => 'Support text',
            'descripcion_fr' => 'Texte de soutien',
            '_recurso_regiones' => ['region_1', 'region_2'],
            '_recurso_tematicas' => ['tema_1'],
            '_recurso_paises' => [1, 2],
        ];
        $GLOBALS['__test_post_meta'][12] = [
            '_recurso_titulo_es' => 'Otro recurso',
            '_recurso_titulo_en' => 'Other resource',
            '_recurso_titulo_fr' => 'Autre ressource',
            'descripcion_es' => 'Sin coincidencia',
            'descripcion_en' => 'No match',
            'descripcion_fr' => 'Aucune correspondance',
            '_recurso_regiones' => 'invalid',
            '_recurso_tematicas' => 'invalid',
            '_recurso_paises' => 'invalid',
        ];

        $this->assertNull(neema_build_serialized_filter('_recurso_tematicas', []));
        $serializedFilter = neema_build_serialized_filter('_recurso_tematicas', ['tema_1']);
        $this->assertSame('"tema_1"', array_values($serializedFilter)[1]['value']);
        $this->assertNull(neema_build_paises_filter([]));
        $paisesFilter = neema_build_paises_filter(['pais_2']);
        $this->assertSame('i:2;', array_values($paisesFilter)[1]['value']);

        $filteredByText = neema_filter_by_text([$recurso1, $recurso2], 'toolkit');
        $filteredByRegions = neema_filter_by_regiones([$recurso1, $recurso2], ['region_2']);
        $filteredByTematicas = neema_filter_by_tematicas([$recurso1, $recurso2], ['tema_1']);
        $filteredByPaises = neema_filter_by_paises([$recurso1, $recurso2], ['pais_1']);

        $this->assertCount(1, $filteredByText);
        $this->assertSame(11, array_values($filteredByText)[0]->ID);
        $this->assertCount(1, $filteredByRegions);
        $this->assertCount(1, $filteredByTematicas);
        $this->assertCount(1, $filteredByPaises);
        $this->assertSame([$recurso1, $recurso2], neema_filter_by_text([$recurso1, $recurso2], ''));
    }

    // Este test comprueba que se prefiere el idioma de la query para recursos si está presente.
    public function test_current_lang_prefers_explicit_query_language_for_recurso(): void
    {
        $GLOBALS['__test_is_singular'] = true;
        $GLOBALS['__test_pll_current_language'] = 'es';
        $_GET['lang'] = 'en';
        $GLOBALS['__test_pll_translate_string']['en']['Hola'] = 'Hello';

        $this->assertSame('en', neema_get_current_lang());
        $this->assertSame('Hello', neema_translate('Hola'));
    }
}
}
