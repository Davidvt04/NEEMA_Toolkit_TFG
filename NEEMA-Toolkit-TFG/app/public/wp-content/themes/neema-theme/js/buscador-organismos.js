/**
 * Buscador de Organismos - JavaScript
 * Maneja la búsqueda AJAX y la visualización de resultados
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        const $form = $('#buscador-organismos-form');
        const $btnLimpiar = $('#btn-limpiar-filtros-organismos');
        const isCategoriaPage = $('#categoria-id').length > 0;

        /**
         * Manejo de dropdowns personalizados
         */
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
            const textoLibre = $('#buscar-texto-organismos').val();
            const ambito = getSelectedCheckboxes('ambito[]');
            const paises = getSelectedCheckboxes('paises[]');
            const ciudad = $('#ciudad-localidad').val().trim();
            const categoriaId = $('#categoria-id').val() || '';
            $('.filtro-dropdown').removeClass('active');
            $('.filtro-toggle').removeClass('active');
            mostrarLoading();
            const ajaxUrl = typeof buscadorAjax !== 'undefined' ? buscadorAjax.ajaxurl : (typeof ajaxurl !== 'undefined' ? ajaxurl : '/wp-admin/admin-ajax.php');
            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'buscar_organismos',
                    buscar_texto: textoLibre,
                    ambito: ambito,
                    paises: paises,
                    ciudad: ciudad,
                    categoria_id: categoriaId
                },
                success: function(response) {
                    if (response.success) {
                        mostrarResultados(response.data);
                    } else {
                        console.error('Error en la búsqueda:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', error);
                    ocultarLoading();
                },
                complete: function() {
                    ocultarLoading();
                }
            });
        }

        function mostrarResultados(data) {
            const config = isCategoriaPage 
                ? { hideId: '#seccion-organismos-categoria', showId: '#resultados-organismos-categoria' }
                : { hideId: '#seccion-categorias', showId: '#resultados-organismos' };
            $(config.hideId).hide();
            $(config.showId).show();
            $('#organismos-resultados').html(data.html);
        }

        function getSelectedCheckboxes(name) {
            return $('input[name="' + name + '"]:checked').map(function() {
                return $(this).val();
            }).get();
        }

        function limpiarFiltros() {
            $('#buscar-texto-organismos').val('');
            $('#ciudad-localidad').val('');
            $('input[type="checkbox"]').prop('checked', false);
            $('#filtro-ciudad').hide();
            $('.filtro-dropdown').removeClass('active');
            $('.filtro-toggle').removeClass('active');
            if (isCategoriaPage) {
                $('#resultados-organismos-categoria').hide();
                $('#seccion-organismos-categoria').show();
            } else {
                $('#resultados-organismos').hide();
                $('#seccion-categorias').show();
            }
        }

        function mostrarLoading() {
            const loadingMsg = typeof buscadorAjax !== 'undefined' ? buscadorAjax.loadingMessage : 'Cargando...';
            const loadingHtml = '<p style="text-align: center; padding: 20px;">' + loadingMsg + '</p>';
            const config = isCategoriaPage 
                ? { hideId: '#seccion-organismos-categoria', showId: '#resultados-organismos-categoria' }
                : { hideId: '#seccion-categorias', showId: '#resultados-organismos' };
            
            $(config.hideId).hide();
            $(config.showId).show();
            
            $('#organismos-resultados').html(loadingHtml);
        }

        function ocultarLoading() {
        }

    });

})(jQuery);
