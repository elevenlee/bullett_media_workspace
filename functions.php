<?php

include 'product-meta-box.php';

/* ADDED BY FLAT */
/* ------------------------------------------------------------------------------------ */
function my_products_pages() { // This is needed to get the WYSISWYG editor button in the admin
  return array('products', 'another-post-type');
} 
add_filter('cart66_add_popup_screens', 'my_products_pages');

// Secondary Featured Images, used on rollovers...
if (class_exists('MultiPostThumbnails')) {
	new MultiPostThumbnails( array(
		'label'		=> 'Featured Image Rollover',
		'id'		=> 'featured-image-roll',
		'post_type'	=> 'products'
	));
}


function products_taxonomy_list($post_id = null) { // derived from 'function categories_list', below.
	$categories = get_the_terms($post_id, 'products-categories');
	$cat_count = 1;
	if( isset($categories) && !empty($categories) ) {
		foreach($categories as $category) {
			if( isset($category) && !empty($category) ) {
				if($cat_count <= count($categories)) {
					echo ' / ';
				}
				echo $category->name;
				$cat_count++;
			}
		}
	}
}

// http://www.onextrapixel.com/2012/05/18/the-practical-guide-to-multiple-relationships-between-posts-in-wordpress/
function my_connection_types() { // Used to make post to post relations between products
	p2p_register_connection_type( array(
		'name'	=> 'products_to_products',
		'from'	=> 'products',
		'to'	=> 'products'
	) );
}
add_action( 'p2p_init', 'my_connection_types' );


/* CUSTOM SHORTCODES FOR CART66 */
// http://frankiejarrett.com/tag/cart66/
function cart66_test( $atts, $content = null ) {
	echo "custom shortcode echo...";
}
add_shortcode( 'cart_echo', 'cart66_test' );

function cart66_order_history( $atts, $content = null ) {
	extract( shortcode_atts( array(), $atts ) );
	global $wpdb;
	$results = $wpdb->get_results( "SELECT ouid, ordered_on, trans_id, total, status FROM " . $wpdb->prefix . "cart66_orders WHERE account_id = " . Cart66Session::get( 'Cart66AccountId' ) . ' ORDER BY ordered_on DESC' );
	foreach ( $results as $order ) {
		$data .= sprintf( '<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td><a href="%s" title="%s" target="_blank">%s</a></td></tr>', $order->trans_id, date( 'F j, Y', strtotime( $order->ordered_on ) ), $order->total, ucwords( $order->status ), home_url( '/store/receipt/?ouid=' . $order->ouid ), __( 'Click to view receipt', 'cart66' ), __( 'View Receipt', 'cart66' ) );
	}
	$table = '<table id="viewCartTable">
				<thead>
					<tr>
						<th>Order Number</th>
						<th>Date</th>
						<th>Total</th>
						<th>Order Status</th>
						<th>Receipt</th>
					</tr>
				</thead>
				<tbody>' . $data . '</tbody></table>';
	return $table;
}
add_shortcode( 'order_history', 'cart66_order_history' );
/* ------------------------------------------------------------------------------------ */


add_filter( 'the_content', 'fix_embedded_video' );
function fix_embedded_video($content) {
  return $content;
}

function get_terms_filter( $terms, $taxonomies, $args )
{
	global $wpdb;
	$taxonomy = $taxonomies[0];
	if ( ! is_array($terms) && count($terms) < 1 )
		return $terms;
	$filtered_terms = array();
	foreach ( $terms as $term )
	{
		$result = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts p JOIN $wpdb->term_relationships rl ON p.ID = rl.object_id WHERE rl.term_taxonomy_id = $term->term_id AND p.post_status = 'publish' LIMIT 1");
		if ( intval($result) > 0 )
			$filtered_terms[] = $term;
	}
	return $filtered_terms;
}
//add_filter('get_terms', 'get_terms_filter', 10, 3);



/* ---------------------------------- */

/* WP Link Pages */

add_filter('mce_buttons','wysiwyg_editor');
function wysiwyg_editor($mce_buttons) {
    $pos = array_search('wp_more',$mce_buttons,true);
    if ($pos !== false) {
        $tmp_buttons = array_slice($mce_buttons, 0, $pos+1);
        $tmp_buttons[] = 'wp_page';
        $mce_buttons = array_merge($tmp_buttons, array_slice($mce_buttons, $pos+1));
    }
    return $mce_buttons;
}

