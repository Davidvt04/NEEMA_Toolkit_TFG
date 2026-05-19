jQuery(document).ready(function($) {
    $('.btn-cargar-mas-organismos').on('click', function(e) {
        e.preventDefault();
        var btn = $(this);
        var grid = $('#' + btn.data('target'));
        
        btn.prop('disabled', true).css('opacity', '0.5');
        
        $.post(ajaxurl, {
            action: 'cargar_mas_organismos',
            categoria_ids: btn.data('categoria-ids'),
            offset: parseInt(grid.data('offset'))
        }, function(response) {
            if (response.success && response.data.html) {
                grid.append(response.data.html);
                grid.data('offset', parseInt(grid.data('offset')) + 6);
                response.data.has_more ? btn.prop('disabled', false).css('opacity', '1') : btn.fadeOut();
            } else {
                btn.fadeOut();
            }
        }).fail(function() {
            btn.prop('disabled', false).css('opacity', '1');
            alert('Error al cargar organismos');
        });
    });
});
