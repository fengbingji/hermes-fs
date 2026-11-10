<?php

namespace Blocksy\Extensions\WoocommerceExtra;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ProductWaitlistDashboard {
	public function __construct() {
		add_action(
			'wp_ajax_blc_subcribe_to_waitlist',
			array( $this, 'subcribe_to_waitlist' )
		);

		add_action(
			'wp_ajax_nopriv_blc_subcribe_to_waitlist',
			array( $this, 'subcribe_to_waitlist' )
		);

		add_action(
			'wp_ajax_blc_waitlist_unsubscribe',
			array( $this, 'unsubscribe_from_waitlist' )
		);

		add_action(
			'wp_ajax_nopriv_blc_waitlist_unsubscribe',
			array( $this, 'unsubscribe_from_waitlist' )
		);

		add_action(
			'wp_ajax_blc_waitlist_sync',
			array( $this, 'sync' )
		);

		add_action(
			'wp_ajax_nopriv_blc_waitlist_sync',
			array( $this, 'sync' )
		);

		add_action(
			'wp_ajax_blocksy_ext_waitlist_export_users',
			array( $this, 'export_users' )
		);

		add_filter(
			'blocksy:admin:localizations',
			function ( $localize_data ) {
				if (
					isset( $localize_data['dashboard_actions_nonce'] )
					||
					! current_user_can( 'edit_products' )
				) {
					return $localize_data;
				}

				$localize_data['dashboard_actions_nonce'] = wp_create_nonce( 'ct-dashboard' );

				return $localize_data;
			}
		);

		add_action( 'admin_menu', array( $this, 'register_waitlist_page' ) );

		add_action(
			'admin_enqueue_scripts',
			function () {
				if ( ! function_exists( 'get_plugin_data' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}

				$data = get_plugin_data( BLOCKSY__FILE__ );

				if (
					! is_admin()
					||
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					! isset( $_GET['page'] )
					||
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					'blocksy-waitlist-page' !== $_GET['page']
					||
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					! isset( $_GET['tab'] )
					||
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					'users' !== $_GET['tab']
				) {
					return;
				}

				wp_enqueue_script(
					'blocksy-waitlist-page',
					BLOCKSY_URL . 'framework/premium/extensions/woocommerce-extra/features/admin-static/bundle/admin-waitlist.js',
					array(),
					$data['Version'],
					true
				);
			}
		);

		add_action( 'blocksy_waitlist_remove_not_confirmed', array( $this, 'remove_not_confirmed_emails' ) );

		if ( ! wp_next_scheduled( 'blocksy_waitlist_remove_not_confirmed' ) ) {
			/**
			 * Filters how often unconfirmed waitlist subscriptions are purged.
			 *
			 * The value is a cron schedule name as registered with
			 * `cron_schedules`. It is read only when the event is first
			 * scheduled, so changing it later requires clearing the existing
			 * `blocksy_waitlist_remove_not_confirmed` event.
			 *
			 * @since 2.0.70
			 *
			 * @param string $recurrence How often the event recurs. Default 'daily'.
			 */
			$recurrence = apply_filters(
				'blocksy_waitlist_remove_not_confirmed_time',
				'daily'
			);

			wp_schedule_event(
				time(),
				$recurrence,
				'blocksy_waitlist_remove_not_confirmed'
			);
		}
	}

	public function remove_not_confirmed_emails() {
		ProductWaitlistDb::remove_not_confirmed_emails();
	}

	public function sync() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['product_id'] ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid request', 'blocksy-companion' ),
				)
			);
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$product_id = intval( $_POST['product_id'] );

		$product = wc_get_product( $product_id );

		if (
			! $product
			||
			! blocksy_companion_is_post_viewable( array( 'post' => $product->get_id() ) )
		) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid request', 'blocksy-companion' ),
				)
			);
		}

		$waitlist = ProductWaitlistDb::get_waitlist( $product );

		if ( empty( $waitlist ) ) {
			wp_send_json_success(
				array(
					'message' => __( 'No waitlist found', 'blocksy-companion' ),
				)
			);
		}

		wp_send_json_success(
			array(
				'unsubscribe_token' => $waitlist[0]->unsubscribe_token,
			)
		);
	}

	public function export_users() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['product_id'] ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid request', 'blocksy-companion' ),
				)
			);
		}

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid request', 'blocksy-companion' ),
				)
			);
		}

		if ( ! check_ajax_referer( 'ct-dashboard', 'nonce', false ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid request', 'blocksy-companion' ),
				)
			);
		}

		$product = wc_get_product( absint( $_POST['product_id'] ) );

		if ( ! $product ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid request', 'blocksy-companion' ),
				)
			);
		}

		$csv = ProductWaitlistDb::export_users_by_product( $product );

		if ( false === $csv ) {
			wp_send_json_error(
				array(
					'message' => __( 'Could not generate the export', 'blocksy-companion' ),
				)
			);
		}

		$slug = sanitize_title( $product->get_name() );

		if ( empty( $slug ) ) {
			$slug = $product->get_id();
		}

		wp_send_json_success(
			array(
				'filename' => 'waitlist-' . $slug . '.csv',
				'csv'      => $csv,
			)
		);
	}

	public function register_waitlist_page() {
		global $blocksy_waitlists_page;

		$blocksy_waitlists_page = add_submenu_page(
			'edit.php?post_type=product',
			esc_html__( 'Waitlists', 'blocksy-companion' ),
			esc_html__( 'Waitlists', 'blocksy-companion' ),
			'edit_products',
			'blocksy-waitlist-page',
			array( $this, 'render_waitlist_page' )
		);

		add_action( 'load-' . $blocksy_waitlists_page, array( $this, 'waitlist_screen_options' ) );
	}

	public function waitlist_screen_options() {
		global $blocksy_waitlists_page;

		$screen = get_current_screen();

		if (
			! is_object( $screen )
			||
			$screen->id !== $blocksy_waitlists_page
		) {
			return;
		}

		add_screen_option(
			'per_page',
			array(
				'label'   => esc_html__( 'Number of items per page', 'blocksy-companion' ),
				'default' => 20,
				'option'  => 'blocksy_waitlist_per_page',
			)
		);
	}

	public function render_waitlist_page() {
		$list_table  = new Waitlist_Table();
		$table_title = esc_html__( 'Waitlists', 'blocksy-companion' );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$is_user_table = ! empty( $_GET['tab'] ) && 'users' === $_GET['tab'];

		if ( $is_user_table ) {
			$list_table = new Waitlist_Users_Table();
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$product_id = isset( $_GET['product_id'] ) ? sanitize_text_field( wp_unslash( $_GET['product_id'] ) ) : '';

			$table_title = sprintf(
				// translators: %s is the product title
				esc_html__( 'Waitlist for %s', 'blocksy-companion' ),
				get_the_title( $product_id )
			);
		}

		wp_enqueue_style( 'woocommerce_admin_styles' );

		$list_table->prepare_items();
		?>
			<div class="wrap">
				<h1 class="wp-heading-inline"><?php echo esc_html( $table_title ); ?></h1>

				<form id="posts-filter" class="blocksy-waitlist-form" method="get" action="">
					<input type="hidden" name="page" value="blocksy-waitlist-page" />
					<input type="hidden" name="post_type" value="product" />
					<?php $list_table->display(); ?>
				</form>
			</div>
		<?php
	}

	public function subcribe_to_waitlist() {
		if (
			! isset( $_POST['product_id'] )
			||
			! isset( $_POST['email'] )
		) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid request', 'blocksy-companion' ),
				)
			);
		}

		if (
			! isset( $_POST['_wpnonce'] )
			||
			! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'blocksy_waitlist_subscribe' )
		) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid request', 'blocksy-companion' ),
				)
			);
		}

		$product_id = intval( $_POST['product_id'] );
		$email      = sanitize_email( wp_unslash( $_POST['email'] ) );

		$product = wc_get_product( $product_id );

		if (
			! $product
			||
			! blocksy_companion_is_post_viewable( array( 'post' => $product->get_id() ) )
		) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid request', 'blocksy-companion' ),
				)
			);
		}

		/**
		 * Filters the validation result for a waitlist subscription request.
		 *
		 * Returning a WP_Error rejects the request and its message is shown to
		 * the visitor. Returning an array with an `email` key replaces the
		 * address the subscription is stored under and skips the default email
		 * sanitization, so a handler doing that owns validating it.
		 *
		 * @since 2.0.72
		 *
		 * @param mixed  $custom_validation Validation result. Default null.
		 * @param int    $product_id        ID of the product being subscribed to.
		 * @param string $email             Sanitized email submitted by the visitor.
		 */
		$custom_validation = apply_filters(
			'blocksy:ext:woocommerce-extra:waitlist:subscribe:validate',
			null,
			$product_id,
			$email
		);

		if ( is_wp_error( $custom_validation ) ) {
			wp_send_json_error(
				array(
					'message' => $custom_validation->get_error_message(),
				)
			);
		}

		// Allow custom email sanitization from the filter
		if (
			is_array( $custom_validation )
			&&
			isset( $custom_validation['email'] )
		) {
			$email = $custom_validation['email'];
		} else {
			// No custom sanitizer provided for email field, use default.
			// Also double check email field.
			$email = sanitize_email( $email );

			if (
				empty( $email )
				||
				! is_email( $email )
			) {
				wp_send_json_error(
					array(
						'message' => __( 'Invalid email', 'blocksy-companion' ),
					)
				);
			}
		}

		$maybe_waitlist = ProductWaitlistDb::get_waitlists_from_db( $product, $email, '', false );

		if ( ! empty( $maybe_waitlist ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'You are already on the waitlist', 'blocksy-companion' ),
				)
			);
		}

		$need_confirmation = blocksy_companion_theme_functions()->blocksy_get_theme_mod(
			'waitlist_user_confirmation',
			array(
				'logged_in'  => true,
				'logged_out' => true,
			)
		);

		$need_confirmation = is_user_logged_in()
			? $need_confirmation['logged_in']
			: $need_confirmation['logged_out'];

		$subscribtion = ProductWaitlistDb::create_subscription( $email, $product, ! $need_confirmation );

		if ( ! $subscribtion ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid request', 'blocksy-companion' ),
				)
			);
		}

		$mailer                        = WC()->mailer();
		$confirm_subscription_email    = $mailer->emails['ConfirmSubscriptionEmail'];
		$subscribtion_confirmder_email = $mailer->emails['SubscriptionConfirmedEmail'];

		if (
			$confirm_subscription_email->is_enabled()
			&&
			$need_confirmation
		) {
			$confirm_subscription_email->trigger( $email, $product );
		}

		if (
			$subscribtion_confirmder_email->is_enabled()
			&&
			! $need_confirmation
		) {
			$subscribtion_confirmder_email->trigger( $email, $product );
		}

		$count_data = ProductWaitlistLayer::get_users_count( $product_id );

		wp_send_json_success(
			array(
				'message'                => __( 'You have been added to the waitlist', 'blocksy-companion' ),
				'unsubscribe_token'      => $subscribtion['unsubscribe_token'],
				'waitlist_users'         => $count_data['waitlist_users'],
				'waitlist_users_message' => $count_data['message'],
			)
		);
	}

	public function unsubscribe_from_waitlist() {
		if (
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			! isset( $_POST['token'] )
			||
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			! isset( $_POST['product_id'] )
		) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid request', 'blocksy-companion' ),
				)
			);
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$product_id = intval( $_POST['product_id'] );

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		ProductWaitlistDb::unsubscribe_by_token( sanitize_text_field( wp_unslash( $_POST['token'] ) ) );

		$response = array(
			'message' => __( 'You have been removed from the waitlist', 'blocksy-companion' ),
		);

		if ( blocksy_companion_is_post_viewable( array( 'post' => $product_id ) ) ) {
			$count_data = ProductWaitlistLayer::get_users_count( $product_id );

			$response['waitlist_users']         = $count_data['waitlist_users'];
			$response['waitlist_users_message'] = $count_data['message'];
		}

		wp_send_json_success( $response );
	}
}

