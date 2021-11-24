<?php

use Underpin\Abstracts\Underpin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Add this loader.
Underpin::attach( 'setup', new \Underpin\Factories\Observers\Loader( 'taxonomies', [
	'abstraction_class' => 'Underpin\Taxonomies\Abstracts\Taxonomy',
	'default_factory'  => 'Underpin\Taxonomies\Factories\Taxonomy_Instance',
] ) );