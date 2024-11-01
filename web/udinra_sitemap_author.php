<?php

global $wpdb;

if ( $udinra_sitemap_multisite == 0) {
	$udinra_sitemap_xml   = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
	$udinra_sitemap_xml  .= '<?xml-stylesheet type="text/xsl" href='.'"'. UDINRA_SITEMAP_FRONT_URL . 'udinra-all-image-sitemap/xsl/xml-sitemap.xsl'. '"'.'?>' . PHP_EOL;
	$udinra_sitemap_xml  .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;        
}

$udinra_sitemap_all_users = get_users('orderby=post_count&order=DESC');
$udinra_sitemap_users = array();

foreach($udinra_sitemap_all_users as $udinra_sitemap_currentUser) {
    if(!in_array( 'subscriber', $udinra_sitemap_currentUser->roles )) {
            $udinra_sitemap_users[] = $udinra_sitemap_currentUser;
    }
}
     
foreach( $udinra_sitemap_users as $udinra_sitemap_user ) {
    $udinra_author_id = $udinra_sitemap_user->ID;

    $udinra_sql =   " SELECT MAX(p.post_modified_gmt) AS lastmod " .
					" FROM	$wpdb->posts AS p " .
					" INNER JOIN $wpdb->users AS u " .
					" ON p.post_author = u.ID " .
					" AND u.ID = $udinra_author_id " .
					" WHERE	p.post_status IN ('publish','inherit') " .
					" AND p.post_password = '' ";
	
	$udinra_update_time = mysql2date('c',$wpdb->get_var($udinra_sql),false);	
    $udinra_sitemap_xml .= "\t"."<url>".PHP_EOL;
	$udinra_sitemap_xml .= "\t\t"."<loc>".htmlspecialchars(get_author_posts_url( $udinra_sitemap_user->ID ))."</loc>".PHP_EOL;
	$udinra_sitemap_xml .= "\t\t"."<lastmod>".$udinra_update_time."</lastmod>".PHP_EOL;
	$udinra_sitemap_xml .= "\t\t"."<priority>"."0.65"."</priority>".PHP_EOL;
    $udinra_sitemap_xml .= "\t"."</url>".PHP_EOL;
}  

if ( $udinra_sitemap_multisite == 0) {	
    $udinra_sitemap_xml .= "</urlset>"; 
    $udinra_sitemap_url = ABSPATH . '/sitemap-author.xml'; 
    
    if (file_put_contents ($udinra_sitemap_url, $udinra_sitemap_xml)) {
        $udinra_tempurl = get_bloginfo('url').'/sitemap-author.xml'; 
        $udinra_index_xml .="\t"."<sitemap>".PHP_EOL."\t\t"."<loc>".htmlspecialchars($udinra_tempurl)."</loc>".PHP_EOL.
                            "\t\t"."<lastmod>".$udinra_date."</lastmod>".PHP_EOL.	"\t"."</sitemap>".PHP_EOL;
    } 	        
}

?>