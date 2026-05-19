
<section class="footer-hero">
  <?php get_template_part('template-parts/page-summary'); ?>
  <div class="footer-triangle"></div>
  <div class="footer-triangle-image">
    <img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/cofunded_erasmus.png' ) ); ?>" alt="Co-funded by Erasmus+">
  </div>
</section>

<footer class="site-footer">
  <!-- Contenido del footer (rectángulo) -->
  <div class="footer-content">
  <div class="footer-social">
    <a href="https://www.facebook.com/UlysseusEuropeanUniversity" target="_blank"><i class="fab fa-facebook-f"></i></a>
    <a href="https://x.com/Ulysseus_eu" target="_blank"><i class="fab fa-x-twitter"></i></a>
    <a href="https://www.instagram.com/ulysseus_eu/" target="_blank"><i class="fab fa-instagram"></i></a>
    <a href="https://www.linkedin.com/company/ulysseus-european-university/" target="_blank"><i class="fab fa-linkedin-in"></i></a>
    <a href="https://www.youtube.com/channel/UC07KaFnzR2iNI1xalYeLecg" target="_blank"><i class="fa-brands fa-youtube"></i></a>
    <a href="https://ulysseus.eu/podcast/" target="_blank"><i class="fa-brands fa-spotify"></i></a>
    <a href="https://ulysseusuniversity.sharepoint.com/" target="_blank"><i class="fa-brands fa-microsoft"></i></a>
    <a href="https://social.ulysseus.eu" target="_blank"><i class="fab fa-mastodon"></i></a>
  
  </div>
  <p>
    <a href="mailto:neematoolkit@us.es" class="footer-email">neematoolkit@us.es</a>
  </p>
    <div class="footer-legal">
      <b class="footer-copyright">© <?php echo date('Y'); ?> NEEMA Ulysseus</b>
      <span class="footer-credit"><?php echo neema_translate('Imágenes de la web diseñadas por'); ?> <a href="https://www.freepik.com" target="_blank" rel="noopener noreferrer">Freepik</a></span>
    </div>
  </div>

</footer>


<?php wp_footer(); ?>
</body>
</html>
