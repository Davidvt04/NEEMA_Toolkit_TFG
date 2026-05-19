/**
 * Gestión de favoritos - Toggle de recursos favoritos
 */
(function($) {
    'use strict';
    $(document).on('click', '.fa-bookmark, .fa-bookmark-o', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const $icon = $(this);
        const postId = $icon.data('post-id');

        if (typeof neemaFavoritos === 'undefined') {
            alert('Error: variables de configuración no encontradas');
            return;
        }
        if (!neemaFavoritos.is_logged) {
            const $modal = $('#modal-login-favoritos').length ? $('#modal-login-favoritos') : $('#modal-login');
            if ($modal.length) {
                $modal.addClass('active');
            } else {
                alert('Debes iniciar sesión para guardar recursos favoritos.');
            }
            return;
        }

        toggleFavorito($icon, postId);
    });

    function toggleFavorito($icon, postId) {

        $.ajax({
            url: neemaFavoritos.ajax_url,
            type: 'POST',
            data: {
                action: 'neema_toggle_favorito',
                post_id: postId,
                nonce: neemaFavoritos.nonce
            },
            beforeSend: function() {
                $icon.css('opacity', '0.5');
            },
            success: function(response) {
                if (response.success) {
                    const newFavorited = response.data.favorited;
                    
                    if (newFavorited) {
                        $icon.removeClass('fa-bookmark-o').addClass('fa-bookmark');
                    } else {
                        $icon.removeClass('fa-bookmark').addClass('fa-bookmark-o');
                    }
                    
                    $icon.data('favorited', newFavorited ? 1 : 0);
                    $icon.attr('data-favorited', newFavorited ? '1' : '0');
                    $icon.css('opacity', '1');
                    const $textLabel = $icon.siblings('.recurso-bookmark-text');
                    if ($textLabel.length) {
                        $textLabel.text(newFavorited ? neemaFavoritos.text_guardado : neemaFavoritos.text_guardar);
                    }
                } else {
                    alert(response.data.message || 'Error al actualizar favorito');
                    $icon.css('opacity', '1');
                }
            },
            error: function() {
                alert('Error al conectar con el servidor');
                $icon.css('opacity', '1');
            }
        });
    }

})(jQuery);
