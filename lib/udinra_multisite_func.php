<?php

function udinra_sitemap_rewrite($wp_rewrite) {
	$udinra_img_feed_rules = array( 'sitemap-index.xml$' => $wp_rewrite->index . '?feed=sitemap-index' );
	$wp_rewrite->rules = $udinra_img_feed_rules + $wp_rewrite->rules;
}

function udinra_sitemap_act($networkwide) {
    global $wpdb;
    if (function_exists('is_multisite') && is_multisite()) {
        if ($networkwide) {
            $old_blog = $wpdb->blogid;
            $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blogids as $blog_id) {
                switch_to_blog($blog_id);
				add_filter('generate_rewrite_rules', 'udinra_sitemap_rewrite');
				wp_schedule_event( current_time( 'timestamp' ), 'daily', 'udinra_sitemap_event');
				global $wp_rewrite;
				$wp_rewrite->flush_rules();
				update_option('udinra_sitemap_multisite',1);
            }
            switch_to_blog($old_blog);
            return;
        }
		else {
			add_filter('generate_rewrite_rules', 'udinra_sitemap_rewrite');
			wp_schedule_event( current_time( 'timestamp' ), 'daily', 'udinra_sitemap_event');
			global $wp_rewrite;
			$wp_rewrite->flush_rules();
			update_option('udinra_sitemap_multisite',1);
		}
    }
	else {	
		update_option('udinra_sitemap_multisite',0);
		wp_schedule_event( current_time( 'timestamp' ), 'daily', 'udinra_sitemap_event');
	}
}

function udinra_sitemap_deact($networkwide) {
    global $wpdb;
    if (function_exists('is_multisite') && is_multisite()) {
        if ($networkwide) {
            $old_blog = $wpdb->blogid;
            $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blogids as $blog_id) {
                switch_to_blog($blog_id);
				remove_action( 'do_feed_sitemap-index','load_sitemap_index');
				remove_filter('generate_rewrite_rules', 'udinra_sitemap_rewrite');
				wp_clear_scheduled_hook('udinra_sitemap_event');
				remove_action( 'transition_post_status', 'udinra_sitemap_post_unpub', 10, 3 );
				remove_action('admin_menu','udinra_sitemap_admin');	
				remove_action('admin_notices', 'udinra_sitemap_admin_notice');
				remove_action('admin_init', 'udinra_sitemap_admin_ignore');
				remove_action( 'admin_enqueue_scripts', 'udinra_sitemap_admin_style');
				remove_action( 'wpmu_new_blog', 'udinra_sitemap_new_blog', 10, 6);        
				global $wp_rewrite;
				$wp_rewrite->flush_rules();
            }
            switch_to_blog($old_blog);
            return;
        }   
		else {
			remove_action( 'do_feed_sitemap-index','load_sitemap_index');
			remove_filter('generate_rewrite_rules', 'udinra_sitemap_rewrite');
			remove_action( 'transition_post_status', 'udinra_sitemap_post_unpub', 10, 3 );
			remove_action('admin_menu','udinra_sitemap_admin');	
			remove_action('admin_notices', 'udinra_sitemap_admin_notice');
			remove_action('admin_init', 'udinra_sitemap_admin_ignore');
			remove_action( 'admin_enqueue_scripts', 'udinra_sitemap_admin_style');
			remove_action( 'wpmu_new_blog', 'udinra_sitemap_new_blog', 10, 6);        
			wp_clear_scheduled_hook('udinra_sitemap_event');
			global $wp_rewrite;
			$wp_rewrite->flush_rules();
		}
    }
	else {	
		wp_clear_scheduled_hook('udinra_sitemap_event');
		remove_action( 'transition_post_status', 'udinra_sitemap_post_unpub', 10, 3 );
		remove_action( 'admin_enqueue_scripts', 'udinra_sitemap_admin_style');
		remove_action('admin_menu','udinra_sitemap_admin');	
		remove_action('admin_notices', 'udinra_sitemap_admin_notice');
		remove_action('admin_init', 'udinra_sitemap_admin_ignore');
	}
}
 
function udinra_sitemap_new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
    global $wpdb;
    if (is_plugin_active_for_network('udinra-all-image-sitemap/udinra-all-image-sitemap.php')) {
        $old_blog = $wpdb->blogid;
        switch_to_blog($blog_id);
		add_filter('generate_rewrite_rules', 'udinra_sitemap_rewrite');
		wp_schedule_event( current_time( 'timestamp' ), 'daily', 'udinra_sitemap_event');
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
		update_option('udinra_sitemap_multisite',1);
        switch_to_blog($old_blog);
    }
}

?>