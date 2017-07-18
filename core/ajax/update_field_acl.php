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
 * ajax関数：列アクセス権変更
 */
function ajax_update_field_acl() {
	global $eyeta_biztool;
	eyeta_biztool_log('ajax_update_field_acl start');
	$nonce = $_REQUEST['_wpnonce'];
	if(! $eyeta_biztool->verify_nonce( $nonce ) ) {
		eyeta_biztool_log('ajax_update_field_acl nonce error');
		wp_die('Security Check Error ');
	}

	if($eyeta_biztool->current_user_can('eyeta-biztool/setting/table')) {
		if(is_wp_error($rsl = update_field_acl($_REQUEST))) {
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
 * 列処理
 *
 * @param $args
 */
function update_field_acl($args) {
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
		return new \WP_Error('', __('Specified group can not be found . It may have been deleted .', 'eyeta-biztool'));
	}
	$target_cap_show = $eyeta_biztool->get_cap_by_action('eyeta-biztool/table/show_field|' . $args['post_type'] . '.' . $args['field']);
	$target_cap_edit = $eyeta_biztool->get_cap_by_action('eyeta-biztool/table/edit_field|' . $args['post_type'] . '.' . $args['field']);
	if($args['val'] == 'none') {
		// すべて無し
		$target_role->remove_cap($target_cap_show);
		$target_role->remove_cap($target_cap_edit);
	} elseif($args['val'] == 'show') {
		$target_role->add_cap($target_cap_show);
		$target_role->remove_cap($target_cap_edit);
	} elseif($args['val'] == 'edit') {
		$target_role->add_cap($target_cap_show);
		$target_role->add_cap($target_cap_edit);
	}

	
	return true;
}