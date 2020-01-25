<?php get_header(); ?>
<div class="">
	<?php  
		$banner = wp_get_attachment_url( get_post_thumbnail_id($post->ID, 'full') );
	?>
	<section class="banner-interna container-fluid d-flex justify-content-center align-items-center font-arabic font-upper" style="background: url(<?php echo $banner; ?>) no-repeat center center / cover;">
		<h1 class="container"><?php the_title(); ?></h1>
	</section>
	<section class="py-5 container interna-blog">
		<div class="row font-roboto color-gray">
			<div class="col-12">
				<?php if (have_posts()): while (have_posts()) : the_post(); ?>
					<?php the_content(); ?>
				<?php endwhile; endif; ?>
			</div>
		</div>
	</section>
</div>
<?php get_footer(); ?>
