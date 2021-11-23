<?php

namespace Underpin\Taxonomies\Factories;


use Underpin\Traits\Instance_Setter;
use Underpin\Taxonomies\Abstracts\Taxonomy;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Taxonomy_Instance extends Taxonomy {

	use Instance_Setter;

	public function __construct( $args ) {
		$this->set_values( $args );
	}

}