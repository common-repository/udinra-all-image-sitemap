<?php

if(isset($_POST['udswebsave'])){
	if(isset($_POST['udscat'])){
		update_option( 'udinra_sitemap_category'    , 1 );
	}	
	else{
		update_option( 'udinra_sitemap_category'    , 0 );
	}
	if(isset($_POST['udstag'])){
		update_option( 'udinra_sitemap_tag'    , 1 );
	}	
	else{
		update_option( 'udinra_sitemap_tag'    , 0 );
	}
	if(isset($_POST['udsauth'])){
		update_option( 'udinra_sitemap_author'   , 1 );
	}	
	else{
		update_option( 'udinra_sitemap_author'   , 0 );
	}
	if(isset($_POST['udinra_sitemap_post_type'])){
		update_option('udinra_sitemap_post_type' , implode("," , $_POST['udinra_sitemap_post_type']));
	}
	else {
		update_option('udinra_sitemap_post_type' , 'post,page');
	}
	if(isset($_POST['udsexclude']) && trim($_POST['udsexclude']) != ''){
		update_option('udinra_sitemap_exclude_id' , $_POST['udsexclude']);
	}
	else {
		update_option('udinra_sitemap_exclude_id' , '0');
	}
	
	if(isset($_POST['udscount']) && trim($_POST['udscount']) != '') {
		update_option('udinra_sitemap_url_count' , $_POST['udscount']  );
	}
	else {
		update_option('udinra_sitemap_url_count' , 1000  );
	}
	switch ($_POST['udsfreq']) {
		case "dailyno":
			update_option( 'udinra_sitemap_create_freq' , 0 );
			break;
		case "daily":
			update_option( 'udinra_sitemap_create_freq' , 1 );
			break;
		case "always":
			update_option( 'udinra_sitemap_create_freq' , 2 );
			break;
		default:
			update_option( 'udinra_sitemap_create_freq' , 3 );
	}
	udinra_image_table_create();
	update_option('udinra_sitemap_response','Common Options Saved Successfully');
}

if(isset($_POST['udsimgsave'])){
	if (isset($_POST['udscdn']) && trim($_POST['udscdn']) != '') {
		update_option('udinra_image_Sitemap_cdn',$_POST['udscdn']);
	}
	else {
		update_option('udinra_image_sitemap_cdn',' ');
	}
	switch ($_POST['udsgal']) {
		case "nogal":
			update_option('udinra_sitemap_gallery',0);
			break;
		case "gallery":
			update_option('udinra_sitemap_gallery',7);
			break;
		default:
			update_option('udinra_sitemap_gallery',8);
	}
	
	switch ($_POST['udscom']) {
		case "noecom":
			update_option('udinra_sitemap_ecommerce',0);
			break;
		case "woo":
			update_option('udinra_sitemap_ecommerce',1);
			break;
		case "edd":
			update_option('udinra_sitemap_ecommerce',2);
			break;
		default:
			update_option('udinra_sitemap_ecommerce',4);
	}
	
	if (isset($_POST['udsimgcount']) && trim($_POST['udsimgcount']) != '') {
		update_option('udinra_sitemap_image_count',$_POST['udsimgcount']);
	}
	else {
		update_option('udinra_sitemap_image_count',1000);
	}	
	update_option('udinra_sitemap_response','Image Options Saved Successfully');	
}

if(isset($_POST['udsforumsave'])){
	switch ($_POST['udsforum']) {
		case "no":
			update_option( 'udinra_sitemap_forum' , 0 );
			break;
		case "wpforo":
			update_option( 'udinra_sitemap_forum' , 1 );
			break;
		default:
			update_option( 'udinra_sitemap_forum' , 9 );
	}
	update_option('udinra_sitemap_response','Plugin Options Saved Successfully');
}


?>