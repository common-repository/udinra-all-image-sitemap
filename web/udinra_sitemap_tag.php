<?php

global $wpdb;

if ( $udinra_sitemap_multisite == 0) {
	$udinra_sitemap_xml   = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
	$udinra_sitemap_xml  .= '<?xml-stylesheet type="text/xsl" href='.'"'. UDINRA_SITEMAP_FRONT_URL . 'udinra-all-image-sitemap/xsl/xml-sitemap.xsl'. '"'.'?>' . PHP_EOL;
	$udinra_sitemap_xml  .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;        
}

$udinra_sql = "SELECT 1 AS t FROM $wpdb->term_taxonomy WHERE taxonomy = 'post_tag' LIMIT 1";
$udinra_sitemap_product_cat = $wpdb->get_var($udinra_sql);

if($udinra_sitemap_product_cat == 1) {

	$udinra_sitemap_tag_list = get_tags( array( 'orderby' => 'name' , 'order'   => 'ASC' ) );

	foreach( $udinra_sitemap_tag_list as $udinra_sitemap_tag ) {
		$udinra_tag_id = $udinra_sitemap_tag->term_id;
		$udinra_sql =   " SELECT MAX(p.post_modified_gmt) AS lastmod " .
						" FROM	$wpdb->posts AS p " .
						" INNER JOIN $wpdb->term_relationships AS term_rel " .
						" ON term_rel.object_id = p.ID " .
						" INNER JOIN $wpdb->term_taxonomy AS term_tax " .
						" ON term_tax.term_taxonomy_id = term_rel.term_taxonomy_id " .
						" AND term_tax.term_id = $udinra_tag_id " .
						" WHERE	p.post_status IN ('publish','inherit') " .
						" AND p.post_password = '' ";
	
		$udinra_update_time = mysql2date('c',$wpdb->get_var($udinra_sql),false);
	
    	$udinra_sitemap_xml .= "\t"."<url>".PHP_EOL;
		$udinra_sitemap_xml .= "\t\t"."<loc>".htmlspecialchars(get_tag_link( $udinra_sitemap_tag->term_id ))."</loc>".PHP_EOL;
		$udinra_sitemap_xml .= "\t\t"."<lastmod>".$udinra_update_time."</lastmod>".PHP_EOL;
		$udinra_sitemap_xml .= "\t\t"."<priority>"."0.65"."</priority>".PHP_EOL;
    	$udinra_sitemap_xml .= "\t"."</url>".PHP_EOL;
	}     
}

$udinra_sql = "SELECT 1 AS t FROM $wpdb->term_taxonomy WHERE taxonomy = 'product_tag' LIMIT 1";
$udinra_sitemap_product_cat = $wpdb->get_var($udinra_sql);
if($udinra_sitemap_product_cat == 1){
	$udinra_sitemap_tag_list = get_terms( array( 'taxonomy' => 'product_tag' , 'hide_empty'   => false ) );
 
	foreach( $udinra_sitemap_tag_list as $udinra_sitemap_tag ) {
		$udinra_tag_id = $udinra_sitemap_tag->term_id;
		$udinra_sql =   " SELECT MAX(p.post_modified_gmt) AS lastmod " .
						" FROM	$wpdb->posts AS p " .
						" INNER JOIN $wpdb->term_relationships AS term_rel " .
						" ON term_rel.object_id = p.ID " .
						" INNER JOIN $wpdb->term_taxonomy AS term_tax " .
						" ON term_tax.term_taxonomy_id = term_rel.term_taxonomy_id " .
						" AND term_tax.term_id = $udinra_tag_id " .
						" WHERE	p.post_status IN ('publish','inherit') " .
						" AND p.post_password = '' ";
	
		$udinra_update_time = mysql2date('c',$wpdb->get_var($udinra_sql),false);
	
    	$udinra_sitemap_xml .= "\t"."<url>".PHP_EOL;
		$udinra_sitemap_xml .= "\t\t"."<loc>".htmlspecialchars(get_tag_link( $udinra_sitemap_tag->term_id ))."</loc>".PHP_EOL;
		$udinra_sitemap_xml .= "\t\t"."<lastmod>".$udinra_update_time."</lastmod>".PHP_EOL;
		$udinra_sitemap_xml .= "\t\t"."<priority>"."0.65"."</priority>".PHP_EOL;
    	$udinra_sitemap_xml .= "\t"."</url>".PHP_EOL;
	}     
}

$udinra_sql = "SELECT 1 AS t FROM $wpdb->term_taxonomy WHERE taxonomy = 'download_tag' LIMIT 1";
$udinra_sitemap_product_cat = $wpdb->get_var($udinra_sql);
if($udinra_sitemap_product_cat == 1) {
	$udinra_sitemap_tag_list = get_terms( array( 'taxonomy' => 'download_tag' , 'hide_empty'   => false ) );
 
	foreach( $udinra_sitemap_tag_list as $udinra_sitemap_tag ) {
		$udinra_tag_id = $udinra_sitemap_tag->term_id;
		$udinra_sql =   " SELECT MAX(p.post_modified_gmt) AS lastmod " .
						" FROM	$wpdb->posts AS p " .
						" INNER JOIN $wpdb->term_relationships AS term_rel " .
						" ON term_rel.object_id = p.ID " .
						" INNER JOIN $wpdb->term_taxonomy AS term_tax " .
						" ON term_tax.term_taxonomy_id = term_rel.term_taxonomy_id " .
						" AND term_tax.term_id = $udinra_tag_id " .
						" WHERE	p.post_status IN ('publish','inherit') " .
						" AND p.post_password = '' ";
	
		$udinra_update_time = mysql2date('c',$wpdb->get_var($udinra_sql),false);
	
    	$udinra_sitemap_xml .= "\t"."<url>".PHP_EOL;
		$udinra_sitemap_xml .= "\t\t"."<loc>".htmlspecialchars(get_tag_link( $udinra_sitemap_tag->term_id ))."</loc>".PHP_EOL;
		$udinra_sitemap_xml .= "\t\t"."<lastmod>".$udinra_update_time."</lastmod>".PHP_EOL;
		$udinra_sitemap_xml .= "\t\t"."<priority>"."0.65"."</priority>".PHP_EOL;
    	$udinra_sitemap_xml .= "\t"."</url>".PHP_EOL;
	}   
}
if ( $udinra_sitemap_multisite == 0) {
	$udinra_sitemap_xml .= "</urlset>"; 
	$udinra_sitemap_url = ABSPATH . '/sitemap-tag.xml'; 
    
	if (file_put_contents ($udinra_sitemap_url, $udinra_sitemap_xml)) {
		$udinra_tempurl = get_bloginfo('url').'/sitemap-tag.xml'; 
		$udinra_index_xml .= "\t"."<sitemap>".PHP_EOL."\t\t"."<loc>".htmlspecialchars($udinra_tempurl)."</loc>".PHP_EOL.
							 "\t\t"."<lastmod>".$udinra_date."</lastmod>".PHP_EOL.	"\t"."</sitemap>".PHP_EOL;
	} 	        
}

?>