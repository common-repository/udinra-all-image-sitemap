<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

udinra_uninstall_sitemap_plugin();

function udinra_uninstall_sitemap_plugin () {
    if ( function_exists( 'is_multisite' ) && is_multisite() ) {
        if ( false == is_super_admin() ) {
            return;
        }
        $blogs = wp_get_sites();
        foreach ( $blogs as $blog ) {
            switch_to_blog( $blog[ 'blog_id' ] );
            udinra_delete_sitemap_options();
            restore_current_blog();
        }
    } else {
        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }
		udinra_delete_sitemap_options();
	}
}

function udinra_delete_sitemap_options () {
	delete_option('udinra_sitemap_category');
	delete_option('udinra_sitemap_tag');
	delete_option('udinra_sitemap_author');
	delete_option('udinra_sitemap_url_count');
	delete_option('udinra_sitemap_create_freq');
	delete_option('udinra_sitemap_response');
	delete_option('udinra_image_Sitemap_cdn');
	delete_option('udinra_sitemap_gallery');
	delete_option('udinra_sitemap_slider');
	delete_option('udinra_sitemap_ecommerce');
	delete_option('udinra_sitemap_post_type');
	delete_option('udinra_sitemap_exclude_id');
	delete_option('udinra_sitemap_forum');
	global $wpdb;
	$UdinraImageTable = $wpdb->prefix . 'udsiteimg';
	$udinra_img_sql = "DROP TABLE IF EXISTS $UdinraImageTable;";
	$wpdb->query($udinra_img_sql);		
}

?>