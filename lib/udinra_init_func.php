<?php

function udinra_sitemap_admin_notice() {
	global $current_user ;
	$user_id = $current_user->ID;
	if ( ! get_user_meta($user_id, 'udinra_sitemap_admin_notice') ) {
		echo '<div class="notice notice-info"><p>'; 
		printf(__('<b>Best Sitemap plugin with XML, Image, Video, HTML Sitemap support.</b> <a href="https://udinra.com/downloads/sitemap-pro">Know More</a> | <a href="%1$s">Hide Notice</a>'), '?udinra_image_admin_ignore=0');
		echo "</p></div>";
	}
}

function udinra_sitemap_admin_ignore() {
	global $current_user;
	$user_id = $current_user->ID;
	if ( isset($_GET['udinra_sitemap_admin_ignore']) && '0' == $_GET['udinra_sitemap_admin_ignore'] ) {
		add_user_meta($user_id, 'udinra_sitemap_admin_notice', 'true', true);
	}
}

function udinra_sitemap_post_unpub( $new_status, $old_status, $post) {
	if (get_option('udinra_sitemap_post_type')) {
		$temp_post_type = get_option('udinra_sitemap_post_type');
		if(strpos($temp_post_type , $post->post_type) !== false){
			if ( $old_status !== 'publish'  &&  $new_status == 'publish') {
				udinra_sitemap_cache_image($post);
			}
			if ( $old_status == 'publish'  &&  $new_status == 'publish') {
				udinra_sitemap_cache_image($post);
			}
			if ( $old_status == 'publish'  &&  $new_status !== 'publish') {
				udinra_sitemap_delete_cache_entry($post);
			}			
			if(get_option('udinra_sitemap_create_freq') != 1) {
				udinra_sitemap_loop();
			}
		}	
	}
}

function udinra_sitemap_event() {
	initSitemap();
	udinra_image_cache_create();
	if(get_option('udinra_sitemap_create_freq') != 0) {
		udinra_sitemap_loop($udinra_sitemap_response);
	}
}

function udinra_image_table_create() {
	global $wpdb;
	$UdinraImageTable = $wpdb->prefix . 'udsiteimg';
	$udinra_charset_collate = $wpdb->get_charset_collate();
	update_option( "udinra_image_db_version", '1.0' );	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$udinra_img_sql = "CREATE TABLE IF NOT EXISTS $UdinraImageTable (
		id          bigint(20) NOT NULL AUTO_INCREMENT,
		post_id     bigint(20) NOT NULL,
		image_type  char(10)   NOT NULL,
		image_url   varchar(1023),
		image_title varchar(1023),
		image_cap   varchar(1023),
		PRIMARY KEY  (id)
		) $udinra_charset_collate;";
   dbDelta( $udinra_img_sql );  
}

function udinra_image_table_delete() {
	global $wpdb;
	$UdinraImageTable = $wpdb->prefix . 'udsiteimg';
	$udinra_img_sql = "DROP TABLE IF EXISTS $UdinraImageTable;";
	$wpdb->query($udinra_img_sql);
}

function udinra_sitemap_delete_cache_entry($post) {
	$post_id = $post>ID;
	global $wpdb;
	$UdinraImageTable = $wpdb->prefix . 'udsiteimg';
	$wpdb->delete( 
		$UdinraImageTable, 
		array( 
			'post_id'     => $post_id 
		) 
	);	
}

function udinra_image_cache_delete() {
	global $wpdb;
	$UdinraImageTable = $wpdb->prefix . 'udsiteimg';
	$delete = $wpdb->query("TRUNCATE TABLE $UdinraImageTable");	
}

?>