<?php

?>
<div class="w3-card-4" style="width:100%;">
	<header class="w3-display-container w3-blue"><h1 class="w3-center">Udinra Sitemap (Configuration)</h1></header>
	<div class="w3-container">
		<button onclick="UdinraSitemapJS('commonSitemap')" class="w3-button w3-block w3-light-blue w3-center w3-border w3-border-black"><b class="w3-center">XML Sitemap Configuration</b></button>
		<div class="w3-hide w3-border" id="commonSitemap" ><form action="" method="post">
			<ul class="w3-ul">
				<li><input <?php if (get_option('udinra_sitemap_category') == 1) echo "checked"; ?> class="w3-check" type="checkbox" id="udscat" value="udscat" name="udscat">
					<label>Create Category Sitemap (Not Recommended if creating tag sitemap)</label>
				</li>
				<li><input <?php if (get_option('udinra_sitemap_tag') == 1) echo "checked"; ?> class="w3-check" type="checkbox" id="udstag" value="udstag" name="udstag">
					<label>Create Tag Sitemap      (Not Recommended if creating category sitemap)</label>
				</li>
				<li><input <?php if (get_option('udinra_sitemap_author') == 1) echo "checked"; ?> class="w3-check" type="checkbox" id="udsauth" value="udsauth" name="udsauth">
					<label>Create Author Sitemap   (Not Recommended for single author sites)</label>
				</li>
				<?php
					foreach ( get_post_types( '', 'names' ) as $post_type ) {
						if( $post_type == 'attachment'          || $post_type == 'revision'         || $post_type == 'custom_css'         ||
							$post_type == 'nav_menu_item'       || $post_type == 'oembed_cache'     || $post_type == 'envira'             ||
							$post_type == 'foogallery'          || $post_type == 'bws-gallery'      || $post_type == 'ngg_gallery'        ||
							$post_type == 'ngg_pictures'        || $post_type == 'lightbox_library' || $post_type == 'dlm_download'       ||    
							$post_type == 'ml-slider'           || $post_type == 'ml-slide'         || $post_type == 'amn_envira-lite'    ||  
							$post_type == 'displayed_gallery'   || $post_type == 'display_type'     || $post_type == 'gal_display_source' ||  
							$post_type == 'customize_changeset' || $post_type == 'ngg_album'        || $post_type == 'dlm_download_version' ||
							$post_type == 'edd_payment'			|| $post_type == 'edd_discount'     || $post_type == 'edd_log'            ||
							$post_type == 'shop_order'			|| $post_type == 'shop_coupon'      || $post_type == 'shop_order_refund'  ||
							$post_type == 'shop_webhook'		|| $post_type == 'product_variation' || $post_type == 'vc4_templates' || $post_type == 'vc_grid_item' ||
							$post_type == 'user_request'		|| $post_type == 'wp_block'          || $post_type == 'elementor_library' 
							) {
						}
						else {
							$udinra_sitemap_post_type = 'udinra_image_sitemap_' . $post_type ;
							echo '<li>'              . '<input class="w3-check" type="checkbox" '  . 
								  udinra_sitemap_check_post_type($post_type)           .
								 ' id=udinra_sitemap_post_type"'                                   . '" '   .
						 	 	 ' name=udinra_sitemap_post_type[]"'                               . '" '   .
							 	 ' value="'           . $post_type                                 . '" >'  .
	  					 	 	 ' <label> Include  ' . $post_type . ' in Sitemap</label>'         .  '</li>';
						}
					}
				?>
				<li><input id="udscount" name="udscount" class="w3-input w3-border" type="text" placeholder="Enter Number of URL per Sitemap (default is 1000, Upper Limit 20,000)" value="<?php echo get_option('udinra_sitemap_url_count'); ?>"></li>
				<li><input id="udsexclude" name="udsexclude" class="w3-input w3-border" type="text" placeholder="Enter ID of post or page to exclude from Web Sitemap (separated by ,)" value="<?php echo get_option('udinra_sitemap_exclude_id'); ?>"></li>
				<li><select id="udsfreq" name="udsfreq" class="UdinraSelect" style="background-color: lightyellow;">
					<option value="dailyno" selected disabled ?> >How frequently Sitemap should be generated?</option>
					<option value="daily"   <?php if (get_option('udinra_sitemap_create_freq') == 1) echo "selected"; ?> >Daily (Best if you have large website)    </option>
					<option value="always"  <?php if (get_option('udinra_sitemap_create_freq') == 2) echo "selected"; ?> >After page or post is changed / published </option>			
					<option value="both"    <?php if (get_option('udinra_sitemap_create_freq') == 3) echo "selected"; ?> >Both of above (default)                   </option>						
				</select></li>
			</ul>
			<input name="udswebsave"   id="udswebsave" value="Save Common Configuration Options"  type="submit" class="w3-button w3-ripple w3-block w3-blue w3-border w3-border-black" />
		</form></div>
		<button onclick="UdinraSitemapJS('imageSitemap')" class="w3-button w3-block w3-light-blue w3-center w3-border w3-border-black"><b class="w3-center">Image Sitemap Configuration</b></button>
		<div class="w3-hide w3-border" id="imageSitemap"><form action="" method="post">
			<ul class="w3-ul">
				<li><select id="udsgal" name="udsgal" class="UdinraSelect" style="background-color: azure;">
					<option value="nogal" selected disabled ?> >Select Gallery plugin you are using</option>
					<option value="gallery" <?php if (get_option('udinra_sitemap_gallery') == 7) echo "selected"; ?> >Default WordPress Gallery</option>					
					<option value="none"    <?php if (get_option('udinra_sitemap_gallery') == 8) echo "selected"; ?> >None</option>
				</select></li>
			</ul>
			<input id="udscdn" name="udscdn" class="w3-input w3-border" type="text" placeholder="Enter CDN Name example cdn" value="<?php echo get_option('udinra_image_Sitemap_cdn'); ?>">						
			<input id="udsimgcount" name="udsimgcount" class="w3-input w3-border" type="text" placeholder="Enter Number of Image to create cache at a time (default 1000)" value="<?php echo get_option('udinra_sitemap_image_count'); ?>">													
			<input name="udsimgsave"   id="udsimgsave" value="Save Image Configuration Options"  type="submit" class="w3-button w3-ripple w3-block w3-blue w3-border w3-border-black" />
		</form></div>
		<button onclick="UdinraSitemapJS('otherPlugins')"  class="w3-button w3-block w3-light-blue w3-center w3-border w3-border-black"><b class="w3-center">Other Plugin Supported</b></button>
		<div class="w3-hide w3-border" id="otherPlugins" ><form action="" method="post">
			<ul class="w3-ul">
				<li><select id="udsforum" name="udsforum" class="UdinraSelect" style="background-color: lightyellow;">
					<option value="no" selected disabled ?> >Select Forum plugin you are using</option>
					<option value="wpforo"  <?php if (get_option('udinra_sitemap_forum') == 1) echo "selected"; ?> >WpForo</option>
					<option value="none"    <?php if (get_option('udinra_sitemap_forum') == 9) echo "selected"; ?> >None</option>						
				</select></li>
			</ul>
			<input name="udsforumsave"   id="udsforumsave" value="Save Plugin Configuration Options"  type="submit" class="w3-button w3-ripple w3-block w3-blue w3-border w3-border-black" />
		</form></div>
		<form action="" method="post">
			<input name="udsimage" id="udsimage"  value="Create Image Cache for Image Sitemap" type="submit" class="w3-button w3-ripple w3-block w3-light-yellow w3-border w3-border-black" />					
			<input name="udsimgdel" id="udsimgdel"  value="Delete Image Cache (Use only if facing issues)" type="submit" class="w3-button w3-ripple w3-block w3-light-yellow w3-border w3-border-black" />								
			<input name="udscreate" id="udscreate"  value="Create Sitemap Manually (After Cache Creation Completes)" type="submit" class="w3-button w3-ripple w3-block w3-yellow w3-border w3-border-black" />			
		</form>
	</div>
	<footer class="w3-display-container w3-blue">
		<p><?php echo "<h3>" . get_option('udinra_sitemap_response') . "</h3>" ; ?></p>	
	</footer>	
