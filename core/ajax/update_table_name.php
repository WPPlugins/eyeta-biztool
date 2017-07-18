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
function ajax_update_table_name() {
	global $eyeta_biztool;
	eyeta_biztool_log('ajax_update_table_name start');
	$nonce = $_REQUEST['_wpnonce'];
	if(! $eyeta_biztool->verify_nonce( $nonce ) ) {
		eyeta_biztool_log('ajax_update_table_name nonce error');
		wp_die('Security Check Error ');
	}

	if($eyeta_biztool->current_user_can('eyeta-biztool/setting/base/table')) {
		if(is_wp_error($rsl = update_table_name($_REQUEST))) {
			echo json_encode(array(
				'rsl' => 'NG',
				'msg' => $rsl->get_error_message()
			));
		} else {
			echo json_encode(array(
				'rsl' => 'OK',
				'msg' => __('You have saved the name of the post type .', 'eyeta-biztool')
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
function update_table_name($args) {
	global $eyeta_biztool;

	if(!isset($args['post_type']) || '' == $args['post_type']) {
		$args['post_type'] = $args['target_post_type'];
	}

	if($table = $eyeta_biztool->get_table($args['post_type'])) {
		$table->table_name = $args['table_name'];
		$table->h3_title = $args['table_name'];
		$table->save();

	} else {
		eyeta_biztool_log('update_table_name: no post_type: ' . $args['post_type']);
		return new WP_Error( 'update_table_name', __( "Target of post_type can not be found .", 'eyeta-biztool' ) );
	}
}