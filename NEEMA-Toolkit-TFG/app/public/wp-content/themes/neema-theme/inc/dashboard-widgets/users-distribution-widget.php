<?php
/**
 * Widget de Distribución de Usuarios
 * Gráfico de tarta interactivo con 3 vistas: Rol, Entidad y Perfil WP
 */

if (!defined('ABSPATH')) exit;

function neema_add_users_distribution_widget() {
    wp_add_dashboard_widget(
        'neema_users_distribution',
        'Distribución de Usuarios',
        'neema_users_distribution_widget_content'
    );
}
add_action('wp_dashboard_setup', 'neema_add_users_distribution_widget');

function neema_users_distribution_widget_content() {
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js', array(), '4.4.1', true);
    $users_data = neema_get_users_distribution_data();
    
    ?>
    <style>
        .neema-chart-container {
            position: relative;
            margin: 20px auto;
            max-width: 400px;
        }
        .neema-chart-controls {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .neema-chart-btn {
            padding: 8px 16px;
            border: 2px solid #B691BE;
            background: white;
            color: #3D3073;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .neema-chart-btn:hover {
            background: #F6EBF8;
        }
        .neema-chart-btn.active {
            background: linear-gradient(135deg, #3D3073 0%, #5a4a8f 100%);
            color: white;
            border-color: #3D3073;
        }
        .neema-stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .neema-stat-item {
            text-align: center;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 6px;
        }
        .neema-stat-item strong {
            display: block;
            font-size: 20px;
            color: #3D3073;
            margin-bottom: 5px;
        }
        .neema-stat-item span {
            font-size: 12px;
            color: #666;
        }
    </style>
    
    <div class="neema-chart-controls">
        <button class="neema-chart-btn active" data-type="rol">Por Rol</button>
        <button class="neema-chart-btn" data-type="entidad">Por Entidad</button>
        <button class="neema-chart-btn" data-type="wordpress_role">Por Perfil</button>
    </div>
    
    <div class="neema-chart-container">
        <canvas id="neemaUsersChart"></canvas>
    </div>
    
    <div class="neema-stats-summary">
        <div class="neema-stat-item">
            <strong><?php echo $users_data['total']; ?></strong>
            <span>Total Usuarios</span>
        </div>
        <div class="neema-stat-item">
            <strong><?php echo count($users_data['rol']); ?></strong>
            <span>Roles Diferentes</span>
        </div>
        <div class="neema-stat-item">
            <strong><?php echo count($users_data['entidad']); ?></strong>
            <span>Entidades</span>
        </div>
    </div>
    
    <script>
    (function($) {
        const chartData = <?php echo json_encode($users_data); ?>;
        let currentChart = null;
        
        const colors = {
            primary: [
                '#FF4136', 
                '#0074D9', 
                '#2ECC40', 
                '#FFDC00', 
                '#FF851B', 
                '#B10DC9', 
                '#85144b', 
                '#7FDBFF', 
                '#001f3f', 
                '#39CCCC'  
            ],
            extended: [
                '#FF4136','#0074D9','#2ECC40','#FFDC00','#FF851B','#B10DC9','#85144b','#7FDBFF','#001f3f','#39CCCC',
                '#FF69B4','#3D9970','#AAAA00','#111111','#AAAAFF'
            ]
        };
        
        function createChart(type) {
            const ctx = document.getElementById('neemaUsersChart');
            if (!ctx) return;
            if (currentChart) {
                currentChart.destroy();
            }
            
            const data = chartData[type];
            const labels = Object.keys(data);
            const values = Object.values(data);
            const sortedData = labels.map((label, index) => ({
                label: label || 'Sin especificar',
                value: values[index]
            })).sort((a, b) => b.value - a.value);
            
            const sortedLabels = sortedData.map(item => item.label);
            const sortedValues = sortedData.map(item => item.value);
            const colorPalette = sortedLabels.length > 10 ? colors.extended : colors.primary;
            const backgroundColors = sortedLabels.map((_, index) => 
                colorPalette[index % colorPalette.length]
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
                                    return `${label}: ${value} usuarios (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
        $(document).ready(function() {
            function waitForChart() {
                if (typeof Chart !== 'undefined') {
                    createChart('rol');
                } else {
                    setTimeout(waitForChart, 100);
                }
            }
            waitForChart();
            $('.neema-chart-btn').on('click', function() {
                const type = $(this).data('type');
                $('.neema-chart-btn').removeClass('active');
                $(this).addClass('active');
                createChart(type);
            });
        });
    })(jQuery);
    </script>
    <?php
}

function neema_get_users_distribution_data() {
    $users = get_users(array('fields' => 'all'));
    
    $data = array(
        'total' => count($users),
        'rol' => array(),
        'entidad' => array(),
        'wordpress_role' => array()
    );
    
    foreach ($users as $user) {
        $rol = get_user_meta($user->ID, 'rol_usuario', true);
        if (empty($rol)) {
            $rol = 'Sin especificar';
        }
        if (!isset($data['rol'][$rol])) {
            $data['rol'][$rol] = 0;
        }
        $data['rol'][$rol]++;
        $entidad = get_user_meta($user->ID, 'entidad_proveniente', true);
        if (empty($entidad)) {
            $entidad = 'Sin especificar';
        }
        if (!isset($data['entidad'][$entidad])) {
            $data['entidad'][$entidad] = 0;
        }
        $data['entidad'][$entidad]++;
        $wp_roles = $user->roles;
        $wp_role = !empty($wp_roles) ? $wp_roles[0] : 'sin_rol';
        
        $role_names = array(
            'administrator' => 'Administrador',
            'editor' => 'Editor',
            'author' => 'Autor',
            'contributor' => 'Colaborador',
            'subscriber' => 'Suscriptor',
            'visitante' => 'Visitante',
            'neema_admin' => 'Administrador funcional NEEMA',
            'sin_rol' => 'Sin Rol'
        );
        
        $wp_role_display = isset($role_names[$wp_role]) ? $role_names[$wp_role] : ucfirst($wp_role);
        
        if (!isset($data['wordpress_role'][$wp_role_display])) {
            $data['wordpress_role'][$wp_role_display] = 0;
        }
        $data['wordpress_role'][$wp_role_display]++;
    }
    
    return $data;
}
