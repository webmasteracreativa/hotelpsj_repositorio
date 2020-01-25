<?php get_header(); ?>
<div class="habitaciones pb-5">

	<?php
	    	// Argumentos para una busqueda de post type
	$args = array(
				'post_type' => 'banner_interna', // Nombre del post type
				'order' => 'ASC',
				'banners_interna' => 'Alojamiento'
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
	<section class="container ">
		<?php  
			$torres = get_terms( array(
    		'taxonomy' => 'torre',
    		'hide_empty' => false,
    	));
		?>
		<ul class="nav nav-tabs tabs-habitacion">
			<?php
				$i = 0;
				foreach ($torres as $torre):		
			?>
				<li class="nav-item">
					<a class="nav-link <?php echo ($i == 0) ? 'active' : ''; ?>" data-toggle="tab" href="#<?php echo $torre->slug; ?>"><?php echo $torre->name; ?></a>
				</li>
			<?php
			 $i ++;
				endforeach;
			?>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<?php  
				$torres = get_terms( array(
	    		'taxonomy' => 'torre',
	    		'hide_empty' => false,
	    	));
	    	$j = 0;
	    	foreach ($torres as $torre):
			?>
				<div class="tab-pane container <?php echo ($j == 0) ? 'active' : ''; ?> ?>" id="<?php echo $torre->slug; ?>">
				<?php
        // Argumentos para una busqueda de post type
	        $args = array(
	          'post_type' => 'habitacion', // Nombre del post type
	          'order' => 'ASC',
	          'torre' => $torre->slug
	        );
        	$habitaciones = new WP_Query($args);
        	if ($habitaciones->posts):
        	// Foreach para recorrer el resultado de la busqueda
          	foreach ($habitaciones->posts as $habitacion):
	            $habitacion_name = $habitacion->post_title;
	            $habitacion_desc = $habitacion->post_content;
      	?>
							<div class="row habitacion py-3 color-gray">
								<div class="col-md-7 s-img p-0">
									<?php  
										$argsImages = array(
							        'post_parent'    => $habitacion->ID,
							        'post_type'      => 'attachment',
							        'numberposts'    => -1, // show all
							        'post_mime_type' => 'image',
							        'exclude'        =>  get_post_thumbnail_id($habitacion->ID),
							        'orderby'        => 'menu_order',
							        'order'          => 'ASC'
		       					);
										$images = get_posts($argsImages);
										if($images):
									?>
									<div class="carousel-habi">
										<?php  
											foreach($images as $image):
										?>
												<div class="item">
													<img src="<?php echo $image->guid; ?>" class="w-100">
												</div>
										<?php  
											endforeach;
										?>
									</div>
								<?php endif; ?>
								</div>
								<div class="col-md-5 s-text font-arabic d-flex justify-content-center flex-column">
									<div class="font-upper">
										<h2><a href="http://secuream.e-gds.com/hotelelportondesanjoaquin"><?php echo $habitacion_name;?></a></h2>
									</div>
									<div class="text font-roboto">
										<p>
											<?php echo $habitacion_desc;?>								
										</p>
										<a href="http://secuream.e-gds.com/hotelelportondesanjoaquin" class="btn bg-dorado">Reservar</a>
									</div>
									<div class="torre font-upper">
										<p><?php echo $torre->name; ?></p>
									</div>
								</div>
							</div>
						<?php  
							endforeach;
						endif;
						?>
				</div>
			<?php  
				$j ++;
				endforeach;
			?>
		</div>
	</section>
</div>
<?php get_footer(); ?>
