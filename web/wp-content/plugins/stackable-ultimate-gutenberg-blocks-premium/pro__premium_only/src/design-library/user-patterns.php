<?php
/**
 * User-saved Design Library patterns stored as a private CPT.
 *
 * @package Stackable Premium
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Stackable_User_Patterns' ) ) {

	/**
	 * Manages stk-user-pattern custom post type and REST endpoints.
	 */
	class Stackable_User_Patterns {

		const POST_TYPE = 'stk-user-pattern';

		const META_CATEGORY = '_stk_pattern_category';

		const MAX_LABEL_LENGTH = 200;

		const MAX_CATEGORY_LENGTH = 100;

		const MAX_TEMPLATE_LENGTH = 500000;

		/**
		 * Constructor.
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'register_post_type' ) );
			add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		}

		/**
		 * Register the private custom post type for user-saved patterns.
		 */
		public function register_post_type() {
			register_post_type(
				self::POST_TYPE,
				array(
					'labels' => array(
						'name' => __( 'Stackable User Patterns', STACKABLE_I18N ),
						'singular_name' => __( 'Stackable User Pattern', STACKABLE_I18N ),
					),
					'public' => false,
					'publicly_queryable' => false,
					'show_ui' => false,
					'show_in_menu' => false,
					'show_in_admin_bar' => false,
					'show_in_rest' => false,
					'exclude_from_search' => true,
					'has_archive' => false,
					'rewrite' => false,
					'query_var' => false,
					'map_meta_cap' => true,
					'capability_type' => 'post',
					'supports' => array( 'title', 'editor', 'excerpt' ),
				)
			);
		}

		/**
		 * Register REST routes used exclusively by the Design Library.
		 */
		public function register_routes() {
			register_rest_route(
				'stackable/v3',
				'/user_patterns',
				array(
					array(
						'methods' => 'GET',
						'callback' => array( $this, 'get_patterns' ),
						'permission_callback' => array( $this, 'can_view_patterns' ),
					),
					array(
						'methods' => 'POST',
						'callback' => array( $this, 'create_pattern' ),
						'permission_callback' => array( $this, 'can_manage_patterns' ),
						'args' => $this->get_pattern_args(),
					),
					array(
						'methods' => 'DELETE',
						'callback' => array( $this, 'delete_patterns' ),
						'permission_callback' => array( $this, 'can_manage_patterns' ),
						'args' => array(
							'ids' => array(
								'required' => true,
								'type' => 'array',
							),
						),
					),
				)
			);

			register_rest_route(
				'stackable/v3',
				'/user_patterns/import',
				array(
					'methods' => 'POST',
					'callback' => array( $this, 'import_patterns' ),
					'permission_callback' => array( $this, 'can_import_patterns' ),
					'args' => array(
						'patterns' => array(
							'required' => true,
							'type' => 'array',
						),
						'option' => array(
							'type' => 'string',
							'default' => 'ADD',
						),
					),
				)
			);

			register_rest_route(
				'stackable/v3',
				'/user_patterns/(?P<slug>[\w-]+)',
				array(
					array(
						'methods' => 'PUT',
						'callback' => array( $this, 'update_pattern' ),
						'permission_callback' => array( $this, 'can_manage_patterns' ),
						'args' => $this->get_pattern_args(),
					),
				)
			);
		}

		/**
		 * REST args for create/update pattern requests.
		 *
		 * @return array
		 */
		private function get_pattern_args() {
			return array(
				'id' => array(
					'type' => 'string',
					'required' => true,
				),
				'label' => array(
					'type' => 'string',
					'required' => true,
				),
				'description' => array(
					'type' => 'string',
					'default' => '',
				),
				'category' => array(
					'type' => 'string',
					'required' => true,
				),
				'template' => array(
					'type' => 'string',
					'default' => '',
				),
			);
		}

		/**
		 * Permission check for viewing and using patterns in the design library.
		 *
		 * @return bool
		 */
		public function can_view_patterns() {
			return current_user_can( 'edit_posts' );
		}

		/**
		 * Permission check for creating, updating, and deleting patterns.
		 *
		 * @return bool
		 */
		public function can_manage_patterns() {
			return current_user_can( 'edit_theme_options' );
		}

		/**
		 * Permission check for bulk-importing patterns.
		 *
		 * @return bool
		 */
		public function can_import_patterns() {
			return current_user_can( 'manage_options' );
		}

		/**
		 * Get all user-saved patterns.
		 *
		 * @return WP_REST_Response
		 */
		public function get_patterns() {
			return rest_ensure_response( self::get_all_patterns() );
		}

		/**
		 * Create a new user-saved pattern.
		 *
		 * @param WP_REST_Request $request Request object.
		 * @return WP_REST_Response|WP_Error
		 */
		public function create_pattern( $request ) {
			$pattern = $this->sanitize_pattern_input( $request->get_params() );
			if ( is_wp_error( $pattern ) ) {
				return $pattern;
			}

			if ( $this->get_post_by_slug( $pattern['id'] ) ) {
				return new WP_Error(
					'duplicate_pattern',
					__( 'A pattern with this label already exists. Please choose a different one.', STACKABLE_I18N ),
					array( 'status' => 400 )
				);
			}

			if ( $this->label_exists( $pattern['label'] ) ) {
				return new WP_Error(
					'duplicate_pattern_label',
					__( 'A pattern with this label already exists. Please choose a different one.', STACKABLE_I18N ),
					array( 'status' => 400 )
				);
			}

			$post_id = wp_insert_post(
				array(
					'post_type' => self::POST_TYPE,
					'post_status' => 'publish',
					'post_title' => $pattern['label'],
					'post_name' => $pattern['id'],
					'post_content' => $pattern['template'],
					'post_excerpt' => $pattern['description'],
				),
				true
			);

			if ( is_wp_error( $post_id ) ) {
				return $post_id;
			}

			update_post_meta( $post_id, self::META_CATEGORY, $pattern['category'] );

			return rest_ensure_response( self::post_to_pattern( get_post( $post_id ) ) );
		}

		/**
		 * Update an existing user-saved pattern.
		 *
		 * @param WP_REST_Request $request Request object.
		 * @return WP_REST_Response|WP_Error
		 */
		public function update_pattern( $request ) {
			$original_slug = sanitize_title( $request->get_param( 'slug' ) );
			$post = $this->get_post_by_slug( $original_slug );

			if ( ! $post ) {
				return new WP_Error(
					'pattern_not_found',
					__( 'Pattern not found.', STACKABLE_I18N ),
					array( 'status' => 404 )
				);
			}

			$pattern = $this->sanitize_pattern_input( $request->get_params() );
			if ( is_wp_error( $pattern ) ) {
				return $pattern;
			}

			$duplicate_post = $this->get_post_by_slug( $pattern['id'] );
			if ( $duplicate_post && (int) $duplicate_post->ID !== (int) $post->ID ) {
				return new WP_Error(
					'duplicate_pattern',
					__( 'A pattern with this label already exists. Please choose a different one.', STACKABLE_I18N ),
					array( 'status' => 400 )
				);
			}

			if ( $this->label_exists( $pattern['label'], (int) $post->ID ) ) {
				return new WP_Error(
					'duplicate_pattern_label',
					__( 'A pattern with this label already exists. Please choose a different one.', STACKABLE_I18N ),
					array( 'status' => 400 )
				);
			}

			$update_args = array(
				'ID' => $post->ID,
				'post_title' => $pattern['label'],
				'post_name' => $pattern['id'],
				'post_excerpt' => $pattern['description'],
			);

			$updated = wp_update_post( $update_args, true );
			if ( is_wp_error( $updated ) ) {
				return $updated;
			}

			update_post_meta( $post->ID, self::META_CATEGORY, $pattern['category'] );

			return rest_ensure_response( self::post_to_pattern( get_post( $post->ID ) ) );
		}

		/**
		 * Delete one or more patterns by slug.
		 *
		 * @param WP_REST_Request $request Request object.
		 * @return WP_REST_Response|WP_Error
		 */
		public function delete_patterns( $request ) {
			$ids = $request->get_param( 'ids' );
			if ( ! is_array( $ids ) || empty( $ids ) ) {
				return new WP_Error(
					'invalid_ids',
					__( 'No patterns specified for deletion.', STACKABLE_I18N ),
					array( 'status' => 400 )
				);
			}

			$deleted = array();

			foreach ( $ids as $slug ) {
				$post = $this->get_post_by_slug( sanitize_title( $slug ) );
				if ( $post ) {
					wp_delete_post( $post->ID, true );
					$deleted[] = $slug;
				}
			}

			return rest_ensure_response(
				array(
					'deleted' => $deleted,
					'patterns' => $this->get_patterns_data(),
				)
			);
		}

		/**
		 * Bulk import patterns from export file.
		 *
		 * @param WP_REST_Request $request Request object.
		 * @return WP_REST_Response|WP_Error
		 */
		public function import_patterns( $request ) {
			$patterns = $request->get_param( 'patterns' );
			$option = strtoupper( $request->get_param( 'option' ) ?: 'ADD' );

			if ( ! in_array( $option, array( 'ADD', 'SKIP', 'OVERWRITE' ), true ) ) {
				$option = 'ADD';
			}

			if ( ! is_array( $patterns ) || empty( $patterns ) ) {
				return new WP_Error(
					'invalid_patterns',
					__( 'No patterns to import.', STACKABLE_I18N ),
					array( 'status' => 400 )
				);
			}

			if ( count( $patterns ) > 200 ) {
				return new WP_Error(
					'too_many_patterns',
					__( 'Too many patterns to import at once.', STACKABLE_I18N ),
					array( 'status' => 400 )
				);
			}

			$existing_patterns = $this->get_patterns_data();
			$existing_ids = array();
			$existing_labels = array();

			foreach ( $existing_patterns as $existing_pattern ) {
				$existing_ids[ $existing_pattern['id'] ] = true;
				$existing_labels[ strtolower( $existing_pattern['label'] ) ] = true;
			}

			foreach ( $patterns as $raw_pattern ) {
				if ( ! is_array( $raw_pattern ) ) {
					continue;
				}

				$pattern = $this->sanitize_pattern_input( $raw_pattern );
				if ( is_wp_error( $pattern ) ) {
					continue;
				}

				$is_duplicate_id = isset( $existing_ids[ $pattern['id'] ] );
				$is_duplicate_label = isset( $existing_labels[ strtolower( $pattern['label'] ) ] );

				if ( $option === 'SKIP' && ( $is_duplicate_id || $is_duplicate_label ) ) {
					continue;
				}

				if ( $option === 'ADD' && ( $is_duplicate_id || $is_duplicate_label ) ) {
					$suffix = 2;
					$base_id = preg_replace( '/-\d+$/', '', $pattern['id'] );
					$base_label = preg_replace( '/ \(\d+\)$/', '', $pattern['label'] );
					$new_id = $base_id . '-' . $suffix;
					$new_label = $base_label . ' (' . $suffix . ')';

					while ( isset( $existing_ids[ $new_id ] ) || isset( $existing_labels[ strtolower( $new_label ) ] ) ) {
						$suffix++;
						$new_id = $base_id . '-' . $suffix;
						$new_label = $base_label . ' (' . $suffix . ')';
					}

					$pattern['id'] = $new_id;
					$pattern['label'] = $new_label;
				}

				if ( $option === 'OVERWRITE' && $is_duplicate_id ) {
					$existing_post = $this->get_post_by_slug( $pattern['id'] );
					if ( $existing_post ) {
						wp_update_post(
							array(
								'ID' => $existing_post->ID,
								'post_title' => $pattern['label'],
								'post_name' => $pattern['id'],
								'post_content' => $pattern['template'],
								'post_excerpt' => $pattern['description'],
							)
						);
						update_post_meta( $existing_post->ID, self::META_CATEGORY, $pattern['category'] );
						$existing_labels[ strtolower( $pattern['label'] ) ] = true;
						continue;
					}
				}

				if ( $option === 'OVERWRITE' && ! $is_duplicate_id && $is_duplicate_label ) {
					continue;
				}

				$post_id = wp_insert_post(
					array(
						'post_type' => self::POST_TYPE,
						'post_status' => 'publish',
						'post_title' => $pattern['label'],
						'post_name' => $pattern['id'],
						'post_content' => $pattern['template'],
						'post_excerpt' => $pattern['description'],
					),
					true
				);

				if ( ! is_wp_error( $post_id ) ) {
					update_post_meta( $post_id, self::META_CATEGORY, $pattern['category'] );
					$existing_ids[ $pattern['id'] ] = true;
					$existing_labels[ strtolower( $pattern['label'] ) ] = true;
				}
			}

			return rest_ensure_response( $this->get_patterns_data() );
		}

		/**
		 * Get patterns as plain arrays.
		 *
		 * @return array
		 */
		public static function get_all_patterns() {
			$posts = get_posts(
				array(
					'post_type' => self::POST_TYPE,
					'post_status' => 'publish',
					'posts_per_page' => -1,
					'orderby' => 'title',
					'order' => 'ASC',
				)
			);

			return array_map( array( __CLASS__, 'post_to_pattern' ), $posts );
		}

		/**
		 * Get patterns as plain arrays.
		 *
		 * @return array
		 */
		public function get_patterns_data() {
			return self::get_all_patterns();
		}

		/**
		 * Convert a post object to the pattern shape used by the Design Library.
		 *
		 * @param WP_Post $post Post object.
		 * @return array
		 */
		private static function post_to_pattern( $post ) {
			return array(
				'id' => $post->post_name,
				'label' => $post->post_title,
				'description' => $post->post_excerpt,
				'category' => get_post_meta( $post->ID, self::META_CATEGORY, true ) ?: __( 'General', STACKABLE_I18N ),
				'template' => $post->post_content,
				'modified' => $post->post_modified_gmt,
			);
		}

		/**
		 * Find a pattern post by slug.
		 *
		 * @param string $slug Pattern slug.
		 * @return WP_Post|null
		 */
		private function get_post_by_slug( $slug ) {
			if ( empty( $slug ) ) {
				return null;
			}

			$posts = get_posts(
				array(
					'post_type' => self::POST_TYPE,
					'name' => $slug,
					'post_status' => 'publish',
					'posts_per_page' => 1,
				)
			);

			return ! empty( $posts ) ? $posts[0] : null;
		}

		/**
		 * Check whether a label already exists.
		 *
		 * @param string $label Pattern label.
		 * @param int    $exclude_post_id Post ID to exclude from the check.
		 * @return bool
		 */
		private function label_exists( $label, $exclude_post_id = 0 ) {
			$post = get_page_by_title( $label, OBJECT, self::POST_TYPE );

			if ( ! $post || $post->post_status !== 'publish' ) {
				return false;
			}

			if ( $exclude_post_id && (int) $post->ID === (int) $exclude_post_id ) {
				return false;
			}

			return true;
		}

		/**
		 * Sanitize pattern input from REST requests.
		 *
		 * @param array $input Raw input.
		 * @return array|WP_Error
		 */
		private function sanitize_pattern_input( $input ) {
			if ( ! is_array( $input ) ) {
				return new WP_Error(
					'invalid_pattern',
					__( 'Invalid pattern data.', STACKABLE_I18N ),
					array( 'status' => 400 )
				);
			}

			$label = isset( $input['label'] ) ? sanitize_text_field( $input['label'] ) : '';
			$id = isset( $input['id'] ) ? sanitize_title( $input['id'] ) : '';
			$category = isset( $input['category'] ) ? sanitize_text_field( $input['category'] ) : '';
			$description = isset( $input['description'] ) ? sanitize_text_field( $input['description'] ) : '';
			$template = isset( $input['template'] ) ? $input['template'] : '';

			if ( empty( $label ) || empty( $id ) || empty( $category ) ) {
				return new WP_Error(
					'invalid_pattern',
					__( 'Pattern label and category are required.', STACKABLE_I18N ),
					array( 'status' => 400 )
				);
			}

			if ( strlen( $label ) > self::MAX_LABEL_LENGTH ) {
				return new WP_Error(
					'invalid_pattern',
					__( 'Pattern label is too long.', STACKABLE_I18N ),
					array( 'status' => 400 )
				);
			}

			if ( strlen( $category ) > self::MAX_CATEGORY_LENGTH ) {
				return new WP_Error(
					'invalid_pattern',
					__( 'Pattern category is too long.', STACKABLE_I18N ),
					array( 'status' => 400 )
				);
			}

			if ( strlen( $template ) > self::MAX_TEMPLATE_LENGTH ) {
				return new WP_Error(
					'invalid_pattern',
					__( 'Pattern content is too large.', STACKABLE_I18N ),
					array( 'status' => 400 )
				);
			}

			return array(
				'id' => $id,
				'label' => $label,
				'description' => $description,
				'category' => $category,
				'template' => $template,
			);
		}
	}

	add_filter( 'stackable_design_library_saved_patterns', array( 'Stackable_User_Patterns', 'get_all_patterns' ) );

	new Stackable_User_Patterns();
}
