<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Add this loader.
add_action( 'underpin/before_setup', function ( $class ) {
	if ( 'Underpin\Underpin' === $class ) {
		require_once( plugin_dir_path( __FILE__ ) . 'Taxonomy.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'Taxonomy_Instance.php' );
		Underpin\underpin()->loaders()->add( 'taxonomies', [
			'instance' => 'Underpin_Taxonomies\Abstracts\Taxonomy',
			'default'  => 'Underpin_Taxonomies\Factories\Taxonomy_Instance',
		] );
	}
} );