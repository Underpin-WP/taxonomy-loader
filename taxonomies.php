<?php

use Underpin\Abstracts\Underpin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Add this loader.
Underpin::attach( 'setup', new \Underpin\Factories\Observer( 'taxonomies', [
	'update' => function ( Underpin $plugin, $args ) {
	require_once( plugin_dir_path( __FILE__ ) . 'Taxonomy.php' );
	require_once( plugin_dir_path( __FILE__ ) . 'Taxonomy_Instance.php' );
	$plugin->loaders()->add( 'taxonomies', [
		'instance' => 'Underpin_Taxonomies\Abstracts\Taxonomy',
		'default'  => 'Underpin_Taxonomies\Factories\Taxonomy_Instance',
	] );
	},
] ) );