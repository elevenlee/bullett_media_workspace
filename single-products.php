<?php
/*
Template Name: Product Detail / Default
*/
# -------------------------------------------------------------------------------------- #
# -------------------------------------------------------------------------------------- #
get_header();
# -------------------------------------------------------------------------------------- #
# -------------------------------------------------------------------------------------- #
$related_terms = wp_get_post_terms($post->ID,'products-categories');
$related_args = array(
	'post_type'		=> array('products'),
	'orderby'		=> 'menu_order',
	'order'			=> 'ASC',
	'posts_per_page'	=> 6,
	'tax_query'		=> array(
		array(
			'taxonomy' => 'products-categories',
			'field' => 'slug',
			'terms' => $related_terms[0]->slug
		)
	)
);
$related_prods = get_posts($related_args);
?>
	<div id="fb-root"></div>
	<script>(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = "//connect.facebook.net/en_US/all.js#xfbml=1"; fjs.parentNode.insertBefore(js, fjs); }(document, 'script', 'facebook-jssdk'));</script>
	<script>$(document).ready(function() {$('.jqzoom').jqzoom({zoomType: 'innerzoom', lens: true, preloadImages: false, alwaysOn: false, title: false, showEffect: 'fadein', hideEffect: 'fadeout', fadeinSpeed: 'slow', fadeoutSpeed: 'slow', }); });</script>
	<div id="main-container" class="PRODUCT-DETAIL" role="main" data-script="ProductArticle">
		<?php if ( have_posts() ): while ( have_posts() ) : the_post();
			# -------------------------------------------------------------------------- #
			$post_id		= get_the_ID();
			$post_title		= basename(get_permalink());
			$post_content	= get_the_content();
			$post_madeby	= get_post_meta($post_id,'Manufacturer', true);
			$add_to_cart	= null;
			preg_match_all("/\[add_to_cart (.*)\]/i", $post_content, $add_to_cart);
			preg_match_all("/<img (.*)>/", $post->post_content, $product_images);
			
			$thumb_ID = get_post_thumbnail_id( $post->ID );
			$thumb_hover_ID = null;
			if (class_exists('MultiPostThumbnails') && MultiPostThumbnails::get_post_thumbnail_id('products', 'featured-image-roll', get_the_ID())) : 
				$thumb_hover_ID = MultiPostThumbnails::get_post_thumbnail_id('products', 'featured-image-roll', get_the_ID());
			endif;
			
			$attachments = get_children(array('post_parent'=>get_the_ID(),'post_status'=>'inherit', 'post_type'=>'attachment', 'post_mime_type'=>'image', 'exclude'=>array($thumb_hover_ID), 'order'=>'ASC', 'posts_per_page'=>-1));
			# -------------------------------------------------------------------------- #
			?>
			<div id="left-column" style="margin: 0px 0px; position: absolute; top: 0; left: 0; float: none;">
				<article class="post-column fixed" style="position: static; top: 0; padding: 23px 0 0 0;">
					<div class="post-scroll-pane">
						<h1><?php the_title(); ?></h1>
						<?php if( strlen($post_madeby) ) { ?>
						<h3 class="category-slug">BY <?php echo $post_madeby; ?></h3>
						<?php } ?>
						<div class="product-price">
							<?php echo do_shortcode($add_to_cart[0][0]); ?>
						</div>
						<div class="experpt">
							<p><?php $straight_content = preg_replace("/\[.+?\]/", "", $post_content); echo $straight_content ?></p>
						</div>
						<div class="post-share-container" style="position: relative; top: 0px; left: 0px;">
							<ul>
								<li><fb:like layout="box_count" style="width:48px;"></fb:like></li>
								<li><a class="addthis_button_tweet" tw:count="vertical"></a></li>
								<li class="pin"><a class="addthis_button_pinterest" pi:pinit:url="http://pinterest.com/bullettmedia/"></a></li>
							</ul>
							<ul>
								<li><a class="fb-share-btn" href="http://api.addthis.com/oexchange/0.8/forward/facebook/offer?url=<?php echo get_permalink();?>" rel="nofollow" target="_blank">Share</a></li>
								<li><a class="tumblr-share-btn" href="http://www.tumblr.com/share/link?url=<?php echo urlencode(get_permalink()) ?>&amp;name=<?php echo urlencode(get_the_title()) ?>" title="Share on Tumblr">Share on Tumblr</a></li>
								<li><a class="email-counter" href="http://api.addthis.com/oexchange/0.8/forward/email/offer?url=<?php echo get_permalink();?>" rel="nofollow" target="_blank">EMAIL</a></li>
							</ul>
							<!--
							<ul>
								<li class="image-btn comments"><a id="comments-counter" href="#">COMMENTS</a></li>
							</ul>
							-->
						</div>
					</div>
				</article>
			</div>
			<!--! end of #left-column -->
			
			<div id="content-column" style="margin: 30px 0px 100px 352px; width: 581px;">
				<div class="inner" style="padding: 0 30px;">
					<?php
					/*
					preg_match_all("/wp-image-(d+)/", $post->post_content, $matches);
					unset($matches[0]);
					$attachments = get_children( array('post_parent' => get_the_ID(), 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID', 'posts_per_page' => -1, 'post__not_in' => $matches[1]) );
					if($attachments):
						foreach($attachments as $attachment): if(strstr($post->post_content, "wp-image-" . $attachment->ID)) continue;
						
						endforeach;
					endif;
					*/
					if($attachments):
					?>
					<div id="post-slideshow" style="height: auto; overflow: inherit; width: 480px;">
						<!--<div class="post-slideshow-prev">&lt;</div>-->
						<div class="slideshow-wrapper" style="display: block;">
							<?php
							$slide_count = 0;
							foreach($attachments as $attachment): ?>
							<div class="post-slide <?php if ($slide_count==0) { echo 'current'; } ?>" id="post-slide-<?php echo $attachment->ID; ?>" style="width: 480px; display: <?php if ($slide_count<=0) { echo 'block'; } else { echo 'none'; } ?>;">
								<span style="height: auto; width: 480px;"><?php
								//	echo wp_get_attachment_image($attachment->ID, $size='384high', $icon=0, $attr=array("width"=>"480"));
									$slide	 = wp_get_attachment_image_src($attachment->ID, $size='384high', $icon=0);
									$slide_w = $slide[1];
									$slide_h = $slide[2]; 
									if($slide_w <> 480) { 
										$percentage	 = 480/$slide_w;
										$slide_h = $slide_h * $percentage;
									}
									$slide_w = 480;
									$slide_h = ceil($slide_h);
									echo '<a href="'.$slide[0].'" class="jqzoom">';
									echo '	<img src="'.$slide[0].'" width="480" height="'.$slide_h.'" class="attachment-384high" />';
									echo '</a>';
									if ($attachment->post_excerpt != "null") :
										echo "<p>".$attachment->post_excerpt."</p>";
									endif;
									?>
								</span>
							</div>
							<?php
							$slide_count++;
							endforeach; ?>
							<div class="clear"></div>
						</div>
						<!--<div class="post-slideshow-next">&gt;</div>-->
					</div>
					<!--! end of #post-slideshow -->
					
					<?php endif; ?>
					
					<div class="RELATED-ITEMS">
							<h2>More like this item</h2>
							<ul class="related-items">
							<?php foreach( $related_prods as $rp ) : ?>
								<li><a href="<?php echo get_permalink($rp->ID); ?>">
									<?php the_image("thumbnail", "related-thumnail", $rp->ID); ?>
									<p><?php echo get_the_title($rp->ID); ?></p>
								</a></li>
							<?php endforeach; ?>
							</ul>
					</div>
					
				</div>	
			</div>
			<!--! end of #content-column -->
			
			<div class="right-wrapper" style="float: none; width: auto;">
				<div id="right-column" style="position: relative; top: 0px; left: 0px; min-height: 100%; width: 150px;">
					
					<div id="product-thumbs-container">
						<div id="related-carousel-container">
							<div class="caroufredsel_wrapper" style="float: none; position: relative;">
								<ul style="float: none; position: relative;">
								<?php
								if($attachments):
									foreach($attachments as $attachment):
										echo "<li>";
										echo "<a href='javascript:void(0);' class='slide-toggle' data='post-slide-$attachment->ID'>";
										echo wp_get_attachment_image($attachment->ID, 'thumbnail');
										echo "</a>";
										echo "</li>";
									endforeach;
								endif;
								?>
								</ul>
							</div>
						</div>
						<nav>
							<div class="prev-btn" style="display: block; ">Up</div>					
							<div class="next-btn" style="display: block; ">Down</div>					
						</nav>
					</div>
					
					
					<script language="javascript">
					$('a.slide-toggle').click( function(e) {
						var slide_current = $('.post-slide.current');
						var slide_select = "#" + $(this).attr('data');
						if( !$(slide_select).hasClass('current') ) {
							$(slide_current).removeClass('current').hide();
							$(slide_select).show().addClass('current');
						}
					});
					$(document).ready(function(){
						var ddfix = $('#product-thumbs-container ul').clone().addClass('ddfix').hide().appendTo('#footer-container');
					});
					$('#options_1').change(function(eventobj){
						var num = $('#options_1 option[value="' + $('#options_1').val() + '"]').index();
						var slide_current = $('.post-slide.current');
						slide_select = "#" + $('.ddfix .slide-toggle').eq(num).attr('data');
	
						if( !$(slide_select).hasClass('current') ) {
							$(slide_current).removeClass('current').hide(); 
							$(slide_select).show().addClass('current');
						}
					});
					</script>
					
				</div>
				<!--! end of #right-column -->
			</div>
			<!--! end of .right-wrapper -->
			
		<?php endwhile; endif; ?>
	</div>
	<!--! end of #main-container -->
	
</div>
<!--! end of #page-container -->

<div id="post-fullscreen-gallery">
	<a id="fullscreen-close-btn" class="post-close-btn" href="#">X</a>
</div>
<!--! end of #post-fullscreen-gallery -->	
<?php get_footer(); ?>
