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
function ajax_update_group_name() {
	global $eyeta_biztool;
	eyeta_biztool_log('ajax_update_group_name start');
	$nonce = $_REQUEST['_wpnonce'];
	if(! $eyeta_biztool->verify_nonce( $nonce ) ) {
		eyeta_biztool_log('ajax_update_group_name nonce error');
		wp_die('Security Check Error ');
	}

	if($eyeta_biztool->current_user_can('eyeta-biztool/setting/base/group')) {
		if(is_wp_error($rsl = update_group_name($_REQUEST))) {
			echo json_encode(array(
				'rsl' => 'NG',
				'msg' => $rsl->get_error_message()
			));
		} else {
			echo json_encode(array(
				'rsl' => 'OK',
				'msg' => __('You have saved the name of the role.', 'eyeta-biztool')
			));
		}


	}
	wp_die();
}

/**
 * 表追加処理
 *
 * @param $args
 */
function update_group_name($args) {
	global $eyeta_biztool;
	global $wp_roles;
	if(!isset($wp_roles)) {
		$wp_roles = new \WP_Roles();
	}
	
	$role_key = $_REQUEST['target_group_key'];
	$role_name = $_REQUEST['group_name'];
	$array_role_names = $eyeta_biztool->get_option('role_names', array());
	$array_role_names[$role_key] = $args['group_name'];
	$eyeta_biztool->update_option('role_names', $array_role_names);
		
	$wp_roles -> roles[$role_key]['name'] = $role_name;
	$wp_roles -> role_names[$role_key] = $role_name;
	
	return true;
}