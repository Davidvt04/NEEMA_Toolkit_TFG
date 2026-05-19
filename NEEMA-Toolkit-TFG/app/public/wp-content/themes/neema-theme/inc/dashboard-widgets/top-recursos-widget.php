<?php
/**
 * Widget de Top Recursos (Más Visitados / Más Descargados)
 * Lista ordenada de los 10 recursos con más visitas o descargas
 */

if (!defined('ABSPATH')) exit;

function neema_add_top_recursos_widget() {
    wp_add_dashboard_widget(
        'neema_top_recursos',
        'Top 10 Recursos',
        'neema_top_recursos_widget_content'
    );
}
add_action('wp_dashboard_setup', 'neema_add_top_recursos_widget');

function neema_top_recursos_widget_content() {
    $top_visitados = neema_get_top_recursos_by_visits(10);
    
    ?>
    <style>
        .neema-top-recursos-controls {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        .neema-recursos-scope-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }
        .neema-scope-label {
            font-size: 13px;
            color: #666;
            font-weight: 500;
        }
        .neema-scope-label.active {
            color: #3D3073;
            font-weight: 600;
        }
        .neema-scope-switch {
            position: relative;
            display: inline-block;
            width: 52px;
            height: 28px;
        }
        .neema-scope-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .neema-scope-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #B691BE;
            transition: 0.3s;
            border-radius: 28px;
        }
        .neema-scope-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }
        input:checked + .neema-scope-slider {
            background-color: #3D3073;
        }
        input:checked + .neema-scope-slider:before {
            transform: translateX(24px);
        }
        .neema-metric-btn {
            padding: 8px 20px;
            border: 1px solid #ddd;
            background: white;
            color: #666;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .neema-metric-btn:hover {
            background: #F6EBF8;
            border-color: #B691BE;
        }
        .neema-metric-btn.active {
            background: #3D3073;
            color: white;
            border-color: #3D3073;
        }
        .neema-top-recursos-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .neema-top-recurso-item {
            display: flex;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s ease;
        }
        .neema-top-recurso-item:hover {
            background: #F6EBF8;
        }
        .neema-top-recurso-item:last-child {
            border-bottom: none;
        }
        .neema-recurso-rank {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            background: #3D3073;
            color: white;
            border-radius: 50%;
            font-weight: bold;
            font-size: 13px;
            margin-right: 12px;
        }
        .neema-recurso-rank.gold {
            background: linear-gradient(135deg, #FFD700, #FFA500);
        }
        .neema-recurso-rank.silver {
            background: linear-gradient(135deg, #C0C0C0, #808080);
        }
        .neema-recurso-rank.bronze {
            background: linear-gradient(135deg, #CD7F32, #8B4513);
        }
        .neema-recurso-info {
            flex: 1;
            min-width: 0;
        }
        .neema-recurso-title {
            font-weight: 600;
            color: #23282d;
            margin: 0 0 4px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .neema-recurso-title a {
            text-decoration: none;
            color: inherit;
        }
        .neema-recurso-title a:hover {
            color: #3D3073;
        }
        .neema-recurso-meta {
            font-size: 12px;
            color: #666;
        }
        .neema-recurso-count {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin-left: 12px;
        }
        .neema-recurso-number {
            font-size: 20px;
            font-weight: bold;
            color: #3D3073;
            line-height: 1;
        }
        .neema-recurso-label {
            font-size: 11px;
            color: #666;
            margin-top: 2px;
        }
        .neema-top-recursos-empty {
            padding: 30px 20px;
            text-align: center;
            color: #666;
        }
        .neema-top-recursos-loading {
            padding: 20px;
            text-align: center;
            color: #666;
        }
    </style>
    
    <div class="neema-top-recursos-controls">
        <button class="neema-metric-btn active" data-metric="visits">
            Más Visitados
        </button>
        <button class="neema-metric-btn" data-metric="downloads">
            Más Descargados
        </button>
        <button class="neema-metric-btn" data-metric="favorites">
            Más Guardados
        </button>
    </div>
    
    <div class="neema-recursos-scope-controls">
        <span class="neema-scope-label active" id="neemaScopeAllLabel">Todos</span>
        <label class="neema-scope-switch">
            <input type="checkbox" id="neemaScopeToggle">
            <span class="neema-scope-slider"></span>
        </label>
        <span class="neema-scope-label" id="neemaScopeMineLabel">Míos</span>
    </div>
    
    <div id="neemaTopRecursosList">
        <?php neema_render_top_recursos_list($top_visitados, 'visits'); ?>
    </div>
    
    <script>
    (function($) {
        let currentMetric = 'visits';
        let currentScope = 'all';
        
        function loadTopRecursos() {
            $('#neemaTopRecursosList').html('<div class="neema-top-recursos-loading">Cargando...</div>');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'neema_get_top_recursos',
                    metric: currentMetric,
                    scope: currentScope,
                    nonce: '<?php echo wp_create_nonce('neema_top_recursos'); ?>'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        $('#neemaTopRecursosList').html(response.data);
                    }
                },
                error: function() {
                    $('#neemaTopRecursosList').html('<div class="neema-top-recursos-empty">Error al cargar los datos</div>');
                }
            });
        }
        
        $(document).ready(function() {
            $('.neema-metric-btn').on('click', function() {
                currentMetric = $(this).data('metric');
                $('.neema-metric-btn').removeClass('active');
                $(this).addClass('active');
                loadTopRecursos();
            });
            $('#neemaScopeToggle').on('change', function() {
                if ($(this).is(':checked')) {
                    currentScope = 'mine';
                    $('#neemaScopeAllLabel').removeClass('active');
                    $('#neemaScopeMineLabel').addClass('active');
                } else {
                    currentScope = 'all';
                    $('#neemaScopeAllLabel').addClass('active');
                    $('#neemaScopeMineLabel').removeClass('active');
                }
                loadTopRecursos();
            });
        });
    })(jQuery);
    </script>
    <?php
}

