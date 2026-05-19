/**
 * Buscador de Recursos - JavaScript
 * Maneja la búsqueda AJAX y la visualización de resultados
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        const $form = $('#buscador-recursos-form');
        const $btnLimpiar = $('#btn-limpiar-filtros');
        const isModuloPage = $('#modulo-key').length > 0;
        $(document).on('click', '.filtro-toggle', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const targetId = $(this).data('target');
            const $dropdown = $('#' + targetId);
            $('.filtro-dropdown').not($dropdown).removeClass('active');
            $('.filtro-toggle').not(this).removeClass('active');
            $dropdown.toggleClass('active');
            $(this).toggleClass('active');
        });
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.filtro-item').length) {
                $('.filtro-dropdown').removeClass('active');
                $('.filtro-toggle').removeClass('active');
            }
        });
        $(document).on('click', '.filtro-dropdown', function(e) {
            e.stopPropagation();
        });

        $form.on('submit', function(e) {
            e.preventDefault();
            realizarBusqueda();
        });

        $btnLimpiar.on('click', function() {
            limpiarFiltros();
        });

        function realizarBusqueda() {
            const textoLibre = $('#buscar-texto').val();
            const paises = getSelectedCheckboxes('paises[]');
            const tipos = getSelectedCheckboxes('tipos[]');
            const tematicas = getSelectedCheckboxes('tematicas[]');
            const regiones = getSelectedCheckboxes('regiones[]');
            const categorias = getSelectedCheckboxes('categorias[]');
            const moduloKey = $('#modulo-key').val() || '';
            $('.filtro-dropdown').removeClass('active');
            $('.filtro-toggle').removeClass('active');
            mostrarLoading();
            const ajaxUrl = typeof buscadorAjax !== 'undefined' ? buscadorAjax.ajaxurl : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');
            const currentLang = $('html').attr('lang') ? $('html').attr('lang').substring(0, 2) : 'es';
            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'buscar_recursos',
                    texto: textoLibre,
                    paises: paises,
                    tipos: tipos,
                    tematicas: tematicas,
                    regiones: regiones,
                    categorias: categorias,
                    modulo_key: moduloKey,
                    lang: currentLang
                },
                success: function(response) {
                    if (response.success) {
                        mostrarResultados(response.data);
                    }
                },
                error: function(xhr, status, error) {
                    ocultarLoading();
                },
                complete: function() {
                    ocultarLoading();
                }
            });
        }

        function mostrarResultados(data) {
            const config = isModuloPage 
                ? { hideId: '#recursos-default', showId: '#recursos-filtrados', containerId: 'busqueda-resultados' }
                : { hideId: '#seccion-modulos', showId: '#resultados-recursos', containerId: 'resultados-container' };
            $(config.hideId).hide();
            $(config.showId).show();
            const $container = $('#' + config.containerId);
            if ($container.length) {
                $container.html(data.html || '');
            } else {
                $(config.showId).find('.recursos-grid, .recursos-container').first().html(data.html || '');
            }
            setTimeout(function() {
                $(config.showId)[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 100);
        }

        function mostrarLoading() {
            const loadingMsg = typeof buscadorAjax !== 'undefined' ? buscadorAjax.loadingMessage : 'Loading...';
            const loadingHtml = '<p style="text-align: center; padding: 20px;">' + loadingMsg + '</p>';
            const config = isModuloPage 
                ? { hideId: '#recursos-default', showId: '#recursos-filtrados', containerId: 'busqueda-resultados' }
                : { hideId: '#seccion-modulos', showId: '#resultados-recursos', containerId: 'resultados-container' };
            $(config.hideId).hide();
            $(config.showId).show();
            const $container = $('#' + config.containerId);
            if ($container.length) {
                $container.html(loadingHtml);
            } else {
                $(config.showId).find('.recursos-grid, .recursos-container').first().html(loadingHtml);
            }
        }
        function ocultarLoading() {
        }

        function getSelectedCheckboxes(name) {
            return $('input[name="' + name + '"]:checked').map(function() {
                return $(this).val();
            }).get();
        }

        function limpiarFiltros() {
            $('#buscar-texto').val('');
            $('input[type="checkbox"]').prop('checked', false);
            $('.filtro-dropdown').removeClass('active');
            $('.filtro-toggle').removeClass('active');
            if (isModuloPage) {
                $('#recursos-filtrados').hide();
                $('#recursos-default').show();
            } else {
                $('#resultados-recursos').hide();
                $('#seccion-modulos').show();
            }
        }

    });

})(jQuery);
