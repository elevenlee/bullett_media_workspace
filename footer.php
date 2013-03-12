<footer id="footer-container">
	<nav>
		<div class="inner">
			<hr>
			<ul>
				<li><a href="http://www.bullettmedia.com/about">About</a></li>
				<li><a href="http://www.bullettmedia.com/advertise-with-us">Advertise With Us</a></li>
				<li><a href="http://www.bullettmedia.com/careers">Careers</a></li>
				<li><a href="http://www.bullettmedia.com/contact">Contact</a></li>
				<li><a href="http://www.bullettmedia.com/meet-the-team">Meet the Team</a></li>
				<li><a href="http://www.bullettmedia.com/promotions">Promotions</a></li>
				<li><a href="http://www.bullettmedia.com/magazine-locator">Magazine Locator</a></li>
				<li><a href="http://shop.bullettmedia.com/store/store-policies/">Store Policies</a></li>
				<li><a href="http://shop.bullettmedia.com/privacy/">Privacy</a></li>
			</ul>
		</div>
		<div id="mc_embed_signup">
			<form action="http://bullettmedia.us2.list-manage2.com/subscribe/post?u=f06edd20cd70e2d0fde1248e1&amp;id=0f0c14efbb" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
				<div class="mc-field-group"><input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL" placeholder="SIGN UP FOR OUR NEWSLETTER"></div>
				<div class="clear"><input type="submit" value="SUBMIT" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
			</form>
		</div>
	</nav>
</footer>

<?php
$items		= Cart66Session::get('Cart66Cart')->getItems();
$cartPage	= get_page_by_path("/store/cart");
$prefix		= 'products_';

if(count($items)):
?>
	<div class="IN-CART-ITEMS">
		<h2>Already in cart</h2>
		<ul class="in-cart-items">
			<?php
			$all_args = array(
				'post_type' => array('products'),
				'order' => 'ASC',
				'posts_per_page' => '-1',
			);
			$all_prods = get_posts($all_args);

			foreach($items as $itemIndex => $item):
				$item_ID		= $item->getItemNumber();
				$item_details	= $item->getFullDisplayName();
				$item_name		= preg_replace("/\((.*)\)/i", "", $item_details);
				$item_image		= null;

				foreach($all_prods as $ap):
					$product_ID = get_post_meta($ap->ID, "${prefix}product_id", true);
					if ($item_ID == $product_ID[0] && has_post_thumbnail($ap->ID)):
						$item_image = get_the_post_thumbnail($ap->ID, array(45, 45));
						break;
					endif;
				endforeach;
			?>
				<li>
					<a href="<?php echo get_permalink($cartPage->ID); ?>">
						<?php echo $item_image ?>
						<p>
							<?php echo $item_name; ?><br />
							Quantity: <?php echo $item->getQuantity(); ?>
						</p>
					</a>
				</li>
			<?php
			endforeach;
			?>
		</ul>
	</div>
<?php endif; ?>

<style type="text/css">
div.IN-CART-ITEMS {
	width: 100%;
	padding: 0px 20px;
	border-top: 1px solid black;
	position: fixed;
	bottom: 0px;
	z-index: 99999;
	background-color: white;
}
div.IN-CART-ITEMS h2 {
	font-family: RaisonneDemiBold, Helvetica, Arial, sans-serif;
	font-size: 14px;
	text-transform: uppercase;
	line-height: 2em;
}
div.IN-CART-ITEMS ul.in-cart-items {
	margin-right: -10px;
}
div.IN-CART-ITEMS ul.in-cart-items li {
	float: left;
	display: block;
	margin-left: 15px;
	margin-bottom: 8px;
}
div.IN-CART-ITEMS ul.in-cart-items li a {
	text-decoration: none;
}
div.IN-CART-ITEMS ul.in-cart-items li a img {
	float: left;
	vertical-align: text-bottom;
	margin-right: 8px;
}
div.IN-CART-ITEMS ul.in-cart-items li a p {
	font-family: RaisonneDemiBold, Helvetica, Arial, sans-serif;
	font-size: 12px;
	display: block;
	float: right;
	text-align: center;
	margin-left: auto;
	margin-right: auto;
	margin-top: 8px;
}
</style>

<!--! end of #footer-container -->
<?php wp_reset_query(); ?>

<script>var headerajax = $.post('http://bullettmedia.com/api/banner-ad/').success(function(data){$('#top-ad-container').html(data)});</script>

<!--[if lt IE 8 ]>
	<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
	<script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-22441230-4']);
		_gaq.push(['_trackPageview']);
		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>

<![endif]-->
<?php wp_footer(); ?>
</body>
</html>
