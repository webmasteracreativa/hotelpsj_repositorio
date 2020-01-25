<?php get_header(); ?>
<div class="galeria pb-5">
  <?php
  // Argumentos para una busqueda de post type
    $args = array(
      'post_type' => 'banner_interna', // Nombre del post type
      'order' => 'ASC',
      'banners_interna' => 'Galeria'
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
	<div class="container">
	<?php
	// Argumentos para una busqueda de post type
		$args = array(
			'post_type' => 'galeria', // Nombre del post type
			'order' => 'ASC'
		);
		$galerias = new WP_Query($args);
		if ($galerias->posts):
	?>
		<div class="button-group filters">
			<button class="button is-checked" data-filter="*">VER TODOS</button>
				<?php  
					// Foreach para recorrer el resultado de la busqueda
					foreach ($galerias->posts as $galeria):
						$galeria_name = $galeria->post_title;
						$galeria_slug = $galeria->post_name;
				?>
						<button class="button" data-filter=".<?php echo $galeria_slug; ?>"><?php echo $galeria_name; ?></button>
		<?php
			endforeach;
		?>
			</ul>
		</div>
	<?php  
		endif; 
	?>
	
	<?php
		$args = array(
			'post_type' => 'galeria', // Nombre del post type
			'order' => 'ASC'
		);
		$galerias = new WP_Query($args);
		
		if ($galerias->posts):
	?>
		<div class="grid grid-galery">
	<?php
			foreach ($galerias->posts as $galeria):
				$galeria_name = $galeria->post_title;
				$galeria_slug = $galeria->post_name;
				$argsImages = array(
			        'post_parent'    => $galeria->ID,
			        'post_type'      => 'attachment',
			        'numberposts'    => -1, // show all
			        'post_status'    => 'any',
			        'post_mime_type' => 'image',
			        'exclude'        =>  get_post_thumbnail_id($galeria->ID),
			        'orderby'        => 'menu_order',
			        'order'          => 'ASC'
		       	);
				$images = get_posts($argsImages);
				if($images):
					foreach($images as $image):						
	?>
						<div class="grid-item <?php echo $galeria_slug; ?>" data-category="<?php echo $galeria_slug; ?>">
							<a data-fancybox="<?php echo $galeria_slug; ?>" href="<?php echo $image->guid; ?>">
								<img src="<?php echo $image->guid; ?>" class="w-100">
							</a>
						</div>
	<?php  
					endforeach;
				endif;
			endforeach;
	?>
		</div>
	<?php  
		endif;
	?>
	</div>
</div>
<?php get_footer(); ?>