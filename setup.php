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
 * 各種セットアップ
 */
function setup() {
	global $eyeta_biztool, $wpalchemy_media_access;
	
	// カスタム投稿タイプ定義
	$tables = $eyeta_biztool->get_option('tables', array());
	foreach($tables as $post_type => $array_table) {

		if(!isset($array_table['table_type'])) $array_table['table_type'] = 'simple';
		$obj_table = Biztool_Table::factory($array_table['table_type'], $post_type);
		$obj_table->set_vars($array_table);
		if(is_wp_error($rsl = $eyeta_biztool->set_table($post_type, $obj_table))) {
			// error
			
		}
	}

	// metabox
	$wpalchemy_media_access = new \WPAlchemy_MediaAccess();
	$array_obj_tables = $eyeta_biztool->get_tables();
	foreach($array_obj_tables as $table_key => $obj_table) {
		if(count($obj_table->fields) > 0) {
			// todo セパレーターにいつか対応したい
			$custom_checkbox_mb = new \WPAlchemy_MetaBox(array(
				'id' => '_' . $table_key,
				'prefix' => '',
				'mode' => WPALCHEMY_MODE_EXTRACT,
				'title' => $obj_table->table_name,
				'template' => $eyeta_biztool->get_plugin_path() . '/view/metabox.php',
				'types' => array($obj_table->post_type)
			));
			// 検索対策フィールド生成
			$custom_checkbox_mb->add_action('save', '\eyeta_biztool\wpalchemy_action_save', 999, 2);

		}
	}
	
	// role名変更
	$array_role_names = $eyeta_biztool->get_option('role_names', array());
	if(count($array_role_names) != 0) {
		global $wp_roles;
		if(!isset($wp_roles)) {
			$wp_roles = new \WP_Roles();
		}
		
		foreach($array_role_names as $role_key => $role_name) {
			$wp_roles -> roles [$role_key]['name'] = $role_name;
			$wp_roles -> role_names [$role_key] = $role_name;
		}
	}
	
	//$role = get_role('biztool_group_1');
	//$role->remove_cap('eyeta-biztool/table/edit_post|eyeta_biztool_1');
	//$role->add_cap('edit_eyeta_biztool_1');
	//eyeta_biztool_log('role: ' . print_r($role->capabilities, true));

	//add_action( 'admin_enqueue_scripts', 'no_heartbeat' );
	
	return true;
}

/**
 * selectbox, checkboxの複数選択を検索実現するための処理
 *
 * @param $new_data
 * @param $real_post_id
 */
function wpalchemy_action_save($new_datas, $real_post_id) {
	foreach($new_datas as $key => $new_data) {
		if(is_array($new_data)) {
			// 配列
			delete_post_meta($real_post_id, $key . '_array');
			foreach($new_data as $single_data) {
				add_post_meta($real_post_id, $key . '_array', $single_data);
			}
		}
	}
}

function no_heartbeat() {
    wp_deregister_script('heartbeat');
}

