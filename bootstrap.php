<?php

use Underpin\Abstracts\Underpin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Add this loader.
Underpin::attach( 'setup', new \Underpin\Factories\Observers\Loader( 'taxonomies', [
	'instance' => 'Underpin\Taxonomies\Abstracts\Taxonomy',
	'default'  => 'Underpin\Taxonomies\Factories\Taxonomy_Instance',
] ) );