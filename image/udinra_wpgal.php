<?php

global $wpdb;
$UdinraImageTable = $wpdb->prefix . 'udsiteimg';
$udinra_sitemap_image_count = get_option('udinra_sitemap_image_count');
$udinra_sitemap_exclude_id = get_option('udinra_sitemap_exclude_id');

$udinra_sitemap_post_str   = get_option( 'udinra_sitemap_post_type'  ) ;
$udinra_sitemap_post_array = explode( ',' , $udinra_sitemap_post_str );
$udinra_sitemap_post_type  = '';
foreach ( $udinra_sitemap_post_array AS $udinra_sitemap_post_element ){
	$udinra_sitemap_post_type .= "'" . $udinra_sitemap_post_element . "'," ;
}
$udinra_sitemap_post_type     = substr( $udinra_sitemap_post_type , 0 , -1) ;    
$udinra_upload_dir = wp_upload_dir();
$udinra_upload_dir_url = $udinra_upload_dir['baseurl'] . '/';
$udinra_image_sitemap_cdn = trim(get_option('udinra_image_sitemap_cdn'));

if ($udinra_image_sitemap_cdn != '') {
	if(stristr($udinra_upload_dir_url , 'http://')) {
		$udinra_image_sitemap_cdn      = $udinra_image_sitemap_cdn . '.' ;
		$udinra_upload_dir_url = substr_replace($udinra_upload_dir_url , $udinra_image_sitemap_cdn ,7 , 0);
	}
	if(stristr($udinra_upload_dir_url , 'https://')) {
		$udinra_image_sitemap_cdn      = $udinra_image_sitemap_cdn . '.' ;
		$udinra_upload_dir_url = substr_replace($udinra_upload_dir_url , $udinra_image_sitemap_cdn ,8 , 0);
	}
}	

$udinra_sql = "SELECT out1.ID,out1.post_content	FROM $wpdb->posts out1 ".
				" LEFT JOIN $UdinraImageTable out2 " .
				" ON out1.id = out2.post_id " .
	 			" WHERE out1.post_status = 'publish' " .
				" AND out1.post_type IN ($udinra_sitemap_post_type) " .
				" AND out1.id NOT IN ($udinra_sitemap_exclude_id) " .			 
                " AND (out2.image_type NOT IN ('WPGAL') OR out2.image_type IS NULL) " .
                " AND out1.post_content LIKE '%[gallery%' " .
                " ORDER BY out1.id,out1.post_date ASC LIMIT $udinra_sitemap_image_count";	
                
$udinra_posts = $wpdb->get_results($udinra_sql);
if(count($udinra_posts) < $udinra_sitemap_image_count){
	update_option('udinra_sitemap_response','Cache created for all the Images.Now Create Sitemap');
}
else{
	update_option('udinra_sitemap_response','Some Images are left. Click on Create Cache button again');
}
foreach ($udinra_posts as $udinra_post) { 
	$post_id = $udinra_post->ID;
	if (preg_match_all ("/ids=[\"](.*)[\" ]/U",$udinra_post->post_content, $udinra_matches_img, PREG_SET_ORDER)) {
		$udinra_image_gallery_list = explode(',' , $udinra_matches_img[0][1]);
		foreach ($udinra_image_gallery_list as $udinra_image_gallery_image) { 
			$udinra_gallery_id = filter_var($udinra_image_gallery_image , FILTER_SANITIZE_NUMBER_INT); 
			$udinra_sql =   "SELECT out1.post_title,out1.post_excerpt,pm.meta_value ".	
							" FROM $wpdb->posts out1 ".
							" INNER JOIN $wpdb->postmeta pm ".
							" ON out1.id = pm.post_id ".					
							" WHERE out1.id = $udinra_gallery_id " .
							" AND pm.meta_key = '_wp_attached_file' " ;

			$udinra_image_detail = $wpdb->get_results($udinra_sql);
			$image_url = htmlspecialchars($udinra_upload_dir_url . $udinra_image_detail[0]->meta_value);
			if ( ctype_space($udinra_image_detail[0]->post_excerpt) || $udinra_image_detail[0]->post_excerpt == '' ) {
				$image_cap = $udinra_image_detail[0]->post_title;
			}
			else {
				$image_cap = $udinra_image_detail[0]->post_excerpt;
			}
			$image_title = $udinra_image_detail[0]->post_title;
			$udinra_alt_text_value = get_post_meta($udinra_gallery_id,'_wp_attachment_image_alt',true);
			if ( ctype_space($udinra_alt_text_value) || $udinra_alt_text_value == '' ) {
				add_post_meta($udinra_gallery_id,'_wp_attachment_image_alt',$udinra_image_detail[0]->post_title,true);
			}

			$image_title = '<![CDATA[' . $image_title . ']]>';
			$image_cap   = '<![CDATA[' . $image_cap   . ']]>';

			$wpdb->insert( 
				$UdinraImageTable, 
				array( 
					'post_id'     => $post_id, 
					'image_type'  => 'WPGAL',
					'image_url'   => $image_url, 
					'image_title' => $image_title, 
					'image_cap'   => $image_cap	
				) 
			);		
		}
	}
}


?>