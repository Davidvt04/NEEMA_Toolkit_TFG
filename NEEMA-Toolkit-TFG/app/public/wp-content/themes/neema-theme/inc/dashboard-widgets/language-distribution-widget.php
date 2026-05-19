<?php
/**
 * Widget de Distribución de Visitas por Idioma
 * Analiza las URLs capturadas por WP Statistics para detectar el idioma
 */

if (!defined('ABSPATH')) exit;

function neema_add_language_distribution_widget() {
    if (!function_exists('wp_statistics_pages')) {
        return;
    }
    
    wp_add_dashboard_widget(
        'neema_language_distribution',
        'Distribución de Visitas por Idioma',
        'neema_language_distribution_widget_content'
    );
}
add_action('wp_dashboard_setup', 'neema_add_language_distribution_widget');

function neema_language_distribution_widget_content() {
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js', array(), '4.4.1', true);
    
    $language_data = neema_get_language_distribution_data();
    
    if (empty($language_data['languages']) || $language_data['total'] == 0) {
        ?>
        <div style="padding: 20px; text-align: center; color: #666;">
            <p>No hay datos de visitas disponibles aún.</p>
            <p><small>Los datos se generarán a medida que se registren nuevas visitas.</small></p>
        </div>
        <?php
        return;
    }
    
    ?>
    <style>
        .neema-language-chart-container {
            position: relative;
            margin: 20px auto;
            max-width: 400px;
        }
        .neema-language-stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .neema-language-stat-item {
            text-align: center;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 6px;
        }
        .neema-language-stat-item strong {
            display: block;
            font-size: 20px;
            color: #3D3073;
            margin-bottom: 5px;
        }
        .neema-language-stat-item span {
            font-size: 12px;
            color: #666;
        }
        .neema-language-period-selector {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .neema-period-btn {
            padding: 6px 12px;
            border: 1px solid #ddd;
            background: white;
            color: #666;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }
        .neema-period-btn:hover {
            background: #F6EBF8;
            border-color: #B691BE;
        }
        .neema-period-btn.active {
            background: #3D3073;
            color: white;
            border-color: #3D3073;
        }
    </style>
    
    <div class="neema-language-period-selector">
        <button class="neema-period-btn" data-period="today">Hoy</button>
        <button class="neema-period-btn" data-period="yesterday">Ayer</button>
        <button class="neema-period-btn" data-period="week">Esta semana</button>
        <button class="neema-period-btn active" data-period="month">Este mes</button>
        <button class="neema-period-btn" data-period="year">Este año</button>
    </div>
    
    <div class="neema-language-chart-container">
        <canvas id="neemaLanguageChart"></canvas>
    </div>
    
    <div class="neema-language-stats-summary">
        <div class="neema-language-stat-item">
            <strong><?php echo $language_data['most_visited']; ?></strong>
            <span>Idioma Principal</span>
        </div>
    </div>
    
    <script>
    (function($) {
        const initialData = <?php echo json_encode($language_data); ?>;
        let currentChart = null;
        
        const colors = {
            primary: [
                '#FF4136', 
                '#0074D9', 
                '#2ECC40', 
                '#FFDC00', 
                '#FF851B', 
                '#B10DC9', 
                '#001f3f', 
                '#39CCCC', 
                '#7FDBFF', 
                '#85144b'  
            ]
        };
        
        function createChart(data) {
            const ctx = document.getElementById('neemaLanguageChart');
            if (!ctx) return;
        
            if (currentChart) {
                currentChart.destroy();
            }
            
            if (!data || !data.languages || Object.keys(data.languages).length === 0) {
                return;
            }
            
            const labels = Object.keys(data.languages);
            const values = Object.values(data.languages);
            
            const sortedData = labels.map((label, index) => ({
                label: label,
                value: values[index]
            })).sort((a, b) => b.value - a.value);
            
            const sortedLabels = sortedData.map(item => item.label);
            const sortedValues = sortedData.map(item => item.value);
            
            const backgroundColors = sortedLabels.map((_, index) => 
                colors.primary[index % colors.primary.length]
            );
            
            currentChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: sortedLabels,
                    datasets: [{
                        data: sortedValues,
                        backgroundColor: backgroundColors,
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: {
                                    size: 12
                                },
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map((label, i) => {
                                            const value = data.datasets[0].data[i];
                                            const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                            const percentage = ((value / total) * 100).toFixed(1);
                                            return {
                                                text: `${label} (${value} - ${percentage}%)`,
                                                fillStyle: data.datasets[0].backgroundColor[i],
                                                hidden: false,
                                                index: i
                                            };
                                        });
                                    }
                                    return [];
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} visitas (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        function loadPeriodData(period) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'neema_get_language_stats',
                    period: period,
                    nonce: '<?php echo wp_create_nonce('neema_language_stats'); ?>'
                },
                success: function(response) {
                    if (response.success && response.data) {
                        createChart(response.data);
                        $('.neema-language-stat-item strong').text(response.data.most_visited);
                    }
                },
                error: function() {
                    console.error('Error al cargar datos de idiomas');
                }
            });
        }
        $(document).ready(function() {
            function waitForChart() {
                if (typeof Chart !== 'undefined') {
                    createChart(initialData);
                } else {
                    setTimeout(waitForChart, 100);
                }
            }
            waitForChart();
            $('.neema-period-btn').on('click', function() {
                const period = $(this).data('period');
                $('.neema-period-btn').removeClass('active');
                $(this).addClass('active');
                loadPeriodData(period);
            });
        });
    })(jQuery);
    </script>
    <?php
}