function neema_render_top_recursos_list($recursos, $metric = 'visits') {
    if (empty($recursos)) {
        ?>
        <div class="neema-top-recursos-empty">
            <p>No hay datos disponibles todavía.</p>
        </div>
        <?php
        return;
    }
    
    $labels = array(
        'downloads' => 'descargas',
        'favorites' => 'guardados',
        'visits' => 'visitas'
    );
    $label = isset($labels[$metric]) ? $labels[$metric] : 'visitas';
    
    ?>
    <ol class="neema-top-recursos-list">
        <?php foreach ($recursos as $index => $recurso): 
            $rank = $index + 1;
            $rank_class = '';
            if ($rank === 1) $rank_class = 'gold';
            elseif ($rank === 2) $rank_class = 'silver';
            elseif ($rank === 3) $rank_class = 'bronze';
            
            $count = $recurso['count'];
            $count_formatted = number_format_i18n($count);
        ?>
        <li class="neema-top-recurso-item">
            <div class="neema-recurso-rank <?php echo $rank_class; ?>">
                <?php echo $rank; ?>
            </div>
            <div class="neema-recurso-info">
                <h4 class="neema-recurso-title">
                    <a href="<?php echo get_edit_post_link($recurso['id']); ?>" target="_blank">
                        <?php echo esc_html($recurso['title']); ?>
                    </a>
                </h4>
            </div>
            <div class="neema-recurso-count">
                <span class="neema-recurso-number"><?php echo $count_formatted; ?></span>
                <span class="neema-recurso-label"><?php echo $label; ?></span>
            </div>
        </li>
        <?php endforeach; ?>
    </ol>
    <?php
}

function neema_get_top_recursos_by_visits($limit = 10, $author_id = null) {
    global $wpdb;
    
    if (!class_exists('WP_STATISTICS\DB')) {
        return array();
    }
    
    $pages_table = $wpdb->prefix . 'statistics_pages';
    
    $author_condition = '';
    if ($author_id) {
        $author_condition = $wpdb->prepare(' AND p.post_author = %d', $author_id);
    }
    $query = "
        SELECT 
            p.ID as post_id,
            p.post_title,
            SUM(sp.count) as total_visits
        FROM {$wpdb->posts} p
        INNER JOIN {$pages_table} sp ON (
            sp.uri LIKE CONCAT('%/', p.post_name, '/%') OR
            sp.uri LIKE CONCAT('%/', p.post_name, '?%') OR
            sp.uri LIKE CONCAT('%/', p.post_name)
        )
        WHERE p.post_type = 'recurso'
        AND p.post_status = 'publish'
        {$author_condition}
        GROUP BY p.ID
        ORDER BY total_visits DESC
        LIMIT %d
    ";
    
    $results = $wpdb->get_results($wpdb->prepare($query, $limit));
    
    $top_recursos = array();
    foreach ($results as $row) {
        $top_recursos[] = array(
            'id' => $row->post_id,
            'title' => $row->post_title,
            'count' => intval($row->total_visits)
        );
    }
    
    return $top_recursos;
}

