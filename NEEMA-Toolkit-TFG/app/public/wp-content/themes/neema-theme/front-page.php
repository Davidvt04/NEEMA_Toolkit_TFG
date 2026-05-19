<?php
/* Template Name: Front Page */

function getNeemaTeamUrl() {
    $current_language = pll_current_language();
    if ($current_language === 'es') {
        return 'https://ulysseus.eu/es/neema/equipo/';
    } else if ($current_language === 'en') {
        return 'https://ulysseus.eu/neema/team/';
    } else{
        return 'https://ulysseus.eu/fr/neema/equipe/';
    }
}

if (!function_exists('neema_get_localized_value')) {
  function neema_get_localized_value($es, $en, $fr) {
    $lang = function_exists('neema_get_current_lang') ? neema_get_current_lang() : 'es';

    if ($lang === 'en' && !empty($en)) {
      return $en;
    }

    if ($lang === 'fr' && !empty($fr)) {
      return $fr;
    }

    if (!empty($es)) {
      return $es;
    }

    if (!empty($en)) {
      return $en;
    }

    return $fr;
  }
}

get_header();
?>

<main class="home-main">
  <section class="home-hero1">
    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/hands.jpg" class="hero-img"/>
    <div class="hero-content">
      <h1 class="hero-title"><?php pll_e('¡Bienvenido<br> a la Toolkit de<br> NEEMA!'); ?></h1>
      <img class="hero-icon" src="<?php echo get_template_directory_uri(); ?>/assets/images/cofunded_erasmus.png"/>
    </div>
    <div class="front-page-triangle1"></div>
  </section>
<section class="home-information">
  <div class="info-text">
    <h2 class="info-title-general"><?php pll_e("Qué es la caja de herramientas"); ?></h2>

    <div class="info-block block-1">
      <img src="<?php echo get_template_directory_uri(); ?>/assets/images/africa_map_1.jpg" class="info-icon"/>
      <div class="info-content">
        <p class="info-paragraph">
          <?php pll_e("<b>La Caja de Herramientas</b> o <b>Toolkit</b> es un gestor de contenidos diseñado para consultar y crear formaciones, proyectos, informes, normativas y otros recursos de Seguridad Alimentaria. Reúne documentos, bases de datos, estadísticas, casos prácticos y más, organizados de forma intuitiva para facilitar el trabajo de los usuarios y aportar eficiencia e innovación."); ?>
        </p>
      </div>
    </div>

    <div class="info-block block-2">
      <img src="<?php echo get_template_directory_uri(); ?>/assets/images/neema_food_2.jpg" class="info-logo"/>
      <div class="info-content">
        <p class="info-paragraph">
          <?php pll_e("La <b>Toolkit</b>, parte del Proyecto NEEMA de la Universidad de Sevilla financiado por ERASMUS+, busca adaptar el Pacto Verde Europeo y la Estrategia “De la Granja a la Mesa” al Sahel y África Occidental, promoviendo resiliencia alimentaria y nutricional frente a sequías, conflictos y cambio climático."); ?>
        </p>
      </div>  
    </div>
    <div class="neema-redirect">
      <img src="<?php echo get_template_directory_uri(); ?>/assets/images/neema_logo.png" class="neema-logo"/>
      <p class="info-paragraph"><?php pll_e('¿Quieres saber más sobre el proyecto NEEMA?'); ?></p>
      <a href="https://ulysseus.eu/es/neema/" target="_blank" rel="noopener" class="redirect-link">
        <?php pll_e('Visita la web de NEEMA'); ?>
      </a>

    </div>
</section>



