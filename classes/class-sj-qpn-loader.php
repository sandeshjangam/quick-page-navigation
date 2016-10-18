<?php

if ( ! class_exists( 'SJQpnLoader' ) ) {
	
	/**
	 * Responsible for setting up builder constants, classes and includes.
	 *
	 * @since 1.0
	 */
	final class SJQpnLoader {
		
		/**
		 * Load the builder if it's not already loaded, otherwise
		 * show an admin notice.
		 *
		 * @since 1.0
		 * @return void
		 */ 
		static public function init() {
			self::define_constants();
			self::load_files();
		}
		
		/**
		 * Define builder constants.
		 *
		 * @since 1.0
		 * @return void
		 */ 
		static private function define_constants() {
			define('SJ_QPN_VERSION', '1.0.0');
			define('SJ_QPN_FILE', trailingslashit(dirname(dirname(__FILE__))) . 'quick-page-navigation.php');
			define('SJ_QPN_DIR', plugin_dir_path(SJ_QPN_FILE));
			define('SJ_QPN_URL', plugins_url('/', SJ_QPN_FILE));
		}
		
		/**
		 * Loads classes and includes.
		 *
		 * @since 1.0
		 * @return void
		 */ 
		static private function load_files() {
		
			/* Classes */
			require_once SJ_QPN_DIR . 'classes/class-sj-qpn-helper.php';
		}
	}
}

SJQpnLoader::init();
