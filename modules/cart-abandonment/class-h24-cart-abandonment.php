<?php
/**
 * Cart Abandonment
 *
 * @package Hello24-Order-On-Chat-Apps
 */

/**
 * Cart abandonment tracking class.
 */
class H24_Cart_Abandonment {



	/**
	 * Member Variable
	 *
	 * @var object instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 *  Constructor function that initializes required actions and hooks.
	 */
	public function __construct() {

		$this->define_cart_abandonment_constants();

		add_action( 'admin_menu', array( $this, 'abandoned_cart_tracking_menu' ), 999 );
		add_action( 'admin_enqueue_scripts', array( $this, 'webhook_setting_script' ), 20 );
		add_action( 'woocommerce_after_checkout_form', array( $this, 'cart_abandonment_tracking_script' ) );

		//trigger abandoned checkout event
		add_action( 'wp_ajax_save_cart_abandonment_data', array( $this, 'save_cart_abandonment_data' ) );
		add_action( 'wp_ajax_nopriv_save_cart_abandonment_data', array( $this, 'save_cart_abandonment_data' ) );

		add_action( 'wp_ajax_h24_activate_integration_service', array( $this, 'h24_activate_integration_service' ) );
		add_action( 'wp_ajax_nopriv_h24_activate_integration_service', array( $this, 'h24_activate_integration_service' ) );
	
		add_action( 'wp_ajax_h24_save_chat_button', array( $this, 'h24_save_chat_button' ) );
		add_action( 'wp_ajax_nopriv_h24_save_chat_button', array( $this, 'h24_save_chat_button' ) );
	
		add_action( 'wp_footer', array( $this, 'hello24_chat_widget') );

		add_action( 'rest_api_init', function () {
			register_rest_route( 'api/v1', '/getWoocommerceInfo', array(
			  'methods' => 'POST',
			  'callback' => array( $this, 'getWoocommerceInfo' ),
			  'permission_callback' => array( $this, 'checkValidPermission' ),
			));
		});
		
		add_action( 'rest_api_init', function () {
			register_rest_route( 'api/v1', '/getOrderUrl', array(
			  'methods' => 'POST',
			  'callback' => array( $this, 'getOrderUrl' ),
			  'permission_callback' => array( $this, 'checkValidPermission' ),
			));
		});

		add_action( 'rest_api_init', function () {
			register_rest_route( 'api/v1', '/getAbandonedCarts', array(
			  'methods' => 'POST',
			  'callback' => array( $this, 'getAbandonedCarts' ),
			  'permission_callback' => array( $this, 'checkValidPermission' ),
			));
		});
	
		add_action( 'rest_api_init', function () {
			register_rest_route( 'api/v1', '/listProducts', array(
			  'methods' => 'POST',
			  'callback' => array( $this, 'listProducts' ),
			  'permission_callback' => array( $this, 'checkValidPermission' ),
			));
		});

		add_action( 'rest_api_init', function () {
			register_rest_route( 'api/v1', '/getCategoryByID', array(
			  'methods' => 'POST',
			  'callback' => array( $this, 'getCategoryByID' ),
			  'permission_callback' => array( $this, 'checkValidPermission' ),
			));
		});
		
		add_action( 'rest_api_init', function () {
			register_rest_route( 'api/v1', '/listCategories', array(
			  'methods' => 'POST',
			  'callback' => array( $this, 'listCategories' ),
			  'permission_callback' => array( $this, 'checkValidPermission' ),
			));
		});

		add_action( 'rest_api_init', function () {
			register_rest_route( 'api/v1', '/listOrders', array(
			  'methods' => 'POST',
			  'callback' => array( $this, 'listOrders' ),
			  'permission_callback' => array( $this, 'checkValidPermission' ),
			));
		});

		add_action( 'rest_api_init', function () {
			register_rest_route( 'api/v1', '/getOrdersByPhone', array(
			  'methods' => 'POST',
			  'callback' => array( $this, 'getOrdersByPhone' ),
			  'permission_callback' => array( $this, 'checkValidPermission' ),
			));
		});

		add_action( 'rest_api_init', function () {
			register_rest_route( 'api/v1', '/getOrderByID', array(
			  'methods' => 'POST',
			  'callback' => array( $this, 'getOrderByID' ),
			  'permission_callback' => array( $this, 'checkValidPermission' ),
			));
		});

		add_action( 'rest_api_init', function () {
			register_rest_route( 'api/v1', '/updateOrderStatus', array(
			  'methods' => 'POST',
			  'callback' => array( $this, 'updateOrderStatus' ),
			  'permission_callback' => array( $this, 'checkValidPermission' ),
			));
		});

		add_action( 'rest_api_init', function () {
			register_rest_route( 'api/v1', '/addOrderNote', array(
			  'methods' => 'POST',
			  'callback' => array( $this, 'addOrderNote' ),
			  'permission_callback' => array( $this, 'checkValidPermission' ),
			));
		});

		add_action( 'rest_api_init', function () {
			register_rest_route( 'api/v1', '/refundOrder', array(
			  'methods' => 'POST',
			  'callback' => array( $this, 'refundOrder' ),
			  'permission_callback' => array( $this, 'checkValidPermission' ),
			));
		});

		add_action( 'rest_api_init', function () {
			register_rest_route( 'api/v1', '/addDiscountToOrder', array(
			  'methods' => 'POST',
			  'callback' => array( $this, 'addDiscountToOrder' ),
			  'permission_callback' => array( $this, 'checkValidPermission' ),
			));
		});

		add_action( 'rest_api_init', function () {
			register_rest_route( 'api/v1', '/markOrderAsPaid', array(
			  'methods' => 'POST',
			  'callback' => array( $this, 'markOrderAsPaid' ),
			  'permission_callback' => array( $this, 'checkValidPermission' ),
			));
		});

		add_action( 'rest_api_init', function () {
			register_rest_route( 'api/v1', '/createOrderFromCart', array(
			  'methods' => 'POST',
			  'callback' => array( $this, 'createOrderFromCart' ),
			  'permission_callback' => array( $this, 'checkValidPermission' ),
			));
		});

		add_action( 'rest_api_init', function () {
			register_rest_route( 'api/v1', '/setWebhook', array(
			  'methods' => 'POST',
			  'callback' => array( $this, 'setWebhook' ),
			  'permission_callback' => array( $this, 'checkValidPermission' ),
			));
		});

		add_action( 'rest_api_init', function () {
			register_rest_route( 'api/v1', '/deleteWebhook', array(
			  'methods' => 'POST',
			  'callback' => array( $this, 'deleteWebhook' ),
			  'permission_callback' => array( $this, 'checkValidPermission' ),
			));
		});

		add_action( 'rest_api_init', function () {
			register_rest_route( 'api/v1', '/deleteWebhooks', array(
			  'methods' => 'POST',
			  'callback' => array( $this, 'deleteWebhooks' ),
			  'permission_callback' => array( $this, 'checkValidPermission' ),
			));
		});

		add_action( 'rest_api_init', function () {
			register_rest_route( 'api/v1', '/updateSettings', array(
			  'methods' => 'POST',
			  'callback' => array( $this, 'updateSettings' ),
			  'permission_callback' => array( $this, 'checkValidPermission' ),
			));
		});

		add_filter( 'jwt_auth_whitelist', function ( $endpoints ) {
			return array(
				'/wp-json/api/v1/getWoocommerceInfo',
				'/wp-json/api/v1/getOrderUrl',
				'/wp-json/api/v1/getAbandonedCarts',
				'/wp-json/api/v1/listProducts',
				'/wp-json/api/v1/getCategoryByID',
				'/wp-json/api/v1/listCategories',
				'/wp-json/api/v1/listOrders',
				'/wp-json/api/v1/getOrdersByPhone',
				'/wp-json/api/v1/getOrderByID',
				'/wp-json/api/v1/updateOrderStatus',
				'/wp-json/api/v1/addOrderNote',
				'/wp-json/api/v1/refundOrder',
				'/wp-json/api/v1/addDiscountToOrder',
				'/wp-json/api/v1/markOrderAsPaid',
				'/wp-json/api/v1/createOrderFromCart',
				'/wp-json/api/v1/setWebhook',
				'/wp-json/api/v1/deleteWebhook',
				'/wp-json/api/v1/deleteWebhooks',
				'/wp-json/api/v1/updateSettings',
			);
		});

		add_filter( 'wp', array( $this, 'restore_cart_abandonment_data' ), 10 );		
		add_action( 'woocommerce_order_status_changed', array( $this, 'h24_ca_update_order_status' ), 999, 3 );
	}

