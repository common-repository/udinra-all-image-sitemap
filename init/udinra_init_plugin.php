<?php

$udinra_sitemap_pluginurl = plugins_url();
if ( !preg_match( '/^https/', $udinra_sitemap_pluginurl ) && !preg_match( '/^https/', get_bloginfo('url') ) )
	$udinra_sitemap_pluginurl = preg_replace( '/^https/', 'http', $udinra_sitemap_pluginurl );

define( 'UDINRA_SITEMAP_FRONT_URL', $udinra_sitemap_pluginurl.'/' );

global $wpdb;

$UdinraImageTable = $wpdb->prefix . 'udsiteimg';
$UdinraVideoTable = $wpdb->prefix . 'udsitevid';

$udinra_index_xml   = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
$udinra_index_xml  .= '<?xml-stylesheet type="text/xsl" href='.'"'. UDINRA_SITEMAP_FRONT_URL . 'udinra-all-image-sitemap/xsl/xml-index-sitemap.xsl'. '"'.'?>' .PHP_EOL;
$udinra_index_xml  .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
$udinra_sitemap_xml = '';

$udinra_index_sitemap_url = ABSPATH . '/sitemap-index.xml'; 
$udinra_date = Date(DATE_W3C);
$udinra_sitemap_url_counter = 0;

$udinra_sitemap_length = get_option('udinra_sitemap_url_count');
$udinra_sitemap_multisite = get_option('udinra_sitemap_multisite');
$udinra_sitemap_exclude_id = get_option('udinra_sitemap_exclude_id');

$udinra_sitemap_post_str   = get_option( 'udinra_sitemap_post_type'  ) ;
$udinra_sitemap_post_array = explode( ',' , $udinra_sitemap_post_str );

if ($udinra_sitemap_multisite == 0) {
	$udinra_sitemap_xml   = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
	$udinra_sitemap_xml  .= '<?xml-stylesheet type="text/xsl" href='.'"'. UDINRA_SITEMAP_FRONT_URL . 'udinra-all-image-sitemap/xsl/xml-sitemap.xsl'. '"'.'?>' . PHP_EOL;
	$udinra_sitemap_xml  .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">' . PHP_EOL;	
}

