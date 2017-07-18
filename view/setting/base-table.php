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


function setting_base_table($post_type = null) {
	global $eyeta_biztool;

	if(null != $post_type) {
		$table = $eyeta_biztool->get_table($post_type);
	} else {
		$table = Biztool_Table::factory('simple', $post_type);

	}

	?>
	<h3 class=""><span id="h3-<?php echo $post_type;?>"><?php echo esc_html($table->h3_title);?></span></h3>
		<div>
			<?php
			if($table->is_new) {
				?>
				<form id="frm-setting-base-table-new" name="frm-setting-base-table-new" method="POST" action="<?php echo admin_url('admin-ajax.php');?>" >
					<input type="hidden" name="action" value="eyeta_biztool" />
					<input type="hidden" name="eyeta_biztool_action" value="add_table" />
					<input type="hidden" name="_wpnonce" value="<?php echo $eyeta_biztool->get_nonce();?>" />
				<?php
			} else {
				?>
				<form id="frm-setting-base-table-<?php echo $post_type;?>" name="frm-setting-base-table-<?php echo $post_type;?>" method="POST" action="<?php echo admin_url('admin-ajax.php');?>" >
					<input type="hidden" name="action" value="eyeta_biztool" />
					<input type="hidden" name="eyeta_biztool_action" value="update_table_name" />
					<input type="hidden" name="target_post_type" value="<?php echo esc_attr($table->post_type);?>" />
					<input type="hidden" name="_wpnonce" value="<?php echo $eyeta_biztool->get_nonce();?>" />
				<?php
				}
			?>
					<table class="pure-table pure-table-bordered w100">
						<tr>
							<td class="fbold"><?php echo esc_html__('Posttype name', 'eyeta-biztool');?></td>
							<td><input type="text" name="table_name" id="table-name-<?php echo $post_type;?>" class="w100" value="<?php echo esc_attr($table->table_name);?>" /><br />
							<?php echo esc_html__('Descriptive name of the data , example ) the customer master , orders list , etc.', 'eyeta-biztool');?></td>
						</tr>
						<tr>
							<td class="" colspan="2">
								<?php
								if($table->is_new) {
									?>
									<input type="button" name="btn-add_table" target_post_type="new" class="btn-add_table" value="<?php echo esc_html__('Add Posttype', 'eyeta-biztool');?>" />
									<?php
								} else {
									?>
									<input type="button" name="btn-save_table" class="btn-save_table" target_post_type="<?php echo $post_type;?>" value="<?php echo esc_html__('Save Posttype name', 'eyeta-biztool');?>" />
									<input type="button" name="btn-go_setting_table" class="btn-go_setting_table" target_post_type="<?php echo $post_type;?>" value="<?php echo esc_html__('Setting Posttype', 'eyeta-biztool');?>" />
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