add_filter('wp_link_pages_args','add_next_and_number');
function add_next_and_number($args){
    if($args['next_or_number'] == 'next_and_number'){
        global $page, $numpages, $multipage, $more, $pagenow;
        $args['next_or_number'] = 'number';
        $prev = '';
        $next = '';
        if ( $multipage ) {
            if ( $more ) {
                $i = $page - 1;
                if ( $i && $more ) {
                    $prev .= _wp_link_page($i);
                    $prev .= $args['link_before']. $args['previouspagelink'] . $args['link_after'] . '</a>';
                }
                $i = $page + 1;
                if ( $i <= $numpages && $more ) {
                    $next .= _wp_link_page($i);
                    $next .= $args['link_before']. $args['nextpagelink'] . $args['link_after'] . '</a>';
                }
            }
        }
        $args['before'] = $args['before'].$prev;
        $args['after'] = $next.$args['after'];    
    }
    return $args;
}



function get_subtitle($post_id, $limit = 140) {

  $subtitle = strip_tags(get_post_meta($post_id, "_subtitle", true));
  
  if($limit == "all") {
    return $subtitle;
  }
  
  if(strlen($subtitle) > $limit) {
    $subtitle = substr($subtitle, 0, strrpos(substr($subtitle, 0, $limit), ' ')) . "...";
  } 

  return "<p>" . $subtitle . "</p>";

}

function title_long_or_short($post_id, $bool = false) {
  if(get_post_meta($post_id, "_short_title", true)) {
	  echo get_post_meta($post_id, "_short_title", true);
	} else {
		echo get_the_title($post_id);
  }
}


add_action('init','filter_by'); 

function filter_by() {
  global $wp_query;
  
  $request = $_SERVER['REQUEST_URI'];

  if(strstr($request, "/filter-by/")) {
    preg_match("//category/([a-z-]+)/filter-by/([a-z-]+)/([a-z-+]+)//U", $request, $matches);
    if($matches) {
      $category_slug = $matches[1];
      $taxonomy = $matches[2];
      $terms = explode("+", $matches[3]);
      
      $wp_query->query_vars['category_name'] = $category_slug;
      
      $wp_query->query_vars['tax_query'] = array(
        'relation' => 'AND',
        array(
    			'taxonomy' => 'category',
    			'field' => 'slug',
    			'terms' => array($category_slug),
    			'operator' => 'IN'
    		),
    		array(
    			'taxonomy' => $taxonomy,
    			'field' => 'slug',
    			'terms' => $terms,
    			'operator' => 'IN'
    		)
    	);

      get_template_part('archive');
    }
  }
}

add_action('category_edit_form_fields', 'category_form_extra_fields');

function category_form_extra_fields($cat) {
  
  $args=array(
    'public'   => true,
    '_builtin' => false

  ); 
  $output = 'names'; // or objects
  $operator = 'and'; // 'and' or 'or'
  
  $taxonomies = get_taxonomies($args,$output,$operator);
  
  $selected = get_option("custom_taxonomies_for_cat_id_" . $cat->term_id) or $selected = array();
  
  unset($taxonomies['media-tags']);
  echo "<tr><td>Custom Taxonomies</td><td>";
  foreach($taxonomies as $taxonomy) {
    $checked = null;
    if(in_array($taxonomy, $selected)) {
      $checked = "checked='true'";
    }
    echo "<input type='checkbox' name='custom_taxonomies[]' value='$taxonomy' $checked /> $taxonomy<br />";
  }
  echo "</td></tr>";
}

add_action('edit_category', 'category_save_extra_fields');

function category_save_extra_fields($cat_id) {
  update_option("custom_taxonomies_for_cat_id_" . $cat_id, $_POST['custom_taxonomies']);
}

add_theme_support( 'post-thumbnails' );

function bullett_body_class() {
  global $is_bullet_tv, $post, $is_camera, $is_stikit;
  if(is_page("magazine")) {
    echo "magazine";

	} elseif(is_page("inspired") || is_page_template("inspired-thanks.php")) {
    echo "inspired";

  } elseif(is_page("obsessed") || is_page_template("obsessed-thanks.php")) {
    echo "obsessed";
  
  } elseif(is_page_template("coachella.php")) {
    echo "coachella";

  } elseif(is_page_template("static.php")) {
    echo "static";

  } elseif(is_page_template("page.php")) {
    echo "page";

  } elseif(is_page("registration")) {
    echo "registration";
	
  } elseif(is_page_template("events.php")) {
    echo "events";

  } elseif(is_page_template("team.php")) {
    echo "static meet-the-team";

 	} elseif(is_page_template("party-dyn.php")) {
    echo "party post editorial";
	
 	} elseif(is_singular('camera') || $is_camera) {
    echo "bullett-camera";

  } elseif(is_search()) {
    echo "search";

  } elseif(is_home()) {
    echo "home";

  } elseif($is_bullet_tv) {
    echo "bullett-tv";
	
  } elseif($is_stikit) {
    echo "stickit";		

  } elseif(is_singular('editorial')) {
    $color_class = "";
    if(get_post_meta($post->ID, '_text_color', true) == "white") {
      $color_class = " white";
    }
    echo "fashion post editorial" . $color_class;

  } elseif(is_singular('tv')) {
    echo "bullett-tv post standard";
	
  } elseif(is_singular('stickit')) {
    echo "stickit post standard";		

  } elseif(is_single()) {
    foreach((get_the_category()) as $category) { 
      echo "$category->slug ";
    }
    echo "post standard";

  } elseif(is_category()) {
    global $wp_query;
    echo $wp_query->query_vars['category_name'];
    echo " category";
  }

}


