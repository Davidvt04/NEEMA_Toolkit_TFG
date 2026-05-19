jQuery(document).ready(function($) {
    $('.btn-cargar-mas').on('click', function(e) {
        e.preventDefault();
        var btn = $(this);
        var grid = $('#' + btn.data('target'));
        btn.prop('disabled', true).css('opacity', '0.5');
        function collectExcludeIds($grid) {
            var ids = [];
            $grid.find('[data-post-id]').each(function() {
                var el = $(this);
                var id = parseInt(el.data('post-id'));
                if (id && ids.indexOf(id) === -1) ids.push(id);
            });
            return ids;
        }

        var excludeIds = collectExcludeIds(grid);

        var postData = {
            action: 'cargar_mas_recursos',
            categoria: btn.data('categoria'),
            modulo: grid.data('modulo'),
            offset: parseInt(grid.data('offset')) || 0,
            exclude_ids: excludeIds
        };

        $.post(ajaxurl, postData, function(response) {
            if (response.success && response.data.html) {
                grid.append(response.data.html);
                var loaded = parseInt(response.data.loaded) || 6;
                grid.data('offset', parseInt(grid.data('offset') || 0) + loaded);
                if (response.data.has_more) {
                    btn.prop('disabled', false).css('opacity', '1');
                } else {
                    btn.fadeOut();
                }
            } else {
                btn.fadeOut();
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            btn.prop('disabled', false).css('opacity', '1');
            alert('Error al cargar recursos');
        });
    });
});
