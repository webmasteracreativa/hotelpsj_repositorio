<?php get_header(); ?>
<div class="quienes-somos pb-5">
  <?php
        // Argumentos para una busqueda de post type
    $args = array(
          'post_type' => 'banner_interna', // Nombre del post type
          'order' => 'ASC',
          'banners_interna' => 'Quienes somos'
        );
    $banners = new WP_Query($args);
    if ($banners->posts):
          // Foreach para recorrer el resultado de la busqueda
      foreach ($banners->posts as $banner):
        $banner_name = $banner->post_title;
        $banner = wp_get_attachment_url( get_post_thumbnail_id($banner->ID, 'full') );
  ?>
        <section class="banner-interna container-fluid d-flex justify-content-center align-items-center font-arabic font-upper" style="background: url(<?php echo $banner; ?>) no-repeat center center / cover;">
          <h1><?php echo $banner_name;?></h1>
        </section>
  <?php
      endforeach;
    endif; 
  ?>
  <?php if (have_posts()): while (have_posts()) : the_post(); ?>
    <?php the_content(); ?>
  <?php endwhile; ?>
  <?php else: ?>
    <h2><?php _e( 'Sorry, nothing to display.', 'html5blank' ); ?></h2>
  <?php endif; ?>
</div>
<?php get_footer(); ?>
