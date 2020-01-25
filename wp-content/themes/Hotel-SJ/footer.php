			<!-- Formulario -->
			<section class="formulario container-fluid bg-white" id="contacto">
				<div class="row">
					<div class="offset-md-0 col-md-6 offset-lg-2 col-lg-4 pb-3">
						<h2>
							UBICACIÓN
						</h2>
						<div class="form">
							<div role="main" id="contacto-sitio-web-569d5c4ed4956655d181"></div>
							<script type="text/javascript" src="https://d335luupugsy2.cloudfront.net/js/rdstation-forms/stable/rdstation-forms.min.js"></script>
							<script type="text/javascript">
							 new RDStationForms('contacto-sitio-web-569d5c4ed4956655d181-html', 'UA-146093982-1').createForm();
							</script>
						</div>
					</div>
					<div class="col-md-6 p-0">
						<div class='v-and-m-responsive'>
							<div id="map" class="map-js"></div>
						</div>
					</div>
				</div>
			</section>
			<!-- Formulario -->
			<!-- footer -->
			<div class="container-fluid">
				<div class="row pt-5">
					<div class="col-md-12 font-arabic font-upper text-center py-3 title-footer">
						<h2>Sellos de certificación y gremios asociados</h2>
					</div>
				</div>
			</div>
				<div class="container">
				<div class="row pb-5">
					<div class="col-md-12">
						<div class="footer-carousel">
						<?php
						    	// Argumentos para una busqueda de post type
						$args = array(
									'post_type' => 'corousel-footer', // Nombre del post type
									'order' => 'ASC'
								);
						$f_carouse = new WP_Query($args);
						if ($f_carouse->posts):
						      // Foreach para recorrer el resultado de la busqueda
							foreach ($f_carouse->posts as $f_carousel):
								$f_carousel_img = wp_get_attachment_url( get_post_thumbnail_id($f_carousel->ID, 'full') );
						?>
							<img src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-srcset="<?php echo $f_carousel_img; ?>" class="img-fluid lazy">
						<?php
						endforeach;
						endif; 
						?>
						</div>
					</div>				
				</div>
			</div>
			<a href="https://api.whatsapp.com/send?phone=573147931996&text=Hola%2c%20me%20gustar%c3%ada%20saber%20m%c3%a1s%20informaci%C3%B3n%20sobre%20el%20hotel." class="wp" target="_blank">
				<img src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-srcset="<?php echo get_template_directory_uri(); ?>/img/wp.png" class="img-fluid lazy">
			</a>
			<footer class="footer py-5" role="contentinfo">
				<div class="container">
					<div class="row color-white">
						<div class="col-md-1 p-md-0 col-lg-2 d-flex align-items-center justify-content-center">
							<img src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-srcset="<?php echo get_template_directory_uri(); ?>/img/logo-footer.png" alt="logo" class="img-fluid lazy">
						</div>
						<div class="col-md-5 col-lg-4 datos">
							<p class="ubi">Calle 42 # 68a - 32 <span class="d-block">Medellín - Colombia</span></p>
							<p class="tel">+57 (4) 4444008</p>
							<p class="email">reservas@hotelportonsj.com.co</p>
							<p class="chat">M.Me/hotelportondesanjoaquin</p>
						</div>
						<div class="col-md-2 menu-footer p-md-0">
							<?php html5blank_nav(); ?>
						</div>
						<div class="col-md-4">
							<h4>PODEMOS MANTENERTE INFORMADO</h4>
							<p class="color-dorado">Entérate de nuestras ofertas y eventos.</p>
							<?php 
	                    // Argumentos para una busqueda de post type
							$args = array(
								'post_type' => 'footer-form', // Nombre del post type
								'order' => 'ASC'
							);
							$correos = new WP_Query($args);
							if ($correos->posts):
	                      // Foreach para recorrer el resultado de la busqueda
								foreach ($correos->posts as $correo):
									$correo_desc = $correo->code;
									?>
									<?php
								endforeach;
								endif; 
								?>
							<div class="form-footer">
								<?php echo $correo_desc;?>
							</div>
							<ul class="redes">
								<li>
									<a href="https://www.facebook.com/elportonsj/">
										<img src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-srcset="<?php echo get_template_directory_uri(); ?>/img/fb.png" alt="facebook" class="img-fluid lazy">
									</a>
								</li>
								<li>
									<a href="https://www.facebook.com/elportonsj/">
										<img src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-srcset="<?php echo get_template_directory_uri(); ?>/img/yt.png" alt="facebook" class="img-fluid lazy">
									</a>
								<li>
								</li>
									<a href="https://www.instagram.com/elportonsj/">
										<img src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-srcset="<?php echo get_template_directory_uri(); ?>/img/ig.png" alt="instagram" class="img-fluid lazy">
									</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="col-12 color-white d-flex justify-content-between pt-3 mt-3 copy flex-column flex-lg-row">
						<div class="rnt">RNT 12993</div>
						<p> 
							<a href="http://hotelportonsj.com.co/">www.hotelportonsj.com.co</a>
						</p>
						<p>
							HOTELPORTONDESANJOAQUÍN©  |  TODOS LOS DERECHOS RESERVADOS  |  2019
						</p>
					</div>
				</div>
			</footer>
			<!-- /footer -->
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" async/>
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" async/>
			<link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,700&display=swap" rel="preload" async/>
			<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" async/>	
	 		<?php wp_footer(); ?>
			<script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
			<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
			<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD2tg95tRLV0b3omGIVAB3gD7WXVyjCNSU"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
			<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
			<script>
	        conditionizr.config({
	        	assets: '<?php echo get_template_directory_uri(); ?>',
	        	tests: {}
	        });
    	</script>
</body>
</html>
