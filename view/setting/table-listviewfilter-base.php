<?php
/**
 *
 *
 * Created by PhpStorm.
 * Author: Eyeta Co.,Ltd.(http://www.eyeta.jp)
 *
 */

 
namespace eyeta_biztool;

 
function setting_table_listviewfilter_base($post_type, $filter_num, $listview_num) {

	$is_new = true;
	if(is_object($filter_num)) {
		$obj_listviewfilter = $filter_num;
		$filter_num = $obj_listviewfilter->listviewfilter_num;
		$is_new = false;
	} else {
		$obj_listviewfilter = Biztool_Listviewfilter::factory( '', $post_type, $listview_num, $filter_num );
	}

	?>

	<li class="ui-widget-content ui-corner-all ui-state-default li-listviewfilter-base-<?php echo $listview_num;?>-<?php echo $filter_num;?>">
		<input type="hidden" name="listviewfilter_order_num-<?php echo $listview_num;?>-<?php echo $filter_num;?>" class="listviewfilter_order_num-<?php echo $listview_num;?>" id="listviewfilter_order_num-<?php echo $listview_num;?>-<?php echo $filter_num;?>" value="" />

		<h3 class="ui-widget-header ui-corner-all" style="padding: 0.4em;"><a href="#" class="btn-listviewfilter-toggle" target-listview-num="<?php echo $listview_num;?>" target-listviewfilter-num="<?php echo $filter_num;?>"><span class="icon-toggle-<?php echo $listview_num;?>-<?php echo $filter_num;?> ui-icon ui-icon-triangle-1-e d-inline-block"></span><span class="listviewfilter-h3-title-<?php echo $listview_num;?>-<?php echo $filter_num;?>"><?php echo esc_html($obj_listviewfilter->listviewfilter_name);?></span></a>　<a href="#" class="btn-listviewfilter-delete btn-mini"  target-listview-num="<?php echo $listview_num;?>" target-listviewfilter-num="<?php echo $filter_num;?>"><?php echo esc_html__('delete', 'eyeta-biztool');?></a></h3>
		<div class="listviewfilter-toggle toggle-filter-<?php echo $listview_num;?>-<?php echo $filter_num;?>" id="listviewfilter-<?php echo $listview_num;?>-<?php echo $filter_num;?>">
			<table class="pure-table pure-table-bordered w100">
				<tbody class=" listviewfilter-form-<?php echo $listview_num;?>-<?php echo $filter_num;?>">
					<tr>
						<td class="fbold w30"><?php echo esc_html__('Placeholder', 'eyeta-biztool');?></td>
						<td><input type="text" name="listviewfilter_name-<?php echo $listview_num;?>-<?php echo $filter_num;?>" id="listviewfilter_name-<?php echo $listview_num;?>-<?php echo $filter_num;?>" class="w100 listviewfilter_name" target-listview-num="<?php echo $listview_num;?>" target-listviewfilter-num="<?php echo $filter_num;?>" value="<?php echo esc_attr($obj_listviewfilter->listviewfilter_name);?>" /></td>
					</tr>
					<tr>
						<td class="fbold w30"><?php echo esc_html__('Filter type', 'eyeta-biztool');?></td>
						<td><select class="listviewfilter_type" name="listviewfilter_type-<?php echo $listview_num;?>-<?php echo $filter_num;?>" target-listview-num="<?php echo $listview_num;?>" target-listviewfilter-num="<?php echo $filter_num;?>">
							<option value=""><?php echo esc_html__('Please select', 'eyeta-biztool');?></option>
							<?php
							$array_listviewfilters = apply_filters('eyeta_biztool/init/listviewfilter_types', array());
							$listviewfilter_group = '';
							foreach($array_listviewfilters as $listviewfilter_type => $array_listviewfilter) {
								if($listviewfilter_group != $array_listviewfilter['group']) {
									?>
									<optgroup label="<?php echo esc_attr($array_listviewfilter['group']);?>">
									<?php
									$listviewfilter_group = $array_listviewfilter['group'];
								}
								if($listviewfilter_type == $obj_listviewfilter->listviewfilter_type) {
									?>
									<option value="<?php echo $listviewfilter_type;?>" selected><?php echo esc_html($array_listviewfilter['name']);?></option>
									<?php
								} else {
									?>
									<option value="<?php echo  $listviewfilter_type;?>" ><?php echo esc_html($array_listviewfilter['name']);?></option>
									<?php
								}
							}
							?>

						</select></td>
					</tr>
					<?php
					if(!$is_new) {
						$obj_filter = Biztool_Listviewfilter::factory( $obj_listviewfilter->listviewfilter_type, $post_type, $listview_num, $filter_num );
						echo $obj_filter->get_listviewfilter_form();

					}
					?>

				</tbody>
			</table>

		</div>
	</li>
	<?php
}