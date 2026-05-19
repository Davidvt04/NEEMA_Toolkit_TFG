<?php
/**
 * Widget de Características de Recursos Populares
 * Analiza los 10 recursos más visitados y muestra gráficos de barras por cada aspecto
 */

if (!defined('ABSPATH')) exit;

function neema_add_recursos_caracteristicas_widget() {
    wp_add_dashboard_widget(
        'neema_recursos_caracteristicas',
        'Características de Recursos Populares',
        'neema_recursos_caracteristicas_widget_content'
    );
}
add_action('wp_dashboard_setup', 'neema_add_recursos_caracteristicas_widget');

function neema_recursos_caracteristicas_widget_content() {

    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js', array(), '4.4.1', true);
    $caracteristicas_data = neema_get_recursos_caracteristicas_data();
    
    if (empty($caracteristicas_data)) {
        ?>
        <div style="padding: 20px; text-align: center; color: #666;">
            <p>No hay datos de visitas disponibles aún.</p>
        </div>
        <?php
        return;
    }
    
    ?>
    <style>
        .neema-caracteristicas-controls {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }
        .neema-caracteristica-btn {
            padding: 6px 14px;
            border: 1px solid #ddd;
            background: white;
            color: #666;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .neema-caracteristica-btn:hover {
            background: #F6EBF8;
            border-color: #B691BE;
        }
        .neema-caracteristica-btn.active {
            background: #3D3073;
            color: white;
            border-color: #3D3073;
        }
        .neema-caracteristicas-container {
            padding: 10px 0;
        }
        .neema-caracteristica-section {
            display: none;
        }
        .neema-caracteristica-section.active {
            display: block;
        }
        .neema-caracteristica-title {
            font-size: 14px;
            font-weight: 600;
            color: #3D3073;
            margin: 0 0 15px 0;
            text-align: center;
        }
        .neema-caracteristica-chart {
            position: relative;
            height: 350px;
            margin-bottom: 10px;
        }
    </style>
    
    <div class="neema-caracteristicas-controls">
        <?php 
        $sections = array(
            'modulos' => 'Módulos',
            'categorias' => 'Categorías',
            'tipos' => 'Tipos de Recurso',
            'paises' => 'Países',
            'regiones' => 'Regiones',
            'tematicas' => 'Temáticas'
        );
        
        $first = true;
        foreach ($sections as $key => $title): 
            if (!empty($caracteristicas_data[$key])): ?>
        <button class="neema-caracteristica-btn <?php echo $first ? 'active' : ''; ?>" data-section="<?php echo $key; ?>">
            <?php echo $title; ?>
        </button>
        <?php 
                $first = false;
            endif;
        endforeach; 
        ?>
    </div>
    
    <div class="neema-caracteristicas-container">
        <?php 
        $first = true;
        foreach ($sections as $key => $title): 
            if (!empty($caracteristicas_data[$key])): ?>
        <div class="neema-caracteristica-section <?php echo $first ? 'active' : ''; ?>" data-section="<?php echo $key; ?>">
            <h4 class="neema-caracteristica-title"><?php echo $title; ?></h4>
            <div class="neema-caracteristica-chart">
                <canvas id="neemaChart<?php echo ucfirst($key); ?>"></canvas>
            </div>
        </div>
        <?php 
                $first = false;
            endif;
        endforeach; 
        ?>
    </div>
    
    <script>
    (function($) {
        const data = <?php echo json_encode($caracteristicas_data); ?>;
        
        const colors = {
            palette: [
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
        
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    bottom: 20
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y.toLocaleString() + ' visitas';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45,
                        autoSkip: false,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        };
        
        let charts = {};
        
        function createBarChart(canvasId, chartData) {
            const ctx = document.getElementById(canvasId);
            if (!ctx || !chartData || chartData.labels.length === 0) return;
            const backgroundColors = chartData.labels.map((_, index) => {
                return colors.palette[index % colors.palette.length];
            });
            
            charts[canvasId] = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        data: chartData.values,
                        backgroundColor: backgroundColors,
                        borderColor: colors.primary,
                        borderWidth: 0
                    }]
                },
                options: chartOptions
            });
        }
        
        function initCharts() {
            if (typeof Chart === 'undefined') {
                setTimeout(initCharts, 100);
                return;
            }
            if (data.modulos) createBarChart('neemaChartModulos', data.modulos);
            if (data.categorias) createBarChart('neemaChartCategorias', data.categorias);
            if (data.tipos) createBarChart('neemaChartTipos', data.tipos);
            if (data.paises) createBarChart('neemaChartPaises', data.paises);
            if (data.regiones) createBarChart('neemaChartRegiones', data.regiones);
            if (data.tematicas) createBarChart('neemaChartTematicas', data.tematicas);
        }
        
        $(document).ready(function() {
            initCharts();
            $('.neema-caracteristica-btn').on('click', function() {
                const section = $(this).data('section');
                $('.neema-caracteristica-btn').removeClass('active');
                $(this).addClass('active');
                $('.neema-caracteristica-section').removeClass('active');
                $('.neema-caracteristica-section[data-section="' + section + '"]').addClass('active');
            });
        });
    })(jQuery);
    </script>
    <?php
}

