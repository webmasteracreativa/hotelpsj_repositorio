<?php get_header(); ?>
<div class="servicios pb-5">
	<?php
	    	// Argumentos para una busqueda de post type
	$args = array(
				'post_type' => 'banner_interna', // Nombre del post type
				'order' => 'ASC',
				'banners_interna' => 'Servicios'
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
	<?php
		    	// Argumentos para una busqueda de post type
		$args = array(
					'post_type' => 'servicio', // Nombre del post type
					'order' => 'ASC',
				);
		$servicios = new WP_Query($args);
		if ($servicios->posts):
	?>
	<section class="container-fluid">
	<?php
  	// Foreach para recorrer el resultado de la busqueda
		foreach ($servicios->posts as $servicio):
 	 	 	$servicio_name = $servicio->post_title;
 	 	 	$servicio_desc = $servicio->post_content;
 	 	 	$servicio_icono = $servicio->icono['guid'];
			$servicio_img = wp_get_attachment_url( get_post_thumbnail_id($servicio->ID, 'full') );
	?>
		<div class="row servicio">
			<div class="col-md-6 s-img p-0">
				<img src="<?php echo $servicio_img?>" class="w-100">
			</div>
			<div class="col-md-6 s-text font-arabic d-flex justify-content-center flex-column text-center">
				<div class="row">
					<div class="col-xl-6 col-md-8 py-5">
						<img src="<?php echo $servicio_icono; ?>" class="img-fluid">
						<div class="font-upper">
							<h2><?php echo $servicio_name; ?></h2>
						</div>
						<p><?php echo $servicio_desc; ?></p>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	</section>
	<?php endif; ?>
</div>
<?php get_footer(); ?>
