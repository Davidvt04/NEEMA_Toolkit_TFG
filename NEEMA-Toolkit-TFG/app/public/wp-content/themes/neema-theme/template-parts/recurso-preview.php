<?php
$data = get_query_var('recurso_data');
if ( ! $data ) return;
?>

<article class="recurso-preview">
    <a href="<?php echo esc_url( $data['link']."?lang=".pll_current_language() ); ?>" class="recurso-link">
        
        <?php if ( ! empty($data['thumbnail']) ) : ?>
            <div class="recurso-thumb">
                <?php echo $data['thumbnail']; ?>
            </div>
        <?php endif; ?>

        <div class="recurso-blue-box">
            <?php echo $data['tipo_icon']; ?>
            <div class="recurso-languages">
                <?php foreach ( $data['languages'] as $lang ) : ?>
                    <span class="recurso-language"><?php echo esc_html( $lang ); ?></span>
                <?php endforeach; ?>
            </div>
        </div>

        <h4 class="recurso-preview-title"><?php echo esc_html( $data['title'] ); ?></h4>

    </a>
    
    <i class="fa <?php echo $data['favorited'] ? 'fa-bookmark' : 'fa-bookmark-o'; ?>" data-post-id="<?php echo esc_attr( $data['id'] ); ?>" data-favorited="<?php echo $data['favorited'] ? '1' : '0'; ?>"></i>
</article>
