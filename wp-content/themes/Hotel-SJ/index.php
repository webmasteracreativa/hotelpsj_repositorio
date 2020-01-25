<?php get_header(); ?>
<div class="">
	<!-- banner -->
	<section class="banner">
		<div class="carousel-banner">
			<?php
			    	// Argumentos para una busqueda de post type
			$args = array(
						'post_type' => 'banner_home', // Nombre del post type
						'order' => 'ASC'
					);
			$banners = new WP_Query($args);
			if ($banners->posts):
			      // Foreach para recorrer el resultado de la busqueda
				foreach ($banners->posts as $banner):
					$banner = wp_get_attachment_url( get_post_thumbnail_id($banner->ID, 'full') );
					?>
					<div class="c-home item" style="background: url(<?php echo $banner; ?>) no-repeat center center / cover;">
					</div>
					<?php
				endforeach;
			endif; 
			?>
		</div>
	</section>
	<!-- banner -->
	<!-- formulario -->
	<section class="form" id="form">
		<div class="container form-banner">
			<div class="row justify-content-center justify-content-md-start">
				<div class="separa bg-azul color-white col-12" id="separa">
					<h2 class="bg-dorado font-arabic d-none d-lg-block">
						Así nos califican, los que saben de hoteles en el mundo.
					</h2>
					<div class="row">
						<div class="col califi align-items-center p-0 justify-content-center d-none d-lg-flex">
							<img src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-srcset="<?php echo get_template_directory_uri(); ?>/img/tripadvisor-8.png" class="lazy">
							<div class="calificacion d-inline-block font-roboto">
								<span class="d-block">9,5</span>
								<small>sobre 10</small>
							</div>
						</div>
						<div class="col califi align-items-center p-0 justify-content-center d-none d-lg-flex">
							<img src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-srcset="<?php echo get_template_directory_uri(); ?>/img/despegar.png" class="lazy">
							<div class="calificacion d-inline-block font-roboto">
								<span class="d-block">9,8</span>
								<small>sobre 10</small>
							</div>
						</div>
						<div class="col califi align-items-center p-0 justify-content-center d-none d-lg-flex">
							<img src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-srcset="<?php echo get_template_directory_uri(); ?>/img/hoteles.png" class="lazy">
							<div class="calificacion d-inline-block font-roboto">
								<span class="d-block">9,1</span>
								<small>sobre 10</small>
							</div>
						</div>
						<div class="col califi align-items-center p-0 justify-content-center d-none d-lg-flex">
							<img src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-srcset="<?php echo get_template_directory_uri(); ?>/img/expedia-8.png" class="lazy">
							<div class="calificacion d-inline-block font-roboto">
								<span class="d-block">4,5</span>
								<small>sobre 5</small>
							</div>
						</div>
						<div class="col califi align-items-center p-0 justify-content-center d-none d-lg-flex">
							<img src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-srcset="<?php echo get_template_directory_uri(); ?>/img/booking-8.png" class="lazy">
							<div class="calificacion d-inline-block font-roboto">
								<span class="d-block">9,1</span>
								<small>sobre 10</small>
							</div>
						</div>
						<div class="col d-flex align-items-center p-0 justify-content-center font-arabic">
							<a href="http://secuream.e-gds.com/hotelelportondesanjoaquin/light/" target="_blank" class="btn color-azul bg-dorado">Reservar</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- formulario -->
	<!-- tripAdvisor -->
	<section class="py-5 color-gray">
		<div class="container">
			<div class="row align-items-stretch">
				<div class="col-12 text-center pb-5">
					<img src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-srcset="<?php echo get_template_directory_uri(); ?>/img/TripAdvisor.png" class="lazy">
				</div>
				<?php
		    	// Argumentos para una busqueda de post type
					$args = array(
					'post_type' => 'trip', // Nombre del post type
					'order' => 'ASC'
				);
				$trip = new WP_Query($args);
				if ($trip->posts):
		      // Foreach para recorrer el resultado de la busqueda
				foreach ($trip->posts as $tripad):
					$tripad_name = $tripad->post_title;
					$tripad_decs = $tripad->post_content;
					$tripad_person = $tripad->persona;
				?>
				<div class="col-md-4 item-trip">
					<a href="https://www.tripadvisor.co/Hotel_Review-g297478-d9798254-Reviews-Casa_Hotel_San_Joaquin-Medellin_Antioquia_Department.html" class="color-gray">
						<div class="star"></div>
						<h2><?php echo $tripad_name;?></h2>
						<p class="font-arabic">
							<?php echo $tripad_decs;?>
						</p>
						<h5><?php echo $tripad_person;?></h5>
					</a>
				</div>
				<?php
					endforeach;
					endif; 
				?>
			</div>
		</div>
	</section>
	<!-- tripAdvisor -->
	<!-- nosotros -->
	<section class="nosotros font-arabic nosotros-one container-fluid py-5 bg-azul color-white">
		<div class="row">
			<div class="offset-lg-6 col-lg-4 text-center pt-3 pb-5 py-lg-0">
				<h2 class="color-dorado">ACERCA DE NOSOTROS</h2>
				<div class="carousel font-roboto">
					<?php
		    	// Argumentos para una busqueda de post type
					$args = array(
					'post_type' => 'carrusel_nosotros', // Nombre del post type
					'order' => 'ASC'
				);
					$carousel_n = new WP_Query($args);
					if ($carousel_n->posts):
		      // Foreach para recorrer el resultado de la busqueda
						foreach ($carousel_n->posts as $carousel):
							$carousel_desc = $carousel->post_content;
							?>
							<div>
								<p class="color-gray"><?php echo $carousel_desc;?></p>
							</div>
							<?php
						endforeach;
					endif; 
					?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="offset-lg-1 col-lg-4 img-one d-flex justify-content-center">
				<img src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-srcset="<?php echo get_template_directory_uri(); ?>/img/nosotros1.png" class="lazy img-fluid">
			</div>
			<div class="col-lg-6 d-flex align-items-center">
				<div class="row text-center color-gray">
					<?php
				    	// Argumentos para una busqueda de post type
					$args = array(
							'post_type' => 'items_nosotros', // Nombre del post type
							'order' => 'ASC',
							'posicion' => 'negro'
						);
					$items_n = new WP_Query($args);
					if ($items_n->posts):
				      // Foreach para recorrer el resultado de la busqueda
						foreach ($items_n->posts as $item):
							$item_desc = $item->post_content;
							$item_img = wp_get_attachment_url( get_post_thumbnail_id($item->ID, 'full') );
							?>				
							<div class="col-md-4 item-icon">
								<img src="<?php echo $item_img; ?>" class="  ">
								<p><?php echo $item_desc;?></p>
							</div>
							<?php
						endforeach;
					endif; 
					?>				
				</div>
			</div>
		</div>
	</section>
	<section class="nosotros font-arabic nosotros-two container-fluid">
		<div class="row">
			<div class="offset-md-1 col-lg-6 text-center d-flex align-items-center order-1 order-lg-0">
				<div class="row color-gray">
					<?php
					    	// Argumentos para una busqueda de post type
					$args = array(
								'post_type' => 'items_nosotros', // Nombre del post type
								'order' => 'ASC',
								'posicion' => 'blanco'
							);
					$items_n = new WP_Query($args);
					if ($items_n->posts):
					      // Foreach para recorrer el resultado de la busqueda
						foreach ($items_n->posts as $item):
							$item_desc = $item->post_content;
							$item_img = wp_get_attachment_url( get_post_thumbnail_id($item->ID, 'full') );
							?>				
							<div class="col-md-4 item-icon">
								<img src="<?php echo $item_img; ?>" class="  ">
								<p><?php echo $item_desc;?></p>
							</div>
							<?php
						endforeach;
					endif; 
					?>	
				</div>
			</div>
			<div class="col-lg-5 pr-lg-0 img-two order-0 order-lg-1">
				<img src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-srcset="<?php echo get_template_directory_uri(); ?>/img/nosotros2.png" class="img-fluid lazy">
			</div>
		</div>
	</section>
	<!-- nosotros -->
	<!-- servicios -->
	<section class="servicios container-fluid pb-5" id="servicios">
		<div class="row">
			<div class="title-separate col-12 col-lg-6 text-center">
				<h2>Servicios</h2>
			</div>
		</div>
		<div class="row">
			<?php
		    	// Argumentos para una busqueda de post type
			$args = array(
					'post_type' => 'servicio_index', // Nombre del post type
					'order' => 'ASC'
				);
			$servicios = new WP_Query($args);
			if ($servicios->posts):
		      // Foreach para recorrer el resultado de la busqueda
				foreach ($servicios->posts as $servicio):
					$servicio_img = wp_get_attachment_url( get_post_thumbnail_id($servicio->ID, 'full') );
					$servicio_name = $servicio->post_title;
					$servicio_url = $servicio->url;
					?>
					
					<div class="col-6 col-md p-0 galeria-item d-flex">
						<a href="<?php echo $servicio_url; ?>">
							<img src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-srcset="<?php echo $servicio_img; ?>" class="w-100 lazy">
						</a>
						<div class="titulo">
							<a href="<?php echo $servicio_url; ?>">
								<p><?php echo $servicio_name;?></p>
							</a>
						</div>			
					</div>
					<?php
				endforeach;
			endif; 
			?>
		</div>
	</section>
	<!-- servicios -->
	<!-- galeria -->
	<section class="galeria container-fluid pb-5">
		<div class="row justify-content-end">
			<div class="title-separate col-12 col-md-6 text-center">
				<h2>Galeria</h2>
			</div>
		</div>
		<div class="row">
			<?php
		    	// Argumentos para una busqueda de post type
			$args = array(
					'post_type' => 'galeria', // Nombre del post type
					'order' => 'ASC'
				);
			$galerias = new WP_Query($args);
			if ($galerias->posts):
		      // Foreach para recorrer el resultado de la busqueda
				foreach ($galerias->posts as $galeria):
					$galeria_img = wp_get_attachment_url( get_post_thumbnail_id($galeria->ID, 'full') );
					$galeria_name = $galeria->post_title;
					switch ($galeria->post_name) {
				    case "restaurante":
				        $link = get_home_url().'/menu';
				        break;
				    case "bar":
				        $link = get_home_url().'/menu';
				        break;
				    case "instalaciones":
				        $link = get_home_url().'/salon';
				        break;
				    case "habitaciones-g":
				        $link = get_home_url().'/habitacion';
				        break;
					}
			?>
					<div class="col-6 col-md p-0 galeria-item d-flex">
						<a href="<?php echo $link; ?>" class="d-block w-100">
							<img src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-srcset="<?php echo $galeria_img; ?>" alt="" class="w-100 lazy">
						</a>
						<div class="titulo">
							<a href="<?php echo $link; ?>"><p><?php echo $galeria_name;?></p></a>
						</div>
					</div>
					<?php
				endforeach;
			endif; 
			?>
		</div>
	</section>
	<!-- /galeria -->
</div>
<?php get_footer(); ?>