	/**
	 *  Initialise all the constants
	 */
	public function define_cart_abandonment_constants() {
		define( 'H24_CART_ABANDONMENT_TRACKING_DIR', WP_H24_DIR . 'modules/cart-abandonment/' );
		define( 'H24_CART_ABANDONMENT_TRACKING_URL', WP_H24_URL . 'modules/cart-abandonment/' );
		define( 'H24_Cart_Abandonment_ORDER', 'abandoned' );
		define( 'H24_CART_COMPLETED_ORDER', 'completed' );
		define( 'H24_CART_LOST_ORDER', 'lost' );
		define( 'H24_CART_NORMAL_ORDER', 'normal' );
		define( 'H24_CART_FAILED_ORDER', 'failed' );
		define( 'H24_CA_DATETIME_FORMAT', 'Y-m-d H:i:s' );
	}

	public function abandoned_cart_tracking_menu() {

		$capability = current_user_can( 'manage_woocommerce' ) ? 'manage_woocommerce' : 'manage_options';

		add_submenu_page(
			'woocommerce',
			__( 'Hello24 - Order on Chat Apps', 'hello24-order-on-chat-apps' ),
			__( 'Hello24 - Order on Chat Apps', 'hello24-order-on-chat-apps' ),
			$capability,
			WP_H24_PAGE_NAME,
			array( $this, 'render_abandoned_cart_tracking' )
		);
	}

	public function render_abandoned_cart_tracking() {

		$api_key = $this->get_h24_setting_by_meta("api_key");
		$h24_domain = $this->get_h24_setting_by_meta("h24_domain");
		$h24_domain_front = $this->get_h24_setting_by_meta("h24_domain_front");

		$shop_name = $this->get_h24_setting_by_meta("shop_name");
		$email = $this->get_h24_setting_by_meta("email");
		$phone_number = $this->get_h24_setting_by_meta("phone_number");
		$environment = $this->get_h24_setting_by_meta("environment");
		if($environment == null) {
			$environment = "prod";
		}

		$code = $this->get_h24_setting_by_meta("code");
		$h24_setting_url = $h24_domain_front . "?wordpressDomain=" . get_home_url() . "&apiKey=" . $api_key ;

		$chat_button_enabled = $this->get_h24_setting_by_meta("chat_button_enabled");
		if($chat_button_enabled == null) {
			$chat_button_enabled = "enabled";
		}

		$chat_button_theme_color = $this->get_h24_setting_by_meta('chat_button_theme_color');
		if($chat_button_theme_color == null) {
			$chat_button_theme_color = '#000075';
		}

		$chat_button_theme_color_gradient = $this->get_h24_setting_by_meta('chat_button_theme_color_gradient');
		if($chat_button_theme_color_gradient == null) {
			$chat_button_theme_color_gradient = '#000075';
		}

		$chat_button_title = $this->get_h24_setting_by_meta('chat_button_title');
		if($chat_button_title == null) {
			$chat_button_title = 'Need Help ?';
		}

		$chat_button_sub_title = $this->get_h24_setting_by_meta('chat_button_sub_title');
		if($chat_button_sub_title == null) {
			$chat_button_sub_title = 'Typically replies in minutes';
		}

		$chat_button_greeting_text1 = $this->get_h24_setting_by_meta('chat_button_greeting_text1');
		if($chat_button_greeting_text1 == null) {
			$chat_button_greeting_text1 = 'Hello there 👋';
		}

		$chat_button_greeting_text2 = $this->get_h24_setting_by_meta('chat_button_greeting_text2');
		if($chat_button_greeting_text2 == null) {
			$chat_button_greeting_text2 = 'How can I help you?';
		}

		$chat_button_agent_name = $this->get_h24_setting_by_meta('chat_button_agent_name');
		if($chat_button_agent_name == null) {
			$chat_button_agent_name = 'Customer Support';
		}

		$chat_button_message = $this->get_h24_setting_by_meta('chat_button_message');
		if($chat_button_message == null) {
			$chat_button_message = 'Hi';
		}

		$chat_button_position = $this->get_h24_setting_by_meta('chat_button_position');
		if($chat_button_position == null) {
			$chat_button_position = 'right';
		}

		$chat_button_bottom = $this->get_h24_setting_by_meta('chat_button_bottom');
		if($chat_button_bottom == null) {
			$chat_button_bottom = '40';
		}

		if ($shop_name == "")
			$shop_name = sanitize_text_field($_SERVER['HTTP_HOST']);

		if ($email == ""){			
			global $current_user;			
			$current_user = wp_get_current_user();
			$email = (string) $current_user->user_email;
		}
			
		?>

		<?php
		include_once H24_CART_ABANDONMENT_TRACKING_DIR . 'includes/admin/h24-admin-settings.php';
		?>
		<?php
	}
	
	public function get_h24_setting_by_meta($meta_key) {
		global $wpdb;
		$h24_setting_table = $wpdb->prefix . WP_H24_SETTING_TABLE;
		
		$res = $wpdb->get_row(
			$wpdb->prepare( "select * from $h24_setting_table where meta_key = %s", $meta_key ) // phpcs:ignore
		);

		if ( $res != null )
		{
			return $res->meta_value;
		}

		return null;
	}

	public function set_h24_setting_by_meta($input_meta_key, $input_meta_value) {
		global $wpdb;
		$h24_setting_tb       = $wpdb->prefix . WP_H24_SETTING_TABLE;

		$meta_count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $h24_setting_tb WHERE meta_key = %s ", $input_meta_key) );

		$meta_data = array(
			$input_meta_key  => $input_meta_value
		);

		if ( ( ! $meta_count ) ) {
			foreach ( $meta_data as $meta_key => $meta_value ) {
				$wpdb->query(
					$wpdb->prepare(
						"INSERT INTO $h24_setting_tb ( `meta_key`, `meta_value` ) 
						VALUES ( %s, %s )",
						$meta_key,
						$meta_value
					)
				);
			}
		} else {
			foreach ( $meta_data as $meta_key => $meta_value ) {				
				$wpdb->query(
					$wpdb->prepare(
						"UPDATE $h24_setting_tb SET meta_value = '$meta_value' WHERE meta_key = %s",
						$meta_key
					)
				);
			}
		}

		return true;

	}

	public function cart_abandonment_tracking_script() {
		$current_user        = wp_get_current_user();
		$roles               = $current_user->roles;
		$role                = array_shift( $roles );
		
		global $post;
		wp_enqueue_script(
			'h24-abandonment-tracking',
			H24_CART_ABANDONMENT_TRACKING_URL . 'assets/js/h24-abandonment-tracking.js',
			array( 'jquery' ),
			filemtime(H24_CART_ABANDONMENT_TRACKING_URL . 'assets/js/h24-abandonment-tracking.js'),
			true
		);

		$vars = array(
			'ajaxurl'                   => admin_url( 'admin-ajax.php' ),
			'_nonce'                    => wp_create_nonce( 'save_cart_abandonment_data' ),
			'_post_id'                  => get_the_ID(),
			'_show_gdpr_message'        => false,
			'_gdpr_message'             => get_option( 'h24_ca_gdpr_message' ),
			'_gdpr_nothanks_msg'        => __( 'No Thanks', 'hello24-order-on-chat-apps' ),
			'_gdpr_after_no_thanks_msg' => __( 'You won\'t receive further emails from us, thank you!', 'hello24-order-on-chat-apps' ),
			'enable_ca_tracking'        => true,
		);

		wp_localize_script( 'h24-abandonment-tracking', 'H24Variables', $vars );
	}

