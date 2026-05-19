<?php
if ( is_user_logged_in() ) :
  $current_user = wp_get_current_user();
  $user_roles = $current_user->roles;
  $menu_id = 'user-session-menu-' . uniqid();
?>
<div class="user-session-menu" id="<?php echo $menu_id; ?>" style="display: none;">
  <div class="session-menu-header">
    <span class="session-user-name"><?php echo neema_translate('Hola') . ', ' . esc_html( $current_user->display_name ); ?></span>
    <button class="session-menu-close" type="button" aria-label="<?php echo neema_translate('Cerrar'); ?>">
      <i class="fa fa-times"></i>
    </button>
  </div>
  
  <div class="session-menu-options">
    <a href="<?php echo home_url('/perfil-' . pll_current_language()); ?>" class="session-menu-item">
      <i class="fa fa-user-circle"></i> <?php echo neema_translate('Mi Perfil'); ?>
    </a>
    <a href="<?php echo get_link_guardados(); ?>" class="session-menu-item">
      <i class="fa fa-bookmark"></i> <?php echo neema_translate('Guardados'); ?>
    </a>
    
    <?php if ( in_array('gestor_contenido', $user_roles) || in_array('administrator', $user_roles) || in_array('neema_admin', $user_roles)) : ?>
      <a href="<?php echo admin_url(); ?>" class="session-menu-item">
        <i class="fa fa-cog"></i> <?php echo neema_translate('Panel de Administración'); ?>
      </a>
    <?php endif; ?>

    <?php if ( in_array('gestor_contenido', $user_roles) ) : ?>
      <a href="<?php echo admin_url('edit.php?post_type=recurso'); ?>" class="session-menu-item">
        <i class="fa-solid fa-file-lines"></i> <?php echo neema_translate('Mis Recursos'); ?>
      </a>
    <?php endif; ?>
    
    <a href="<?php echo wp_logout_url( home_url() ); ?>" class="session-menu-item session-logout">
      <i class="fa fa-sign-out"></i> <?php echo neema_translate('Cerrar Sesión'); ?>
    </a>
  </div>
</div>

<script>
(function() {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initUserMenu);
  } else {
    initUserMenu();
  }
  
    function initUserMenu() {
      const menuId = '<?php echo $menu_id; ?>';
      const menu = document.getElementById(menuId);
      const trigger = menu ? menu.previousElementSibling : null;
      const closeBtn = menu ? menu.querySelector('.session-menu-close') : null;

      if (trigger) {
        trigger.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();

          document.querySelectorAll('.user-session-menu').forEach(function(m) {
            if (m !== menu) {
              m.style.display = 'none';
              const otherTrigger = m.previousElementSibling;
              if (otherTrigger && otherTrigger.classList) otherTrigger.classList.remove('open');
            }
          });

          if (menu.style.display === 'none' || menu.style.display === '') {
            menu.style.display = 'block';
            trigger.classList.add('open');
          } else {
            menu.style.display = 'none';
            trigger.classList.remove('open');
          }
        });
      }

      if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          if (menu) menu.style.display = 'none';
          if (trigger && trigger.classList) trigger.classList.remove('open');
        });
      }

      document.addEventListener('click', function(e) {
        if (menu && trigger && !menu.contains(e.target) && !trigger.contains(e.target)) {
          menu.style.display = 'none';
          if (trigger.classList) trigger.classList.remove('open');
        }
      });

      if (menu) {
        menu.addEventListener('click', function(e) {
          e.stopPropagation();
        });
      }
    }
})();
</script>
<?php endif; ?>
