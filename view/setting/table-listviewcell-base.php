<?php
/**
 *
 *
 * Created by PhpStorm.
 * Author: Eyeta Co.,Ltd.(http://www.eyeta.jp)
 *
 */

 
namespace eyeta_biztool;

 
function setting_table_listviewcell_base($post_type, $cell_num, $listview_num) {

	$is_new = true;
	if(is_object($cell_num)) {
		$obj_listviewcell = $cell_num;
		$cell_num = $obj_listviewcell->listviewcell_num;
		$is_new = false;
	} else {
		$obj_listviewcell = Biztool_Listviewcell::factory( '', $post_type, $listview_num, $cell_num );
	}

	?>

	<li class="ui-widget-content ui-corner-all ui-state-default li-listviewcell-base-<?php echo $listview_num;?>-<?php echo $cell_num;?>">
		<input type="hidden" name="listviewcell_order_num-<?php echo $listview_num;?>-<?php echo $cell_num;?>" class="listviewcell_order_num-<?php echo $listview_num;?>" id="listviewcell_order_num-<?php echo $listview_num;?>-<?php echo $cell_num;?>" value="" />

		<h3 class="ui-widget-header ui-corner-all" style="padding: 0.4em;"><a href="#" class="btn-listviewcell-toggle" target-listview-num="<?php echo $listview_num;?>" target-listviewcell-num="<?php echo $cell_num;?>"><span class="icon-toggle-<?php echo $listview_num;?>-<?php echo $cell_num;?> ui-icon ui-icon-triangle-1-e d-inline-block"></span><span class="listviewcell-h3-title-<?php echo $listview_num;?>-<?php echo $cell_num;?>"><?php echo esc_html($obj_listviewcell->listviewcell_name);?></span></a>　<a href="#" class="btn-listviewcell-delete btn-mini"  target-listview-num="<?php echo $listview_num;?>" target-listviewcell-num="<?php echo $cell_num;?>"><?php echo esc_html__('delete', 'eyeta-biztool');?></a></h3>
		<div class="listviewcell-toggle toggle-<?php echo $listview_num;?>-<?php echo $cell_num;?>" id="listviewcell-<?php echo $listview_num;?>-<?php echo $cell_num;?>">
			<table class="pure-table pure-table-bordered w100">
				<tbody class=" listviewcell-form-<?php echo $listview_num;?>-<?php echo $cell_num;?>">
					<tr>
						<td class="fbold w30"><?php echo esc_html__('Column Name', 'eyeta-biztool');?></td>
						<td><input type="text" name="listviewcell_name-<?php echo $listview_num;?>-<?php echo $cell_num;?>" id="listviewcell_name-<?php echo $listview_num;?>-<?php echo $cell_num;?>" class="w100 listviewcell_name" target-listview-num="<?php echo $listview_num;?>" target-listviewcell-num="<?php echo $cell_num;?>" value="<?php echo esc_attr($obj_listviewcell->listviewcell_name);?>" /></td>
					</tr>
					<tr>
						<td class="fbold w30"><?php echo esc_html__('Column Type', 'eyeta-biztool');?></td>
						<td><select class="listviewcell_type" name="listviewcell_type-<?php echo $listview_num;?>-<?php echo $cell_num;?>" target-listview-num="<?php echo $listview_num;?>" target-listviewcell-num="<?php echo $cell_num;?>">
							<option value=""><?php echo esc_html__('Please select', 'eyeta-biztool');?></option>
							<?php
							$array_listviewcells = apply_filters('eyeta_biztool/init/listviewcell_types', array());
							$listviewcell_group = '';
							foreach($array_listviewcells as $listviewcell_type => $array_listviewcell) {
								if($listviewcell_group != $array_listviewcell['group']) {
									?>
									<optgroup label="<?php echo esc_attr($array_listviewcell['group']);?>">
									<?php
									$listviewcell_group = $array_listviewcell['group'];
								}
								if($listviewcell_type == $obj_listviewcell->listviewcell_type) {
									?>
									<option value="<?php echo $listviewcell_type;?>" selected><?php echo esc_html($array_listviewcell['name']);?></option>
									<?php
								} else {
									?>
									<option value="<?php echo  $listviewcell_type;?>" ><?php echo esc_html($array_listviewcell['name']);?></option>
									<?php
								}
							}
							?>

						</select></td>
					</tr>
					<?php
					if(!$is_new) {
						$obj_field = Biztool_Listviewcell::factory( $obj_listviewcell->listviewcell_type, $post_type, $listview_num, $cell_num );
						echo $obj_field->get_listviewcell_form();

					}
					?>

				</tbody>
			</table>

		</div>
	</li>
	<?php
}