	public function webhook_setting_script() {
		wp_enqueue_style( 'wp-color-picker');
		wp_enqueue_script( 'wp-color-picker' );

		$current_user        = wp_get_current_user();
		$roles               = $current_user->roles;
		$role                = array_shift( $roles );
		
		global $post;
		wp_enqueue_script(
			'webhook_setting_script',
			H24_CART_ABANDONMENT_TRACKING_URL . 'assets/js/webhook-setting.js',
			array( 'jquery' ),
			filemtime(H24_CART_ABANDONMENT_TRACKING_URL . 'assets/js/webhook-setting.js'),
			true
		);

		$vars = array(
			'ajaxurl'                   => admin_url( 'admin-ajax.php' )
		);

		wp_localize_script( 'webhook_setting_script', 'WPVars', $vars );
	}

	public function save_cart_abandonment_data() {
		$post_data = $this->sanitize_post_data();
		if ( isset( $post_data['h24_phone'] ) ) {
			$user_email = sanitize_email( $post_data['h24_email'] );
			global $wpdb;
			$cart_abandonment_table = $wpdb->prefix . WP_H24_ABANDONMENT_TABLE;

			// Verify if email is already exists.
			$session_id               = WC()->session->get( 'h24_session_id' );
			$session_checkout_details = null;
			if ( isset( $session_id ) ) {
				$session_checkout_details = $this->get_checkout_details( $session_id );
			} else {
				$session_id = md5( uniqid( wp_rand(), true ) );
			}

			$checkout_details = $this->prepare_abandonment_data( $post_data );

			if ( isset( $session_checkout_details ) && $session_checkout_details->order_status === "completed" ) {
				WC()->session->__unset( 'h24_session_id' );
				$session_id = md5( uniqid( wp_rand(), true ) );
			}

			if ( isset( $checkout_details['cart_total'] ) && $checkout_details['cart_total'] > 0 ) {

				if ( ( ! is_null( $session_id ) ) && ! is_null( $session_checkout_details ) ) {

					$checkout_details['time'] = $session_checkout_details->time;
					$checkout_details['local_time'] = $session_checkout_details->local_time;
					
					// Updating row in the Database where users Session id = same as prevously saved in Session.
					$wpdb->update(
						$cart_abandonment_table,
						$checkout_details,
						array( 'session_id' => $session_id )
					);
				} else {

					$checkout_details['session_id'] = sanitize_text_field( $session_id );
					// Inserting row into Database.
					$wpdb->insert(
						$cart_abandonment_table,
						$checkout_details
					);

					// Storing session_id in WooCommerce session.
					WC()->session->set( 'h24_session_id', $session_id );
				}
			}

			wp_send_json_success();
		}
	}

	public function h24_ca_update_order_status( $order_id, $old_order_status, $new_order_status ) {
		if ( ( H24_CART_FAILED_ORDER === $new_order_status ) ) {
			return;
		}

		$session_id = null;

		if ( WC()->session ) {
			$session_id = WC()->session->get( 'h24_session_id' );
		}

		if ( $order_id  && $session_id ) {

			$session_id = WC()->session->get( 'h24_session_id' );
			$captured_data = $this->get_checkout_details( $session_id );
			if ( $captured_data ) {
				$captured_data->order_status = H24_CART_COMPLETED_ORDER;
				
				global $wpdb;
				$cart_abandonment_table = $wpdb->prefix . WP_H24_ABANDONMENT_TABLE;
				$wpdb->delete( $cart_abandonment_table, array( 'session_id' => sanitize_key( $session_id ) ) );
				if ( WC()->session ) {
					WC()->session->__unset( 'h24_session_id' );
				}
			}
		}
	}

	public function restore_cart_abandonment_data( $fields = array() ) {
		global $woocommerce;
		$result = array();
		// Restore only of user is not logged in.
		$h24_session_id = filter_input( INPUT_GET, 'session_id', FILTER_SANITIZE_STRING );
		$result = $this->get_checkout_details( $h24_session_id );
		if ( isset( $result ) && (H24_Cart_Abandonment_ORDER === $result->order_status || H24_CART_LOST_ORDER === $result->order_status) ) {
			WC()->session->set( 'h24_session_id', $h24_session_id );
		}
		if ( $result ) {
			$cart_content = unserialize( $result->cart_contents );

			if ( $cart_content ) {
				$woocommerce->cart->empty_cart();
				wc_clear_notices();
				foreach ( $cart_content as $cart_item ) {

					$cart_item_data = array();
					$variation_data = array();
					$id             = $cart_item['product_id'];
					$qty            = $cart_item['quantity'];

					// Skip bundled products when added main product.
					if ( isset( $cart_item['bundled_by'] ) ) {
						continue;
					}

					if ( isset( $cart_item['variation'] ) ) {
						foreach ( $cart_item['variation']  as $key => $value ) {
							$variation_data[ $key ] = $value;
						}
					}

					$cart_item_data = $cart_item;

					$woocommerce->cart->add_to_cart( $id, $qty, $cart_item['variation_id'], $variation_data, $cart_item_data );
				}

				if ( isset( $token_data['h24_coupon_codes'] ) && ! $woocommerce->cart->applied_coupons ) {
					$woocommerce->cart->add_discount( $token_data['h24_coupon_codes'] );
				}
			}
			$other_fields = unserialize( $result->other_fields );

			$parts = explode( ',', $other_fields['h24_location'] );
			if ( count( $parts ) > 1 ) {
				$country = $parts[0];
				$city    = trim( $parts[1] );
			} else {
				$country = $parts[0];
				$city    = '';
			}

			foreach ( $other_fields as $key => $value ) {
				$key           = str_replace( 'h24_', '', $key );
				$_POST[ $key ] = sanitize_text_field( $value );
			}
			$_POST['billing_first_name'] = sanitize_text_field( $other_fields['h24_first_name'] );
			$_POST['billing_last_name']  = sanitize_text_field( $other_fields['h24_last_name'] );
			$_POST['billing_phone']      = sanitize_text_field( $other_fields['h24_phone_number'] );
			$_POST['billing_email']      = sanitize_email( $result->email );
			$_POST['billing_city']       = sanitize_text_field( $city );
			$_POST['billing_country']    = sanitize_text_field( $country );

		}
		return $fields;
	}
	
	public function prepare_abandonment_data( $post_data = array() ) {

		if ( function_exists( 'WC' ) ) {

			// Retrieving cart total value and currency.
			$cart_total = WC()->cart->total;
			$cart_total_tax = WC()->cart->get_total_tax();
			$cart_subtotal = WC()->cart->get_subtotal();
			$cart_subtotal_tax = WC()->cart->get_subtotal_tax();
			$cart_shipping_total = WC()->cart->get_shipping_total();
			$cart_fee_total = WC()->cart->get_fee_total();
			$cart_discount_total = WC()->cart->get_discount_total();

			// Retrieving cart products and their quantities.
			$products     = WC()->cart->get_cart();
			$current_time = current_time( H24_CA_DATETIME_FORMAT, 1 ); //GMT TIME
			$local_time = current_time( H24_CA_DATETIME_FORMAT );
			$other_fields = array(
				'h24_billing_company'     => $post_data['h24_billing_company'],
				'h24_billing_address_1'   => $post_data['h24_billing_address_1'],
				'h24_billing_address_2'   => $post_data['h24_billing_address_2'],
				'h24_billing_state'       => $post_data['h24_billing_state'],
				'h24_billing_postcode'    => $post_data['h24_billing_postcode'],
				'h24_shipping_first_name' => $post_data['h24_shipping_first_name'],
				'h24_shipping_last_name'  => $post_data['h24_shipping_last_name'],
				'h24_shipping_company'    => $post_data['h24_shipping_company'],
				'h24_shipping_country'    => $post_data['h24_shipping_country'],
				'h24_shipping_address_1'  => $post_data['h24_shipping_address_1'],
				'h24_shipping_address_2'  => $post_data['h24_shipping_address_2'],
				'h24_shipping_city'       => $post_data['h24_shipping_city'],
				'h24_shipping_state'      => $post_data['h24_shipping_state'],
				'h24_shipping_postcode'   => $post_data['h24_shipping_postcode'],
				'h24_order_comments'      => $post_data['h24_order_comments'],
				'h24_first_name'          => $post_data['h24_name'],
				'h24_last_name'           => $post_data['h24_surname'],
				'h24_phone_number'        => $post_data['h24_phone'],
				'h24_location'            => $post_data['h24_country'] . ', ' . $post_data['h24_city'],
			);

			$coupons = WC()->cart->get_coupons();

			$coupon_codes = array();

			foreach ( $coupons as $coupon ) {
				$codeStr = $coupon->get_code();
				$coupon_codes[] = $codeStr;
			}

			$shipping_methods = WC()->cart->calculate_shipping();

			$shipping_methods_formatted = array();

			foreach($shipping_methods as $shipping_method) {
				$shipping_method_formatted = array(
					'id'   			=>  $shipping_method->get_id(),
					'instance_id'   =>  $shipping_method->get_instance_id(),
					'label'   		=>  $shipping_method->get_label(),
					'meta_data'  	=>  $shipping_method->get_meta_data(),
					'method_id'   	=>  $shipping_method->get_method_id(),
					'taxes'   		=>  $shipping_method->get_taxes(),
					'shipping_tax'  =>  $shipping_method->get_shipping_tax(),
					'cost'  		=>  $shipping_method->get_cost(),
				);

				$shipping_methods_formatted[] = $shipping_method_formatted;
			}

			$checkout_details = array(
				'email'         		=> $post_data['h24_email'],
				'cart_contents' 		=> serialize( $products ),
				'cart_total'    		=> sanitize_text_field( $cart_total ),
				'cart_total_tax'    	=> sanitize_text_field( $cart_total_tax ),
				'cart_subtotal'    		=> sanitize_text_field( $cart_subtotal ),
				'cart_subtotal_tax'    	=> sanitize_text_field( $cart_subtotal_tax ),
				'cart_shipping_total'   => sanitize_text_field( $cart_shipping_total ),
				'cart_fee_total'    	=> sanitize_text_field( $cart_fee_total ),
				'cart_discount_total'   => sanitize_text_field( $cart_discount_total ),
				'time'          		=> sanitize_text_field( $current_time ),
				'local_time'    		=> sanitize_text_field( $local_time ),
				'other_fields'  		=> serialize( $other_fields ),
				'checkout_id'   		=> $post_data['h24_post_id'],
				'coupon_codes'   		=> serialize($coupon_codes),
				'shipping_methods'  	=> serialize($shipping_methods_formatted)
			);
		}
		return $checkout_details;
	}

