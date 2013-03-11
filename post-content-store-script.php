<?php
// this should go in the functions.php file.
// it pulls the product ID from the meta box and creates the shortcode out of it
// then executes the shortcode.

function bullett_add_cart_button ($content) {
	global $post;
	// make sure the $key here matches the meta key you set up for the input in the meta box
	$product_id = get_post_meta($post->ID, 'product_id', true);
	if( $product_id != '' && $product_id != null ):
		$cart_code = do_shortcode("[add_to_cart item=\"$product_id\" quantity=\"user:1\" ]");
		$content = $content . $cart_code;
	endif;
	
	return $content;
}

add_filter('the_content', 'bullett_add_cart_button');

?>