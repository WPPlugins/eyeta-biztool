<?php
/**
 *
 *
 * Created by PhpStorm.
 * Author: Eyeta Co.,Ltd.(http://www.eyeta.jp)
 *
 */

namespace eyeta_biztool;

global $eyeta_biztool;

require_once $eyeta_biztool->get_plugin_path() . '/view/setting/table-field-base.php';
require_once $eyeta_biztool->get_plugin_path() . '/view/setting/table-listview.php';
require_once $eyeta_biztool->get_plugin_path() . '/view/setting/table-listviewcell-base.php';
require_once $eyeta_biztool->get_plugin_path() . '/view/setting/table-listviewfilter-base.php';
require_once $eyeta_biztool->get_plugin_path() . '/view/setting/table-acl.php';

/**
 * テーブル設定画面
 */
function setting_table() {
	global $eyeta_biztool;

	$post_type = $_REQUEST['post_type'];
	if (!isset($_REQUEST['post_type']) || '' == $_REQUEST['post_type']) {
		$post_type = $_REQUEST['target_post_type'];
	}

	$table = $eyeta_biztool->get_table($post_type);
	if (false == $table) {
		eyeta_biztool_log('setting_table: no post type: ' . $post_type, 'ERROR');
		wp_die('post_typeが見つかりません');
	}
	?>
	<div class="wrap">
		<h2>【<?php echo esc_html($table->table_name); ?>】<?php echo esc_html__('Setting', 'eyeta-biztool');?></h2>
		<form id="frm-setting-table" name="frm-setting-table" method="POST" action="<?php echo admin_url('admin-ajax.php'); ?>" onsubmit="return false;" >
			<input type="hidden" name="action" value="eyeta_biztool" />
			<input type="hidden" name="eyeta_biztool_action" value="save_table_setting" />
			<input type="hidden" name="target_post_type" id="target_post_type" value="<?php echo $post_type; ?>" />
			<input type="hidden" name="_wpnonce"  value="<?php echo $eyeta_biztool->get_nonce(); ?>" />
			<div class="tabs" id="setting-base-tabs">
				<ul>
					<li><a href="#setting-table-base"><?php echo esc_html__('Basic Setting', 'eyeta-biztool');?></a></li>
					<li><a href="#setting-table-field"><?php echo esc_html__('Field Setting', 'eyeta-biztool');?></a></li>
					<li><a href="#setting-table-listview"><?php echo esc_html__('Listview Setting', 'eyeta-biztool');?></a></li>
					<li><a href="#setting-table-acl"><?php echo esc_html__('Access right Setting', 'eyeta-biztool');?></a></li>
				</ul>

				<div id="setting-table-base">

					<table class="pure-table pure-table-bordered w100">
						<tr>
							<td class="fbold w30"><?php echo esc_html__('Posttype Type', 'eyeta-biztool');?></td>
							<td>
	<?php
	$table_types = apply_filters('eyeta_biztool/init/table_types', array());
	foreach ($table_types as $table_type_key => $array_table_type) {
		?>
									<input type="radio" checked name="table_type" id="table_type-<?php echo $table_type_key; ?>" class="" value="<?php echo esc_attr($table_type_key); ?>" /><?php echo $array_table_type['name']; ?><br />
									<?php
								}
								?>


							</td>
						</tr>
						<tr>
							<td class="fbold w30"><?php echo esc_html__('Posttype name', 'eyeta-biztool');?></td>
							<td><?php echo $post_type; ?></td>
						</tr>
					</table>

				</div>
				<div id="setting-table-field" class="setting-table-field">
					<p><?php echo esc_html__('You can set the column . Add and edit the column , please to complete the required tables .', 'eyeta-biztool');?></p>
					<ul id="setting-table-field-ul" class="setting-table-field-ul sortable">
	<?php
	foreach ($table->fields as $field) {
		$obj_field = $table->get_obj_field($field['field_num']);
		setting_table_field_base($obj_field, $post_type);
	}
	?>
					</ul>
					<input type="hidden" name="eyeta_biztool_max_field_num" id="eyeta_biztool_max_field_num" value="<?php echo $table->max_field_num; ?>" />
					<input type="button" name="btn-add-field" class="btn-add-field" value="<?php echo esc_html__('Add field', 'eyeta-biztool');?>" />

				</div>
				<div id="setting-table-listview">
					<p><?php echo esc_html__("It may not be displayed all of the columns in the list screen, set a combination of columns to display , let's use a good screen .", 'eyeta-biztool');?></p>
					<ul id="setting-table-listview-ul" class="setting-table-listview-ul sortable">
	<?php
	$max_listview_cnt = 0;
	foreach ($table->listviews as $key => $array_listviewcells) {
		setting_table_listview($post_type, $array_listviewcells['listview_num'], $array_listviewcells);
		if ($max_listview_cnt < $array_listviewcells['listview_num']) {
			$max_listview_cnt = $array_listviewcells['listview_num'];
		}
	}
	?>
					</ul>
					<input type="hidden" name="eyeta_biztool_max_listview_num" id="eyeta_biztool_max_listview_num" value="<?php echo $max_listview_cnt; ?>" />
					<input type="button" name="btn-add-listview" class="btn-add-listview" value="<?php echo esc_html__('Add Listview', 'eyeta-biztool');?>" />
				</div>
				<div id="setting-table-acl">
					<?php
					setting_table_acl($post_type);
					?>
				</div>
			</div>

			<p>
				<input type="button" name="button" class="btn-save_table_setting" value="<?php echo esc_html__('Save', 'eyeta-biztool');?>" />
			</p>

		</form>
	<?php
}
