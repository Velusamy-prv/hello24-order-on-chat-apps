<?php
/**
 * Hello24 - Order on Chat, Abandoned cart recovery & Marketing Automation
 *
 * @package Hello24-Order-On-Chat-Apps
 */

/**
 * Hello24 - Order on Chat, Abandoned cart recovery & Marketing Automation class.
 */
class H24_Cartflows_Ca_Module_Loader {



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
	 *  Constructor
	 */
	public function __construct() {

		$this->load_module_files();
	}


	/**
	 *  Load required files for module.
	 */
	private function load_module_files() {
		/* Cart abandonment tracking */
		include_once WP_H24_DIR . 'modules/cart-abandonment/class-h24-cart-abandonment.php';
	}

}

H24_Cartflows_Ca_Module_Loader::get_instance();
