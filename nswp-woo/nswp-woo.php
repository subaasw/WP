<?php

/**
 * Plugin Name: WooCommerce Subscriptions Packages
 * Author: Subash
 * Author URI: https://www.twitter.com/subaasw
 * Version: 1.0
 * Description: WooCommerce custom pricing for subscriptions.
 * Requires Plugins: woocommerce
**/

if ( ! defined( 'ABSPATH' ) )  exit;

if ( ! class_exists( 'NSWC_Main' ) ){

	final class NSWC_Main {

		public static function init() {
			return new self();
		}

		function setup_plugin() {
			$this->define_constraints();
			$this->includes();
		}

		function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		function includes() {
			require_once NSWC_PATH . '/inc/class-core.php';
            require_once NSWC_PATH . '/inc/class-packages-pricing.php';
		}

		private function define_constraints() {
			$this->define( 'NSWC_PATH', plugin_dir_path( __FILE__ ) );
			$this->define( 'NSWC_URL', plugin_dir_url( __FILE__ ) );
		}
	}
}

function load_nswc_main() {
	$instance = NSWC_Main::init();
	$instance->setup_plugin();
}

add_action( 'plugins_loaded', 'load_nswc_main' );
