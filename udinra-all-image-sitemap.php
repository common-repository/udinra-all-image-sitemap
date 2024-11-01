<?php
/*
Plugin Name: Udinra All Image Sitemap
Plugin URI: https://udinra.com/downloads/udinra-sitemap-pro
Description: Automatically generates Google Sitemap and submits it to Google,Bing and Ask.com.
Author: Udinra
Version: 4.1.0
Author URI: https://udinra.com
*/

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

function Udinra_Sitemap() {
	
	switch (true) {
		case isset($_POST['udswebsave']):
		case isset($_POST['udsimgsave']):
		case isset($_POST['udsforumsave']):
			update_option('udinra_sitemap_user_options_save',1);
			include 'lib/udinra_save_options.php';
			break;		
		case isset($_POST['udscreate']):
			if(get_option('udinra_sitemap_user_options_save') == 1) {
				if(get_option('udinra_sitemap_user_cache_image') == 1 )  {
					udinra_sitemap_loop();
				}
				else {
					update_option('udinra_sitemap_response','Create Image Cache before Creating Sitemap');
				}
			}
			break;		
		case isset($_POST['udsimgdel']):
			update_option('udinra_sitemap_user_cache_image',0);
			udinra_image_cache_delete();
			break;																
		case isset($_POST['udsimage']):
			update_option('udinra_sitemap_user_cache_image',1);
			udinra_image_cache_create();
			break;					
		default:
			update_option('udinra_sitemap_response','Select Options, Generate Cache and Click Create Sitemap');
			break;
	}
	include 'lib/udinra_panel_html.php';
}

function udinra_sitemap_loop() {
	include 'init/udinra_init_plugin.php';		
	if(get_option('udinra_sitemap_category') == 1) {
		include 'web/udinra_sitemap_cat.php';
	}
		
	if(get_option('udinra_sitemap_tag') == 1) {
		include 'web/udinra_sitemap_tag.php';
	}
		
	if(get_option('udinra_sitemap_author') == 1) {
		include 'web/udinra_sitemap_author.php';
	}				
	include 'exit/udinra_ping_sitemap.php';
}

function udinra_image_cache_create() {
	include 'image/udinra_image.php';
	switch (get_option('udinra_sitemap_gallery')) {
		case 7:
			include 'image/udinra_wpgal.php';	
			break;				
		default:
			break;
	}
}

function udinra_sitemap_cache_image($udinra_sitemap_cache_post) {
	$udinra_sitemap_cache_post_id = $udinra_sitemap_cache_post->ID;

	include 'imgcache/udinra_image.php';
	switch (get_option('udinra_sitemap_gallery')) {
		case 7:
			include 'imgcache/udinra_wpgal.php';	
			break;				
		default:
			break;
	}
}

function udinra_sitemap_admin() {
	if (function_exists('add_options_page')) {
		add_options_page('Udinra Sitemap', 'Udinra Sitemap', 'manage_options', basename(__FILE__), 'Udinra_Sitemap');
	}
}

function udinra_sitemap_admin_style($hook) {
	if($hook == 'settings_page_udinra-all-image-sitemap') {
		wp_enqueue_style( 'udinra_sitemap_style', plugins_url('css/udstyle.css', __FILE__) );	
		wp_enqueue_script( 'udinra_sitemap_script', plugins_url('js/udscript.js', __FILE__),null,null,false );	
    }
}

function udinra_sitemap_settings_plugin_link( $links, $file ) 
{
    if ( $file == plugin_basename(dirname(__FILE__) . '/udinra-all-image-sitemap.php') ) 
    {
        $in = '<a href="options-general.php?page=udinra-all-image-sitemap">' . __('Settings','udsitemap') . '</a>';
        array_unshift($links, $in);
   }
    return $links;
}

function load_sitemap_index() {
	load_template( dirname( __FILE__ ) . '/feed-sitemap-index.php' );
}

include 'lib/udinra_init_func.php';
include 'lib/udinra_multisite_func.php';

register_activation_hook(__FILE__, 'udinra_sitemap_act');
register_deactivation_hook(__FILE__, 'udinra_sitemap_deact');

add_action( 'transition_post_status', 'udinra_sitemap_post_unpub', 10, 3 );
add_action('admin_menu','udinra_sitemap_admin');	
add_action('admin_notices', 'udinra_sitemap_admin_notice');
add_action('admin_init', 'udinra_sitemap_admin_ignore');
add_action( 'do_feed_sitemap-index','load_sitemap_index',10,1 );
add_action( 'wpmu_new_blog', 'udinra_sitemap_new_blog', 10, 6);        
add_action( 'admin_enqueue_scripts', 'udinra_sitemap_admin_style' );
add_filter( 'plugin_action_links', 'udinra_sitemap_settings_plugin_link', 10, 2 );

?>
