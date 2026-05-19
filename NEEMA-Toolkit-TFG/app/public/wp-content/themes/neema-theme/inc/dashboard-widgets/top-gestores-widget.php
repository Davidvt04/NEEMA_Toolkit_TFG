<?php
/**
 * Widget de Top Gestores de Contenido
 * Lista ordenada de los 10 gestores con mejores métricas acumuladas de sus recursos
 */

if (!defined('ABSPATH')) exit;

function neema_add_top_gestores_widget() {
    wp_add_dashboard_widget(
        'neema_top_gestores',
        'Top 10 Gestores de Contenido',
        'neema_top_gestores_widget_content'
    );
}
add_action('wp_dashboard_setup', 'neema_add_top_gestores_widget');

function neema_top_gestores_widget_content() {
    $top_gestores = neema_get_top_gestores_by_visits(10);
    
    ?>
    <style>
        .neema-top-gestores-controls {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }
        .neema-gestor-metric-btn {
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
        .neema-gestor-metric-btn:hover {
            background: #F6EBF8;
            border-color: #B691BE;
        }
        .neema-gestor-metric-btn.active {
            background: #3D3073;
            color: white;
            border-color: #3D3073;
        }
        .neema-top-gestores-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .neema-top-gestor-item {
            display: flex;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s ease;
        }
        .neema-top-gestor-item:hover {
            background: #F6EBF8;
        }
        .neema-top-gestor-item:last-child {
            border-bottom: none;
        }
        .neema-gestor-rank {
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
        .neema-gestor-rank.gold {
            background: linear-gradient(135deg, #FFD700, #FFA500);
        }
        .neema-gestor-rank.silver {
            background: linear-gradient(135deg, #C0C0C0, #808080);
        }
        .neema-gestor-rank.bronze {
            background: linear-gradient(135deg, #CD7F32, #8B4513);
        }
        .neema-gestor-info {
            flex: 1;
            min-width: 0;
        }
        .neema-gestor-name {
            font-weight: 600;
            color: #23282d;
            margin: 0 0 4px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .neema-gestor-meta {
            font-size: 12px;
            color: #666;
        }
        .neema-gestor-count {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin-left: 12px;
        }
        .neema-gestor-number {
            font-size: 20px;
            font-weight: bold;
            color: #3D3073;
            line-height: 1;
        }
        .neema-gestor-label {
            font-size: 11px;
            color: #666;
            margin-top: 2px;
        }
        .neema-top-gestores-empty {
            padding: 30px 20px;
            text-align: center;
            color: #666;
        }
        .neema-top-gestores-loading {
            padding: 20px;
            text-align: center;
            color: #666;
        }
    </style>
    
    <div class="neema-top-gestores-controls">
        <button class="neema-gestor-metric-btn active" data-metric="visits">
            Más Visitados
        </button>
        <button class="neema-gestor-metric-btn" data-metric="downloads">
            Más Descargados
        </button>
        <button class="neema-gestor-metric-btn" data-metric="favorites">
            Más Guardados
        </button>
    </div>
    
    <div id="neemaTopGestoresList">
        <?php neema_render_top_gestores_list($top_gestores, 'visits'); ?>
    </div>
    
    <script>
    (function($) {
        $(document).ready(function() {
            $('.neema-gestor-metric-btn').on('click', function() {
                const metric = $(this).data('metric');
                
                $('.neema-gestor-metric-btn').removeClass('active');
                $(this).addClass('active');
                
                $('#neemaTopGestoresList').html('<div class="neema-top-gestores-loading">Cargando...</div>');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'neema_get_top_gestores',
                        metric: metric,
                        nonce: '<?php echo wp_create_nonce('neema_top_gestores'); ?>'
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            $('#neemaTopGestoresList').html(response.data);
                        }
                    },
                    error: function() {
                        $('#neemaTopGestoresList').html('<div class="neema-top-gestores-empty">Error al cargar los datos</div>');
                    }
                });
            });
        });
    })(jQuery);
    </script>
    <?php
}

function neema_render_top_gestores_list($gestores, $metric = 'visits') {
    if (empty($gestores)) {
        ?>
        <div class="neema-top-gestores-empty">
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
    <ol class="neema-top-gestores-list">
        <?php foreach ($gestores as $index => $gestor): 
            $rank = $index + 1;
            $rank_class = '';
            if ($rank === 1) $rank_class = 'gold';
            elseif ($rank === 2) $rank_class = 'silver';
            elseif ($rank === 3) $rank_class = 'bronze';
            
            $count = $gestor['count'];
            $count_formatted = number_format_i18n($count);
            $recursos_count = $gestor['recursos_count'];
        ?>
        <li class="neema-top-gestor-item">
            <div class="neema-gestor-rank <?php echo $rank_class; ?>">
                <?php echo $rank; ?>
            </div>
            <div class="neema-gestor-info">
                <div class="neema-gestor-name">
                    <?php echo esc_html($gestor['name']); ?>
                </div>
                <div class="neema-gestor-meta">
                    <?php echo $recursos_count; ?> recurso<?php echo $recursos_count != 1 ? 's' : ''; ?> publicado<?php echo $recursos_count != 1 ? 's' : ''; ?>
                </div>
            </div>
            <div class="neema-gestor-count">
                <span class="neema-gestor-number"><?php echo $count_formatted; ?></span>
                <span class="neema-gestor-label"><?php echo $label; ?></span>
            </div>
        </li>
        <?php endforeach; ?>
    </ol>
    <?php
}

function neema_get_top_gestores_by_visits($limit = 10) {
    global $wpdb;
    if (!class_exists('WP_STATISTICS\DB')) {
        return array();
    }
    
    $pages_table = $wpdb->prefix . 'statistics_pages';
    $gestores = get_users(array(
        'role__in' => array('gestor_contenido', 'administrator', 'neema_admin'),
        'fields' => array('ID', 'display_name')
    ));
    
    if (empty($gestores)) {
        return array();
    }
    
    $gestores_data = array();
    
    foreach ($gestores as $gestor) {
        $recursos = get_posts(array(
            'post_type' => 'recurso',
            'post_status' => 'publish',
            'author' => $gestor->ID,
            'posts_per_page' => -1,
            'fields' => 'ids'
        ));
        
        if (empty($recursos)) {
            continue;
        }
        
        $total_visits = 0;
        foreach ($recursos as $recurso_id) {
            $post = get_post($recurso_id);
            $slug = $post->post_name;
            $query = $wpdb->prepare(
                "SELECT SUM(count) as total 
                FROM {$pages_table} 
                WHERE uri LIKE %s OR uri LIKE %s OR uri LIKE %s",
                '%/' . $slug . '/%',
                '%/' . $slug . '?%',
                '%/' . $slug
            );
            
            $result = $wpdb->get_var($query);
            $total_visits += intval($result);
        }
        
        if ($total_visits > 0) {
            $gestores_data[] = array(
                'id' => $gestor->ID,
                'name' => $gestor->display_name,
                'count' => $total_visits,
                'recursos_count' => count($recursos)
            );
        }
    }
    usort($gestores_data, function($a, $b) {
        return $b['count'] - $a['count'];
    });
    return array_slice($gestores_data, 0, $limit);
}

function neema_get_top_gestores_by_downloads($limit = 10) {
    global $wpdb;
    $gestores = get_users(array(
        'role__in' => array('gestor_contenido', 'administrator', 'neema_admin'),
        'fields' => array('ID', 'display_name')
    ));
    
    if (empty($gestores)) {
        return array();
    }
    
    $gestores_data = array();
    
    foreach ($gestores as $gestor) {
        $recursos = get_posts(array(
            'post_type' => 'recurso',
            'post_status' => 'publish',
            'author' => $gestor->ID,
            'posts_per_page' => -1,
            'fields' => 'ids'
        ));
        
        if (empty($recursos)) {
            continue;
        }
        
        $total_downloads = 0;
        foreach ($recursos as $recurso_id) {
            $downloads = get_post_meta($recurso_id, 'numDescargas', true);
            $total_downloads += intval($downloads);
        }
        
        if ($total_downloads > 0) {
            $gestores_data[] = array(
                'id' => $gestor->ID,
                'name' => $gestor->display_name,
                'count' => $total_downloads,
                'recursos_count' => count($recursos)
            );
        }
    }
    usort($gestores_data, function($a, $b) {
        return $b['count'] - $a['count'];
    });
    return array_slice($gestores_data, 0, $limit);
}

function neema_get_top_gestores_by_favorites($limit = 10) {
    global $wpdb;
    $favorites_table = $wpdb->prefix . 'recursos_favoritos_usuario';
    $gestores = get_users(array(
        'role__in' => array('gestor_contenido', 'administrator', 'neema_admin'),
        'fields' => array('ID', 'display_name')
    ));
    
    if (empty($gestores)) {
        return array();
    }
    
    $gestores_data = array();
    
    foreach ($gestores as $gestor) {
        $recursos = get_posts(array(
            'post_type' => 'recurso',
            'post_status' => 'publish',
            'author' => $gestor->ID,
            'posts_per_page' => -1,
            'fields' => 'ids'
        ));
        
        if (empty($recursos)) {
            continue;
        }
        
        $total_favorites = 0;
        foreach ($recursos as $recurso_id) {
            $query = $wpdb->prepare(
                "SELECT COUNT(DISTINCT user_id) as total 
                FROM {$favorites_table} 
                WHERE recurso_id = %d",
                $recurso_id
            );
            
            $result = $wpdb->get_var($query);
            $total_favorites += intval($result);
        }
        
        if ($total_favorites > 0) {
            $gestores_data[] = array(
                'id' => $gestor->ID,
                'name' => $gestor->display_name,
                'count' => $total_favorites,
                'recursos_count' => count($recursos)
            );
        }
    }
    usort($gestores_data, function($a, $b) {
        return $b['count'] - $a['count'];
    });
    return array_slice($gestores_data, 0, $limit);
}

function neema_ajax_get_top_gestores() {
    check_ajax_referer('neema_top_gestores', 'nonce');
    
    $metric = isset($_POST['metric']) ? sanitize_text_field($_POST['metric']) : 'visits';
    $limit = 10;
    
    if ($metric === 'downloads') {
        $gestores = neema_get_top_gestores_by_downloads($limit);
    } elseif ($metric === 'favorites') {
        $gestores = neema_get_top_gestores_by_favorites($limit);
    } else {
        $gestores = neema_get_top_gestores_by_visits($limit);
    }
    
    ob_start();
    neema_render_top_gestores_list($gestores, $metric);
    $html = ob_get_clean();
    
    wp_send_json_success($html);
}
add_action('wp_ajax_neema_get_top_gestores', 'neema_ajax_get_top_gestores');