function neema_get_recursos_caracteristicas_data() {
    global $wpdb;
    if (!class_exists('WP_STATISTICS\DB')) {
        return array();
    }
    $top_recursos = neema_get_top_recursos_by_visits(10);
    
    if (empty($top_recursos)) {
        return array();
    }
    $modulos = array();
    $categorias = array();
    $tipos = array();
    $paises = array();
    $regiones = array();
    $tematicas = array();
    $debug_info = array();
    foreach ($top_recursos as $recurso) {
        $recurso_id = $recurso['id'];
        $visitas = $recurso['count'];
        
        $recurso_debug = array('id' => $recurso_id, 'visitas' => $visitas);

        $modulo_id = get_post_meta($recurso_id, '_recurso_modulo', true);
        $recurso_debug['modulo_id'] = $modulo_id;
        if ($modulo_id) {
            if (is_numeric($modulo_id)) {
                $modulo = get_post($modulo_id);
            } else {
                $modulo = get_page_by_path($modulo_id, OBJECT, 'modulo');
            }
            if ($modulo) {
                $modulo_name = $modulo->post_title;
                if (preg_match('/^(M\d+)/', $modulo_name, $matches)) {
                    $modulo_name = $matches[1];
                }
                $recurso_debug['modulo_name'] = $modulo_name;
                if (!isset($modulos[$modulo_name])) {
                    $modulos[$modulo_name] = 0;
                }
                $modulos[$modulo_name] += $visitas;
            }
        }
        
        $categoria = get_post_meta($recurso_id, '_recurso_categoria', true);
        if ($categoria) {
            if (!isset($categorias[$categoria])) {
                $categorias[$categoria] = 0;
            }
            $categorias[$categoria] += $visitas;
        }
        
        $tipo_key = get_post_meta($recurso_id, '_recurso_tipo', true);
        $recurso_debug['tipo_key'] = $tipo_key;
        if ($tipo_key) {
            $tipo_posts = get_posts(array(
                'post_type'      => 'tipo-recurso',
                'meta_query'     => array(
                    array(
                        'key'     => '_tipo_recurso_key',
                        'value'   => $tipo_key,
                        'compare' => '='
                    )
                ),
                'posts_per_page' => 1,
                'lang'           => ''
            ));
            
            if (!empty($tipo_posts)) {
                $tipo_name = $tipo_posts[0]->post_title;
                $recurso_debug['tipo_name'] = $tipo_name;
                if (!isset($tipos[$tipo_name])) {
                    $tipos[$tipo_name] = 0;
                }
                $tipos[$tipo_name] += $visitas;
            } else {
                $recurso_debug['tipo_name'] = 'No encontrado';
            }
        }
        
        $debug_info[] = $recurso_debug;
        $paises_data = get_post_meta($recurso_id, '_recurso_paises', true);
        if (is_array($paises_data)) {
            foreach ($paises_data as $pais_id) {
                $pais_post = get_post($pais_id);
                if ($pais_post) {
                    $pais_nombre = $pais_post->post_title;
                    if (!isset($paises[$pais_nombre])) {
                        $paises[$pais_nombre] = 0;
                    }
                    $paises[$pais_nombre] += $visitas;
                }
            }
        }
        $regiones_data = get_post_meta($recurso_id, '_recurso_regiones', true);
        if (is_array($regiones_data)) {
            $regiones_nombres = array(
                'africa' => 'África',
                'america' => 'América',
                'asia' => 'Asia',
                'europa' => 'Europa',
                'oceania' => 'Oceanía'
            );
            
            foreach ($regiones_data as $region_key) {
                $region_name = isset($regiones_nombres[$region_key]) ? $regiones_nombres[$region_key] : ucfirst($region_key);
                if (!isset($regiones[$region_name])) {
                    $regiones[$region_name] = 0;
                }
                $regiones[$region_name] += $visitas;
            }
        }
        $tematicas_data = get_post_meta($recurso_id, '_recurso_tematicas', true);
        if (is_array($tematicas_data)) {
            $tematicas_nombres = array(
                'agricultura' => 'Agricultura',
                'ganaderia' => 'Ganadería',
                'pesca' => 'Pesca',
                'forestal' => 'Forestal',
                'apicultura' => 'Apicultura',
                'acuicultura' => 'Acuicultura'
            );
            
            foreach ($tematicas_data as $tematica_key) {
                $tematica_name = isset($tematicas_nombres[$tematica_key]) ? $tematicas_nombres[$tematica_key] : ucfirst($tematica_key);
                if (!isset($tematicas[$tematica_name])) {
                    $tematicas[$tematica_name] = 0;
                }
                $tematicas[$tematica_name] += $visitas;
            }
        }
    }
    $result = array();
    if (!empty($modulos)) {
        arsort($modulos);
        $result['modulos'] = array(
            'labels' => array_keys($modulos),
            'values' => array_values($modulos)
        );
    }
    if (!empty($categorias)) {
        arsort($categorias);
        $result['categorias'] = array(
            'labels' => array_keys($categorias),
            'values' => array_values($categorias)
        );
    }
    if (!empty($tipos)) {
        arsort($tipos);
        $result['tipos'] = array(
            'labels' => array_keys($tipos),
            'values' => array_values($tipos)
        );
    }
    if (!empty($paises)) {
        arsort($paises);
        $result['paises'] = array(
            'labels' => array_keys($paises),
            'values' => array_values($paises)
        );
    }
    if (!empty($regiones)) {
        arsort($regiones);
        $result['regiones'] = array(
            'labels' => array_keys($regiones),
            'values' => array_values($regiones)
        );
    }
    if (!empty($tematicas)) {
        arsort($tematicas);
        $result['tematicas'] = array(
            'labels' => array_keys($tematicas),
            'values' => array_values($tematicas)
        );
    }
    $result['_debug'] = $debug_info;
    
    return $result;
}