<section class="home-members">
    <div class="front-page-triangle2"></div>
  <img src="<?php echo get_template_directory_uri(); ?>/assets/images/jungles-south-africa.jpg" class="members-back-img"/>
  <div class="members-content">
    <h2 class="members-title"><?php pll_e('Quiénes somos'); ?></h2>

    <div class="members-list-container">
      <div class="members-list">
        <?php
          $miembros = new WP_Query(array(
            'post_type' => 'miembro',
            'posts_per_page' => -1,
            'meta_key' => '_member_order',
            'orderby' => array(
                'meta_value_num' => 'ASC',
                'title' => 'ASC'
            ),
            'order' => 'ASC'
          ));
        if ($miembros->have_posts()) :
          while ($miembros->have_posts()) : $miembros->the_post();
            $member_url = get_post_meta(get_the_ID(), '_member_url', true);
            $member_title = get_the_title();
            $member_description = neema_get_localized_value(
              get_post_meta(get_the_ID(), '_member_description_es', true),
              get_post_meta(get_the_ID(), '_member_description_en', true),
              get_post_meta(get_the_ID(), '_member_description_fr', true)
            );
            $member_entities = get_post_meta(get_the_ID(), '_member_entities', true);
            if (!is_array($member_entities)) {
              $member_entities = array();
            }
            $member_entities_panel_id = 'member-entities-' . get_the_ID();
        ?>
            <div class="member-row">
              <div class="member-main-row<?php echo !empty($member_entities) ? ' is-collapsible' : ''; ?>">
                <?php if (has_post_thumbnail()) : ?>
                  <?php the_post_thumbnail('medium', array('class' => 'member-photo member-photo-inline')); ?>
                <?php endif; ?>

                <div class="member-main-content">
                  <div class="member-title"><?php echo esc_html($member_title); ?></div>
                  <?php if (!empty($member_url)) : ?>
                    <a href="<?php echo esc_url($member_url); ?>" target="_blank" rel="noopener" class="member-url-link"><?php echo esc_html($member_url); ?></a>
                  <?php endif; ?>
                  <?php if (!empty($member_description)) : ?>
                    <p class="member-description"><?php echo esc_html($member_description); ?></p>
                  <?php endif; ?>
                </div>

                <?php if (!empty($member_entities)) : ?>
                  <button
                    type="button"
                    class="member-entities-trigger"
                    aria-expanded="false"
                    aria-controls="<?php echo esc_attr($member_entities_panel_id); ?>"
                  >
                    <span class="member-entities-arrow" aria-hidden="true">▾</span>
                  </button>
                <?php endif; ?>
              </div>

              <?php if (!empty($member_entities)) : ?>
                <div class="member-entities-panel" id="<?php echo esc_attr($member_entities_panel_id); ?>" hidden>
                  <div class="member-entities-list">
                    <?php foreach ($member_entities as $entity_index => $entity) :
                      $entity_title = isset($entity['title']) ? $entity['title'] : '';
                      $entity_url = isset($entity['url']) ? $entity['url'] : '';
                      $entity_image = isset($entity['image']) ? $entity['image'] : '';
                      $entity_description = neema_get_localized_value(
                        isset($entity['description_es']) ? $entity['description_es'] : '',
                        isset($entity['description_en']) ? $entity['description_en'] : '',
                        isset($entity['description_fr']) ? $entity['description_fr'] : ''
                      );
                      $participants = (isset($entity['participants']) && is_array($entity['participants'])) ? $entity['participants'] : array();
                      $entity_participants_panel_id = 'entity-participants-' . get_the_ID() . '-' . $entity_index;
                    ?>
                      <div class="member-entity-row">
                        <?php if (!empty($participants)) : ?>
                          <div class="member-entity-header">
                            <div class="member-entity-line">
                              <?php if (!empty($entity_image)) : ?>
                                <img src="<?php echo esc_url($entity_image); ?>" alt="<?php echo esc_attr($entity_title); ?>" class="entity-photo-inline" />
                              <?php endif; ?>

                              <div class="entity-content">
                                <span class="entity-title"><?php echo esc_html($entity_title); ?></span>

                                <?php if (!empty($entity_url)) : ?>
                                  <a href="<?php echo esc_url($entity_url); ?>" target="_blank" rel="noopener" class="entity-url-link"><?php echo esc_html($entity_url); ?></a>
                                <?php endif; ?>

                                <?php if (!empty($entity_description)) : ?>
                                  <p class="entity-description"><?php echo esc_html($entity_description); ?></p>
                                <?php endif; ?>
                              </div>
                            </div>
                            <button
                              type="button"
                              class="entity-participants-trigger"
                              aria-expanded="false"
                              aria-controls="<?php echo esc_attr($entity_participants_panel_id); ?>"
                            >
                              <span class="entity-participants-arrow" aria-hidden="true">▾</span>
                            </button>
                          </div>
                          <div class="member-participants-panel" id="<?php echo esc_attr($entity_participants_panel_id); ?>" hidden>
                            <div class="member-participants-list">
                              <?php foreach ($participants as $participant) :
                                $participant_title = isset($participant['title']) ? $participant['title'] : '';
                                $participant_url = isset($participant['url']) ? $participant['url'] : '';
                                $participant_image = isset($participant['image']) ? $participant['image'] : '';
                                $participant_description = neema_get_localized_value(
                                  isset($participant['description_es']) ? $participant['description_es'] : '',
                                  isset($participant['description_en']) ? $participant['description_en'] : '',
                                  isset($participant['description_fr']) ? $participant['description_fr'] : ''
                                );
                              ?>
                                <div class="member-participant-row">
                                  <?php if (!empty($participant_image)) : ?>
                                    <img src="<?php echo esc_url($participant_image); ?>" alt="<?php echo esc_attr($participant_title); ?>" class="participant-photo-inline" />
                                  <?php endif; ?>

                                  <div class="participant-content">
                                    <span class="participant-title"><?php echo esc_html($participant_title); ?></span>

                                    <?php if (!empty($participant_url)) : ?>
                                      <a href="<?php echo esc_url($participant_url); ?>" target="_blank" rel="noopener" class="participant-url-link"><?php echo esc_html($participant_url); ?></a>
                                    <?php endif; ?>

                                    <?php if (!empty($participant_description)) : ?>
                                      <p class="participant-description"><?php echo esc_html($participant_description); ?></p>
                                    <?php endif; ?>
                                  </div>
                                </div>
                              <?php endforeach; ?>
                            </div>
                          </div>
                        <?php else : ?>
                          <div class="member-entity-line">
                            <?php if (!empty($entity_image)) : ?>
                              <img src="<?php echo esc_url($entity_image); ?>" alt="<?php echo esc_attr($entity_title); ?>" class="entity-photo-inline" />
                            <?php endif; ?>

                            <div class="entity-content">
                              <span class="entity-title"><?php echo esc_html($entity_title); ?></span>

                              <?php if (!empty($entity_url)) : ?>
                                <a href="<?php echo esc_url($entity_url); ?>" target="_blank" rel="noopener" class="entity-url-link"><?php echo esc_html($entity_url); ?></a>
                              <?php endif; ?>

                              <?php if (!empty($entity_description)) : ?>
                                <p class="entity-description"><?php echo esc_html($entity_description); ?></p>
                              <?php endif; ?>
                            </div>
                          </div>
                        <?php endif; ?>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>
            </div>
        <?php
          endwhile;
          wp_reset_postdata();
        endif;
        ?>
      </div>
    </div>
  </div>
  <?php get_template_part('template-parts/funding-statement'); ?>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const triggers = document.querySelectorAll('.member-entities-trigger, .entity-participants-trigger');

  const shouldIgnoreToggleClick = function (target, scopeElement) {
    if (!scopeElement || !target) {
      return false;
    }

    if (target.closest('a') && scopeElement.contains(target.closest('a'))) {
      return true;
    }

    if (target.closest('button') && scopeElement.contains(target.closest('button'))) {
      return true;
    }

    return false;
  };

  document.querySelectorAll('.member-main-row').forEach(function (row) {
    row.addEventListener('click', function (event) {
      if (shouldIgnoreToggleClick(event.target, row)) {
        return;
      }

      const trigger = row.querySelector('.member-entities-trigger');
      if (trigger) {
        trigger.click();
      }
    });
  });

  document.querySelectorAll('.member-entity-header').forEach(function (header) {
    header.addEventListener('click', function (event) {
      if (shouldIgnoreToggleClick(event.target, header)) {
        return;
      }

      const trigger = header.querySelector('.entity-participants-trigger');
      if (trigger) {
        trigger.click();
      }
    });
  });

  triggers.forEach(function (trigger) {
    const panelId = trigger.getAttribute('aria-controls');
    const panel = panelId ? document.getElementById(panelId) : null;

    if (!panel) {
      return;
    }

    trigger.addEventListener('click', function () {
      const isOpen = trigger.getAttribute('aria-expanded') === 'true';

      if (isOpen) {
        panel.style.maxHeight = panel.scrollHeight + 'px';
        panel.style.opacity = '1';

        requestAnimationFrame(function () {
          panel.style.maxHeight = '0px';
          panel.style.opacity = '0';
        });

        const onCloseEnd = function (event) {
          if (event.propertyName !== 'max-height') {
            return;
          }

          panel.hidden = true;
          panel.removeEventListener('transitionend', onCloseEnd);
        };

        panel.addEventListener('transitionend', onCloseEnd);
        trigger.setAttribute('aria-expanded', 'false');
        return;
      }

      panel.hidden = false;
      panel.style.maxHeight = '0px';
      panel.style.opacity = '0';

      requestAnimationFrame(function () {
        panel.style.maxHeight = panel.scrollHeight + 'px';
        panel.style.opacity = '1';
      });

      const onOpenEnd = function (event) {
        if (event.propertyName !== 'max-height') {
          return;
        }

        panel.style.maxHeight = 'none';
        panel.removeEventListener('transitionend', onOpenEnd);
      };

      panel.addEventListener('transitionend', onOpenEnd);
      trigger.setAttribute('aria-expanded', 'true');
    });
  });
});
</script>
</main>
<?php get_footer(); ?>
