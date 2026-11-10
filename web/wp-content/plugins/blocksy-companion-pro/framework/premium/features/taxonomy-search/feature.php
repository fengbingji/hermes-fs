<?php

namespace Blocksy;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TaxonomySearch {
	public function __construct() {
		add_filter(
			'posts_search',
			function ( $search, \WP_Query $query ) {
				if (
				! $query->is_search()
				||
				is_admin()
				||
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reading GET parameter for search functionality, not form processing
				! isset( $_GET['ct_search_taxonomies'] )
				||
				empty( $query->get( 's' ) )
				) {
					return $search;
				}

				global $wpdb;

				$post_types = $query->get( 'post_type' );

				if ( ! is_array( $post_types ) ) {
					$post_types = array( $post_types );
				}

				$taxonomies = array_filter(
					get_object_taxonomies( $post_types, 'objects' ),
					array( $this, 'is_searchable_taxonomy' )
				);

				if ( empty( $taxonomies ) ) {
					return $search;
				}

				$terms = get_terms(
					array(
						'taxonomy'   => array_keys( $taxonomies ),
						'name__like' => strtolower( $query->get( 's' ) ),
					)
				);

				if ( empty( $terms ) || is_wp_error( $terms ) ) {
					return $search;
				}

				$term_ids = array();

				foreach ( $terms as $term ) {
					$term_ids[] = $term->term_id;

					if ( $taxonomies[ $term->taxonomy ]->hierarchical ) {
						$term_ids = array_merge(
							$term_ids,
							get_term_children( $term->term_id, $term->taxonomy )
						);
					}
				}

				$post_ids = get_objects_in_term(
					array_unique( $term_ids ),
					array_keys( $taxonomies )
				);

				if ( empty( $post_ids ) || is_wp_error( $post_ids ) ) {
					return $search;
				}

				return str_replace(
					')))',
					")) OR ({$wpdb->posts}.ID IN (" . implode( ',', $post_ids ) . ')))',
					$search
				);
			},
			9,
			2
		);
	}

	public function is_searchable_taxonomy( $taxonomy ) {
		if ( $taxonomy->public && $taxonomy->show_ui ) {
			return true;
		}

		return (
			function_exists( 'taxonomy_is_product_attribute' )
			&&
			taxonomy_is_product_attribute( $taxonomy->name )
		);
	}
}
