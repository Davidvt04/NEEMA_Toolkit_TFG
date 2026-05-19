<?php

declare(strict_types=1);

namespace NeemaTheme\Tests\Unit;

use NeemaTheme\Tests\TestCase;

require_once __DIR__ . '/../../inc/recursos/revision-recursos.php';

final class RevisionRecursosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['__test_current_user'] = (object) ['ID' => 10, 'roles' => ['administrator']];
        $GLOBALS['__test_current_user_id'] = 10;
        $GLOBALS['__test_is_admin'] = true;
        $GLOBALS['__test_post_meta'] = [];
        $GLOBALS['__test_user_data'] = [];
        $GLOBALS['__test_get_post_return'] = [];
        $GLOBALS['__test_wp_query_posts'] = [];
        $GLOBALS['__test_transients'] = [];
        $GLOBALS['__test_delete_transient_calls'] = [];
        $GLOBALS['__test_set_transient_calls'] = [];
        $GLOBALS['__test_insert_post_calls'] = [];
        $GLOBALS['__test_update_post_calls'] = [];
        $GLOBALS['__test_delete_post_calls'] = [];
        $GLOBALS['__test_set_thumbnail_calls'] = [];
        $GLOBALS['__test_delete_thumbnail_calls'] = [];
        $GLOBALS['__test_set_object_terms_calls'] = [];
        $GLOBALS['__test_update_post_meta_calls'] = [];
        $GLOBALS['__test_delete_post_meta_calls'] = [];
        $GLOBALS['__test_add_post_meta_calls'] = [];
        $GLOBALS['__test_redirect_calls'] = [];
        $GLOBALS['__test_wp_mail_calls'] = [];
        $GLOBALS['__test_query_vars'] = [];
    }

    // Este test comprueba que los gestores de contenido cambian el estado y versión al forzar pendiente.
    public function test_force_pending_for_gestor_contenido_changes_status_and_version(): void
    {
        $GLOBALS['__test_current_user'] = (object) ['ID' => 10, 'roles' => ['gestor_contenido']];
        $GLOBALS['__test_post_meta'][99] = [
            '_recurso_id_original' => '',
            '_recurso_version' => '2',
        ];

        $data = neema_force_pending_for_gestor_contenido(['post_type' => 'recurso', 'post_status' => 'publish'], ['ID' => 99]);

        $this->assertSame('pending', $data['post_status']);
        $this->assertSame([[99, '_recurso_version', 3]], $GLOBALS['__test_update_post_meta_calls']);
    }

    // Este test comprueba que los borradores automáticos se mantienen como draft.
    public function test_force_pending_keeps_auto_draft_as_draft(): void
    {
        $data = neema_force_pending_for_gestor_contenido(['post_type' => 'recurso', 'post_status' => 'auto-draft'], ['ID' => 1]);

        $this->assertSame('auto-draft', $data['post_status']);
    }

    // Este test comprueba que solo los recursos y acciones válidas cambian el estado.
    public function test_force_pending_ignores_non_recurso_and_approval_actions(): void
    {
        $_POST['neema_review_action'] = 'approve';
        $data = neema_force_pending_for_gestor_contenido(['post_type' => 'post', 'post_status' => 'publish'], ['ID' => 1]);
        $this->assertSame('publish', $data['post_status']);
        unset($_POST['neema_review_action']);
    }

    // Este test comprueba que se obtiene la primera versión borrador disponible.
    public function test_get_draft_version_returns_first_matching_post(): void
    {
        $GLOBALS['__test_wp_query_posts'] = [(object) ['ID' => 77]];

        $this->assertSame(77, neema_get_draft_version(55)->ID);
    }

    // Este test comprueba que al crear una versión se copian metadatos y términos.
    public function test_create_resource_version_copies_metadata_and_terms(): void
    {
        $GLOBALS['__test_get_post_return'] = [
            55 => (object) [
                'ID' => 55,
                'post_title' => 'Original',
                'post_content' => 'Content',
                'post_excerpt' => 'Excerpt',
                'post_name' => 'original',
            ],
        ];
        $GLOBALS['__test_post_meta'][55]['_recurso_version'] = '4';
        $GLOBALS['__test_thumbnail_ids'][55] = 88;
        $GLOBALS['__test_object_taxonomies']['recurso'] = ['tax1'];
        $GLOBALS['__test_object_terms'][55]['tax1'] = [1, 2];
        $GLOBALS['__test_get_post_return'][55]->post_type = 'recurso';
        $GLOBALS['__test_wp_query_posts'] = [];
        $GLOBALS['wpdb'] = new class {
            public string $postmeta = 'wp_postmeta';
            public function prepare($query, ...$args) { return $query; }
            public function get_results($query) {
                return [
                    (object) ['meta_key' => 'visible_meta', 'meta_value' => 'abc'],
                    (object) ['meta_key' => '_recurso_version', 'meta_value' => '4'],
                    (object) ['meta_key' => '_wp_internal', 'meta_value' => 'skip'],
                ];
            }
        };
        $GLOBALS['__test_insert_post_return'] = 1234;

        $clone_id = neema_create_resource_version(55, 10);

        $this->assertSame(1234, $clone_id);
        $this->assertSame([[1234, '_recurso_id_original', 55], [1234, '_recurso_version', 5]], $GLOBALS['__test_update_post_meta_calls']);
        $this->assertSame([[1234, 88]], $GLOBALS['__test_set_thumbnail_calls']);
        $this->assertSame([[1234, [1, 2], 'tax1', false]], $GLOBALS['__test_set_object_terms_calls']);
        $this->assertSame([[1234, 'visible_meta', 'abc', false]], $GLOBALS['__test_add_post_meta_calls']);
    }

    // Este test comprueba que solo se actualizan los metadatos que no son de versión.
    public function test_copy_all_metadata_updates_non_version_meta_only(): void
    {
        $GLOBALS['wpdb'] = new class {
            public string $postmeta = 'wp_postmeta';
            public function prepare($query, ...$args) { return $query; }
            public function get_results($query) {
                return [
                    (object) ['meta_key' => 'visible_meta', 'meta_value' => 'abc'],
                    (object) ['meta_key' => '_wp_internal', 'meta_value' => 'skip'],
                    (object) ['meta_key' => '_recurso_version', 'meta_value' => '3'],
                ];
            }
        };

        neema_copy_all_metadata(10, 20);

        $this->assertSame([[20, 'visible_meta']], array_map(static fn ($call) => [$call[0], $call[1]], $GLOBALS['__test_delete_post_meta_calls']));
        $this->assertSame([[20, 'visible_meta', 'abc']], $GLOBALS['__test_update_post_meta_calls']);
    }

    // Este test comprueba que aprobar una revisión actualiza y redirige correctamente.
    public function test_process_review_approve_branch_updates_and_redirects(): void
    {
        $GLOBALS['__test_nonce_verification_result'] = true;
        $GLOBALS['__test_get_post_return'] = [
            5 => (object) ['ID' => 5, 'post_type' => 'recurso', 'post_status' => 'pending', 'post_author' => 10],
        ];
        $GLOBALS['__test_post_meta'][5] = [
            '_recurso_id_original' => '',
        ];
        $_POST['neema_recursos_review_nonce'] = 'nonce';
        $_POST['neema_review_action'] = 'approve';

        neema_recursos_process_review(5, $GLOBALS['__test_get_post_return'][5], true);

        $this->assertSame([[5, '_recurso_motivo_rechazo', '']], array_map(static fn ($call) => [$call[0], $call[1], $call[2]], $GLOBALS['__test_delete_post_meta_calls']));
        $this->assertSame([[['ID' => 5, 'post_status' => 'publish'], true, false]], array_map(static fn ($call) => [$call[0], $call[1], $call[2]], $GLOBALS['__test_update_post_calls']));
        $this->assertSame(['neema_redirect_after_approval_10', true, 60], $GLOBALS['__test_set_transient_calls'][0]);
    }

    // Este test comprueba que rechazar una revisión guarda el motivo.
    public function test_process_review_reject_branch_saves_reason(): void
    {
        $GLOBALS['__test_nonce_verification_result'] = true;
        $GLOBALS['__test_current_user'] = (object) ['ID' => 10, 'roles' => ['neema_admin']];
        $GLOBALS['__test_get_post_return'] = [
            8 => (object) ['ID' => 8, 'post_type' => 'recurso', 'post_status' => 'pending', 'post_author' => 10],
        ];
        $_POST['neema_recursos_review_nonce'] = 'nonce';
        $_POST['neema_review_action'] = 'reject';
        $_POST['recurso_motivo_rechazo'] = 'Falta contenido';

        neema_recursos_process_review(8, $GLOBALS['__test_get_post_return'][8], true);

        $this->assertSame([[8, '_recurso_motivo_rechazo', 'Falta contenido']], $GLOBALS['__test_update_post_meta_calls']);
    }

    // Este test comprueba que los usuarios privilegiados ven los meta boxes de revisión y versión.
    public function test_review_meta_box_adds_review_and_version_boxes_for_privileged_users(): void
    {
        neema_recursos_add_review_meta_box();

        $this->assertCount(2, $GLOBALS['__test_add_meta_box_calls']);
        $this->assertSame('neema_recursos_review', $GLOBALS['__test_add_meta_box_calls'][0][0]);
        $this->assertSame('neema_recursos_version', $GLOBALS['__test_add_meta_box_calls'][1][0]);
    }

    // Este test comprueba que los callbacks de versión y revisión muestran las secciones esperadas.
    public function test_version_and_review_callbacks_render_expected_sections(): void
    {
        $post = (object) ['ID' => 42];
        $GLOBALS['__test_post_meta'][42] = [
            '_recurso_version' => '7',
            '_recurso_id_original' => 11,
            '_recurso_motivo_rechazo' => 'Pendiente de ajuste',
        ];

        ob_start();
        neema_recursos_version_callback($post);
        $versionOutput = ob_get_clean();

        $GLOBALS['__test_get_post_return'] = [
            42 => (object) ['ID' => 42, 'post_type' => 'recurso', 'post_status' => 'draft', 'post_author' => 10],
        ];
        $GLOBALS['__test_current_user'] = (object) ['ID' => 10, 'roles' => ['gestor_contenido']];
        $GLOBALS['__test_current_user_id'] = 10;
        $GLOBALS['__test_is_admin'] = true;

        ob_start();
        neema_recursos_review_callback($GLOBALS['__test_get_post_return'][42]);
        $reviewOutput = ob_get_clean();

        $this->assertStringContainsString('Versión:', $versionOutput);
        $this->assertStringContainsString('Borrador', $reviewOutput);
        $this->assertStringContainsString('Este recurso fue rechazado', $reviewOutput);
    }

    // Este test comprueba que tras aprobar se redirige al dashboard si existe la bandera.
    public function test_redirect_after_approval_returns_dashboard_url_when_flag_exists(): void
    {
        $GLOBALS['__test_transients']['neema_redirect_after_approval_10'] = true;

        $this->assertSame('http://example.test/wp-admin/edit.php?post_type=recurso', neema_redirect_after_approval('keep-me'));
    }

    // Este test comprueba que se añaden las columnas de estado y son ordenables.
    public function test_status_columns_and_sortable_columns_are_added(): void
    {
        $columns = neema_recursos_add_status_column(['title' => 'Título']);
        $this->assertSame(['title' => 'Título', 'version' => 'Versión', 'review_status' => 'Estado', 'author' => 'Autor'], $columns);

        $sortable = neema_recursos_sortable_columns([]);
        $this->assertSame('version', $sortable['version']);
        $this->assertSame('review_status', $sortable['review_status']);
        $this->assertSame('author', $sortable['author']);
    }

    // Este test comprueba que la columna de estado muestra el contenido esperado.
    public function test_status_column_content_outputs_expected_markup(): void
    {
        $GLOBALS['__test_get_post_return'] = [
            21 => (object) ['ID' => 21, 'post_type' => 'recurso', 'post_status' => 'publish', 'post_author' => 5, 'post_title' => 'Original'],
        ];
        $GLOBALS['__test_user_data'][5] = (object) ['ID' => 5, 'display_name' => 'Autor Uno', 'roles' => ['administrator'], 'user_email' => 'a@example.test'];
        $GLOBALS['__test_post_meta'][21] = [
            '_recurso_version' => '2',
            '_recurso_id_original' => '',
            '_recurso_motivo_rechazo' => '',
        ];

        ob_start();
        neema_recursos_status_column_content('version', 21);
        neema_recursos_status_column_content('review_status', 21);
        neema_recursos_status_column_content('author', 21);
        $output = ob_get_clean();

        $this->assertStringContainsString('v2', $output);
        $this->assertStringContainsString('Publicado', $output);
        $this->assertStringContainsString('Autor Uno', $output);
    }
}
