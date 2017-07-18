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
function ajax_add_table() {
	global $eyeta_biztool;
	eyeta_biztool_log('ajax_add_table start');
	$nonce = $_REQUEST['_wpnonce'];
	if(! $eyeta_biztool->verify_nonce( $nonce )  ) {
		eyeta_biztool_log('ajax_add_table nonce error');
		wp_die('Security Check Error ');
	}

	if($eyeta_biztool->current_user_can('eyeta-biztool/setting/base/table')) {
		$table = add_table($_REQUEST);

		echo json_encode(array(
			'rsl' => 'OK',
			'msg' => __('Added post type . To start the details of the setting of post type .', 'eyeta-biztool'),
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
function add_table($args) {
	global $eyeta_biztool;
	
	$tables = $eyeta_biztool->get_option('tables');
	$table_cnt = $eyeta_biztool->get_option('table_cnt', 0);
	$table_cnt++;
	$post_type = Biztool_Table::get_post_type_from_cnt($table_cnt);
	$tables[$post_type] = array(
		'post_type' => $post_type,
		'table_name' => $args['table_name'],
		'h3_title' => $args['table_name'],
		'table_type' => 'simple'
	);
	
	$eyeta_biztool->update_option('table_cnt', $table_cnt);
	$eyeta_biztool->update_option('tables', $tables);

	$obj_table = Biztool_Table::factory('simple', $post_type);
	$eyeta_biztool->set_table($post_type, $obj_table);

	// admin向けアクセス権セット
	$role = get_role( 'administrator' );
	$obj_table->add_all_cap($role);

	
	return $obj_table;
}