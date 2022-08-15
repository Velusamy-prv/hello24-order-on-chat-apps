<?php
/**
 * Hello24 - Order on Chat, Abandoned cart recovery & Marketing Automation
 * Unscheduling the events.
 *
 * @package Hello24-Order-On-Chat-Apps
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;
$cart_abandonment_table = $wpdb->prefix . WP_H24_ABANDONMENT_TABLE;
$h24_setting_tb       = $wpdb->prefix . WP_H24_SETTING_TABLE;

$wpdb->query( "DROP TABLE IF EXISTS {$cart_abandonment_table}" );
$wpdb->query( "DROP TABLE IF EXISTS {$h24_setting_tb}" );

wp_clear_scheduled_hook( 'h24_cartflow_ca_update_order_status_action' );
