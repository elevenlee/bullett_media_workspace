<?php
/**
 * Resigstering product meta box
 */

/******************** META BOX DEFINITIONS ********************/
// Prefix of meta keys
$prefix = 'products_';

global $meta_boxes;

$meta_boxes = array();

// Standard meta box
$meta_boxes[] = array(
	'id' => 'product_information',
	'title' => 'Product Information',
	'pages' => array('products', 'page'),
	'context' => 'side',
	'priority' => 'high',

	'fields' => array(
		// Product ID
		array(
			'name' => 'Product ID',
			'id' => "${prefix}product_id",
			'desc' => 'Product ID',
			'type' => 'text',
			'size' => '12',
			'std' => '',
			'clone' => true,
		),
		// Product description
		array(
			'name' => 'Product Description',
			'id' => "${prefix}product_description",
			'desc' => 'Product Description',
			'type' => 'textarea',
			'cols' => '20',
			'rows' => '4',
			'std' => '',
			'clone' => true,
		),
	),

	'validation' => array(
		'rules' => array (
			"${prefix}product_id" => array(
				'required' => true,
			),
			"${prefix}product_description" => array(
				'required' => true,
			)
		),
		// Optional override of default jquery.validate messages
		'message' => array(
			"${prefix}product_id" => array(
				'required' => 'Product ID is required',
			),
			"${prefix}product_description" => array(
				'required' => 'Product description is required',
			)
		),
	)
);

/******************** META BOX REGISTERING *******************/
/**
 * Register meta boxes
 *
 * @return void
 */
function products_register_meta_boxes()
{
	// Make sure there's no errors when the plugin is deactivated or
	// during upgrade
	if (!class_exists('RW_Meta_Box'))
		return;
	
	global $meta_boxes;
	foreach ($meta_boxes as $meta_box)
	{
		new RW_Meta_Box($meta_box);
	}
}

// Hook to 'admin_init' to make sure the meta box class is loaded before
add_action('admin_init', 'products_register_meta_boxes');
?>
