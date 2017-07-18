<?php
/**
 *
 *
 * Created by PhpStorm.
 * Author: Eyeta Co.,Ltd.(http://www.eyeta.jp)
 *
 */

namespace eyeta_biztool;

function setting_table_acl($post_type) {
	global $eyeta_biztool;
	$table = $eyeta_biztool->get_table($post_type);
	
	?>

	<p>この表に対する各種アクセス権を設定出来ます。</p>
	<div class="accordion" id="setting-table-acls-accordion" active_num="0">
		<h3 class="">表基本アクセス権</h3>
		<div>
			<table class="pure-table pure-table-bordered">
				<thead>
					<tr>
						<th class="fbold w30"> </th>
						<?php
						table_acl_role_ths();
						?>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="fbold w30"><?php echo esc_html__('Could Show Posttype List', 'eyeta-biztool');?></td>
						<?php
						table_acl_role_inputs('eyeta-biztool/table/show_posts|' . $post_type);
						?>
					</tr>
					<tr>
						<td class="fbold w30"><?php echo esc_html__('Could Edit Posttype post', 'eyeta-biztool');?></td>
						<?php
						table_acl_role_inputs('eyeta-biztool/table/edit_post|' . $post_type);
						?>
					</tr>
					<tr>
						<td class="fbold w30"><?php echo esc_html__('Could Add New Posttype post', 'eyeta-biztool');?></td>
						<?php
						table_acl_role_inputs('eyeta-biztool/table/add_post|' . $post_type);
						?>
					</tr>
					<tr>
						<td class="fbold w30"><?php echo esc_html__('Could Delete New Posttype post', 'eyeta-biztool');?></td>
						<?php
						table_acl_role_inputs('eyeta-biztool/table/delete_post|' . $post_type);
						?>
					</tr>

				</tbody>
			</table>
		</div>
		<!--<h3 class="">列アクセス権</h3>
		<div>
			<?php
			// todo 列別アクセス権
			
			?>
			<table class="pure-table pure-table-bordered">
				<thead>
					<tr>
						<th class="fbold w30">フィールド名</th>
						<?php
						table_acl_role_ths();
						?>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ($table->fields as $field) {
					?>
					<tr>
						<td class="fbold w30"><?php echo esc_html($field['field_name']);?></td>
					<?php
					$obj_field = $table->get_obj_field($field['field_num']);
					table_acl_role_field_select($post_type, $obj_field->field_key);
					?>
						
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
			
				
		</div>
		<h3 class="">一覧表示アクセス権</h3>
		<div>
		</div>-->



	</div>


	<?php
}

/**
 * 対象の権限のセレクトボックス
 * WordPressオリジナルのアクセス権考慮不要
 * アクセス権の配列考慮不要
 */
function table_acl_role_field_select($post_type, $field_key) {
	global $eyeta_biztool;
	$target_cap_show = $eyeta_biztool->get_cap_by_action('eyeta-biztool/table/show_field|' . $post_type . '.' . $field_key);
	$target_cap_edit = $eyeta_biztool->get_cap_by_action('eyeta-biztool/table/edit_field|' . $post_type . '.' . $field_key);
	$array_roles = wp_roles()->roles;
	foreach ($array_roles as $role_key => $array_role) {
		//print_r($array_role['capabilities']);
		if ('administrator' == $role_key) {
			continue;
		}
		
		// 権限チェック
		$cap = 'none';
		if($eyeta_biztool->current_user_can($target_cap_show)) {
			$cap = 'show';
			if($eyeta_biztool->current_user_can($target_cap_edit)) {
				$cap = 'edit';
			}
		}
		?>
	<td><select name="<?php echo esc_attr($role_key); ?>-<?php echo esc_attr($field_key); ?>" class="table_acl_role_select" target-role="<?php echo esc_attr($role_key); ?>" target-field="<?php echo esc_attr($field_key); ?>" >
		<option value="none" <?php if('none' == $cap) { echo "selected"; } ?> >権限無し</option>
		<option value="show" <?php if('show' == $cap) { echo "selected"; } ?> >表示</option>
		<option value="edit" <?php if('edit' == $cap) { echo "selected"; } ?> >編集</option>
		</select></td>
		<?php
	}
}

function table_acl_role_ths() {
	$array_roles = wp_roles()->roles;
	foreach ($array_roles as $role_key => $array_role) {
		if ('administrator' == $role_key) {
			continue;
		}
		?>
		<th><?php echo esc_html($array_role['name']); ?></th>
		<?php
	}
}

function table_acl_role_inputs($target_cap) {
	global $eyeta_biztool;
	$target_caps = $eyeta_biztool->get_cap_by_action($target_cap);
	$array_roles = wp_roles()->roles;
	foreach ($array_roles as $role_key => $array_role) {
		//print_r($array_role['capabilities']);
		if ('administrator' == $role_key) {
			continue;
		}
		$checked = "";
		if(is_array($target_caps)) {
			$checked = true;
			foreach($target_caps as $tmp_target_cap) {
				if (!isset($array_role['capabilities'][$tmp_target_cap]) || !$array_role['capabilities'][$tmp_target_cap]) {
					$checked = false;
				}
			}
			if($checked) {
				$checked = " checked ";
			}
		} else {
			if (isset($array_role['capabilities'][$target_caps]) && $array_role['capabilities'][$target_caps]) {
				$checked = " checked ";
			}
		}
		?>
		<td><input type="checkbox" class="table_acl_role_input" target-role="<?php echo esc_attr($role_key); ?>" target-cap="<?php echo esc_attr($target_cap); ?>" <?php echo $checked; ?> value="1" /></td>
		<?php
	}
}
