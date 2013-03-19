<!doctype html>
<!--[if lt IE 7]><html class="no-js ie6 oldie" lang="en"><![endif]-->
<!--[if IE 7]><html class="no-js ie7 oldie" lang="en"><![endif]-->
<!--[if IE 8]><html class="no-js ie8 oldie" lang="en"><![endif]-->
<!--[if IE 9]><html class="no-js ie9 oldie" lang="en"><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" lang="en"> <!--<![endif]-->
<head>
<!-- Never gonna give you up, never gonna let you down, never gonna run around and desert you. -->
	<!--<?php wp_title(''); ?>-->
	<meta charset="<?php bloginfo('charset'); ?>">
	<title><?php bloginfo('name'); ?></title>
	<meta name="author" content="Bullett Media">
	<meta name="title" content="<?php wp_title("//"); ?>"  property="og:title">
	<meta name="viewport" content="width=device-width">
	<link rel="shortcut icon" href="<?php bloginfo('template_directory'); ?>/favicon.ico">
	<?php $attachments = get_children( array(
		'post_parent'    => get_the_ID(),
		'post_type'      => 'attachment',
		'numberposts'    => 1,
		'post_status'    => 'inherit',
		'post_mime_type' => 'image',
		'order'          => 'ASC',
		'orderby'        => 'menu_order ASC'
	));
	foreach ( $attachments as $attachment_id => $attachment ) {
		$the_fb_src = wp_get_attachment_image_src( $attachment_id );
		echo '<meta name="image" property="og:image" content="' . $the_fb_src[0] . '" />';
	}
	?>
	<meta name="news_keywords" content="<?php
		$tags = wp_get_post_tags( $post->ID );
		$tagline = '';
		foreach( $tags as $tag ):
			$tagline .= $tag->name;
			$tagline .= ', ';
		endforeach; 
		$tagline = substr( $tagline, 0, -2 );
		echo( $tagline ); ?>">
	<link rel="stylesheet" href="<?php bloginfo('template_directory') ?>/css/style-min-3.0.css">
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/css/jquery.jqzoom.css">
	<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<?php if ( 'slideshow' == get_post_type() ) { ?>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory') ?>/css/lightview/lightview.css"/>
	<?php } //end if(slideshow)
	?>
	
<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->
<script>window.jQuery || document.write('<script src="<?php bloginfo('template_url'); ?>/scripts/jquery-1.7.2.min.js">x3C/script>')</script>
<script src="<?php bloginfo('template_directory'); ?>/scripts/jquery-1.6.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/scripts/jquery.jqzoom-core.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
<script src="http://cdn.jquerytools.org/1.2.6/all/jquery.tools.min.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/scripts/main-raw.js"></script>
<script src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4d66e78a3e962981"></script>
<script src="http://platform.tumblr.com/v1/share.js"></script>

<?php if ( 'slideshow' == get_post_type() ) { ?>
<!--[if lt IE 9]><script type="text/javascript" src="<?php bloginfo('template_directory') ?>/scripts/lightview/excanvas.js"></script><![endif]-->
<script type="text/javascript" src="<?php bloginfo('template_directory') ?>/scripts/lightview/spinners.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory') ?>/scripts/lightview/lightview.js"></script>
<?php } //end if(slideshow)
?>

	
	<?php wp_head(); ?>
</head>
<body class="standard editorial">
<div id="page-container">
	<header id="header-container">
		<?php
// ---------------------------------------		
		$empty_cart_msg = "&nbsp;";
		echo do_shortcode('[shopping_cart empty_msg="'.$empty_cart_msg.'"]'); // modified in cart66/models/Cart66ShortcodeManager.php
// ---------------------------------------		
		?>

			<h1><a id="header-logo" href="<?php bloginfo('url'); ?>"><img src="http://shop.bullettmedia.com/wp-content/uploads/2013/01/shop-logo.png" alt="The Bullett Shop" /></a></h1>
			
			<div id="top-ad-container">
				<a href="http://shop.bullettmedia.com/products/magazine-subscription/"><img src="http://bullett.wpengine.netdna-cdn.com/wp-content/uploads/2012/12/imgad-3.jpeg"></a>
			</div>
			<div class="clear"></div><!-- added a clear, by FLAT -->
			<nav>
				<?php
				wp_nav_menu(array(
					'theme_location' => 'header-menu',
					'container' => false,
					'menu_class' => 'menu',
					'menu_id' => 'main-navigation',
					'items_wrap' => '<ul id="%1$s">%3$s</ul>'
				));
				?>
			</nav>
<!--! end of nav #main-navigation -->
			<div id="header-social-nav">
				<h1>FOLLOW US ON</h1>
				<ul>
					<li id="fb-btn"><a href="http://www.facebook.com/pages/Bullett-Magazine/143235499055215" title="facebook">facebook</a></li>
					<li id="twitter-btn"><a href="http://twitter.com/#!/BULLETTMedia" title="twitter">twitter</a></li>
					<li id="tumblr-btn"><a href="http://bullettmedia.tumblr.com/" title="tumblr">tumblr</a></li>
					<li id="pinterest-btn"><a href="http://pinterest.com/bullettmedia/" title="pinterest">pinterest</a></li>
					<li id="rss-btn"><a href="feed://www.bullettmagazine.com/rss" title="rss">rss</a></li>
				</ul>
				<div class="clear"></div><!-- added a clear, by FLAT -->
			</div>
<!--! end of #header-social-nav -->
			<form method="get" id="header-search-form" action="http://bullettmedia.com">
				<input type="text" class="expand" name="s" id="s" value="SEARCH" onFocus="if (this.value == 'SEARCH') {this.value = '';}" onBlur="if (this.value == '') {this.value = 'SEARCH';}">
				<input type="submit" name="submit" value="SUBMIT">
			</form>
<!--! end of #header-search-form -->
			<div class="clear"></div>
	</header>
<!--! end of #header-container -->