foreach ( $udinra_sitemap_post_array AS $udinra_sitemap_post_element ){
	$udinra_sql = "SELECT out1.ID	FROM $wpdb->posts out1 ".
		 		  " WHERE out1.post_status = 'publish' " .
				  " AND out1.post_type IN ('$udinra_sitemap_post_element') " .
				  " AND out1.id NOT IN ($udinra_sitemap_exclude_id) " .			 
                  " ORDER BY out1.post_date DESC";	
	
	$udinra_posts = $wpdb->get_results($udinra_sql);
	$udinra_sitemap_counter = 0;
  	foreach ($udinra_posts as $udinra_post) { 
		$udinra_sitemap_xml .= "\t"."<url>".PHP_EOL;
		$udinra_sitemap_xml .= "\t\t"."<loc>".htmlspecialchars(get_permalink($udinra_post->ID))."</loc>".PHP_EOL;
		$udinra_sitemap_xml .= "\t\t"."<lastmod>".get_post_modified_time('c',false,$udinra_post->ID)."</lastmod>".PHP_EOL;
		if ( $udinra_sitemap_post_element == 'page') {
			$udinra_sitemap_xml .= "\t\t"."<priority>"."0.8"."</priority>".PHP_EOL;
		}
		elseif ($udinra_sitemap_post_element == 'post') {
			$udinra_sitemap_xml .= "\t\t"."<priority>"."0.6"."</priority>".PHP_EOL;
		}
		elseif ($udinra_sitemap_post_element == 'product' or  $udinra_sitemap_post_element == 'download' or  $udinra_sitemap_post_element =='wpsc-product') {
			$udinra_sitemap_xml .= "\t\t"."<priority>"."0.75"."</priority>".PHP_EOL;
		}
		else {
			$udinra_sitemap_xml .= "\t\t"."<priority>"."0.70"."</priority>".PHP_EOL;
		}
		$udinra_img_sql =   "SELECT distinct(image_url) , image_title , image_cap " .
							" FROM $UdinraImageTable WHERE post_id = $udinra_post->ID GROUP BY image_url";
		$udinra_images = $wpdb->get_results($udinra_img_sql);
		foreach ($udinra_images as $udinra_image) { 
			if(empty($udinra_image->image_url) || (stripos($udinra_image->image_url,'.jpg') == FALSE &&  strpos($udinra_image->image_url,'.png') == FALSE)){

			}
			else {
				$udinra_sitemap_xml .= "\t\t"."<image:image>".PHP_EOL;	
				$udinra_sitemap_xml .= "\t\t\t"."<image:loc>". $udinra_image->image_url ."</image:loc>".PHP_EOL;
				$udinra_sitemap_xml .= "\t\t\t"."<image:caption>". $udinra_image->image_cap ."</image:caption>".PHP_EOL;
				$udinra_sitemap_xml .= "\t\t\t"."<image:title>". $udinra_image->image_title ."</image:title>".PHP_EOL;
				$udinra_sitemap_xml .= "\t\t"."</image:image>".PHP_EOL;	
			}
		}
		
		$udinra_sitemap_xml .= "\t</url>\n"; 
	  	$udinra_sitemap_url_counter = $udinra_sitemap_url_counter + 1;
	  	if($udinra_sitemap_url_counter == $udinra_sitemap_length && $udinra_sitemap_multisite == 0) {
			$udinra_sitemap_url_counter = 0;
			$udinra_sitemap_counter = $udinra_sitemap_counter + 1;
			$udinra_sitemap_xml .= "</urlset>"; 
			$udinra_sitemap_url = ABSPATH . '/sitemap-'. $udinra_sitemap_post_element . '-' . $udinra_sitemap_counter .'.xml'; 
			if (file_put_contents ($udinra_sitemap_url, $udinra_sitemap_xml)) {
				$udinra_tempurl = get_bloginfo('url').'/sitemap-'. $udinra_sitemap_post_element . '-' . $udinra_sitemap_counter .'.xml'; 
				$udinra_index_xml .="\t"."<sitemap>".PHP_EOL."\t\t"."<loc>".htmlspecialchars($udinra_tempurl)."</loc>".PHP_EOL.
									  "\t\t"."<lastmod>".$udinra_date."</lastmod>".PHP_EOL.	"\t"."</sitemap>".PHP_EOL;
				$udinra_sitemap_xml   = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
				$udinra_sitemap_xml  .= '<?xml-stylesheet type="text/xsl" href='.'"'. UDINRA_SITEMAP_FRONT_URL . 'udinra-all-image-sitemap/xsl/xml-sitemap.xsl'. '"'.'?>' . PHP_EOL;
				$udinra_sitemap_xml  .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">' . PHP_EOL;				  
			}	
	  	}
	}
	if($udinra_sitemap_url_counter > 0 && $udinra_sitemap_multisite == 0){
		$udinra_sitemap_url_counter = 0;
		$udinra_sitemap_counter = $udinra_sitemap_counter + 1;
		$udinra_sitemap_xml .= "</urlset>"; 
		$udinra_sitemap_url = ABSPATH . '/sitemap-'. $udinra_sitemap_post_element . '-' . $udinra_sitemap_counter .'.xml'; 
		if (file_put_contents ($udinra_sitemap_url, $udinra_sitemap_xml)) {
			$udinra_tempurl = get_bloginfo('url').'/sitemap-'. $udinra_sitemap_post_element . '-' . $udinra_sitemap_counter .'.xml'; 
			$udinra_index_xml .="\t"."<sitemap>".PHP_EOL."\t\t"."<loc>".htmlspecialchars($udinra_tempurl)."</loc>".PHP_EOL.
								  "\t\t"."<lastmod>".$udinra_date."</lastmod>".PHP_EOL.	"\t"."</sitemap>".PHP_EOL;
			$udinra_sitemap_xml   = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
			$udinra_sitemap_xml  .= '<?xml-stylesheet type="text/xsl" href='.'"'. UDINRA_SITEMAP_FRONT_URL . 'udinra-all-image-sitemap/xsl/xml-sitemap.xsl'. '"'.'?>' . PHP_EOL;
		    $udinra_sitemap_xml  .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">' . PHP_EOL;				  														
		} 				
	}		  
}
if (get_option('udinra_sitemap_forum') == 1) {
	$udinra_sitemap_wpforo_base = get_option('wpforo_url');
	$UdinraTableTopics = $wpdb->prefix . 'wpforo_topics';
	$UdinraTableForums = $wpdb->prefix . 'wpforo_forums';

	$udinra_sql =   "SELECT out2.slug AS base ,out1.slug ,out1.modified FROM $UdinraTableTopics out1 ".
					" INNER JOIN $UdinraTableForums out2 " .
					" ON out1.forumid = out2.forumid " .
					" WHERE out1.status = 0 " .
					" AND out1.private  = 0 " ;

	$udinra_posts = $wpdb->get_results($udinra_sql);

	$udinra_sitemap_xml   = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
	$udinra_sitemap_xml  .= '<?xml-stylesheet type="text/xsl" href='.'"'. UDINRA_SITEMAP_FRONT_URL . 'udinra-all-image-sitemap/xsl/xml-sitemap.xsl'. '"'.'?>' . PHP_EOL;
	$udinra_sitemap_xml  .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">' . PHP_EOL;				  
	$udinra_sitemap_counter = 0;
	$udinra_sitemap_url_counter = 0;

	foreach ($udinra_posts as $udinra_post) { 
		$udinra_sitemap_xml .= "\t"."<url>".PHP_EOL;
		$udinra_sitemap_forum_url = $udinra_sitemap_wpforo_base . $udinra_post->base . '/'  . $udinra_post->slug;
		$udinra_sitemap_xml .= "\t\t"."<loc>".htmlspecialchars($udinra_sitemap_forum_url)."</loc>".PHP_EOL;
		$udinra_sitemap_xml .= "\t\t"."<lastmod>".date('c',$udinra_post->modified)."</lastmod>".PHP_EOL;
		$udinra_sitemap_xml .= "\t\t"."<priority>"."0.65"."</priority>".PHP_EOL;
		$udinra_sitemap_xml .= "\t</url>\n"; 
		$udinra_sitemap_url_counter = $udinra_sitemap_url_counter + 1;
	}
	if($udinra_sitemap_url_counter > 0 && $udinra_sitemap_multisite == 0){
		$udinra_sitemap_url_counter = 0;
		$udinra_sitemap_counter = $udinra_sitemap_counter + 1;
		$udinra_sitemap_xml .= "</urlset>"; 
		$udinra_sitemap_url = ABSPATH . '/sitemap-'. 'topic' . '-' . $udinra_sitemap_counter .'.xml'; 
		if (file_put_contents ($udinra_sitemap_url, $udinra_sitemap_xml)) {
			$udinra_tempurl = get_bloginfo('url').'/sitemap-'. 'topic' . '-' . $udinra_sitemap_counter .'.xml'; 
			$udinra_index_xml .=   "\t"."<sitemap>".PHP_EOL."\t\t"."<loc>".htmlspecialchars($udinra_tempurl)."</loc>".PHP_EOL.
								   "\t\t"."<lastmod>".$udinra_date."</lastmod>".PHP_EOL.	"\t"."</sitemap>".PHP_EOL;
			$udinra_sitemap_xml   = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
			$udinra_sitemap_xml  .= '<?xml-stylesheet type="text/xsl" href='.'"'. UDINRA_SITEMAP_FRONT_URL . 'udinra-all-image-sitemap/xsl/xml-sitemap.xsl'. '"'.'?>' . PHP_EOL;
			$udinra_sitemap_xml  .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">' . PHP_EOL;				  														
		} 				
	}
}

?>