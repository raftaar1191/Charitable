<?php
/**
 * Charitable Meta Box Helper
 *
 * @package 	Charitable/Classes/Charitable_Meta_Box_Helper
 * @version     1.0.0
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2017, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Charitable_Meta_Box_Helper' ) ) :

	/**
	 * Charitable Meta Box Helper
	 *
	 * @since   1.0.0
	 */
	class Charitable_Meta_Box_Helper {

		/**
		 * Nonce action.
		 *
		 * @since 1.0.0
		 *
		 * @var   string 		
		 */
		protected $nonce_action;

		/**
		 * Nonce name.
		 *
		 * @since 1.0.0
		 *
		 * @var   string 		
		 */
		protected $nonce_name = '_charitable_nonce';

		/**
		 * Whether nonce has been added.
		 *
		 * @since 1.0.0
		 *
		 * @var   boolean
		 */
		protected $nonce_added = false;

		/**
		 * Current WP_Post object.
		 *
		 * @since 1.5.0
		 *
		 * @var   WP_Post
		 */
		protected $post;

		/**
		 * Custom keys for the current post.
		 *
		 * @since 1.5.0
		 *
		 * @var   array
		 */
		protected $post_custom_keys;		

		/**
		 * Create a helper instance.
		 *
		 * @since  1.0.0
		 *
		 * @global WP_Post $post
		 *
		 * @param  string $nonce_action
		 */
		public function __construct( $nonce_action = 'charitable' ) {
			global $post;

			$this->nonce_action = $nonce_action;
			$this->post         = $post;
		} 

		/**
		 * Metabox callback wrapper.
		 *
		 * Every meta box is registered with this method as its callback, 
		 * and then delegates to the appropriate view.
		 *
		 * @since   1.0.0
		 *
		 * @param 	WP_Post $post 		The post object.
		 * @param 	array $args 		The arguments passed to the meta box, including the view to render.
		 * @return 	void
		 */
		public function metabox_display( WP_Post $post, array $args ) {	
			if ( ! isset( $args['args']['view'] ) ) {
				return;
			}	

			$view_args = $args['args'];
			unset( $view_args['view'] );

			$this->display( $args['args']['view'], $view_args );
		}

		/**
		 * Display a metabox with the given view.
		 *
		 * @since   1.0.0
		 *
		 * @param 	string $view 		The view to render.
		 * @return 	void
		 */
		public function display( $view, $view_args ) {		
			/**
			 * Set the nonce.
			 */
			if ( $this->nonce_added === false ) {

				wp_nonce_field( $this->nonce_action, $this->nonce_name );

				$this->nonce_added = true;
			}

			do_action( 'charitable_metabox_before', $view, $view_args );

			charitable_admin_view( $view, $view_args );

			do_action( 'charitable_metabox_after', $view, $view_args );
		}

		/**
		 * Display the fields to show inside a metabox.
		 *
		 * The fields parameter should contain an array of fields, 
		 * all of which are arrays with a 'priority' key and a 'view' 
		 * key.
		 *
		 * @since   1.0.0
		 *
		 * @param 	array $fields
		 * @return 	void
		 */
		public function display_fields( array $fields ) {
			/**
			 * Sort the fields by priority.
			 */
			usort( $fields, 'charitable_priority_sort' );

			$callback = array( $this, 'display_field' );

			array_walk( $callback, $fields );
		}
		
		/**
		 * Verifies that the user who is currently logged in has permission to save the data
		 * from the meta box to the database.
		 *
		 * Hat tip Tom McFarlin: http://tommcfarlin.com/wordpress-meta-boxes-each-component/
		 *
		 * @since   1.0.0
		 *
		 * @param 	integer $post_id 	The current post being saved.
		 * @return 	boolean 			True if the user can save the information
		 */
		public function user_can_save( $post_id ) {
		    $is_autosave    = wp_is_post_autosave( $post_id );
		    $is_revision    = wp_is_post_revision( $post_id );
		    $is_valid_nonce = ( isset( $_POST[ $this->nonce_name ] ) && wp_verify_nonce( $_POST[ $this->nonce_name ], $this->nonce_action ) );

		    return ! ( $is_autosave || $is_revision ) && $is_valid_nonce;
		}
		
		/**
		 * Display a field inside a meta box.
		 *
		 * @since  1.5.0
		 *
		 * @param  array $field Field definition.
		 * @return void
		 */
		protected function display_field( $field ) {
			$view           = $this->get_field_view( $field );
			$field['key']   = $this->get_field_key( $field );
			$field['value'] = $this->get_field_value( $field );

			charitable_admin_view( $field['view'], $field );
		}

		/**
		 * Return the key for a particular field.
		 *
		 * @since  1.5.0
		 *
		 * @param  array $field Field definition.
		 * @return string|void
		 */
		protected function get_field_key( $field ) {
			foreach ( array( 'key', 'meta_key' ) as $key ) {
				if ( array_key_exists( $key, $field ) ) {
					return $field[ $key ];
				}
			}
		}

		/**
		 * Return the current value of a particular field.
		 *
		 * @since  1.5.0
		 *
		 * @param  array $field Field definition.
		 * @return string
		 */
		protected function get_field_value( $field ) {
			if ( array_key_exists( 'value', $field ) ) {
				return $field['value'];
			}

			if ( empty( $field['key'] ) ) {
				return;
			}

			$default = array_key_exists( 'default', $field ) ? $field['default'] : '';

			if ( ! is_a( $this->post, 'WP_Post' ) ) {
				return $default;
			}

			if ( array_key_exists( 'meta_key', $field ) && in_array( $field['meta_key'], $this->get_post_custom_keys() ) ) {
				return get_post_meta( $this->post->ID, $field['meta_key'], true );
			}

			return $default;
		}

		/**
		 * Return the custom keys for the current post.
		 *
		 * @since  1.5.0
		 *
		 * @return array
		 */
		protected function get_post_custom_keys() {
			if ( ! is_a( $this->post, 'WP_Post' ) ) {
				return array();
			}

			if ( ! isset( $this->post_custom_keys ) ) {
				$this->post_custom_keys = get_post_custom_keys( $this->post->ID );

				if ( ! is_array( $this->post_custom_keys ) ) {
					$this->post_custom_keys = array();
				}
			}

			return $this->post_custom_keys;
		}
	}

endif;
