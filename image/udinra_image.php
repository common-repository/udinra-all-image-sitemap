<?php

global $wpdb;
$udinra_sitemap_siteurl = get_home_url();
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

$udinra_sql = "SELECT out1.ID,out1.post_content FROM $wpdb->posts out1 ".
				" LEFT JOIN $UdinraImageTable out2 " .
				" ON out1.id = out2.post_id " .
	 			" WHERE out1.post_status = 'publish' " .
				" AND out1.post_type IN ($udinra_sitemap_post_type) " .
				" AND out1.id NOT IN ($udinra_sitemap_exclude_id) " .			 
//				" AND out1.post_content LIKE '%<img%' " . 
				" AND (out2.image_type NOT IN ('IMAGE') OR out2.image_type IS NULL) " .			
                " ORDER BY out1.id,out1.post_date ASC LIMIT $udinra_sitemap_image_count";	

$udinra_posts = $wpdb->get_results($udinra_sql);
if(count($udinra_posts) < $udinra_sitemap_image_count){
	update_option('udinra_sitemap_response','Cache created for all the Images.Now Create Sitemap');
}
else{
	update_option('udinra_sitemap_response','Some Images are left. Click on Create Cache button again');
}
foreach ($udinra_posts as $udinra_post) { 
	$udinra_image_found = 0;
	if (preg_match_all ("/<img(.*?)[>]/ui",$udinra_post->post_content, $udinra_matches, PREG_SET_ORDER)) {
		for ( $udinra_i = 0; $udinra_i < count($udinra_matches); $udinra_i++) {
			$udinra_sitemap_flag = 1;
			$udinra_ret_code  = preg_match_all ("/src=[\"](.*?)[\"]/ui"  ,$udinra_matches[$udinra_i][0], $udinra_matches_src,  PREG_SET_ORDER);
			$udinra_matches_src[0][1] = preg_replace( "/-\d+x\d+/", "", $udinra_matches_src[0][1] );
			if ($udinra_image_sitemap_cdn != '' || ! ctype_space($udinra_image_sitemap_cdn)) {
				if(stristr($udinra_matches_src[0][1] , 'http://')) {
					$image_url = substr_replace($udinra_matches_src[0][1] , $udinra_image_sitemap_cdn ,7 , 0);
				}
				if(stristr($udinra_matches_src[0][1] , 'https://')) {
					$image_url = substr_replace($udinra_matches_src[0][1] , $udinra_image_sitemap_cdn ,8 , 0);
				}
			}
			if(stripos($udinra_matches_src[0][1],$udinra_sitemap_siteurl) === FALSE){
				$udinra_sitemap_flag = 0;
			}

			if (preg_match_all ("/alt=[\"](.*?)[\"]/ui"  ,$udinra_matches[$udinra_i][0], $udinra_matches_alt,  PREG_SET_ORDER)) {
				$image_cap   = $udinra_matches_alt[0][1];
			}
			else {
				$udinra_ret_code  = strrpos($udinra_matches_src[0][1] ,'/'); 
				$udinra_ret_code1 = strrpos($udinra_matches_src[0][1] ,'.'); 
				$udinra_file_name = substr($udinra_matches_src[0][1],$udinra_ret_code + 1 , $udinra_ret_code1 - $udinra_ret_code - 1);			
				$image_cap = $udinra_file_name;
				add_post_meta(get_post_thumbnail_id($udinra_post->ID),'_wp_attachment_image_alt',$udinra_file_name,true);
			}

			if (preg_match_all ("/title=[\"](.*?)[\"]/ui",$udinra_matches[$udinra_i][0], $udinra_matches_title,PREG_SET_ORDER)) {
				$image_title = $udinra_matches_title[0][1];
			}
			else {
				$image_title = $image_cap;
			}

           	$image_url   = htmlspecialchars(trim($udinra_matches_src[0][1]));
			$post_id     = $udinra_post->ID;
			$image_title = '<![CDATA[' . $image_title . ']]>';
			$image_cap   = '<![CDATA[' . $image_cap . ']]>';

			if($udinra_sitemap_flag == 1) {
				$wpdb->insert( 
					$UdinraImageTable, 
					array( 
						'post_id'     => $post_id, 
						'image_type'  => 'IMAGE',
						'image_url'   => $image_url, 
						'image_title' => $image_title, 
						'image_cap'   => $image_cap	
					) 
				);
			}
		}
		$udinra_post_thumbnail_url = get_the_post_thumbnail_url($udinra_post->ID);
		$udinra_post_thumbnail_url = preg_replace( "/-\d+x\d+/", "", $udinra_post_thumbnail_url );
		if ($udinra_image_sitemap_cdn != '' || ! ctype_space($udinra_image_sitemap_cdn)) {
			if(stristr($udinra_post_thumbnail_url , 'http://')) {
				$udinra_post_thumbnail_url = substr_replace($udinra_post_thumbnail_url , $udinra_image_sitemap_cdn ,7 , 0);
			}
			if(stristr($udinra_post_thumbnail_url , 'https://')) {
				$udinra_post_thumbnail_url = substr_replace($udinra_post_thumbnail_url , $udinra_image_sitemap_cdn ,8 , 0);
			}
		}	
		if($udinra_post_thumbnail_url){
			$udinra_post_thumbnail_alt = get_post_meta( get_post_thumbnail_id($udinra_post->ID), '_wp_attachment_image_alt', true );
			if ( ctype_space($udinra_post_thumbnail_alt) || $udinra_post_thumbnail_alt == '' ) {	
				$udinra_ret_code1 = strrpos($udinra_post_thumbnail_url ,'.');
				$udinra_ret_code  = strrpos($udinra_post_thumbnail_url ,'/'); 
				$udinra_file_name = substr($udinra_post_thumbnail_url,$udinra_ret_code + 1 , $udinra_ret_code1 - $udinra_ret_code - 1);
				$udinra_post_thumbnail_alt = $udinra_file_name;
				add_post_meta(get_post_thumbnail_id($udinra_post->ID),'_wp_attachment_image_alt',$udinra_post_thumbnail_alt,true);
			}
			$image_url   = htmlspecialchars(trim($udinra_post_thumbnail_url));
			$image_title = $udinra_post_thumbnail_alt;
			$image_cap   = $image_title;
			$post_id     = $udinra_post->ID;   
			$image_title = '<![CDATA[' . $image_title . ']]>';
			$image_cap   = '<![CDATA[' . $image_cap . ']]>';

			$wpdb->insert( 
				$UdinraImageTable, 
				array( 
					'post_id'     => $post_id, 
					'image_type'  => 'IMAGE',
					'image_url'   => $image_url, 
					'image_title' => $image_title, 
					'image_cap'   => $image_cap	
				) 
			);
		}
		$udinra_image_found = 1;
    }    
    if($udinra_image_found == 0) {
		$udinra_post_thumbnail_url = get_the_post_thumbnail_url($udinra_post->ID);
		$udinra_post_thumbnail_url = preg_replace( "/-\d+x\d+/", "", $udinra_post_thumbnail_url );
		if ($udinra_image_sitemap_cdn != '' || ! ctype_space($udinra_image_sitemap_cdn)) {
			if(stristr($udinra_post_thumbnail_url , 'http://')) {
				$udinra_post_thumbnail_url = substr_replace($udinra_post_thumbnail_url , $udinra_image_sitemap_cdn ,7 , 0);
			}
			if(stristr($udinra_post_thumbnail_url , 'https://')) {
				$udinra_post_thumbnail_url = substr_replace($udinra_post_thumbnail_url , $udinra_image_sitemap_cdn ,8 , 0);
			}
		}			
        if($udinra_post_thumbnail_url){
            $udinra_post_thumbnail_alt = get_post_meta( get_post_thumbnail_id($udinra_post->ID), '_wp_attachment_image_alt', true );
            if ( ctype_space($udinra_post_thumbnail_alt) || $udinra_post_thumbnail_alt == '' ) {
                $udinra_ret_code1 = strrpos($udinra_post_thumbnail_url ,'.');
                $udinra_ret_code = strrpos($udinra_post_thumbnail_url ,'/'); 
                $udinra_file_name = substr($udinra_post_thumbnail_url,$udinra_ret_code + 1 , $udinra_ret_code1 - $udinra_ret_code - 1);
                $udinra_post_thumbnail_alt = $udinra_file_name;
            }
			$image_url   = htmlspecialchars(trim($udinra_post_thumbnail_url));
			$image_title = $udinra_post_thumbnail_alt;
			$image_cap   = $image_title;
			$post_id     = $udinra_post->ID;
			$image_title = '<![CDATA[' . $image_title . ']]>';
			$image_cap   = '<![CDATA[' . $image_cap . ']]>';

			$wpdb->insert( 
				$UdinraImageTable, 
				array( 
					'post_id'     => $post_id, 
					'image_type'  => 'IMAGE',
					'image_url'   => $image_url, 
					'image_title' => $image_title, 
					'image_cap'   => $image_cap	
				) 
			);
        }
    }    
}

?>