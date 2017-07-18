<?php
/**
 *
 *
 * Created by PhpStorm.
 * Author: Eyeta Co.,Ltd.(http://www.eyeta.jp)
 *
 */

 
namespace eyeta_biztool;


function ajax_get_listviewfilter_base() {

	global $eyeta_biztool;
	eyeta_biztool_log('ajax_get_listviewfilter_base start');
	$nonce = $_REQUEST['_wpnonce'];
	if(! $eyeta_biztool->verify_nonce( $nonce ) ) {
		eyeta_biztool_log('ajax_get_listviewfilter_base nonce error');
		wp_die('Security Check Error ');
	}

	if($eyeta_biztool->current_user_can('eyeta-biztool/setting/table')) {
		if(isset($_REQUEST['num']) && '' != $_REQUEST['num'] && is_numeric($_REQUEST['num']) && 0 < intval($_REQUEST['num'])) {
			require_once $eyeta_biztool->get_plugin_path() . '/view/setting/table-listviewfilter-base.php';
			setting_table_listviewfilter_base($_REQUEST['post_type'], $_REQUEST['num'], $_REQUEST['listview_num']);
		} else {
			wp_die(__('Target of the column number is invalid .:', 'eyeta-biztool')  . $_REQUEST['num']);
		}

	}
	wp_die();
}