	public function sanitize_post_data() {

		$input_post_values = array(
			'h24_billing_company'     => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_STRING,
			),
			'h24_email'               => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_EMAIL,
			),
			'h24_billing_address_1'   => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_STRING,
			),
			'h24_billing_address_2'   => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_STRING,
			),
			'h24_billing_state'       => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_STRING,
			),
			'h24_billing_postcode'    => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_STRING,
			),
			'h24_shipping_first_name' => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_STRING,
			),
			'h24_shipping_last_name'  => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_STRING,
			),
			'h24_shipping_company'    => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_STRING,
			),
			'h24_shipping_country'    => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_STRING,
			),
			'h24_shipping_address_1'  => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_STRING,
			),
			'h24_shipping_address_2'  => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_STRING,
			),
			'h24_shipping_city'       => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_STRING,
			),
			'h24_shipping_state'      => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_STRING,
			),
			'h24_shipping_postcode'   => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_STRING,
			),
			'h24_order_comments'      => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_STRING,
			),
			'h24_name'                => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_STRING,
			),
			'h24_surname'             => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_STRING,
			),
			'h24_phone'               => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_STRING,
			),
			'h24_country'             => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_STRING,
			),
			'h24_city'                => array(
				'default'  => '',
				'sanitize' => FILTER_SANITIZE_STRING,
			),
			'h24_post_id'             => array(
				'default'  => 0,
				'sanitize' => FILTER_SANITIZE_NUMBER_INT,
			),
		);

		$sanitized_post = array();
		foreach ( $input_post_values as $key => $input_post_value ) {

			if ( isset( $_POST[ $key ] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
				$sanitized_post[ $key ] = filter_input( INPUT_POST, $key, $input_post_value['sanitize'] );
			} else {
				$sanitized_post[ $key ] = $input_post_value['default'];
			}
		}
		return $sanitized_post;

	}
	
	public function get_checkout_details( $h24_session_id ) {
		global $wpdb;
		$cart_abandonment_table = $wpdb->prefix . WP_H24_ABANDONMENT_TABLE;
		$result                 = $wpdb->get_row(
            $wpdb->prepare('SELECT * FROM `' . $cart_abandonment_table . '` WHERE session_id = %s AND order_status <> %s', $h24_session_id, H24_CART_COMPLETED_ORDER) // phpcs:ignore
		);
		return $result;
	}

	public function get_checkout_details_by_email( $email ) {
		global $wpdb;
		$cart_abandonment_table = $wpdb->prefix . WP_H24_ABANDONMENT_TABLE;
		$result                 = $wpdb->get_row(
            $wpdb->prepare('SELECT * FROM `' . $cart_abandonment_table . '` WHERE email = %s AND `order_status` IN ( %s, %s )', $email, H24_Cart_Abandonment_ORDER, H24_CART_NORMAL_ORDER ) // phpcs:ignore
		);
		return $result;
	}

	public function h24_activate_integration_service() {		
		$api_key = sanitize_text_field( $_POST['api_key'] );	
		$shop_name = sanitize_text_field( $_POST['shop_name'] );
		$phone_number = sanitize_text_field( $_POST['phone_number'] );
		$environment = sanitize_text_field( $_POST['environment'] );

		$email = sanitize_email( $_POST['email'] );

		$url = WP_HELLO24_SERVICE_BASE_URL . "/" . $environment . "/webhook_woocommerce/wordpress_plugin_installed";

		$code = $this->rand_string(16);

		$this->set_h24_setting_by_meta("code", $code);
		$this->set_h24_setting_by_meta("shop_name", $shop_name);
		$this->set_h24_setting_by_meta("email", $email);
		$this->set_h24_setting_by_meta("phone_number", $phone_number);
		$this->set_h24_setting_by_meta("environment", $environment);
		$this->set_h24_setting_by_meta("api_key", md5( uniqid( wp_rand(), true ) ));

		$data = array(
			'apiKey' => $api_key,
			'shopName' => $shop_name,
			'email' => $email,
			'phoneNumber' => $phone_number,
			'environment' => $environment,
			'wordpressDomain' => get_home_url(),
			"code" => $code
		);

		$options = [
			'body'        => json_encode($data),
			'headers'     => [
				'Content-Type' => 'application/json',
			],
			'timeout'     => 60,
			'redirection' => 5,
			'blocking'    => true,
			'httpversion' => '1.0',
			'sslverify'   => false,
			'data_format' => 'body',
		];
		
		$response = wp_remote_post( $url, $options );
		$response = json_decode( $response['body'] );
		if ($response && $response->result)
		{
			$this->set_h24_setting_by_meta("h24_domain", $response->h24Domain);
			$this->set_h24_setting_by_meta("h24_domain_front", $response->h24DomainFront);
			$this->set_h24_setting_by_meta("hello24_chat_mobile_link", $response->hello24ChatMobileLink);
			$this->set_h24_setting_by_meta("hello24_chat_web_link", $response->hello24ChatWebLink);
			if($response->hello24ChatButton !=  null) {
				$this->set_h24_setting_by_meta("hello24_chat_button", $response->hello24ChatButton);
			}

			$this->set_h24_setting_by_meta("api_key", $api_key);
			wp_send_json_success($response);
		}
		else{
			wp_send_json_success();
		}

	}

	public function h24_save_chat_button() {		
		$chat_button_enabled = sanitize_text_field( $_POST['chat_button_enabled'] );	
		$chat_button_theme_color = sanitize_text_field( $_POST['chat_button_theme_color'] );	
		$chat_button_theme_color_gradient = sanitize_text_field( $_POST['chat_button_theme_color_gradient'] );	
		$chat_button_title = sanitize_text_field( $_POST['chat_button_title'] );	
		$chat_button_sub_title = sanitize_text_field( $_POST['chat_button_sub_title'] );	
		$chat_button_greeting_text1 = sanitize_text_field( $_POST['chat_button_greeting_text1'] );	
		$chat_button_greeting_text2 = sanitize_text_field( $_POST['chat_button_greeting_text2'] );	
		$chat_button_agent_name = sanitize_text_field( $_POST['chat_button_agent_name'] );	
		$chat_button_message = sanitize_text_field( $_POST['chat_button_message'] );	
		$chat_button_position = sanitize_text_field( $_POST['chat_button_position'] );	
		$chat_button_bottom = sanitize_text_field( $_POST['chat_button_bottom'] );	

		$this->set_h24_setting_by_meta("chat_button_enabled", $chat_button_enabled);
		$this->set_h24_setting_by_meta("chat_button_theme_color", $chat_button_theme_color);
		$this->set_h24_setting_by_meta("chat_button_theme_color_gradient", $chat_button_theme_color_gradient);
		$this->set_h24_setting_by_meta("chat_button_title", $chat_button_title);
		$this->set_h24_setting_by_meta("chat_button_sub_title", $chat_button_sub_title);
		$this->set_h24_setting_by_meta("chat_button_greeting_text1", $chat_button_greeting_text1);
		$this->set_h24_setting_by_meta("chat_button_greeting_text2", $chat_button_greeting_text2);
		$this->set_h24_setting_by_meta("chat_button_agent_name", $chat_button_agent_name);
		$this->set_h24_setting_by_meta("chat_button_message", $chat_button_message);
		$this->set_h24_setting_by_meta("chat_button_position", $chat_button_position);
		$this->set_h24_setting_by_meta("chat_button_bottom", $chat_button_bottom);

		wp_send_json_success();
	}

	public function checkValidPermission($request) {
		$api_key = sanitize_text_field( $request->get_header('apiKey') );
		if ($api_key == $this->get_h24_setting_by_meta('api_key')){
			return true;
		} else {
			return false;
		}
	}

	public function getWoocommerceInfo($request) {
		return array(
			"currency" => get_woocommerce_currency(),
			"woocommerceApiUrl" => get_woocommerce_api_url(''),
			"shopName" => $this->get_h24_setting_by_meta('shop_name'),
			"email" => $this->get_h24_setting_by_meta('email'),
			"phoneNumber" => $this->get_h24_setting_by_meta('phone_number'),
			"environment" => $this->get_h24_setting_by_meta('environment'),
			"pluginActivated" => $this->get_h24_setting_by_meta('plugin_activated')
		);
	}

	public function getOrderUrl($request){
		$order_id = sanitize_text_field( $request->get_param('orderID') );
		$order = wc_get_order($order_id);

		if (!$order){
			return null;
		}

		return array(
			"order_url" => $order->get_checkout_order_received_url()
		);
	}
	
	
	public function getAbandonedCarts($request){
		$startTime = sanitize_text_field( $request->get_param('startTime') );
		$endTime = sanitize_text_field($request->get_param('endTime') );

		global $wpdb;
		$cart_abandonment_table = $wpdb->prefix . WP_H24_ABANDONMENT_TABLE;
		$carts                 = $wpdb->get_results(
			$wpdb->prepare('SELECT * FROM `' . $cart_abandonment_table . '` WHERE time BETWEEN %s AND %s', $startTime, $endTime ) // phpcs:ignore
		);

		$abandoned_carts = array();
		
		foreach($carts as $cart) {

			$cart_contents = unserialize($cart->cart_contents);
			$cartFormatted = array();

			foreach($cart_contents as $cart_content) {
				$product = wc_get_product( $cart_content['product_id'] );
				$cart_content["product_title"] = $product->get_title();
				
				$variation = wc_get_product($cart_content['variation_id']);
				if($variation) {
					$cart_content["variation"] = $variation;
					$variation_attributes = $variation->get_variation_attributes();
					$cart_content["variation_attributes"] = $variation_attributes;
					
					$variation_title = '';
					foreach($variation_attributes as $attribute) {
						if($variation_title == '') {
							$variation_title =str_replace('attribute_pa_', '', $attribute);
						}else {
							$variation_title = $variation_title . ' - ' .str_replace('attribute_pa_', '', $attribute);
						}
					}
					
					$cart_content["variation_title"] = $variation_title;						
				}
				
				$cartFormatted[] = $cart_content;
			}

			$checkout_base_url = get_permalink( $cart->checkout_id );
			$session_id_param  = array(
				'session_id' => $cart->session_id,
			);
			
			$checkout_url = add_query_arg( $session_id_param, $checkout_base_url );

			$abandoned_cart = array(
				'id'         			=> $cart->id,
				'checkout_id'   		=> $cart->checkout_id,
				'checkout_url'			=> $checkout_url,
				'email'         		=> $cart->email,
				'line_items' 			=> $cartFormatted,
				'cart_total'    		=> $cart->cart_total,
				'cart_total_tax'    	=> $cart->cart_total_tax,
				'cart_subtotal'    		=> $cart->cart_subtotal,
				'cart_subtotal_tax' 	=> $cart->cart_subtotal_tax,
				'cart_shipping_total'   => $cart->cart_shipping_total,
				'cart_fee_total'    	=> $cart->cart_fee_total,
				'cart_discount_total'   => $cart->cart_discount_total,
				'session_id' 			=> $cart->session_id,
				'other_fields'  		=> unserialize($cart->other_fields),
				'order_status'  		=> $cart->order_status,
				'unsubscribed'  		=> $cart->unsubscribed,
				'coupon_codes'  		=> unserialize($cart->coupon_codes),
				'shipping_methods'  	=> unserialize($cart->shipping_methods),
				'time'          		=> $cart->time,
				'local_time'   	 		=> $cart->local_time,
			);

			$abandoned_carts[] = $abandoned_cart;
		}
		
		return array(
			"code" => "SUCCESS",
			"data" => $abandoned_carts
		);
	}

	public function listCategories($request){
		$categories = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'orderby'    => 'name',
				'hide_empty' => false,
			)
		);
		
		$formatted_categories = array();

		foreach($categories as $category) {
			$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true ); 
			$image = null;
			if($thumbnail_id != null || $thumbnail_id != false) {
				$image = $this->get_image_by_id($thumbnail_id);
			}

			$formatted_category = array(
				"id" => $category->term_id,
				"parent" => $category->parent,
				"name" => $category->name,
				"slug" => $category->slug,
				"description" => $category->description,
				"image" => $image,
				"count" => $category->count
			);

			$formatted_categories[] = $formatted_category;
		}

		return array(
			"code" => "SUCCESS",
			"data" => $formatted_categories
		);
	}

	public function listOrders($request){
		$params = $this->wporg_recursive_sanitize_text_field( $request->get_params() );

		$query = new WC_Order_Query($params);
		
		$orders = $query->get_orders();
		$orderDatas = array();
		
		foreach($orders as $order) {
			$order_data = $order->get_data(); // The Order data
			$order_data['line_items'] = $this->getLineItemsData($order);
			$orderDatas[] = $order_data;
		}
		
		return array(
			"code" => "SUCCESS",
			"data" => $orderDatas
		);
	}

	public function getLineItemsData($order) {
		$itemDatas = array();

		// Get and Loop Over Order Items
		foreach ( $order->get_items() as $item_id => $item ) {
			$itemData = array();

			$itemData['id'] = $item->get_id();
			$itemData['product_id'] = $item->get_product_id();
			$itemData['variation_id'] = $item->get_variation_id();

			$product = $item->get_product();
			$itemData['product_attributes'] = $product->get_attributes();

			if($itemData['variation_id'] != 0) {
				$product_variation = wc_get_product( $itemData['variation_id'] );
				$itemData['variation_attributes'] = $product_variation->get_attributes();
			}
			
			$itemData['product_name'] = $item->get_name();
			$itemData['quantity'] = $item->get_quantity();

			$itemData['subtotal'] = $item->get_subtotal();
			$itemData['total'] = $item->get_total();
			$itemData['tax'] = $item->get_subtotal_tax();
			$itemData['tax_class'] = $item->get_tax_class();
			$itemData['tax_status'] = $item->get_tax_status();
			$itemData['item_type'] = $item->get_type();
			$itemData['meta_data'] = $item->get_meta_data();

			$itemDatas[] = $itemData;
		}

		return $itemDatas;
	}

	public function getCategoryByID($request){
		$category_id = sanitize_text_field($request->get_param('categoryID') );
		$category = get_term_by( 'id', $category_id, 'product_cat', 'ARRAY_A' );
		return array(
			"code" => "SUCCESS",
			"data" => $category
		);
	}

	public function listProducts($request){
		$params = $this->wporg_recursive_sanitize_text_field( $request->get_params() );

		$query = new WC_Product_Query($params);
		
		$products = $query->get_products();
		$productDatas = array();
		
		foreach($products as $product) {
			$product_data = $product->get_data();

			if ($product->get_type() == "variable") {
				$children_ids = $product->get_children();
				foreach($children_ids as $children_id) {
					$variant = wc_get_product( $children_id );
					$variant_data = $variant->get_data();
					$variant_data["images"] = $this->get_images_of_product($variant);
					$variant_data["permalink"] = $variant->get_permalink();

					$variants[] = $variant_data;
				}
				$product_data["variants"] = $variants;
			}

			$product_data["images"] = $this->get_images_of_product($product);
			$product_data["permalink"] = $product->get_permalink();
			$productDatas[] = $product_data;
		}
		
		return array(
			"code" => "SUCCESS",
			"data" => $productDatas
		);
	}

	public function get_image_by_id($image_id) {
		$image_attributes = wp_get_attachment_image_src( $attachment_id = $image_id);
		if($image_attributes != false) {
			$image = array(
				"src" => $image_attributes[0],
				"width" => $image_attributes[1],
				"height" => $image_attributes[2],
				"is_intermediate" => $image_attributes[3]
			);

			return $image;
		}

		return null;
	}

	public function get_images_of_product($product) {
		$images = array();
		$image_id = $product->get_image_id();
		if($image_id != null) {
			$image = $this->get_image_by_id($image_id);
			if($image != null) {
				$images[] = $image;
			}
		}

		$gallery_image_ids = $product->get_gallery_image_ids();
		foreach($gallery_image_ids as $gallery_image_id) {
			$gallery_image = $this->get_image_by_id($gallery_image_id);
			if($gallery_image != null) {
				$images[] = $gallery_image;
			}
		}

		return $images;
	}

	public function wporg_recursive_sanitize_text_field( $array ) {
		foreach ( $array as $key => &$value ) {
			if ( is_array( $value ) ) {
				$value = wporg_recursive_sanitize_text_field( $value );
			} else {
				$value = sanitize_text_field( $value );
			}
		}
		return $array;
	}

	public function getOrdersByPhone($request){
		$phone = sanitize_text_field( $request->get_param('phone') );
		$limit = sanitize_text_field( $request->get_header('limit') );

		$query = new WC_Order_Query( array(
			'limit' => $limit,
			'orderby' => 'date',
			'order' => 'DESC',
			'billing_phone' => $phone,
		));
		
		$orders = $query->get_orders();
		$orderDatas = array();
		
		foreach($orders as $order) {
			$order_data = $order->get_data(); // The Order data
			$order_data['line_items'] = $this->getLineItemsData($order);
			$orderDatas[] = $order_data;
		}
		
		return $orderDatas;
	}
	
	public function getOrderByID($request){
		$order_id = sanitize_text_field( $request->get_param('orderID') );	
		$order = wc_get_order( $order_id );
		$order_data = $order->get_data();
		$order_data['line_items'] = $this->getLineItemsData($order);

		if($order == false) {
			return array(
				"code" => "FAILURE",
				"data" => "No order found with provided order ID"
			);
		}else {
			return array(
				"code" => "SUCCESS",
				"data" => $order_data
			);
		}
	}

	public function updateOrderStatus($request){
		$order_id = sanitize_text_field( $request->get_param('orderID') );	
		$status = sanitize_text_field( $request->get_param('status') );	
		$order = wc_get_order( $order_id );
		$order->update_status($status); 

		$order_data = $order->get_data();
		$order_data['line_items'] = $this->getLineItemsData($order);

		return array(
			"code" => "SUCCESS",
			"data" => $order_data
		);
	}

	public function markOrderAsPaid($request){
		$order_id = sanitize_text_field( $request->get_param('orderID') );	
		$payment_gateway_id = sanitize_text_field( $request->get_param('paymentGatewayID') );	
		$status = sanitize_text_field( $request->get_param('status') );	
		$order = wc_get_order( $order_id );
		$order->update_status($status); 

			
		$payment_gateways = WC()->payment_gateways->payment_gateways();
		// add payment method
		$order->set_payment_method($payment_gateways[$payment_gateway_id]);
		$order->save();

		$order_data = $order->get_data();
		$order_data['line_items'] = $this->getLineItemsData($order);

		return array(
			"code" => "SUCCESS",
			"data" => $order_data
		);
	}

	public function addOrderNote($request){
		$order_id = sanitize_text_field( $request->get_param('orderID') );	
		$note = sanitize_text_field( $request->get_param('note') );	
		$order = wc_get_order( $order_id );
		$order->add_order_note($note); 

		$order_data = $order->get_data();
		$order_data['line_items'] = $this->getLineItemsData($order);

		return array(
			"code" => "SUCCESS",
			"data" => $order_data
		);
	}

	public function refundOrder( $request) {
		$order_id = sanitize_text_field( $request->get_param('orderID') );	
		$refund_reason = sanitize_text_field( $request->get_param('refundReason') );	
		$order  = wc_get_order( $order_id );
	
		// If it's something else such as a WC_Order_Refund, we don't want that.
		if( ! is_a( $order, 'WC_Order') ) {
			return array(
				"code" => "FAILURE",
				"data" => "Order ID is not valid."
			);
		}
		
		if( 'refunded' == $order->get_status() ) {
			return array(
				"code" => "FAILURE",
				"data" => "Order ID is not valid."
			);
		}
			
		// Get Items
		$order_items   = $order->get_items();
		
		// Refund Amount
		$refund_amount = 0;
		
		// Prepare line items which we are refunding
		$line_items = array();
		
		if ( $order_items ) {
			foreach( $order_items as $item_id => $item ) {
				
				$item_meta 	= $order->get_item_meta( $item_id );
				
				$tax_data = $item_meta['_line_tax_data'];
				
				$refund_tax = 0;
		
				if( is_array( $tax_data[0] ) ) {
		
				$refund_tax = array_map( 'wc_format_decimal', $tax_data[0] );
		
				}
		
				$refund_amount = wc_format_decimal( $refund_amount ) + wc_format_decimal( $item_meta['_line_total'][0] );
		
				$line_items[ $item_id ] = array( 
				'qty' => $item_meta['_qty'][0], 
				'refund_total' => wc_format_decimal( $item_meta['_line_total'][0] ), 
				'refund_tax' =>  $refund_tax );
				
			}
		}

		$refund = wc_create_refund( 
			array(
				'amount'         => $refund_amount,
				'reason'         => $refund_reason,
				'order_id'       => $order_id,
				'line_items'     => $line_items,
				'refund_payment' => true
				)
		);
	
		return array(
			"code" => "SUCCESS",
			"data" => $refund
		);
	}

	public function addDiscountToOrder($request){
		$order_id = sanitize_text_field( $request->get_param('orderID') );	
		$title = sanitize_text_field( $request->get_param('title') );	
		$amount = sanitize_text_field( $request->get_param('amount') );	

		$this->wc_order_add_discount( $order_id, $title, $amount );

		$order = wc_get_order( $order_id );

		$order_data = $order->get_data();
		$order_data['line_items'] = $this->getLineItemsData($order);

		return array(
			"code" => "SUCCESS",
			"data" => $order_data
		);
	}

	public function createOrderFromCart($request){
		$cart = $request->get_param('cart');	
		$payment_gateway_id = $request->get_param('paymentGatewayID');	
		$discount_title = $request->get_param('discountTitle');
		$discount_percent = $request->get_param('discountPercent');		
		$flat_discount_amount = $request->get_param('flatDiscountAmount');	
		$is_free_shipping = $request->get_param('isFreeShipping');	
		$note = $request->get_param('note');	
		
		if($payment_gateway_id == null) {
			return array(
				"code" => "FAILURE",
				"data" => "payment_gateway_id input not given"
			);
		}

		if($cart) {
			$order = wc_create_order();
			$line_items = $cart['line_items'];
	
			foreach($line_items as $line_item) {
				$quantity = $line_item['quantity'];
				$product_id = $line_item['product_id'];
				$variation_id = $line_item['variation_id'];
				if($variation_id == 0) {
					$order->add_product( wc_get_product( $product_id ), $quantity );
				}else {
					$order->add_product( wc_get_product( $variation_id ), $quantity );
				}
			}
	
			$other_fields = $cart['other_fields'];
			$email = $cart['email'];
			$phone = $other_fields['h24_phone_number'];
			$first_name = $other_fields['h24_name'];
			$last_name = $other_fields['h24_surname'];
	
			$h24_billing_first_name = $first_name;
			$h24_billing_last_name = $last_name;
			$h24_billing_company = $other_fields['h24_billing_company'];
			$h24_billing_address_1 = $other_fields['h24_billing_address_1'];
			$h24_billing_address_2 = $other_fields['h24_billing_address_2'];
			$h24_billing_city = $other_fields['h24_billing_city'];
			$h24_billing_state = $other_fields['h24_billing_state'];
			$h24_billing_postcode = $other_fields['h24_billing_postcode'];
			$h24_billing_country = $other_fields['h24_billing_country'];

			$h24_shipping_first_name = $other_fields['h24_shipping_first_name'];
			$h24_shipping_last_name = $other_fields['h24_shipping_last_name'];
			$h24_shipping_company = $other_fields['h24_shipping_company'];
			$h24_shipping_address_1 = $other_fields['h24_shipping_address_1'];
			$h24_shipping_address_2 = $other_fields['h24_shipping_address_2'];
			$h24_shipping_city = $other_fields['h24_shipping_city'];
			$h24_shipping_state = $other_fields['h24_shipping_state'];
			$h24_shipping_postcode = $other_fields['h24_shipping_postcode'];
			$h24_shipping_country = $other_fields['h24_shipping_country'];
			
			if($h24_shipping_first_name == null || $h24_shipping_first_name == '') {
				$h24_shipping_first_name = $h24_billing_first_name;
			}

			if($h24_shipping_last_name == null || $h24_shipping_last_name == '') {
				$h24_shipping_last_name = $h24_billing_last_name;
			}

			if($h24_shipping_company == null || $h24_shipping_company == '') {
				$h24_shipping_company = $h24_billing_company;
			}

			if($h24_shipping_address_1 == null || $h24_shipping_address_1 == '') {
				$h24_shipping_address_1 = $h24_billing_address_1;
			}

			if($h24_shipping_address_2 == null || $h24_shipping_address_2 == '') {
				$h24_shipping_address_2 = $h24_billing_address_2;
			}

			if($h24_shipping_city == null || $h24_shipping_city == '') {
				$h24_shipping_city = $h24_billing_city;
			}

			if($h24_shipping_state == null || $h24_shipping_state == '') {
				$h24_shipping_state = $h24_billing_state;
			}

			if($h24_shipping_postcode == null || $h24_shipping_postcode == '') {
				$h24_shipping_postcode = $h24_billing_postcode;
			}

			if($h24_shipping_country == null || $h24_shipping_country == '') {
				$h24_shipping_country = $h24_billing_country;
			}

			// add billing and shipping addresses
			$shipping_address = array(
				'first_name' => $h24_shipping_first_name,
				'last_name'  => $h24_shipping_last_name,
				'company'    => $h24_shipping_company,
				'address_1'  => $h24_shipping_address_1,
				'address_2'  => $h24_shipping_address_2,
				'city'       => $h24_shipping_city,
				'state'      => $h24_shipping_state,
				'postcode'   => $h24_shipping_postcode,
				'country'    => $h24_shipping_country,
				'email'      => $email,
				'phone'      => $phone
			);

			$billing_address = array(
				'first_name' => $h24_billing_first_name,
				'last_name'  => $h24_billing_last_name,
				'company'    => $h24_billing_company,
				'address_1'  => $h24_billing_address_1,
				'address_2'  => $h24_billing_address_2,
				'city'       => $h24_billing_city,
				'state'      => $h24_billing_state,
				'postcode'   => $h24_billing_postcode,
				'country'    => $h24_billing_country,
				'email'      => $email,
				'phone'      => $phone
			);
	
			$order->set_address( $shipping_address, 'shipping' );
			$order->set_address( $billing_address, 'billing' );
	
			if($is_free_shipping == false) {
				$shipping_methods = $cart['shipping_methods'];

				// add shipping
				foreach($shipping_methods as $shipping_method) {
					$shipping_method_label = $shipping_method['label'];
					$shipping_method_id = $shipping_method['id'];
					$shipping_method_cost = floatval($shipping_method['cost']);
	
					$shipping = new WC_Order_Item_Shipping();
					$shipping->set_method_title( $shipping_method_label );
					$shipping->set_method_id( $shipping_method_id ); // set an existing Shipping method ID
					$shipping->set_total( $shipping_method_cost ); // optional
					$order->add_item( $shipping );
				}
			}
	
			$coupon_codes = $cart['coupon_codes'];
			if($coupon_codes) {
				// $coupon_codes = explode (",", $coupon_code); 
				foreach($coupon_codes as $codeStr) {
					$order->apply_coupon($codeStr);
				}
			}
	
			$payment_gateways = WC()->payment_gateways->payment_gateways();
			// add payment method
			$order->set_payment_method($payment_gateways[$payment_gateway_id]);
			
			// $order->calculate_shipping();
	
			// calculate and save
			$order->calculate_totals();
	
			if($note == null) {
				$note = 'Order is created from cart';
			}

			// order status
			$order->set_status( 'wc-processing', $note );
				
			if($discount_title == null) {
				$discount_title = 'Discount';
			}

			if($discount_percent != null) {
				$discount_percent_str = '' . $discount_percent . '%';
				$this->wc_order_add_discount( $order->get_id(), $discount_title, $discount_percent_str );
			}else if($flat_discount_amount != null) {
				$this->wc_order_add_discount( $order->get_id(), $discount_title, $flat_discount_amount );
			}

			$order->save();

			$order_data = $order->get_data();
			$order_data['line_items'] = $this->getLineItemsData($order);
	
			return array(
				"code" => "SUCCESS",
				"data" => $order_data
			);
		}else {
			return array(
				"code" => "FAILURE",
				"data" => "Input cart is not valid."
			);
		}

	}

	public function get_available_shipping_methods(){

        if ( ! class_exists( 'WC_Shipping_Zones' ) ) {
            return array();
        }

        $zones = WC_Shipping_Zones::get_zones();

        if ( ! is_array( $zones ) ) {
            return array();
        }

        $shipping_methods = array_column( $zones, 'shipping_methods' );

        $flatten = array_merge( ...$shipping_methods );

        $normalized_shipping_methods = array();

        foreach ( $flatten as $key => $class ) {
            $normalized_shipping_methods[ $class->id ] = array(
				'title' => $class->method_title, 
				'fee' => $class->fee,
				'enabled' => $class->enabled,
				'rates' => $class->rates,
				'minimum_fee' => $class->minimum_fee,
				'countries' => $class->countries,
			);
        }

        return $normalized_shipping_methods;

    }

	/**
	 * Add a discount to an Orders programmatically
	 * (Using the FEE API - A negative fee)
	 *
	 * @since  3.2.0
	 * @param  int     $order_id  The order ID. Required.
	 * @param  string  $title  The label name for the discount. Required.
	 * @param  mixed   $amount  Fixed amount (float) or percentage based on the subtotal. Required.
	 * @param  string  $tax_class  The tax Class. '' by default. Optional.
	 */
	public function wc_order_add_discount( $order_id, $title, $amount, $tax_class = '' ) {
		$order    = wc_get_order($order_id);
		// $subtotal = $order->get_subtotal();
		$total = $order->get_total();

		$item     = new WC_Order_Item_Fee();

		if ( strpos($amount, '%') !== false ) {
			$percentage = (float) str_replace( array('%', ' '), array('', ''), $amount );
			$percentage = $percentage > 100 ? -100 : -$percentage;
			$discount   = $percentage * $total / 100;
		} else {
			$discount = (float) str_replace( ' ', '', $amount );
			if($discount > $total) {
				return;
			}else {
				$discount = -$discount;
			}
		}

		$item->set_tax_class( $tax_class );
		$item->set_name( $title );
		$item->set_amount( $discount );
		$item->set_total( $discount );

		if ( '0' !== $item->get_tax_class() && 'taxable' === $item->get_tax_status() && wc_tax_enabled() ) {
			$tax_for   = array(
				'country'   => $order->get_shipping_country(),
				'state'     => $order->get_shipping_state(),
				'postcode'  => $order->get_shipping_postcode(),
				'city'      => $order->get_shipping_city(),
				'tax_class' => $item->get_tax_class(),
			);
			$tax_rates = WC_Tax::find_rates( $tax_for );
			$taxes     = WC_Tax::calc_tax( $item->get_total(), $tax_rates, false );
			print_pr($taxes);

			if ( method_exists( $item, 'get_subtotal' ) ) {
				$subtotal_taxes = WC_Tax::calc_tax( $item->get_subtotal(), $tax_rates, false );
				$item->set_taxes( array( 'total' => $taxes, 'subtotal' => $subtotal_taxes ) );
				$item->set_total_tax( array_sum($taxes) );
			} else {
				$item->set_taxes( array( 'total' => $taxes ) );
				$item->set_total_tax( array_sum($taxes) );
			}
			$has_taxes = true;
		} else {
			$item->set_taxes( false );
			$has_taxes = false;
		}
		$item->save();

		$order->add_item( $item );
		$order->calculate_totals( $has_taxes );
		$order->save();
	}

	public function setWebhook($request){
		$name = sanitize_text_field( $request->get_param('name') );

		// DELETE ALREADY EXISTING WEBHOOK WITH SAME NAME
		$this->deleteWebhookWithName($name);

		$topic = sanitize_text_field( $request->get_param('topic') );
		$callbackUrl = sanitize_text_field( $request->get_param('callbackUrl') );
		$userID = $this->get_h24_setting_by_meta('user_id');

		$webhook = new WC_Webhook($this->get_h24_setting_by_meta( $name ));
		$webhook->set_user_id( $userID ); 
		$webhook->set_topic( $topic ); 
		$webhook->set_delivery_url( $callbackUrl ); 
		$webhook->set_status( "active" ); 
		$webhook->set_name( $name );
		$webhook->save();
		return array(
			"code" => "SUCCESS"
		);
	}

	public function deleteWebhook($request){
		$name = sanitize_text_field( $request->get_param('name') );

		$this->deleteWebhookWithName($name);

		return array(
			"code" => "SUCCESS"
		);
	}
	
	public function updateSettings($request){
		$params = $this->wporg_recursive_sanitize_text_field( $request->get_params() );

		foreach ( $params as $param_key => $param_value ) {
			$this->set_h24_setting_by_meta($param_key, $param_value);
		}

		return array(
			"code" => "SUCCESS"
		);
	}

	public function deleteWebhookWithName($name){
		$data_store = WC_Data_Store::load( 'webhook' );
		$webhookIds   = $data_store->search_webhooks();
		foreach ( $webhookIds as $webhookId ) {
			$webhook = new WC_Webhook($webhookId);

			if($webhook->get_name() == $name ) {
				$webhook->delete();
			}
		}
	}

	public function deleteWebhooks($request){
		$this->deleteAllHello24Webhooks();
		return array(
			"code" => "SUCCESS"
		);
	}

	public function deleteAllHello24Webhooks() {
		$data_store = WC_Data_Store::load( 'webhook' );
		$webhookIds   = $data_store->search_webhooks();
		foreach ( $webhookIds as $webhookId ) {
			$webhook = new WC_Webhook($webhookId);

			if(str_starts_with($webhook->get_name(), 'Hello24' )) {
				$webhook->delete();
			}
		}
	}

	function hello24_chat_widget() {
		$phone_number = $this->get_h24_setting_by_meta('phone_number');
		$chat_button_enabled = $this->get_h24_setting_by_meta('chat_button_enabled');
		if($chat_button_enabled == null) {
			$chat_button_enabled = "enabled";
		}

		$chat_button_title = $this->get_h24_setting_by_meta('chat_button_title');
		if($chat_button_title == null) {
			$chat_button_title = 'Need Help ?';
		}

		$chat_button_theme_color = $this->get_h24_setting_by_meta('chat_button_theme_color');
		if($chat_button_theme_color == null) {
			$chat_button_theme_color = '#000075';
		}

		$chat_button_theme_color_gradient = $this->get_h24_setting_by_meta('chat_button_theme_color_gradient');
		if($chat_button_theme_color_gradient == null) {
			if($chat_button_theme_color != null) {
				$chat_button_theme_color_gradient = $chat_button_theme_color;
			}else {
				$chat_button_theme_color_gradient = '#000075';
			}
		}

		$chat_button_sub_title = $this->get_h24_setting_by_meta('chat_button_sub_title');
		if($chat_button_sub_title == null) {
			$chat_button_sub_title = 'Typically replies in minutes';
		}

		$chat_button_greeting_text1 = $this->get_h24_setting_by_meta('chat_button_greeting_text1');
		if($chat_button_greeting_text1 == null) {
			$chat_button_greeting_text1 = 'Hello there 👋';
		}

		$chat_button_greeting_text2 = $this->get_h24_setting_by_meta('chat_button_greeting_text2');
		if($chat_button_greeting_text2 == null) {
			$chat_button_greeting_text2 = 'How can I help you?';
		}

		$chat_button_agent_name = $this->get_h24_setting_by_meta('chat_button_agent_name');
		if($chat_button_agent_name == null) {
			$chat_button_agent_name = 'Customer Support';
		}

		$chat_button_message = $this->get_h24_setting_by_meta('chat_button_message');
		if($chat_button_message == null) {
			$chat_button_message = 'Hi';
		}

		$chat_button_position = $this->get_h24_setting_by_meta('chat_button_position');
		if($chat_button_position == null) {
			$chat_button_position = 'right';
		}

		$chat_button_bottom = $this->get_h24_setting_by_meta('chat_button_bottom');
		if($chat_button_bottom == null) {
			$chat_button_bottom = '40';
		}

		$hello24_chat_button_size = $this->get_h24_setting_by_meta('hello24_chat_button_size');
		$hello24_chat_mobile_link = $this->get_h24_setting_by_meta('hello24_chat_mobile_link');
		$hello24_chat_web_link = $this->get_h24_setting_by_meta('hello24_chat_web_link');
		$hello24_chat_button = $this->get_h24_setting_by_meta('hello24_chat_button');

		if ($phone_number && $chat_button_enabled == "enabled"){

			$chat_button_js_path = H24_CART_ABANDONMENT_TRACKING_URL . 'assets/js/hello24-chat-button1.js';

			echo '<script>
				//MANDATORY
				window.hello24_phoneNumber = "' . esc_attr($phone_number) . '";
		
				//OPTIONAL
				window.hello24_companyName = "Hello24";
				window.hello24_title = "' . esc_attr($chat_button_title) . '";
				window.hello24_subTitle = "' . esc_attr($chat_button_sub_title) . '";
				window.hello24_greetingText1 = "' . esc_attr($chat_button_greeting_text1) . '";
				window.hello24_greetingText2 = "' . esc_attr($chat_button_greeting_text2) . '";
				window.hello24_agentName = "' . esc_attr($chat_button_agent_name) . '";
				window.hello24_message = "' . esc_attr($chat_button_message) . '";
				
				window.hello24_chat_theme_color = "' . esc_attr($chat_button_theme_color) . '";
				window.hello24_chat_theme_color_gradient = "' . esc_attr($chat_button_theme_color_gradient) . '";
				window.hello24_chat_button_size = "' . esc_attr($hello24_chat_button_size) . '";
				window.hello24_chat_button_position = "' . esc_attr($chat_button_position) . '";
				window.hello24_chat_button_bottom = "' . esc_attr($chat_button_bottom) . '";
				window.hello24_chat_mobile_link = "' . esc_url($hello24_chat_mobile_link) . '";
				window.hello24_chat_web_link = "' . esc_url($hello24_chat_web_link) . '";
				window.hello24_chat_button = "' . esc_attr($hello24_chat_button) . '";

			</script>
			<script src="' . esc_attr($chat_button_js_path) . '"></script>
		';
		}
	}

	public function rand_string( $length ) {  
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";  
		$size = strlen( $chars );  
		$str = "";
		for( $i = 0; $i < $length; $i++ ) {  
			$str .= $chars[ rand( 0, $size - 1 ) ];
		}
		return $str;
	}
}

H24_Cart_Abandonment::get_instance();
