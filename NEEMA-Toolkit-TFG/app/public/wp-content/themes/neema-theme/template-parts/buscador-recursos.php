<?php
/**
 * Template part: Buscador de Recursos
 * 
 * @param array $args['modulo_key'] - Key del módulo (null para búsqueda global en página RAN)
 */

$modulo_key = isset($args['modulo_key']) ? $args['modulo_key'] : null;
$paises = neema_get_all_paises();
$tipos_recurso = neema_get_all_tipos_recurso();
$tematicas = neema_get_all_tematicas();
$regiones = neema_get_all_regiones();
?>

<div class="buscador-recursos">
    <form id="buscador-recursos-form" method="post">
        
        <!-- Campo de búsqueda por texto -->
        <div class="buscador-texto">
            <input 
                type="text" 
                id="buscar-texto" 
                name="buscar_texto" 
                placeholder="<?php pll_e('Buscar por palabras o frases...', 'Buscador'); ?>"
            >
        </div>

        <!-- Filtros múltiples -->
        <div class="buscador-filtros">
            
            <!-- Países -->
            <div class="filtro-item">
                <button type="button" class="filtro-toggle" data-target="dropdown-paises">
                    <span><?php pll_e('Países', 'Buscador'); ?></span>
                    <span class="arrow">▼</span>
                </button>
                <div class="filtro-dropdown" id="dropdown-paises">
                    <?php foreach ($paises as $pais): ?>
                        <label>
                            <input type="checkbox" name="paises[]" value="<?php echo esc_attr($pais['key']); ?>">
                            <?php echo esc_html($pais['title']); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Tipos de recurso -->
            <div class="filtro-item">
                <button type="button" class="filtro-toggle" data-target="dropdown-tipos">
                    <span><?php pll_e('Tipo de recurso', 'Buscador'); ?></span>
                    <span class="arrow">▼</span>
                </button>
                <div class="filtro-dropdown" id="dropdown-tipos">
                    <?php foreach ($tipos_recurso as $tipo): ?>
                        <label>
                            <input type="checkbox" name="tipos[]" value="<?php echo esc_attr($tipo['key']); ?>">
                            <?php echo esc_html($tipo['title']); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Temáticas -->
            <div class="filtro-item">
                <button type="button" class="filtro-toggle" data-target="dropdown-tematicas">
                    <span><?php pll_e('Temáticas', 'Buscador'); ?></span>
                    <span class="arrow">▼</span>
                </button>
                <div class="filtro-dropdown" id="dropdown-tematicas">
                    <?php foreach ($tematicas as $tematica): ?>
                        <label>
                            <input type="checkbox" name="tematicas[]" value="<?php echo esc_attr($tematica['key']); ?>">
                            <?php echo esc_html($tematica['title']); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Regiones -->
            <div class="filtro-item">
                <button type="button" class="filtro-toggle" data-target="dropdown-regiones">
                    <span><?php pll_e('Regiones', 'Buscador'); ?></span>
                    <span class="arrow">▼</span>
                </button>
                <div class="filtro-dropdown" id="dropdown-regiones">
                    <?php foreach ($regiones as $region): ?>
                        <label>
                            <input type="checkbox" name="regiones[]" value="<?php echo esc_attr($region['key']); ?>">
                            <?php echo esc_html($region['title']); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Categorías -->
            <div class="filtro-item">
                <button type="button" class="filtro-toggle" data-target="dropdown-categorias">
                    <span><?php pll_e('Categorías', 'Buscador'); ?></span>
                    <span class="arrow">▼</span>
                </button>
                <div class="filtro-dropdown" id="dropdown-categorias">
                    <label>
                        <input type="checkbox" name="categorias[]" value="Contextual">
                        <?php pll_e('Contextuales', 'Buscador'); ?>
                    </label>
                    <label>
                        <input type="checkbox" name="categorias[]" value="Formativo">
                        <?php pll_e('Formativos', 'Buscador'); ?>
                    </label>
                    <label>
                        <input type="checkbox" name="categorias[]" value="Metodológico">
                        <?php pll_e('Metodológicos', 'Buscador'); ?>
                    </label>
                    <label>
                        <input type="checkbox" name="categorias[]" value="Procedimental">
                        <?php pll_e('Procedimentales', 'Buscador'); ?>
                    </label>
                </div>
            </div>

        </div>

        <!-- Área visible con filtros seleccionados -->
        <div class="selected-filters" aria-live="polite" style="margin-top:12px;">
            <div class="selected-list" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:6px;"></div>
        </div>

        <!-- Campo oculto para el módulo (si aplica) -->
        <?php if ($modulo_key): ?>
            <input type="hidden" id="modulo-key" name="modulo_key" value="<?php echo esc_attr($modulo_key); ?>">
        <?php endif; ?>

        <!-- Botones de acción -->
        <div class="buscador-botones">
            <button type="submit" class="btn-buscar">
                <i class="fas fa-search"></i>
                <?php pll_e('Buscar', 'Buscador'); ?>
            </button>
            <button type="button" class="btn-limpiar" id="btn-limpiar-filtros">
                <?php pll_e('Limpiar filtros', 'Buscador'); ?>
            </button>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('buscador-recursos-form');
    if(!form) return;
    const groupLabels = {
        paises: <?php echo json_encode(pll__('Países', 'Buscador')); ?>,
        tipos: <?php echo json_encode(pll__('Tipo de recurso', 'Buscador')); ?>,
        tematicas: <?php echo json_encode(pll__('Temáticas', 'Buscador')); ?>,
        regiones: <?php echo json_encode(pll__('Regiones', 'Buscador')); ?>,
        categorias: <?php echo json_encode(pll__('Categorías', 'Buscador')); ?>
    };
    function updateSelected(){
        const container = form.querySelector('.selected-list');
        container.innerHTML = '';
        ['paises','tipos','tematicas','regiones','categorias'].forEach(group => {
            const inputs = Array.from(form.querySelectorAll(`input[name="${group}[]"]:checked`));
            if(inputs.length){
                const groupWrap = document.createElement('div');
                groupWrap.className = 'selected-group';
                groupWrap.style.display = 'flex';
                groupWrap.style.alignItems = 'center';
                groupWrap.style.gap = '6px';

                const title = document.createElement('span');
                title.style.fontWeight = '600';
                title.textContent = groupLabels[group] + ':';
                groupWrap.appendChild(title);

                inputs.forEach(input => {
                    const labelText = input.parentElement ? input.parentElement.textContent.trim() : input.value;
                    const chip = document.createElement('button');
                    chip.type = 'button';
                    chip.className = 'selected-chip';
                    chip.textContent = labelText + " ✕";
                    chip.style.border = '1px solid rgba(0,0,0,0.08)';
                    chip.style.background = '#fff';
                    chip.style.padding = '4px 8px';
                    chip.style.borderRadius = '999px';
                    chip.style.cursor = 'pointer';
                    chip.dataset.group = group;
                    chip.dataset.value = input.value;
                    chip.addEventListener('click', function(){
                        input.checked = false;
                        input.dispatchEvent(new Event('change'));
                    });
                    groupWrap.appendChild(chip);
                });

                container.appendChild(groupWrap);
            }
        });
    }
    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(cb => cb.addEventListener('change', updateSelected));
    const clearFiltersLabel = <?php echo json_encode(pll__('Limpiar filtros', 'Buscador')); ?>;
    const clearSearchLabel = <?php echo json_encode(pll__('Limpiar Búsqueda', 'Buscador')); ?>;
    const btnLimpiar = document.getElementById('btn-limpiar-filtros');
    if(btnLimpiar){
        btnLimpiar.textContent = clearFiltersLabel;
        btnLimpiar.addEventListener('click', function(){
            const inputText = form.querySelector('#buscar-texto');
            if(inputText){ inputText.value = ''; }
            checkboxes.forEach(cb => { cb.checked = false; cb.dispatchEvent(new Event('change')); });
            btnLimpiar.textContent = clearFiltersLabel;
        });
    }
    const btnBuscar = form.querySelector('.btn-buscar');
    if(btnBuscar){
        btnBuscar.addEventListener('click', function(){
            if(btnLimpiar) btnLimpiar.textContent = clearSearchLabel;
        });
    }
    updateSelected();
});
</script>


