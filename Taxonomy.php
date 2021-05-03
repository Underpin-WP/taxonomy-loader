<?php
/**
 * Taxonomy Abstraction.
 *
 * @since   1.0.0
 * @package Underpin\Abstracts
 */


namespace Underpin_Taxonomies\Abstracts;


use Underpin\Traits\Feature_Extension;
use function Underpin\underpin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Taxonomy
 *
 * @since   1.0.0
 * @package Underpin\Abstracts
 */
abstract class Taxonomy {

	use Feature_Extension;

	/**
	 * Taxonomy ID (slug).
	 *
	 * @since 1.2.0
	 *
	 * @var string The Taxonomy identifier
	 */
	protected $id = '';

	/**
	 * The post type args.
	 *
	 * @since 1.0.0
	 *
	 * @var array The list of post type args. See https://developer.wordpress.org/reference/functions/register_post_type/
	 */
	protected $args = [];

	/**
	 * The post type, or types to use.
	 *
	 * @var string|array A single post type, or an array of registered post types.
	 */
	protected $post_type = 'post';

	/**
	 * @inheritDoc
	 */
	public function do_actions() {
		add_action( 'init', [ $this, 'register' ], 11 );
	}

	/**
	 * Registers the post type.
	 *
	 * @since 1.0.0
	 */
	public function register() {

		// Fallback to name.
		if ( ! isset( $this->args['label'] ) ) {
			$this->args['label'] = $this->name;
		}

		$registered = register_taxonomy( $this->id, $this->post_type, $this->args );

		if ( is_wp_error( $registered ) ) {
			underpin()->logger()->log_wp_error( 'error', $registered );
		} else {
			underpin()->logger()->log(
				'notice',
				'registered_taxonomy',
				'The taxonomy ' . $this->name . ' has been registered to ' . $this->post_type . '.',
				[ 'ref' => $this->name, 'post_type' => $this->post_type ]
			);
		}
	}

	public function __get( $key ) {
		if ( isset( $this->$key ) ) {
			return $this->$key;
		} else {
			return new \WP_Error( 'param_not_set', 'The key ' . $key . ' could not be found.' );
		}
	}

}