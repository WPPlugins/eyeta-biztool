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


function setting_base_group($group_key = 'new', $array_role = array()) {
	global $eyeta_biztool;

	if('new' != $group_key) {
		//$role = get_role($group_key);
	} else {
		$array_role['name'] = __('Create New', 'eyeta-biztool');
	}

	?>
	<h3 class=""><span id="h3-<?php echo $group_key;?>"><?php echo esc_html($array_role['name']);?></span></h3>
	<div>
		<?php
		if('new' == $group_key) {
			?>
			<form id="frm-setting-base-group-new" name="frm-setting-base-group-new" method="POST" action="<?php echo admin_url('admin-ajax.php');?>" >
				<input type="hidden" name="action" value="eyeta_biztool" />
				<input type="hidden" name="eyeta_biztool_action" value="add_group" />
				<input type="hidden" name="_wpnonce" value="<?php echo $eyeta_biztool->get_nonce();?>" />
			<?php
		} else {
			?>
			<form id="frm-setting-base-group-<?php echo $group_key;?>" name="frm-setting-base-group-<?php echo $group_key;?>" method="POST" action="<?php echo admin_url('admin-ajax.php');?>" >
				<input type="hidden" name="action" value="eyeta_biztool" />
				<input type="hidden" name="eyeta_biztool_action" value="update_group_name" />
				<input type="hidden" name="target_group_key" value="<?php echo esc_attr($group_key);?>" />
				<input type="hidden" name="_wpnonce" value="<?php echo $eyeta_biztool->get_nonce();?>" />
			<?php
			}
		?>
				<table class="pure-table pure-table-bordered w100">
					<tr>
						<td class="fbold"><?php echo esc_html__('Role name', 'eyeta-biztool');?></td>
						<?php
						if('new' == $group_key) {
							?>
						<td><input type="text" name="group_name" id="group-name-<?php echo $group_key;?>" class="w100" value="" /><br />
						<?php
						} else {
							?>
						<td><input type="text" name="group_name" id="group-name-<?php echo $group_key;?>" class="w100" value="<?php echo esc_attr($array_role['name']);?>" /><br />
						<?php
						}
						?>
						担当者の名前 「管理者」「入力担当」など</td>
					</tr>
					<tr>
						<td class="" colspan="2">
							<?php
							if('new' == $group_key) {
								?>
								<input type="button" name="btn-add_group" target_post_type="new" class="btn-add_group" value="グループを追加" />
								<?php
							} else {
								?>
								<input type="button" name="btn-save_group" class="btn-save_group" target_group="<?php echo $group_key;?>" value="グループの名称を保存" />
								<?php
							}
								?>
						</td>
					</tr>
				</table>
			</form>


	</div>

	<?php
}

