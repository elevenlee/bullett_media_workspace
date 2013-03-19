<?php
/**
 * Resigstering product meta box
 */

/******************** META BOX DEFINITIONS ********************/
// Prefix of meta keys
global $prefix;
$prefix = 'products_';

global $meta_boxes;

$meta_boxes = array();

// Standard meta box
$meta_boxes[] = array(
	'id' => 'product_information',
	'title' => 'Product Information',
	'pages' => array('products'),
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

?>
