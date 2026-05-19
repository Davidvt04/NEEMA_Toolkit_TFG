<?php
/**
 * Template part: Buscador de Organismos
 * 
 * @param array $args['categoria_id'] - ID de la categoría (null para búsqueda global en página servicios de apoyo)
 */

$categoria_id = isset($args['categoria_id']) ? $args['categoria_id'] : null;
$paises = neema_get_all_paises();
?>

<div class="buscador-organismos">
    <form id="buscador-organismos-form" method="post">
        
        <!-- Campo de búsqueda por texto -->
        <div class="buscador-texto">
            <input 
                type="text" 
                id="buscar-texto-organismos" 
                name="buscar_texto" 
                placeholder="<?php pll_e('Buscar por palabras o frases...', 'Buscador'); ?>"
            >
        </div>

        <!-- Filtros múltiples -->
        <div class="buscador-filtros">
            
            <!-- Ámbito -->
            <div class="filtro-item">
                <button type="button" class="filtro-toggle" data-target="dropdown-ambito">
                    <span><?php pll_e('Ámbito', 'Buscador'); ?></span>
                    <span class="arrow">▼</span>
                </button>
                <div class="filtro-dropdown" id="dropdown-ambito">
                    <label>
                        <input type="checkbox" name="ambito[]" value="Internacional">
                        <?php pll_e('Internacional', 'Servicios de Apoyo'); ?>
                    </label>
                    <label>
                        <input type="checkbox" name="ambito[]" value="Nacional">
                        <?php pll_e('Nacional', 'Servicios de Apoyo'); ?>
                    </label>
                    <label>
                        <input type="checkbox" name="ambito[]" value="Local">
                        <?php pll_e('Local', 'Servicios de Apoyo'); ?>
                    </label>
                </div>
            </div>

            <!-- Países -->
            <div class="filtro-item">
                <button type="button" class="filtro-toggle" data-target="dropdown-paises">
                    <span><?php pll_e('Países', 'Buscador'); ?></span>
                    <span class="arrow">▼</span>
                </button>
                <div class="filtro-dropdown" id="dropdown-paises">
                    <?php foreach ($paises as $pais): ?>
                        <label>
                            <input type="checkbox" name="paises[]" value="<?php echo esc_attr($pais['id']); ?>">
                            <?php echo esc_html($pais['title']); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Ciudad/Localidad (solo se muestra cuando ámbito es Local) -->
            <div class="filtro-item" id="filtro-ciudad" style="display: none;">
                <div class="filtro-input-wrapper">
                    <input 
                        type="text" 
                        id="ciudad-localidad" 
                        name="ciudad" 
                        placeholder="<?php pll_e('Ciudad o Localidad', 'Buscador'); ?>"
                    >
                </div>
            </div>

        </div>

        <!-- Área visible con filtros seleccionados -->
        <div class="selected-filters" aria-live="polite" style="margin-top:12px;">
            <div class="selected-list" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:6px;"></div>
        </div>

        <!-- Campo oculto para la categoría (si aplica) -->
        <?php if ($categoria_id): ?>
            <input type="hidden" id="categoria-id" name="categoria_id" value="<?php echo esc_attr($categoria_id); ?>">
        <?php endif; ?>

        <!-- Botones de acción -->
        <div class="buscador-botones">
            <button type="submit" class="btn-buscar">
                <i class="fas fa-search"></i>
                <?php pll_e('Buscar', 'Buscador'); ?>
            </button>
            <button type="button" class="btn-limpiar" id="btn-limpiar-filtros-organismos">
                <?php pll_e('Limpiar filtros', 'Buscador'); ?>
            </button>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('buscador-organismos-form');
    if(!form) return;
    const groupLabels = {
        ambito: <?php echo json_encode(pll__('Ámbito', 'Buscador')); ?>,
        paises: <?php echo json_encode(pll__('Países', 'Buscador')); ?>,
        ciudad: <?php echo json_encode(pll__('Ciudad/Localidad', 'Buscador')); ?>
    };
    const ambitoCheckboxes = form.querySelectorAll('input[name="ambito[]"]');
    const filtroCiudad = document.getElementById('filtro-ciudad');
    const ciudadInput = document.getElementById('ciudad-localidad');
    ambitoCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const localSelected = Array.from(ambitoCheckboxes).some(cb => cb.checked && cb.value === 'Local');
            if (localSelected) {
                filtroCiudad.style.display = 'block';
            } else {
                filtroCiudad.style.display = 'none';
                ciudadInput.value = '';
            }
            updateSelected();
        });
    });

    function updateSelected(){
        const container = form.querySelector('.selected-list');
        container.innerHTML = '';
        const ambitoInputs = Array.from(form.querySelectorAll('input[name="ambito[]"]:checked'));
        if (ambitoInputs.length) {
            const ambitoData = ambitoInputs.map(input => ({
                label: input.parentElement ? input.parentElement.textContent.trim() : input.value,
                value: input.value
            }));
            addFilterGroup('ambito', ambitoData);
        }
        const paisesInputs = Array.from(form.querySelectorAll('input[name="paises[]"]:checked'));
        if (paisesInputs.length) {
            const paisesData = paisesInputs.map(input => ({
                label: input.parentElement ? input.parentElement.textContent.trim() : input.value,
                value: input.value
            }));
            addFilterGroup('paises', paisesData);
        }
        const localSelected = ambitoInputs.some(input => input.value === 'Local');
        if (localSelected && ciudadInput.value.trim()) {
            addFilterGroup('ciudad', [{ label: ciudadInput.value.trim(), value: ciudadInput.value.trim() }]);
        }

        function addFilterGroup(group, items) {
            const groupWrap = document.createElement('div');
            groupWrap.className = 'selected-group';
            groupWrap.style.display = 'flex';
            groupWrap.style.alignItems = 'center';
            groupWrap.style.gap = '6px';
            const title = document.createElement('span');
            title.style.fontWeight = '600';
            title.textContent = groupLabels[group] + ':';
            groupWrap.appendChild(title);
            items.forEach(item => {
                const chip = document.createElement('button');
                chip.type = 'button';
                chip.className = 'selected-chip';
                chip.textContent = item.label + " ✕";
                chip.style.border = '1px solid rgba(0,0,0,0.08)';
                chip.style.background = '#fff';
                chip.style.padding = '4px 8px';
                chip.style.borderRadius = '999px';
                chip.style.cursor = 'pointer';
                chip.dataset.group = group;
                chip.dataset.value = item.value;
                chip.addEventListener('click', function(){
                    if (group === 'ambito') {
                        form.querySelector(`input[name="ambito[]"][value="${item.value}"]`).checked = false;
                        const localStillSelected = Array.from(form.querySelectorAll('input[name="ambito[]"]')).some(cb => cb.checked && cb.value === 'Local');
                        if (!localStillSelected) {
                            filtroCiudad.style.display = 'none';
                            ciudadInput.value = '';
                        }
                    } else if (group === 'paises') {
                        form.querySelector(`input[name="paises[]"][value="${item.value}"]`).checked = false;
                    } else if (group === 'ciudad') {
                        ciudadInput.value = '';
                    }
                    updateSelected();
                });
                groupWrap.appendChild(chip);
            });

            container.appendChild(groupWrap);
        }
    }

    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(cb => cb.addEventListener('change', updateSelected));
    ciudadInput.addEventListener('input', updateSelected);
    const clearFiltersLabel = <?php echo json_encode(pll__('Limpiar filtros', 'Buscador')); ?>;
    const clearSearchLabel = <?php echo json_encode(pll__('Limpiar Búsqueda', 'Buscador')); ?>;
    const btnLimpiar = document.getElementById('btn-limpiar-filtros-organismos');
    if(btnLimpiar){
        btnLimpiar.textContent = clearFiltersLabel;
        btnLimpiar.addEventListener('click', function(){
            const inputText = form.querySelector('#buscar-texto-organismos');
            if(inputText){ inputText.value = ''; }
            ciudadInput.value = '';
            checkboxes.forEach(cb => { cb.checked = false; });
            filtroCiudad.style.display = 'none';
            updateSelected();
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
