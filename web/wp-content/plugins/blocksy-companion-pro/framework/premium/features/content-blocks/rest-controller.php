<?php

namespace Blocksy;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ContentBlocksRestController extends \WP_REST_Posts_Controller {
	public function get_items_permissions_check( $request ) {
		$post_type = get_post_type_object( $this->post_type );

		if ( ! current_user_can( $post_type->cap->edit_posts ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'Sorry, you are not allowed to view these items.', 'blocksy-companion' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return parent::get_items_permissions_check( $request );
	}

	public function check_read_permission( $post ) {
		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return false;
		}

		return parent::check_read_permission( $post );
	}
}
