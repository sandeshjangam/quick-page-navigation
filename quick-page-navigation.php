<?php
/*
Plugin Name: Quick Page Navigation
Description: Quick Access to any page from admin bar at frontend / backend.
Author: sandesh055
Version: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once 'classes/class-sj-qpn-loader.php';


if( !class_exists( "sqpn_Quick_Page_Navigation" ) ) {
	class sqpn_Quick_Page_Navigation {

		/*
		* Constructor function that initializes required actions and hooks
		* @Since 1.0
		*/
		function __construct() {
			
			// Activation & Deactivation
			register_deactivation_hook( __FILE__, array( $this, 'sqpn_plugin_deactivate' ) );
			register_activation_hook( __FILE__, array( $this, 'sqpn_plugin_activate' ) );

			// Check User and Add Menu
			add_action( 'init', array( $this, 'sqpn_check_user' ) );

			// Equeue Beaver Builder CSS & JS if Exist
			add_action('wp_enqueue_scripts', array( $this, 'sqpn_bb_css_js' ) );
		}

		// Plugin Deactivation
		function sqpn_plugin_deactivate() {
			if ( ! current_user_can( 'activate_plugins' ) )
				return;
		}

		// Plugin Activation
		function sqpn_plugin_activate() {
			if ( ! current_user_can( 'activate_plugins' ) )
				return;
		}

		function sqpn_check_user() {
			$user = wp_get_current_user();
			$allowed_roles = array('editor', 'administrator', 'author');
			
			if( is_user_logged_in() && !array_intersect( $allowed_roles, $user->roles ) ) {
				return;
			}
			
			if( !current_user_can( 'edit_others_pages' ) ) {
				return;
			}
    		
			// Admin Bar Menu
			add_action( 'admin_bar_menu', array( $this, 'sqpn_add_admin_bar_wp_menu' ), 555 );
    		add_action( 'admin_bar_menu', array( $this, 'sqpn_add_admin_bar_bb_menu' ), 556 );
    		add_action('admin_enqueue_scripts', array( $this, 'sqpn_common_scripts' ), 555 );
    		add_action('wp_enqueue_scripts', array( $this, 'sqpn_common_scripts' ), 555 );
		}

		function sqpn_bb_css_js() {
			if ( class_exists( 'FLBuilder' ) && FLBuilderModel::is_builder_active() ) {
				wp_enqueue_style('sqpn-bb', plugins_url('/assets/css/sqpn-bb.css', __FILE__), array() );
				wp_enqueue_script('sqpn-bb', plugins_url('/assets/js/sqpn-bb.js', __FILE__), array('jquery'), '', true);
			}
		}

		function sqpn_common_scripts() {
			wp_enqueue_style('sqpn-common', plugins_url('/assets/css/sqpn-common.css', __FILE__), array() );
			wp_enqueue_script('sqpn-common', plugins_url('/assets/js/sqpn-common.js', __FILE__), array('jquery'), '', true);
		}
				
		function sqpn_add_admin_bar_wp_menu( $wp_admin_bar ) {
			
			$wp_admin_bar->add_node( 
				array(
		    		'id' => 'sqpn_wp_pages', // an unique id (required)
		    		'title' => 'WP Pages', // title/menu text to display
		    		'href' => admin_url( '/edit.php?post_type=page'), // target url of this menu item
		    		'meta' => array(
		    		    'class' => 'sqpn-wp-pages-menu',
		    		)
		    	)
		    );

		    $wp_admin_bar->add_node( 
				array(
		    		'parent'	=> 'sqpn_wp_pages',
		    		'id' 		=> 'search_sqpn_wp_sub_pages', // an unique id (required)
		    		'title' 	=> '<input type="text" class="sqpn-search-input sqpn-wp-search-page" placeholder="Search Page" data-type="wp"/>',
		    		/*'href' 		=> '#', // target url of this menu item*/
		    		'meta' 		=> array(
		    		    'class' => 'sqpn-search-input-group sqpn-wp-pages-search',
		    		)
		    	)
		    );

		    $options = array(
				'sort_column' => 'menu_order',
				'parent' => 0,
				'post_type' => 'page',
			); 
			$pages = get_pages($options); 

			//$edit_wrap = '<span class="sqpn-edit-pages-wrap"><i class="fa fa-wordpress"></i><i class="fa fa-behance-square"></i><i class="fa fa-eye"></i></span>';
			foreach ($pages as $page) {
				$id 	= $page->ID;
				$title 	= $page->post_title;
				$url  	= get_page_link( $id ).'?fl_builder';
								
				$wp_admin_bar->add_node( 
					array(
			    		'parent'	=> 'sqpn_wp_pages',
			    		'id' 		=> $id.'_sqpn_wp_sub_pages', // an unique id (required)
			    		'title' 	=> $title, // title/menu text to display
			    		'href' 		=> admin_url( '/post.php?post=' . $id . '&action=edit'),
			    		'meta' 		=> array(
			    		    'class' => 'sqpn-wp-pages-sub-menu',
			    		)
			    	)
			    );
				//var_dump( $page );
			}
		}

		function sqpn_add_admin_bar_bb_menu( $wp_admin_bar ) {
			
			global $wp;
			
			// Cheeck if beaver builder is active
			if ( !class_exists( 'FLBuilder' ) ) {
			return;
			}

			$current_url  = ( isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on' ) ? 'https://'.$_SERVER["SERVER_NAME"] :  'http://'.$_SERVER["SERVER_NAME"];
		  	$current_url .= ( $_SERVER["SERVER_PORT"] !== 80 ) ? ":".$_SERVER["SERVER_PORT"] : "";
		  	$current_url .= $_SERVER["REQUEST_URI"];

		  	//var_dump( $current_url );

			$pos = strpos($current_url, 'wp-admin');
			if ($pos === false){
				$current_url = $current_url . '?fl_builder';
			}else{
				$current_url = '';
			}
			
			//var_dump( $ )
			$wp_admin_bar->add_node( 
				array(
		    		'id' => 'sqpn_bb_pages', // an unique id (required)
		    		'title' => 'Pages In BB', // title/menu text to display
		    		'href' => $current_url, // target url of this menu item
		    		'meta' => array(
		    		    'class' => 'sqpn-bb-pages-menu',
		    		)
		    	)
		    );

			$wp_admin_bar->add_node( 
				array(
		    		'parent'	=> 'sqpn_bb_pages',
		    		'id' 		=> 'search_sqpn_bb_sub_pages', // an unique id (required)
		    		'title' 	=> '<input type="text" class="sqpn-search-input sqpn-bb-search-page" placeholder="Search Page" data-type="bb"/>', // title/menu text to display
		    		/*'href' 		=> '#', // target url of this menu item*/
		    		'meta' 		=> array(
		    		    'class' => 'sqpn-search-input-group sqpn-bb-pages-search',
		    		)
		    	)
		    );

		    $options = array(
				'sort_column' => 'menu_order',
				'parent' => 0,
				'post_type' => 'page',
			); 
			$pages = get_pages($options); 

			foreach ($pages as $page) {
				$id 	= $page->ID;
				$title 	= $page->post_title;
				$url  	= get_page_link( $id ).'?fl_builder';
				/*echo "<pre>";
				var_dump( $id );
				var_dump( $title );
				var_dump( $url );
				echo "</pre>";*/
				
				$wp_admin_bar->add_node( 
					array(
			    		'parent'	=> 'sqpn_bb_pages',
			    		'id' 		=> $id.'_sqpn_bb_sub_pages', // an unique id (required)
			    		'title' 	=> $title, // title/menu text to display
			    		'href' 		=> $url, // target url of this menu item
			    		'meta' 		=> array(
			    		    'class' => 'sqpn-bb-pages-sub-menu',
			    		)
			    	)
			    );
				//var_dump( $page );
			}
		}
	}
	new sqpn_Quick_Page_Navigation();
}
