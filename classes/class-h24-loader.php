<?php
/**
 * CartFlows Loader.
 *
 * @package Hello24-Order-On-Chat-Apps
 */

if ( ! class_exists( 'WP_H24_Loader' ) ) {

	/**
	 * Class WP_H24_Loader.
	 */
	final class WP_H24_Loader {


		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance = null;

		/**
		 * Member Variable
		 *
		 * @var utils
		 */
		public $utils = null;


		/**
		 *  Initiator
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self();

				/**
				 * CartFlows CA loaded.
				 *
				 * Fires when Cartflows CA was fully loaded and instantiated.
				 *
				 * @since 1.0.0
				 */
				do_action( 'h24_cartflow_ca_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			$this->define_constants();

			// Activation hook.
			register_activation_hook( H24_PLUGIN_FILE, array( $this, 'activation_reset' ) );

			// deActivation hook.
			register_deactivation_hook( H24_PLUGIN_FILE, array( $this, 'deactivation_reset' ) );

			add_action( 'plugins_loaded', array( $this, 'load_plugin' ), 99 );

		}

		/**
		 * Defines all constants
		 *
		 * @since 1.0.0
		 */
		public function define_constants() {
			define( 'WP_H24_BASE', plugin_basename( H24_PLUGIN_FILE ) );
			define( 'WP_H24_DIR', plugin_dir_path( H24_PLUGIN_FILE ) );
			define( 'WP_H24_URL', plugins_url( '/', H24_PLUGIN_FILE ) );
			define( 'WP_HELLO24_SERVICE_BASE_URL', 'https://api.hello24.in' );
			define( 'WP_H24_VER', '1.0.0' );
			define( 'WP_H24_SLUG', 'h24_cartflows_ca' );
			define( 'WP_H24_SETTING_TABLE', 'h24_setting' );
			define( 'WP_H24_ABANDONMENT_TABLE', 'h24_abandonment' );
			define( 'WP_H24_PAGE_NAME', 'hello24-order-on-chat-apps' );
			define( 'WP_H24_GENERAL_SETTINGS_SECTION', 'cartflows_cart_abandonment_settings_section' );
			
		}

		/**
		 * Loads plugin files.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function load_plugin() {

			if ( ! function_exists( 'WC' ) ) {
				add_action( 'admin_notices', array( $this, 'fails_to_load' ) );
				return;
			}

			$this->load_core_components();

			$this->initialize_cart_abandonment_tables();
			
			include_once WP_H24_DIR . 'modules/cart-abandonment/class-h24-cart-abandonment.php';
			$Abandonment = H24_Cart_Abandonment::get_instance();
			$h24Domain = $Abandonment -> get_h24_setting_by_meta("h24_domain");
			$Abandonment -> set_h24_setting_by_meta("plugin_activated", "true");
		}




		/**
		 * Fires admin notice when Elementor is not installed and activated.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function fails_to_load() {

			$this->initialize_cart_abandonment_tables();
			$screen = get_current_screen();

			if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
				return;
			}

			$class = 'notice notice-error';
			/* translators: %s: html tags */
			$message = sprintf( __( 'The %1$sHello24 - Order on Chat, Abandoned cart recovery & Marketing Automation%2$s plugin requires %1$sWooCommerce%2$s plugin installed & activated.', 'hello24-order-on-chat-apps' ), '<strong>', '</strong>' );
			$plugin  = 'woocommerce/woocommerce.php';

			if ( $this->is_woo_installed() ) {
				if ( ! current_user_can( 'activate_plugins' ) ) {
					return;
				}

				$action_url   = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
				$button_label = __( 'Activate WooCommerce', 'hello24-order-on-chat-apps' );

			} else {
				if ( ! current_user_can( 'install_plugins' ) ) {
					return;
				}

				$action_url   = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ), 'install-plugin_woocommerce' );
				$button_label = __( 'Install WooCommerce', 'hello24-order-on-chat-apps' );
			}

			$button = '<p><a href="' . $action_url . '" class="button-primary">' . $button_label . '</a></p><p></p>';

			printf( '<div class="%1$s"><p>%2$s</p>%3$s</div>', esc_attr( $class ), wp_kses_post( $message ), wp_kses_post( $button ) );
		}


		/**
		 * Is woocommerce plugin installed.
		 *
		 * @since 1.0.0
		 *
		 * @access public
		 */
		public function is_woo_installed() {

			$path    = 'woocommerce/woocommerce.php';
			$plugins = get_plugins();

			return isset( $plugins[ $path ] );
		}

		/**
		 * Create new database tables for plugin updates.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function initialize_cart_abandonment_tables() {

			include_once WP_H24_DIR . 'modules/cart-abandonment/class-h24-cart-abandonment-db.php';
			$db = H24_Cart_Abandonment_Db::get_instance();
			$db->create_tables();			
			$db->init_tables();
			
			include_once WP_H24_DIR . 'modules/cart-abandonment/class-h24-cart-abandonment.php';
			$Abandonment = H24_Cart_Abandonment::get_instance();
		}


		/**
		 * Load Core Components.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function load_core_components() {

			/* Cart abandonment templates class */
			include_once WP_H24_DIR . 'classes/class-h24-settings.php';
			include_once WP_H24_DIR . 'modules/cart-abandonment/class-h24-module-loader.php';			
		}


		/**
		 * Activation Reset
		 */
		public function activation_reset() {
			register_uninstall_hook( H24_PLUGIN_FILE, array( $this, 'uninstall_plugin' ));
			if ( !class_exists( 'WooCommerce' ) ) {
				return;
			}
			$this->initialize_cart_abandonment_tables();
			include_once WP_H24_DIR . 'modules/cart-abandonment/class-h24-cart-abandonment.php';
			$Abandonment = H24_Cart_Abandonment::get_instance();
			$h24Domain = $Abandonment -> get_h24_setting_by_meta("h24_domain");
			$environment = $Abandonment -> get_h24_setting_by_meta("environment");
			$Abandonment -> set_h24_setting_by_meta("plugin_activated", "true");			
			$user_id = get_current_user_id();
			$Abandonment -> set_h24_setting_by_meta("user_id", $user_id);
		}

		/**
		 * Deactivation Reset
		 */
		public function deactivation_reset() {
			if ( !class_exists( 'WooCommerce' ) ) {
				return;
			}
			include_once WP_H24_DIR . 'modules/cart-abandonment/class-h24-cart-abandonment.php';
			$Abandonment = H24_Cart_Abandonment::get_instance();
			$h24Domain = $Abandonment -> get_h24_setting_by_meta("h24_domain");
			$Abandonment -> set_h24_setting_by_meta("plugin_activated", "false");
			$Abandonment-> deleteAllHello24Webhooks();
		}

		/**
		 * Uninstall Plugin
		 */
		public function uninstall_plugin() {
			if ( !class_exists( 'WooCommerce' ) ) {
				return;
			}
			include_once WP_H24_DIR . 'modules/cart-abandonment/class-h24-cart-abandonment.php';
			$Abandonment = H24_Cart_Abandonment::get_instance();
			$h24Domain = $Abandonment -> get_h24_setting_by_meta("h24_domain");
			$Abandonment -> set_h24_setting_by_meta("plugin_activated", "false");
			$Abandonment-> deleteAllHello24Webhooks();
		}
	}

	/**
	 *  Prepare if class 'WP_H24_Loader' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	WP_H24_Loader::get_instance();
}


if ( ! function_exists( 'h24_ca' ) ) {
	/**
	 * Get global class.
	 *
	 * @return object
	 */
	function h24_ca() {
		return WP_H24_Loader::get_instance();
	}
}

