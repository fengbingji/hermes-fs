<?php

namespace Blocksy;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ContentBlocksViewingAccess {
	private $post_types = array(
		'ct_content_block',
		'ct_thank_you_page',
	);

	public function __construct() {
		add_filter( 'register_post_type_args', array( $this, 'set_rest_controller' ), 10, 2 );
		add_action( 'template_redirect', array( $this, 'limit_single_view' ) );
	}

	public function set_rest_controller( $args, $post_type ) {
		if ( in_array( $post_type, $this->post_types, true ) ) {
			$args['rest_controller_class'] = '\Blocksy\ContentBlocksRestController';
		}

		return $args;
	}

	public function limit_single_view() {
		if ( ! is_singular( $this->post_types ) ) {
			return;
		}

		if ( current_user_can( 'edit_post', get_queried_object_id() ) ) {
			return;
		}

		global $wp_query;

		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
	}
}
