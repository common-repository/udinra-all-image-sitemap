<?php

status_header( '200' ); 
header( 'Content-Type: text/xml; charset=' . get_bloginfo( 'charset' ), true );

$udinra_sitemap_pluginurl = plugins_url();
if ( !preg_match( '/^https/', $udinra_sitemap_pluginurl ) && !preg_match( '/^https/', get_bloginfo('url') ) )
	$udinra_sitemap_pluginurl = preg_replace( '/^https/', 'http', $udinra_sitemap_pluginurl );

define( 'UDINRA_SITEMAP_XSL_URL', $udinra_sitemap_pluginurl.'/' );

echo '<?xml version="1.0" encoding="' . get_bloginfo( 'charset' ) . '"?>' . 
	 '<?xml-stylesheet type="text/xsl" href='.'"'. UDINRA_SITEMAP_XSL_URL . 'udinra-all-image-sitemap/xsl/xml-sitemap.xsl'. '"'.'?>' .
	 '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">' . PHP_EOL;

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
	echo $udinra_sitemap_xml;


?>