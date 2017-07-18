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
function ajax_get_listviewcell_form() {

	global $eyeta_biztool;
	eyeta_biztool_log('ajax_get_listviewcell_form start');
	$nonce = $_REQUEST['_wpnonce'];
	if(! $eyeta_biztool->verify_nonce( $nonce ) ) {
		eyeta_biztool_log('ajax_get_listviewcell_form nonce error');
		wp_die('Security Check Error ');
	}

	if($eyeta_biztool->current_user_can('eyeta-biztool/setting/table')) {
		if(isset($_REQUEST['num']) && '' != $_REQUEST['num'] && is_numeric($_REQUEST['num']) && 0 < intval($_REQUEST['num'])) {
			if('' == $_REQUEST['listviewcell_type']) {
				echo "";
			} else {
				$obj_field = Biztool_Listviewcell::factory( $_REQUEST['listviewcell_type'], $_REQUEST['post_type'], $_REQUEST['listview_num'], $_REQUEST['num'] );
				echo $obj_field->get_listviewcell_form();
			}
		} else {
			wp_die(__('Target of the column number is invalid .:', 'eyeta-biztool')  . $_REQUEST['num']);
		}

	}
	die();
}
