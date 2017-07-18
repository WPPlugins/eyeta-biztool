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
 * ajax関数：表アクセス権変更
 */
function ajax_update_table_acl() {
	global $eyeta_biztool;
	eyeta_biztool_log('ajax_update_table_acl start');
	$nonce = $_REQUEST['_wpnonce'];
	if(! $eyeta_biztool->verify_nonce( $nonce ) ) {
		eyeta_biztool_log('ajax_update_table_acl nonce error');
		wp_die('Security Check Error ');
	}

	if($eyeta_biztool->current_user_can('eyeta-biztool/setting/table')) {
		if(is_wp_error($rsl = update_table_acl($_REQUEST))) {
			echo json_encode(array(
				'rsl' => 'NG',
				'msg' => $rsl->get_error_message()
			));
		} else {
			echo json_encode(array(
				'rsl' => 'OK',
				'msg' => ''
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
function update_table_acl($args) {
	global $eyeta_biztool;
	global $wp_roles;
	if(!isset($wp_roles)) {
		$wp_roles = new \WP_Roles();
	}
	
	// 					'post_type': $("#target_post_type").val(),
	//				'role': $(this).attr('target-role'),
	//				'cap': $(this).attr('target-cap'),

	$target_role = get_role($args['role']);
	if(!$target_role) {
		return new \WP_Error('', __('Specified role can not be found . It may have been deleted .', 'eyeta-biztool'));
	}
	$target_caps = $eyeta_biztool->get_cap_by_action($args['cap']);
	
	if(is_array($target_caps)) {
		foreach($target_caps as $tmp_target_cap) {
			if($args['val'] == '1') {
				$target_role->add_cap($tmp_target_cap);
			} else {
				$target_role->remove_cap($tmp_target_cap);
			}
		}
	} else {
		if($args['val'] == '1') {
			$target_role->add_cap($target_caps);
		} else {
			$target_role->remove_cap($target_caps);
		}
	}
	
	return true;
}