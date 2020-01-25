<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
	<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		
		<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?></title>

		<link href="//www.google-analytics.com" rel="dns-prefetch">
        <link href="<?php echo get_template_directory_uri(); ?>/img/icons/favicon.ico" rel="shortcut icon">
        <link href="<?php echo get_template_directory_uri(); ?>/img/icons/touch.png" rel="apple-touch-icon-precomposed">

		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="<?php bloginfo('description'); ?>">
		<?php wp_head(); ?>
        <!-- Facebook Pixel Code -->
        <script>
          !function(f,b,e,v,n,t,s)
          {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
          n.callMethod.apply(n,arguments):n.queue.push(arguments)};
          if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
          n.queue=[];t=b.createElement(e);t.async=!0;
          t.src=v;s=b.getElementsByTagName(e)[0];
          s.parentNode.insertBefore(t,s)}(window, document,'script',
          'https://connect.facebook.net/en_US/fbevents.js');
          fbq('init', '663387497180810');
          fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
          src="https://www.facebook.com/tr?id=663387497180810&ev=PageView&noscript=1"
        /></noscript>
        <!-- End Facebook Pixel Code -->
        <!-- Google Tag Manager -->
        <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-NFKZGPR');
        </script>
        <!-- End Google Tag Manager -->
	</head>
	<body <?php body_class(); ?>>

    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NFKZGPR" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
	<!-- header -->
	<header class="nav-page">
	    <div class="container">
	        <div class="row">
	            <nav class="col-12 navbar navbar-expand-lg ">
	                <a class="navbar-brand" href="<?php echo get_home_url(); ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-src="<?php echo get_template_directory_uri(); ?>/img/white.png" data-srcset="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="logo" class="img-fluid lazy"></a>
	                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
	                    <span class="bar-menu"></span>
	                    <span class="bar-menu"></span>
	                    <span class="bar-menu"></span>
	                </button>                
	                <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
	                	<?php html5blank_nav(); ?>
	                </div> 
	            </nav>
	        </div>
	    </div>
	</header>			
	<!-- /header -->