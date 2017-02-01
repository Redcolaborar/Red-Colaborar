<?php
add_filter("grassblade_shortcode_atts", "grassblade_video_shortcode_atts", 2,2);

function grassblade_video_shortcode_atts($shortcode_atts, $attr) {
	if(!empty($shortcode_atts["video"])) 
	{
		$shortcode_atts["activity_id"] = $shortcode_atts["video"];
		$shortcode_atts["src"] =  plugins_url( 'index.html' , __FILE__ );
		//$shortcode_atts["src"] =  str_replace("imac.com", "192.168.10.105", $shortcode_atts["src"] );
		if(!empty($shortcode_atts["activity_name"]))
			 $shortcode_atts["src"] = $shortcode_atts["src"]."?activity_name=".rawurlencode($shortcode_atts["activity_name"]);
	}
	return $shortcode_atts;
}