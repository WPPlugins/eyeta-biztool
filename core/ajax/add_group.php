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
function ajax_add_group() {
	global $eyeta_biztool;
	eyeta_biztool_log('ajax_add_group start');
	$nonce = $_REQUEST['_wpnonce'];
	if(! $eyeta_biztool->verify_nonce( $nonce )  ) {
		eyeta_biztool_log('ajax_add_group nonce error');
		wp_die('Security Check Error ');
	}

	if($eyeta_biztool->current_user_can('eyeta-biztool/setting/base/group')) {
		$table = add_group($_REQUEST);

		echo json_encode(array(
			'rsl' => 'OK',
			'msg' => __('Added role.', 'eyeta-biztool'),
			'data' => array(
				'post_type' => $table->post_type
			)
		));

	}
	wp_die();
}

/**
 * 表追加処理
 *
 * @param $args
 */
function add_group($args) {
	global $eyeta_biztool;
	
	$max_group_num = intval($eyeta_biztool->get_option('max_group_num', 0));
	$max_group_num++;
	$role_key = 'biztool_group_' . $max_group_num;
	$role = add_role( $role_key, $args['group_name'], array() );
	$eyeta_biztool->update_option('max_group_num', $max_group_num);
	
	// 最低限のcap
	$role->add_cap('read');
	
	// 名称
	$array_role_names = $eyeta_biztool->get_option('role_names', array());
	$array_role_names[$role_key] = $args['group_name'];
	$eyeta_biztool->update_option('role_names', $array_role_names);
	
	return $role;
}