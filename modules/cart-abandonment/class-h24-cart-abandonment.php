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
	
		add_action( 'wp_ajax_h24_save_whatsapp_button', array( $this, 'h24_save_whatsapp_button' ) );
		add_action( 'wp_ajax_nopriv_h24_save_whatsapp_button', array( $this, 'h24_save_whatsapp_button' ) );
	
		add_action( 'wp_footer', array( $this, 'whatsapp_chat_widget') );

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

		add_filter( 'jwt_auth_whitelist', function ( $endpoints ) {
			return array(
				'/wp-json/api/v1/getWoocommerceInfo',
				'/wp-json/api/v1/getOrderUrl',
				'/wp-json/api/v1/getAbandonedCarts',
				'/wp-json/api/v1/listProducts',
				'/wp-json/api/v1/listCategories',
				'/wp-json/api/v1/listOrders',
				'/wp-json/api/v1/getOrdersByPhone',
				'/wp-json/api/v1/getOrderByID',
				'/wp-json/api/v1/updateOrderStatus',
				'/wp-json/api/v1/setWebhook',
				'/wp-json/api/v1/deleteWebhook',
				'/wp-json/api/v1/deleteWebhooks',
			);
		});

		add_filter( 'wp', array( $this, 'restore_cart_abandonment_data' ), 10 );		
		add_action( 'woocommerce_order_status_changed', array( $this, 'h24_ca_update_order_status' ), 999, 3 );
	}

	/**
	 *  Initialise all the constants
	 */
	public function define_cart_abandonment_constants() {
		define( 'H24_CARTFLOWS_CART_ABANDONMENT_TRACKING_DIR', WP_H24_DIR . 'modules/cart-abandonment/' );
		define( 'H24_CARTFLOWS_CART_ABANDONMENT_TRACKING_URL', WP_H24_URL . 'modules/cart-abandonment/' );
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
		$whatsapp_number = $this->get_h24_setting_by_meta("whatsapp_number");
		$environment = $this->get_h24_setting_by_meta("environment");
		if($environment == null) {
			$environment = "prod";
		}

		$code = $this->get_h24_setting_by_meta("code");
		$h24_setting_url = $h24_domain_front . "?wordpressDomain=" . get_home_url();

		$whatsapp_button_enabled = $this->get_h24_setting_by_meta("whatsapp_button_enabled");
		if($whatsapp_button_enabled == null) {
			$whatsapp_button_enabled = true;
		}

		$whatsapp_button_title = $this->get_h24_setting_by_meta('whatsapp_button_title');
		if($whatsapp_button_title == null) {
			$whatsapp_button_title = 'Need Help ?';
		}

		$whatsapp_button_sub_title = $this->get_h24_setting_by_meta('whatsapp_button_sub_title');
		if($whatsapp_button_sub_title == null) {
			$whatsapp_button_sub_title = 'Typically replies in minutes';
		}

		$whatsapp_button_greeting_text1 = $this->get_h24_setting_by_meta('whatsapp_button_greeting_text1');
		if($whatsapp_button_greeting_text1 == null) {
			$whatsapp_button_greeting_text1 = 'Hello there 👋';
		}

		$whatsapp_button_greeting_text2 = $this->get_h24_setting_by_meta('whatsapp_button_greeting_text2');
		if($whatsapp_button_greeting_text2 == null) {
			$whatsapp_button_greeting_text2 = 'How can I help you?';
		}

		$whatsapp_button_agent_name = $this->get_h24_setting_by_meta('whatsapp_button_agent_name');
		if($whatsapp_button_agent_name == null) {
			$whatsapp_button_agent_name = 'Customer Support';
		}

		$whatsapp_button_message = $this->get_h24_setting_by_meta('whatsapp_button_message');
		if($whatsapp_button_message == null) {
			$whatsapp_button_message = 'Hi';
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
		include_once H24_CARTFLOWS_CART_ABANDONMENT_TRACKING_DIR . 'includes/admin/h24-admin-settings.php';
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
			H24_CARTFLOWS_CART_ABANDONMENT_TRACKING_URL . 'assets/js/h24-abandonment-tracking.js',
			array( 'jquery' ),
			"1.0",
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
		$current_user        = wp_get_current_user();
		$roles               = $current_user->roles;
		$role                = array_shift( $roles );
		
		global $post;
		wp_enqueue_script(
			'webhook_setting_script',
			H24_CARTFLOWS_CART_ABANDONMENT_TRACKING_URL . 'assets/js/webhook-setting.js',
			array( 'jquery' ),
			"1.0",
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

				if ( isset( $token_data['h24_coupon_code'] ) && ! $woocommerce->cart->applied_coupons ) {
					$woocommerce->cart->add_discount( $token_data['h24_coupon_code'] );
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

			$checkout_details = array(
				'email'         => $post_data['h24_email'],
				'cart_contents' => serialize( $products ),
				'cart_total'    => sanitize_text_field( $cart_total ),
				'time'          => sanitize_text_field( $current_time ),
				'local_time'          => sanitize_text_field( $local_time ),
				'other_fields'  => serialize( $other_fields ),
				'checkout_id'   => $post_data['h24_post_id'],
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
		$whatsapp_number = sanitize_text_field( $_POST['whatsapp_number'] );
		$environment = sanitize_text_field( $_POST['environment'] );

		$email = sanitize_email( $_POST['email'] );

		$url = WP_HELLO24_SERVICE_BASE_URL . "/" . $environment . "/webhook_woocommerce/wordpress_plugin_installed";

		$code = $this->rand_string(16);

		$this->set_h24_setting_by_meta("code", $code);
		$this->set_h24_setting_by_meta("shop_name", $shop_name);
		$this->set_h24_setting_by_meta("email", $email);
		$this->set_h24_setting_by_meta("whatsapp_number", $whatsapp_number);
		$this->set_h24_setting_by_meta("environment", $environment);
		$this->set_h24_setting_by_meta("api_key", md5( uniqid( wp_rand(), true ) ));

		$data = array(
			'apiKey' => $api_key,
			'shopName' => $shop_name,
			'email' => $email,
			'whatsappNumber' => $whatsapp_number,
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
			$this->set_h24_setting_by_meta("api_key", $api_key);
			wp_send_json_success($response);
		}
		else{
			wp_send_json_success();
		}

	}

	public function h24_save_whatsapp_button() {		
		$whatsapp_button_enabled =  rest_sanitize_boolean( $_POST['whatsapp_button_enabled'] );	
		$whatsapp_button_title = sanitize_text_field( $_POST['whatsapp_button_title'] );	
		$whatsapp_button_sub_title = sanitize_text_field( $_POST['whatsapp_button_sub_title'] );	
		$whatsapp_button_greeting_text1 = sanitize_text_field( $_POST['whatsapp_button_greeting_text1'] );	
		$whatsapp_button_greeting_text2 = sanitize_text_field( $_POST['whatsapp_button_greeting_text2'] );	
		$whatsapp_button_agent_name = sanitize_text_field( $_POST['whatsapp_button_agent_name'] );	
		$whatsapp_button_message = sanitize_text_field( $_POST['whatsapp_button_message'] );	

		$this->set_h24_setting_by_meta("whatsapp_button_enabled", $whatsapp_button_enabled);
		$this->set_h24_setting_by_meta("whatsapp_button_title", $whatsapp_button_title);
		$this->set_h24_setting_by_meta("whatsapp_button_sub_title", $whatsapp_button_sub_title);
		$this->set_h24_setting_by_meta("whatsapp_button_greeting_text1", $whatsapp_button_greeting_text1);
		$this->set_h24_setting_by_meta("whatsapp_button_greeting_text2", $whatsapp_button_greeting_text2);
		$this->set_h24_setting_by_meta("whatsapp_button_agent_name", $whatsapp_button_agent_name);
		$this->set_h24_setting_by_meta("whatsapp_button_message", $whatsapp_button_message);

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
		$api_key = sanitize_text_field( $request->get_header('apiKey') );
		if ($api_key == $this->get_h24_setting_by_meta('api_key')){
			return array(
				"currency" => get_woocommerce_currency(),
				"woocommerceApiUrl" => get_woocommerce_api_url(''),
				"shopName" => $this->get_h24_setting_by_meta('shop_name'),
				"email" => $this->get_h24_setting_by_meta('email'),
				"whatsappNumber" => $this->get_h24_setting_by_meta('whatsapp_number'),
				"environment" => $this->get_h24_setting_by_meta('environment'),
				"pluginActivated" => $this->get_h24_setting_by_meta('plugin_activated')
			);
		} else {
			return null;
		}
	}

	public function getOrderUrl($request){
		$api_key = sanitize_text_field( $request->get_header('apiKey') );
		if ($api_key == $this->get_h24_setting_by_meta('api_key')){
			$order_id = sanitize_text_field( $request->get_param('orderID') );
			$order = wc_get_order($order_id);

			if (!$order){
				return null;
			}

			return array(
				"order_url" => $order->get_checkout_order_received_url()
			);
		} else {
			return 'NOT AUTHORIZED';
		}
	}
	
	
	public function getAbandonedCarts($request){
		$api_key = sanitize_text_field( $request->get_header('apiKey') );
		if ($api_key == $this->get_h24_setting_by_meta('api_key')){
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
					'id'         	=> $cart->id,
					'checkout_id'   => $cart->checkout_id,
					'checkout_url'	=> $checkout_url,
					'email'         => $cart->email,
					'line_items' 	=> $cartFormatted,
					'cart_total'    => $cart->cart_total,
					'session_id' 	=> $cart->session_id,
					'other_fields'  => unserialize($cart->other_fields),
					'order_status'  => $cart->order_status,
					'unsubscribed'  => $cart->unsubscribed,
					'coupon_code'  	=> $cart->coupon_code,
					'time'          => $cart->time,
					'local_time'      => $cart->local_time,
				);

				$abandoned_carts[] = $abandoned_cart;
			}
			
			return array(
				"abandoned_carts" => $abandoned_carts
			);
		} else {
			return 'NOT AUTHORIZED';
		}
	}

	public function listCategories($request){
		$api_key = sanitize_text_field( $request->get_header('apiKey') );
		if ($api_key == $this->get_h24_setting_by_meta('api_key')){
			$categories = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'orderby'    => 'name',
					'hide_empty' => false,
				)
			);
			
			return array(
				"code" => "SUCCESS",
				"data" => $categories
			);
		} else {
			return array(
				"code" => "FAILURE",
				"data" => "NOT AUTHORIZED"
			);
		}
	}

	public function listOrders($request){
		$api_key = sanitize_text_field( $request->get_header('apiKey') );
		if ($api_key == $this->get_h24_setting_by_meta('api_key')){
			$params = $this->wporg_recursive_sanitize_text_field( $request->get_params() );
	
			$query = new WC_Order_Query($params);
			
			$orders = $query->get_orders();
			$orderDatas = array();
			
			foreach($orders as $order) {
				$order_data = $order->get_data(); // The Order data
				$orderDatas[] = $order_data;
			}
			
			return array(
				"code" => "SUCCESS",
				"data" => $orderDatas
			);
		} else {
			return array(
				"code" => "FAILURE",
				"data" => "NOT AUTHORIZED"
			);
		}
	}

	public function listProducts($request){
		$api_key = sanitize_text_field( $request->get_header('apiKey') );
		if ($api_key == $this->get_h24_setting_by_meta('api_key')){
			$params = $this->wporg_recursive_sanitize_text_field( $request->get_params() );
	
			$query = new WC_Product_Query($params);
			
			$products = $query->get_products();
			$productDatas = array();
			
			foreach($products as $product) {
				$product_data = $product->get_data(); // The Order data

				if ($product->get_type() == "variable") {
					$product_data["variations"] = $product->get_available_variations();
				}

				$productDatas[] = $product_data;
			}
			
			return array(
				"code" => "SUCCESS",
				"data" => $productDatas
			);
		} else {
			return array(
				"code" => "FAILURE",
				"data" => "NOT AUTHORIZED"
			);
		}
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
		$api_key = sanitize_text_field( $request->get_header('apiKey') );
		if ($api_key == $this->get_h24_setting_by_meta('api_key')){
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
				$orderDatas[] = $order_data;
			}
			
			return $orderDatas;
		} else {
			return 'NOT AUTHORIZED';
		}
	}
	
	public function getOrderByID($request){
		$api_key = sanitize_text_field( $request->get_header('apiKey') );
		if ($api_key == $this->get_h24_setting_by_meta('api_key')){
			$order_id = sanitize_text_field( $request->get_param('orderID') );	
			$order = wc_get_order( $order_id );
			if($order == false) {
				return array(
					"code" => "FAILURE",
					"data" => "No order found with provided order ID"
				);
			}else {
				return array(
					"code" => "SUCCESS",
					"data" => $order->get_data()
				);
			}
		} else {
			return 'NOT AUTHORIZED';
		}
	}

	public function updateOrderStatus($request){
		$api_key = sanitize_text_field( $request->get_header('apiKey') );
		if ($api_key == $this->get_h24_setting_by_meta('api_key')){
			$order_id = sanitize_text_field( $request->get_param('orderID') );	
			$status = sanitize_text_field( $request->get_param('status') );	
			$order = wc_get_order( $order_id );
			$order->update_status($status); 
			return array(
				"code" => "SUCCESS"
			);
		} else {
			return array(
				"code" => "FAILURE"
			);
		}
	}

	public function setWebhook($request){
		$api_key = sanitize_text_field( $request->get_header('apiKey') );
		if ($api_key == $this->get_h24_setting_by_meta('api_key')){
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
		} else {
			return array(
				"code" => "NOT AUTHORIZED"
			);		
		}
	}

	public function deleteWebhook($request){
		$api_key = sanitize_text_field( $request->get_header('apiKey') );
		if ($api_key == $this->get_h24_setting_by_meta('api_key')){
			$name = sanitize_text_field( $request->get_param('name') );

			$this->deleteWebhookWithName($name);

			return array(
				"code" => "SUCCESS"
			);
		} else {
			return array(
				"code" => "NOT AUTHORIZED"
			);		
		}
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
		$api_key = sanitize_text_field( $request->get_header('apiKey') );
		if ($api_key == $this->get_h24_setting_by_meta('api_key')){
			$this->deleteAllHello24Webhooks();
			return array(
				"code" => "SUCCESS"
			);
		} else {
			return array(
				"code" => "FAILURE"
			);		
		}
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

	function whatsapp_chat_widget() {
		$whatsapp_number = $this->get_h24_setting_by_meta('whatsapp_number');
		$whatsapp_button_enabled = $this->get_h24_setting_by_meta('whatsapp_button_enabled');
		if($whatsapp_button_enabled == null) {
			$whatsapp_button_enabled = false;
		}

		$whatsapp_button_title = $this->get_h24_setting_by_meta('whatsapp_button_title');
		if($whatsapp_button_title == null) {
			$whatsapp_button_title = 'Need Help ?';
		}

		$whatsapp_button_sub_title = $this->get_h24_setting_by_meta('whatsapp_button_sub_title');
		if($whatsapp_button_sub_title == null) {
			$whatsapp_button_sub_title = 'Typically replies in minutes';
		}

		$whatsapp_button_greeting_text1 = $this->get_h24_setting_by_meta('whatsapp_button_greeting_text1');
		if($whatsapp_button_greeting_text1 == null) {
			$whatsapp_button_greeting_text1 = 'Hello there 👋';
		}

		$whatsapp_button_greeting_text2 = $this->get_h24_setting_by_meta('whatsapp_button_greeting_text2');
		if($whatsapp_button_greeting_text2 == null) {
			$whatsapp_button_greeting_text2 = 'How can I help you?';
		}

		$whatsapp_button_agent_name = $this->get_h24_setting_by_meta('whatsapp_button_agent_name');
		if($whatsapp_button_agent_name == null) {
			$whatsapp_button_agent_name = 'Customer Support';
		}

		$whatsapp_button_message = $this->get_h24_setting_by_meta('whatsapp_button_message');
		if($whatsapp_button_message == null) {
			$whatsapp_button_message = 'Hi';
		}

		if ($whatsapp_number && $whatsapp_button_enabled == true){

			$whatsapp_button_path = H24_CARTFLOWS_CART_ABANDONMENT_TRACKING_URL . 'assets/js/hello24-whatsapp-chat-button1.js';

			echo '<script>
				//MANDATORY
				window.hello24_whatsappNumber = "' . esc_attr($whatsapp_number) . '";
		
				//OPTIONAL
				window.hello24_companyName = "Hello24";
				window.hello24_title = "' . esc_attr($whatsapp_button_title) . '";
				window.hello24_subTitle = "' . esc_attr($whatsapp_button_sub_title) . '";
				window.hello24_greetingText1 = "' . esc_attr($whatsapp_button_greeting_text1) . '";
				window.hello24_greetingText2 = "' . esc_attr($whatsapp_button_greeting_text2) . '";
				window.hello24_agentName = "' . esc_attr($whatsapp_button_agent_name) . '";
				window.hello24_message = "' . esc_attr($whatsapp_button_message) . '";
			</script>
			<script src="' . esc_attr($whatsapp_button_path) . '"></script>
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
