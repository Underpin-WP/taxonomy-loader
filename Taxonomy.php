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

	/**
	 * Retrieves the terms for this taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Taxonomy args. See WP_Term_Query for a list of args.
	 *
	 * @return \WP_Term_Query The query results.
	 */
	public function query( array $args ) {
		$args['taxonomy'] = $this->id;
		return new \WP_Term_Query( $args );
	}

	/**
	 * Deletes a single term.
	 *
	 * @since 1.0.0
	 *
	 * @param int          $term          The term ID.
	 * @param bool         $force_delete  Optional. Whether to bypass Trash and force deletion.
	 *                                    Default false.
	 * @param array|string $args {
	 *                                        Optional. Array of arguments to override the default term ID. Default empty
	 *                                        array.
	 *
	 *     @type int  $default       The term ID to make the default term. This will only override
	 *                               the terms found if there is only one term found. Any other and
	 *                               the found terms are used.
	 *     @type bool $force_default Optional. Whether to force the supplied term as default to be
	 *                               assigned even if the object was not going to be term-less.
	 *                               Default false.
	 *     }
	 *
	 * @return bool|int|\WP_Error True on success, false if term does not exist. Zero on attempted
	 *                            deletion of default Category. WP_Error if the taxonomy does not exist.
	 */
	protected function _delete( $term, $args = [] ) {
		return wp_delete_term( $term, $this->id, $args );
	}

	/**
	 * Update a taxonomy term with new data.
	 *
	 * @since 1.0.0
	 *
	 * @param int          $term_id The ID of the term.
	 * @param array|object $args    Term update args. See wp_update_term
	 *
	 * @return array|\WP_Error An array containing the `term_id` and `term_taxonomy_id`,
	 *                         WP_Error otherwise.
	 */
	protected function _update( $term_id, $args ) {
		return wp_update_term( $term_id, $this->id, $args );
	}

	/**
	 * Insert a term.
	 *
	 * @since 1.0.0
	 *
	 * @param string $term     The term name to add.
	 * @param string $taxonomy The taxonomy to which to add the term.
	 * @param array  $args     Term insert args. see wp_insert_term.
	 *
	 * @return array|\WP_Error An array containing the `term_id` and `term_taxonomy_id`,
	 *                         WP_Error otherwise.
	 */
	protected function _insert( $term, $args ) {
		return wp_insert_term( $term, $args, $this->id );
	}

	/**
	 * Saves a term to the database.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 *
	 * @return array|\WP_Error An array containing the `term_id` and `term_taxonomy_id`,
	 *                         WP_Error otherwise.
	 */
	public function save( array $args ) {

		if ( isset( $args['id'] ) ) {
			$id = $args['id'];
			unset( $args['id'] );
		} elseif ( isset( $args['name'] ) ) {
			$name = $args['name'];
			unset( $args['name'] );
		}

		if ( ! isset( $name ) && ! isset( $id ) ) {
			return underpin()->logger()->log_as_error(
				'error',
				'save_term_invalid_args',
				'To save a term, you must provide an id or a term name to create.',
				[ 'args' => $args ]
			);
		}

		$saved = isset( $id ) ? $this->_update( $id, $args ) : $this->_insert( $args );

		if ( is_wp_error( $saved ) ) {
			underpin()->logger()->log_wp_error( $saved );
		} else {
			underpin()->logger()->log(
				'notice',
				$this->id . '_saved',
				'A ' . $this->id . ' term was saved',
				[ $args ]
			);
		}

		return $saved;
	}

	/**
	 * Deletes a single term.
	 *
	 * @since 1.0.0
	 *
	 * @param int          $term          The term ID.
	 * @param bool         $force_delete  Optional. Whether to bypass Trash and force deletion.
	 *                                    Default false.
	 * @param array|string $args {
	 *                                        Optional. Array of arguments to override the default term ID. Default empty
	 *                                        array.
	 *
	 *     @type int  $default       The term ID to make the default term. This will only override
	 *                               the terms found if there is only one term found. Any other and
	 *                               the found terms are used.
	 *     @type bool $force_default Optional. Whether to force the supplied term as default to be
	 *                               assigned even if the object was not going to be term-less.
	 *                               Default false.
	 *     }
	 *
	 * @return bool|int|\WP_Error True on success, false if term does not exist. Zero on attempted
	 *                            deletion of default Category. WP_Error if the taxonomy does not exist.
	 */
	public function delete( $term, $args = [] ) {
		$deleted = $this->_delete( $term, $args );

		if ( false === $deleted ) {
			$deleted = new \WP_Error(
				'term_does_not_exist',
				'The provided term could not be deleted because it does not exist',
				[ 'args' => $args, 'term' => $term ]
			);
			underpin()->logger()->log_wp_error( 'warning', $deleted );
		} elseif ( is_wp_error( $deleted ) ) {
			underpin()->logger()->log_wp_error( 'error', $deleted );
		}

		return $deleted;
	}

}