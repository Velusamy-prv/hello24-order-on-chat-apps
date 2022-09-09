<?php
/**
 * Hello24 - Order on Chat, Abandoned cart recovery & Marketing Automation
 *
 * @package Hello24-Order-On-Chat-Apps
 */

/**
 * Hello24 - Order on Chat, Abandoned cart recovery & Marketing Automation class.
 */
class H24_Cart_Abandonment_Db {



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
	 *  Create tables
	 */
	public function create_tables() {
		$this->create_cart_abandonment_table();
		$this->create_h24_setting_table();
	}

	/**
	 *  Create Plugin Setting meta table.
	 */
	public function create_h24_setting_table() {
		global $wpdb;

		$h24_setting_tb       = $wpdb->prefix . WP_H24_SETTING_TABLE;
		$charset_collate              = $wpdb->get_charset_collate();

		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $h24_setting_tb );
		
		if ( !$wpdb->get_var( $query ) == $h24_setting_tb ) {
			$sql = "CREATE TABLE $h24_setting_tb (
				`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
				`meta_key` varchar(255) NOT NULL,
				`meta_value` longtext NOT NULL,
				PRIMARY KEY (`id`)
				) $charset_collate;\n";
				include_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
		}
	}

	/**
	 *  Create tables for analytics.
	 */
	public function create_cart_abandonment_table() {

		global $wpdb;

		$cart_abandonment_db = $wpdb->prefix . WP_H24_ABANDONMENT_TABLE;
		$charset_collate     = $wpdb->get_charset_collate();
		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $cart_abandonment_db );
		if ( !$wpdb->get_var( $query ) == $cart_abandonment_db ) {
			// Cart abandonment tracking db sql command.
			$sql = "CREATE TABLE $cart_abandonment_db (
				id BIGINT(20) NOT NULL AUTO_INCREMENT,
				checkout_id int(11) NOT NULL, 
				email VARCHAR(100),
				cart_contents LONGTEXT,
				cart_total DECIMAL(10,2),
				cart_total_tax DECIMAL(10,2),
				cart_subtotal DECIMAL(10,2),
				cart_subtotal_tax DECIMAL(10,2),
				cart_shipping_total DECIMAL(10,2),
				cart_fee_total DECIMAL(10,2),
				cart_discount_total DECIMAL(10,2),
				session_id VARCHAR(60) NOT NULL,
				other_fields LONGTEXT,
				order_status ENUM( 'normal','abandoned','completed','lost') NOT NULL DEFAULT 'normal',
				unsubscribed  boolean DEFAULT 0,
				coupon_codes LONGTEXT,
				shipping_methods LONGTEXT,
				time DATETIME DEFAULT NULL,
				local_time DATETIME DEFAULT NULL,
				PRIMARY KEY  (`id`, `session_id`),
				UNIQUE KEY `session_id_UNIQUE` (`session_id`)
			) $charset_collate;\n";

			include_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
		}

	}

	/**
	 *  Insert initial data.
	 */
	public function init_tables() {
		global $wpdb;
		$h24_setting_tb       = $wpdb->prefix . WP_H24_SETTING_TABLE;

		$meta_count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $h24_setting_tb") );
		if ( ( ! $meta_count ) ) {
			
			$meta_data = array();
			$meta_data["api_key"] = md5( uniqid( wp_rand(), true ) );
			$meta_data["environment"] = "prod";

			$meta_data["chat_button_enabled"] = "enabled";
			$meta_data["chat_button_title"] = "Need help ?";
			$meta_data["chat_button_sub_title"] = "Typically replies in minutes";
			$meta_data["chat_button_greeting_text1"] = "Hello there 👋";
			$meta_data["chat_button_greeting_text2"] = "How can I help you?";
			$meta_data["chat_button_agent_name"] = "Customer Support";
			$meta_data["chat_button_message"] = "Hi";
			$meta_data["chat_button_position"] = "right";

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
		}
	}
}

H24_Cart_Abandonment_Db::get_instance();