function neema_get_top_recursos_by_favorites($limit = 10, $author_id = null) {
    global $wpdb;
    
    $favorites_table = $wpdb->prefix . 'recursos_favoritos_usuario';
    $author_condition = '';
    if ($author_id) {
        $author_condition = $wpdb->prepare(' AND p.post_author = %d', $author_id);
    }
    $query = "
        SELECT 
            p.ID as post_id,
            p.post_title,
            COUNT(rf.user_id) as total_favorites
        FROM {$wpdb->posts} p
        INNER JOIN {$favorites_table} rf ON p.ID = rf.recurso_id
        WHERE p.post_type = 'recurso'
        AND p.post_status = 'publish'
        {$author_condition}
        GROUP BY p.ID
        ORDER BY total_favorites DESC
        LIMIT %d
    ";
    
    $results = $wpdb->get_results($wpdb->prepare($query, $limit));
    
    $top_recursos = array();
    foreach ($results as $row) {
        $top_recursos[] = array(
            'id' => $row->post_id,
            'title' => $row->post_title,
            'count' => intval($row->total_favorites)
        );
    }
    
    return $top_recursos;
}
function neema_get_top_recursos_by_downloads($limit = 10, $author_id = null) {
    global $wpdb;
    $author_condition = '';
    if ($author_id) {
        $author_condition = $wpdb->prepare(' AND p.post_author = %d', $author_id);
    }
    $query = "
        SELECT 
            p.ID as post_id,
            p.post_title,
            CAST(pm.meta_value AS UNSIGNED) as total_downloads
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE p.post_type = 'recurso'
        AND p.post_status = 'publish'
        AND pm.meta_key = 'numDescargas'
        AND pm.meta_value != ''
        AND pm.meta_value > 0
        {$author_condition}
        ORDER BY total_downloads DESC
        LIMIT %d
    ";
    
    $results = $wpdb->get_results($wpdb->prepare($query, $limit));
    
    $top_recursos = array();
    foreach ($results as $row) {
        $top_recursos[] = array(
            'id' => $row->post_id,
            'title' => $row->post_title,
            'count' => intval($row->total_downloads)
        );
    }
    if (empty($top_recursos)) {
        return array();
    }
    
    return $top_recursos;
}

function neema_ajax_get_top_recursos() {
    check_ajax_referer('neema_top_recursos', 'nonce');
    
    $metric = isset($_POST['metric']) ? sanitize_text_field($_POST['metric']) : 'visits';
    $scope = isset($_POST['scope']) ? sanitize_text_field($_POST['scope']) : 'all';
    $limit = 10;
    $author_id = null;
    if ($scope === 'mine') {
        $author_id = get_current_user_id();
    }
    
    if ($metric === 'downloads') {
        $recursos = neema_get_top_recursos_by_downloads($limit, $author_id);
    } elseif ($metric === 'favorites') {
        $recursos = neema_get_top_recursos_by_favorites($limit, $author_id);
    } else {
        $recursos = neema_get_top_recursos_by_visits($limit, $author_id);
    }
    
    ob_start();
    neema_render_top_recursos_list($recursos, $metric);
    $html = ob_get_clean();
    
    wp_send_json_success($html);
}
add_action('wp_ajax_neema_get_top_recursos', 'neema_ajax_get_top_recursos');

function neema_ajax_track_download() {
    check_ajax_referer('neema_track_download', 'nonce');
    
    $recurso_id = isset($_POST['recurso_id']) ? intval($_POST['recurso_id']) : 0;
    
    if (!$recurso_id || get_post_type($recurso_id) !== 'recurso') {
        wp_send_json_error('Recurso no válido');
    }
    $current_count = get_post_meta($recurso_id, 'numDescargas', true);
    $current_count = $current_count ? intval($current_count) : 0;
    $new_count = $current_count + 1;
    
    update_post_meta($recurso_id, 'numDescargas', $new_count);
    
    wp_send_json_success(array(
        'count' => $new_count,
        'message' => 'Descarga registrada'
    ));
}
add_action('wp_ajax_neema_track_download', 'neema_ajax_track_download');
add_action('wp_ajax_nopriv_neema_track_download', 'neema_ajax_track_download');