</div>
<div class="w3-card-4" style="width:100%;">
	<div class="w3-container">
	<h2 class="w3-center w3-blue">Udinra Sitemap Pro Features</h2>
		<ul class="w3-ul w3-light-blue">
			<li>Get 25% discount on Pro version. Use coupon code FREE25 <a class="w3-large" href="https://udinra.com/downloads/sitemap-pro">Click Here</a></li>
			<li>Pro Plugin supports Web Sitemap, Image Sitemap, Video Sitemap and HTML Sitemap.</li>
			<li>Support for popular Gallery (e.g. NextGen) ,eCommerce (e.g. WooCommerce) and Slider (e.g. Meta Slider) plugins</li>
			<li>Support for WooCommerce Videos, YouTube, Dailymotion and Vimeo</li>
			<li>No need to use multiple Sitemap plugins. Use one for all sitemaps.</li>
		</ul>	
	</div>
</div>
<?php
function udinra_sitemap_check_post_type($post_type){
	if (get_option('udinra_sitemap_post_type')) {
		$temp_post_type = get_option('udinra_sitemap_post_type')  ;
		if (strpos($temp_post_type , $post_type) !== false) {
			return " checked ";
		}
	} 
}
function udinra_sitemap_html_post_type($post_type){
	if (get_option('udinra_html_sitemap_post_type')) {
		$temp_post_type = get_option('udinra_html_sitemap_post_type')  ;
		if (strpos($temp_post_type , $post_type) !== false) {
			return " checked ";
		}
	} 
}

?>