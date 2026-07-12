<?php

// ``This function enable option to put PHP code inside Widget Custom HTML 
add_filter('widget_text','execute_php',100);
function execute_php($html){
	if(strpos($html,"<"."?php")!==false){
		ob_start();
		eval("?".">".$html);
		$html=ob_get_contents();
		ob_end_clean();
	}
	return $html;
}	
add_filter('widget_text','do_shortcode',10);