function neema_get_language_distribution_data($period = 'month') {
    global $wpdb;
    
    if (!class_exists('WP_STATISTICS\DB')) {
        return array(
            'total' => 0,
            'languages' => array(),
            'most_visited' => 'N/A'
        );
    }
    $available_languages = array('es', 'en', 'fr');
    
    $date_condition = neema_get_date_condition_for_period($period);
    
    $pages_table = $wpdb->prefix . 'statistics_pages';
    
    $query = "SELECT uri, COUNT(*) as visits 
              FROM {$pages_table} 
              WHERE {$date_condition}
              GROUP BY uri 
              ORDER BY visits DESC";
    
    $results = $wpdb->get_results($query);
    
    $language_counts = array();
    $total_visits = 0;
    
    foreach ($available_languages as $lang) {
        $language_counts[$lang] = 0;
    }
    
    foreach ($results as $row) {
        $uri = $row->uri;
        $visits = intval($row->visits);
        $total_visits += $visits;
        
        $detected_lang = neema_detect_language_from_url($uri, $available_languages);
        
        if (!isset($language_counts[$detected_lang])) {
            $language_counts[$detected_lang] = 0;
        }
        $language_counts[$detected_lang] += $visits;
    }
    
    $language_counts = array_filter($language_counts, function($count) {
        return $count > 0;
    });
    
    $language_names = array(
        'es' => 'Español',
        'en' => 'English',
        'fr' => 'Français'
    );
    
    $translated_counts = array();
    foreach ($language_counts as $code => $count) {
        $name = isset($language_names[$code]) ? $language_names[$code] : strtoupper($code);
        $translated_counts[$name] = $count;
    }
    
    $most_visited = 'N/A';
    if (!empty($translated_counts)) {
        arsort($translated_counts);
        $most_visited = key($translated_counts);
    }
    
    return array(
        'total' => $total_visits,
        'languages' => $translated_counts,
        'most_visited' => $most_visited
    );
}

function neema_detect_language_from_url($uri, $available_languages = array()) {
    $uri = strtolower(trim($uri, '/'));
    if (preg_match('/[?&]lang=([a-z]{2})/', $uri, $matches)) {
        return $matches[1];
    }
    foreach ($available_languages as $lang) {
        if ($lang === 'es') continue; 
        if (preg_match('/^' . preg_quote($lang, '/') . '(\/|$)/', $uri)) {
            return $lang;
        }
    }
    return 'es';
}

function neema_get_date_condition_for_period($period) {
    global $wpdb;
    
    switch ($period) {
        case 'today':
            return "DATE(date) = CURDATE()";
        
        case 'yesterday':
            return "DATE(date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
        
        case 'week':
            return "date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        
        case 'month':
            return "date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        
        case 'year':
            return "date >= DATE_SUB(NOW(), INTERVAL 365 DAY)";
        
        default:
            return "date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    }
}

function neema_ajax_get_language_stats() {
    check_ajax_referer('neema_language_stats', 'nonce');
    
    $period = isset($_POST['period']) ? sanitize_text_field($_POST['period']) : 'month';
    $data = neema_get_language_distribution_data($period);
    
    wp_send_json_success($data);
}
add_action('wp_ajax_neema_get_language_stats', 'neema_ajax_get_language_stats');