function format_content($content) {
  $content = apply_filters('the_content', $content);
  $content = str_replace(']]>', ']]&gt;', $content);
  return $content;
}

function categories_list($post_id = false) {
  if($post_id) {
    $categories = get_the_category($post_id);
  } else {
    $categories = get_the_category();
  }
	
	$cat_count = 1;
  foreach(($categories) as $category) { 
      echo $category->name; 
      if($cat_count < count($categories)) {
        echo ' / ';
      }
      $cat_count++;
  } 
}

function the_image($size, $class = null, $post_id = false, $position = 1) {
  global $post;

  if(!$post_id) {
    $post_id = $post->ID;
  }
  if( !$post_id )
      return ;  

  if(has_post_thumbnail($post_id)) {
    echo get_the_post_thumbnail($post_id, $size, array("class" => $class) );
  } else {
    $images = get_children( array('post_parent' => $post_id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID', 'posts_per_page' => -1) );
    for($i = 0; $i < $position; $i++) {
      $image = each($images);
      $image = $image['value'];
    }
    
    $fl = $_SERVER["DOCUMENT_ROOT"] . "/stickituploads/post-".$post_id."-200x400.jpg";
    if(file_exists( $fl )) {
      $fl = "http://".$_SERVER["HTTP_HOST"] . "/stickituploads/post-".$post_id."-200x400.jpg";
      echo "<img src='$fl' alt='' class='$class'>";
    }
    else if($image) {
      $src = wp_get_attachment_image_src($image->ID, $size);
      echo "<img src='$src[0]' alt='' class='$class'>";
    } elseif($size == "300x9999") {
      echo "<img src='" . get_bloginfo('template_directory') . "/images/global/post-default.gif' alt='' class='$class'>";
    } else {
      echo "<!-- image not available -->";
    }
  }
}

function the_fb_image($size, $class = null, $post_id = false, $position = 1) {
	global $post;
	
	if(!$post_id) {
		$post_id = $post->ID;
	}
	if( !$post_id ) return;  
	
	if(has_post_thumbnail($post_id)) {
		$thumbnail = wp_get_attachment_image_src ( get_post_thumbnail_id ( $post->ID ));
		echo $thumbnail[0];
	}
	else {
		$images = get_children( array('post_parent' => $post_id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID', 'posts_per_page' => -1) );
		
		for($i = 0; $i < $position; $i++) {
			$image = each($images);
			$image = $image['value'];
		}
		
		if($image) {
			$src = wp_get_attachment_image_src($image->ID, $size);
			echo $src[0];
		}
		else {
			echo get_bloginfo('template_directory') . "/images/global/post-default.gif";
		}
  }
}


function get_the_image($size, $class = null, $post_id = false, $position = 1) {
  global $post;

  if(!$post_id) {
    $post_id = $post->ID;
  }
  if( !$post_id )
      return ;  

  $images = get_children( array('post_parent' => $post_id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID', 'posts_per_page' => -1) );
  for($i = 0; $i < $position; $i++) {
    $image = each($images);
    $image = $image['value'];
  }
  
  if($image) {
    $src = wp_get_attachment_image_src($image->ID, $size);
    return $src[0];
  } else {
    return false;
  }

}

function myfeed_request($qv) {
	if (isset($qv['feed']) && !isset($qv['post_type']))
		$qv['post_type'] = array('post', 'page', 'article', 'editorial', 'tv', 'camera', 'issue');
	return $qv;
}
add_filter('request', 'myfeed_request');



/* ---------------------------------- */

/* Truncate At Word */

function truncateToWord($str, $length) {
	if(strlen($str) > $length) {
		if(strrchr($str, " ") && strrchr($str, " ") < $length) {
			return substr( $str, 0, strrpos( substr( $str, 0, $length), ' ' ) )."…";
		}
		else {
			return substr( $str, 0, $length )."…";
		}
	}
	else {
		return $str;
	}
}





/* ---------------------------------- */

/* Navigation Menus */

function register_my_menus() {
	register_nav_menus(
		array( 'header-menu' => __( 'Header Menu' )) );
}
add_action( 'init', 'register_my_menus' );


/* ---------------------------------- */

/* Widgets */

function quickchic_widgets_init() {
	register_sidebar(array(
		'name' => __( 'Sidebar', 'quickchic' ),
		'id' => 'sidebar',
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => '',
		));
	}

add_action( 'init', 'quickchic_widgets_init' );



// hide the admin bar on the front end.
add_filter('show_admin_bar', '__return_false');



register_post_type('slideshow', array(
  'label' => __('Slideshow'),
  'singular_label' => __('Slideshow'),
  'labels' => array(
    'add_new_item' => __('Add New Slideshow'),
    'edit_item' => __('Edit Slideshow'),
    'new_item' => __('New Slideshow'),
    'view_item' => __('View Slideshow')),
  'public' => true,
  'show_ui' => true,
  'capability_type' => 'post',
  'hierarchical' => false,
  'query_var' => false,
  'supports' => array('title', 'editor', 'author', 'comments', 'excerpt'),
  'menu_position' => 4,
  'taxonomies' => array('category', 'post_tag')
));






// THIS INCLUDES THE THUMBNAIL IN OUR RSS FEED
function insertThumbnailRSS($content) {
	global $post;
	$rss_attachments = get_children('post_parent=' . $post->ID . '&post_status=inherit&post_type=attachment&post_mime_type=image&order=ASC&posts_per_page=1');
	$rss_img = '';
	foreach ( $rss_attachments as $attach ) {
		$rss_img = wp_get_attachment_image( $attach->ID, 'medium' );
	}
	$new_content = '' . $rss_img . $content;

	return $new_content;
}
add_filter('the_excerpt_rss', 'insertThumbnailRSS');
add_filter('the_content_feed', 'insertThumbnailRSS');





//Adding product page order and item number to product page admin tables
function custom_pagerank_column($defaults) {
	$defaults['custom_pagerank'] = 'Order';
	$defaults['cart66_product_num'] = 'Product Number';
	return $defaults;
}
add_filter('manage_products_posts_columns', 'custom_pagerank_column');

function custom_pagerank_column_content($column_name, $post_ID) {
	if ($column_name == 'custom_pagerank') {
		$the_order_return = get_post_field('menu_order', $post_ID);
		echo $the_order_return;
	}
	if ($column_name == 'cart66_product_num') {
		$the_post_cont = get_post_field('post_content', $post_ID);
		$num_finder = preg_match("/item=\"([^\"]+)\"/", $the_post_cont, $the_prod_num);
		if( $the_prod_num[1] ) {
			 echo( $the_prod_num[1] );
		}
		else {
			echo("Not Associated");
		}
	}
}
add_filter('manage_products_posts_custom_column', 'custom_pagerank_column_content', 10, 2);

function custom_pagerank_sortable( $columns ) {
	$columns['custom_pagerank'] = 'custom_pagerank';
	return $columns;
}
add_filter( 'manage_edit-products_sortable_columns', 'custom_pagerank_sortable' );

function pagerank_column_orderby( $vars ) {
	if ( isset( $vars['orderby'] ) && 'custom_pagerank' == $vars['orderby'] ) {
		$vars = array_merge( $vars, array(
			'orderby' => 'menu_order'
		) );
	}
 
	return $vars;
}
add_filter( 'request', 'pagerank_column_orderby' );



/* THEME SETTINGS PAGE FUNCTIONS ********************************************/

function setup_theme_admin_menus() {  
    add_menu_page('BULLETT Shop Theme', 'Theme settings', 'manage_options', 'SHOP_theme_settings', 'SHOP_settings_page', '', 11);  
          
    add_submenu_page('SHOP_theme_settings', 'Front Page Elements', 'Front Page', 'manage_options', 'front-page-elements', 'SHOP_front_page_settings');   
}  

function SHOP_settings_page() {
	echo "Settings page";
	if (!current_user_can('manage_options')) {  
		wp_die('You do not have sufficient permissions to access this page.');  
	}
}
function SHOP_front_page_settings() {  
	if (!current_user_can('manage_options')) {  
		wp_die('You do not have sufficient permissions to access this page.');  
	}

	if ( isset($_POST['update_settings']) ) {
		update_option("homepage_vintage_item1img", esc_attr($_POST["homepage_vintage_item1img"]) );
		update_option("homepage_vintage_item1link", esc_attr($_POST["homepage_vintage_item1link"]) );
		update_option("homepage_vintage_item2img", esc_attr($_POST["homepage_vintage_item2img"]) );
		update_option("homepage_vintage_item2link", esc_attr($_POST["homepage_vintage_item2link"]) );
		update_option("homepage_vintage_item3img", esc_attr($_POST["homepage_vintage_item3img"]) );
		update_option("homepage_vintage_item3link", esc_attr($_POST["homepage_vintage_item3link"]) );
		update_option("homepage_vintage_item4img", esc_attr($_POST["homepage_vintage_item4img"]) );
		update_option("homepage_vintage_item4link", esc_attr($_POST["homepage_vintage_item4link"]) );
		update_option("homepage_exclusives_img", esc_attr($_POST["homepage_exclusives_img"]) );
		update_option("homepage_exclusives_link", esc_attr($_POST["homepage_exclusives_link"]) );

	}

?>  
	<div class="wrap">  
		<?php screen_icon('themes'); ?> <h2>Front page elements</h2>  
		<?php if ( isset($_POST['update_settings']) ) echo "<div id=\"message\" class=\"updated\">Settings saved</div>"; ?>

		<form method="POST" action="">
			<input type="hidden" name="update_settings" value="Y" />
			<table class="form-table">
				<h3>BULLETT Exclusives Landscape image</h3>  
				<tr valign="top">
					<th scope="row"><label for="homepage_vintage_item1img">Image url:</label></th>  
					<td>
						<input type="text" name="homepage_exclusives_img" size="25" value="<?php echo get_option("homepage_exclusives_img"); ?>" />
						<p class="description">eg. http://shop.bullettmedia.com/wp-content/2013/02/example.jpg</p>
					</td>
					<td><label for="homepage_vintage_item1link">Product link:</label></td>
					<td>
						<input type="text" name="homepage_exclusives_link" size="25" value="<?php echo get_option("homepage_exclusives_link"); ?>" />
						<p class="description">eg. http://shop.bullettmedia.com/products/some_store_item/</p>
					</td>
				</tr>
			</table>
			<table class="form-table">
				<h3>Images and links for the Vintage Featured items</h3>  
				<tr valign="top">  
					<th scope="row"><label for="homepage_vintage_item1img">Item 1 image url:</label></th>  
					<td>
						<input type="text" name="homepage_vintage_item1img" size="25" value="<?php echo get_option("homepage_vintage_item1img"); ?>" />
						<p class="description">eg. http://shop.bullettmedia.com/wp-content/2013/02/example.jpg</p>
					</td>
					<td><label for="homepage_vintage_item1link">Item 1 product link:</label></td>
					<td>
						<input type="text" name="homepage_vintage_item1link" size="25" value="<?php echo get_option("homepage_vintage_item1link"); ?>" />
						<p class="description">eg. http://shop.bullettmedia.com/products/some_store_item/</p>
					</td>
				</tr>
				<tr valign="top">  
					<th scope="row"><label for="homepage_vintage_item2img">Item 2 image url:</label></th>  
					<td><input type="text" name="homepage_vintage_item2img" size="25" value="<?php echo get_option("homepage_vintage_item2img"); ?>" /></td>
					<td><label for="homepage_vintage_item2link">Item 2 product link:</label></td>
					<td><input type="text" name="homepage_vintage_item2link" size="25" value="<?php echo get_option("homepage_vintage_item2link"); ?>" /></td>
				</tr>
				<tr valign="top">  
					<th scope="row"><label for="homepage_vintage_item3img">Item 3 image url:</label></th>  
					<td><input type="text" name="homepage_vintage_item3img" size="25" value="<?php echo get_option("homepage_vintage_item3img"); ?>" /></td>
					<td><label for="homepage_vintage_item3link">Item 3 product link:</label></td>
					<td><input type="text" name="homepage_vintage_item3link" size="25" value="<?php echo get_option("homepage_vintage_item3link"); ?>" /></td>
				</tr>
				<tr valign="top">  
					<th scope="row"><label for="homepage_vintage_item4img">Item 4 image url:</label></th>  
					<td><input type="text" name="homepage_vintage_item4img" size="25" value="<?php echo get_option("homepage_vintage_item4img"); ?>" /></td>
					<td><label for="homepage_vintage_item4link">Item 4 product link:</label></td>
					<td><input type="text" name="homepage_vintage_item4link" size="25" value="<?php echo get_option("homepage_vintage_item4link"); ?>" /></td>
				</tr>
			</table>
			<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>

		</form>
 
	</div><!-- end of .wrap -->
<?php  
}  
add_action("admin_menu", "setup_theme_admin_menus");

?>
