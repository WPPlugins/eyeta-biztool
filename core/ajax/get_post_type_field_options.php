<?php
/**
 *
 *
 * Created by PhpStorm.
 * Author: Eyeta Co.,Ltd.(http://www.eyeta.jp)
 *
 */

 


namespace eyeta_biztool;


/**
 * ajax関数：表追加
 */
function ajax_get_post_type_field_options() {

	global $eyeta_biztool;
	eyeta_biztool_log('ajax_get_post_type_field_options start');
	$nonce = $_REQUEST['_wpnonce'];
	if(! $eyeta_biztool->verify_nonce( $nonce ) ) {
		eyeta_biztool_log('ajax_get_post_type_field_options nonce error');
		wp_die('Security Check Error ');
	}

	if($eyeta_biztool->current_user_can('eyeta-biztool/setting/table')) {
		echo Biztool_Field_Post::get_post_type_field_options($_REQUEST['post_type']);
	}
	die();
}

