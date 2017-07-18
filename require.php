<?php
/**
 *
 *
 * Created by PhpStorm.
 * Author: Eyeta Co.,Ltd.(http://www.eyeta.jp)
 *
 */

 
namespace eyeta_biztool;

function require_phps() {

	$files = apply_filters('eyeta_biztool/init/require', array());
	foreach($files as $file) {
		require_once $file;
	}


}