<?php
/**
 *
 *
 * Created by PhpStorm.
 * Author: Eyeta Co.,Ltd.(http://www.eyeta.jp)
 *
 */

 
namespace eyeta_biztool;


function setting_table_field_base($field_num, $post_type = null) {

	$is_new = true;
	if(is_object($field_num)) {
		$field = $field_num;
		$is_new = false;
	} else {
		$field = new Biztool_Field($post_type, $field_num );
	}

	?>
	<li class="ui-widget-content ui-corner-all ui-state-default li-field-base-<?php echo $field->field_num;?>">
		<input type="hidden" name="field_order_num-<?php echo $field->field_num;?>" class="field_order_num field_order_num-<?php echo $field->field_num;?>" id="field_order_num-<?php echo $field->field_num;?>" value="" />

		<h3 class="ui-widget-header ui-corner-all" style="padding: 0.4em;"><a href="#" class="btn-field-toggle" target-field-num="<?php echo $field->field_num;?>"><span class="icon-toggle-<?php echo $field->field_num;?> ui-icon ui-icon-triangle-1-e d-inline-block"></span><span class="field-h3-title-<?php echo $field->field_num;?>"><?php echo esc_html($field->h3_title);?></span></a>　<a href="#" class="btn-field-delete btn-mini" target-field-num="<?php echo $field->field_num;?>"><?php echo esc_html__('delete', 'eyeta-biztool');?></a>　<a href="#" class="btn-field-copy btn-mini" target-field-num="<?php echo $field->field_num;?>"><?php echo esc_html__('duplicate', 'eyeta-biztool');?></a></h3>
		<div class="field-toggle toggle-<?php echo $field->field_num;?>" id="field-<?php echo $field->field_num;?>">
			<table class="pure-table pure-table-bordered w100">
				<tbody class=" field-form-<?php echo $field->field_num;?>">
				<tr>
					<td class="fbold w30"><?php echo esc_html__('Field name', 'eyeta-biztool');?></td>
					<td><input type="text" name="field_name-<?php echo $field->field_num;?>" id="field_name-<?php echo $field->field_num;?>" class="w100 field_name field_name-<?php echo $field->field_num;?>" target-field-num="<?php echo $field->field_num;?>" value="<?php echo esc_attr($field->field_name);?>" /><br />
					データのわかりやすい名称、例）顧客名、会社名など</td>
				</tr>
				<tr>
					<td class="fbold w30"><?php echo esc_html__('Field type', 'eyeta-biztool');?></td>
					<td><select class="field_type field_type-<?php echo $field->field_num;?>" name="field_type-<?php echo $field->field_num;?>" id="field_type-<?php echo $field->field_num;?>" target-field-num="<?php echo $field->field_num;?>">
							<option value=""><?php echo esc_html__('Please select', 'eyeta-biztool');?></option>
							<?php
							$array_fields = apply_filters('eyeta_biztool/init/field_types', array());
							$field_group = '';
							foreach($array_fields as $field_type => $array_field) {
								if($field_group != $array_field['group']) {
									?>
									<optgroup label="<?php echo esc_attr($array_field['group']);?>">
									<?php
									$field_group = $array_field['group'];
								}
								if($field_type == $field->field_type) {
									?>
									<option value="<?php echo $field_type;?>" selected><?php echo esc_html($array_field['name']);?></option>
									<?php
								} else {
									?>
									<option value="<?php echo $field_type;?>" ><?php echo esc_html($array_field['name']);?></option>
									<?php
								}
							}
							?>

						</select></td>
				</tr>
				<?php
				if(!$is_new) {
					$obj_field = Biztool_Field::factory($field->field_type, $post_type, $field->field_num);
					echo $obj_field->get_field_form();

				}
				?>
				</tbody>
			</table>


		</div>
	</li>
	<?php
}