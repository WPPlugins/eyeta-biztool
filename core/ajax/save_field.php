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
function ajax_save_field() {
	
	global $eyeta_biztool;
	eyeta_biztool_log('ajax_save_field start');
	$nonce = $_REQUEST['_wpnonce'];
	if(! $eyeta_biztool->verify_nonce( $nonce ) ) {
		eyeta_biztool_log('ajax_save_field nonce error');
		wp_die('Security Check Error ');
	}
	
	
	$args = $_REQUEST;
	if(!isset($args['target_post_id']) || '' == $args['target_post_id']) {
		echo json_encode(array('rsl' => 'NG', 'msg' => __('System error , does not have to post is specified .', 'eyeta-biztool')));
		die;
	}
	$target_post = get_post(intval($args['target_post_id']));
	if(!$target_post) {
		echo json_encode(array('rsl' => 'NG', 'msg' => __('The specified post can not be found . It might have been deleted .', 'eyeta-biztool')));
		die;
	}
	
	// todo いずれフィールド単位のアクセス権実装
	if(current_user_can('edit_' . $target_post->post_type . 's')) {
		
		$obj_table = $eyeta_biztool->get_table($target_post->post_type);
		$obj_field = $obj_table->get_obj_field($args['target_field_key']);
		
		if(is_wp_error($obj_field)) {
			echo json_encode(array(
				'rsl' => 'NG',
				'msg' => $obj_field->get_error_message()
			));

		} else {
			// 保存
			if(is_wp_error($rsl = $obj_field->save_field_value(intval($args['target_post_id']), $args['new_value'], $args))) {
				echo json_encode(array(
					'rsl' => 'NG',
					'msg' => $rsl->get_error_message()
				));
			} else {
				echo json_encode(array(
					'rsl' => 'OK',
					'msg' => __('saved.', 'eyeta-biztool')
				));
				
			}
			

		}
	} else {
		echo json_encode(array(
			'rsl' => 'NG',
			'msg' =>  __('Do not have permission.', 'eyeta-biztool')
		));
	}
	die();
}


/**
 * テーブル設定を保存
 * @param $args
 */
function save_table_setting($args) {
	global $eyeta_biztool;
	
	// 対象のテーブルクラスを取得
	if(!isset($args['post_type']) || '' == $args['post_type']) {
		if(!isset($args['target_post_type']) || '' == $args['target_post_type']) {
			return new WP_Error('save_table_setting', __('Post type has not been specified .', 'eyeta-biztool'));
		}
		$args['post_type'] = $args['target_post_type'];
	}
	$obj_table = $eyeta_biztool->get_table($args['post_type']);
	if(!table) {
		return new WP_Error('save_table_setting', __('There is no table definition of post type: ', 'eyeta-biztool') . $args['post_type']);
	}
	
	// 基本設定情報を保存
	$obj_table = $obj_table->save_basic_setting($args);
	if(null == $obj_table) {
		return new WP_Error('save_table_setting', __('There is no table definition of post type: ', 'eyeta-biztool') . $args['post_type']);
	}

	// フィールドのパラメーター保存
	$obj_table->save_fields_setting($args);

	// 表示列の保存
	$obj_table->save_listviews_setting($args);

	return $obj_table;
}
