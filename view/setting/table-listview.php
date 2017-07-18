<?php
/**
 *
 *
 * Created by PhpStorm.
 * Author: Eyeta Co.,Ltd.(http://www.eyeta.jp)
 *
 */
namespace eyeta_biztool;

 
function setting_table_listview($post_type, $listview_num, $array_listview = array()) {
	global $eyeta_biztool;

	if(array() == $array_listview) {
		$obj_table = $eyeta_biztool->get_table($post_type);
		$array_listview = $obj_table->listviewcells;
	}
	
	
	?>

	<li class="ui-widget-content ui-corner-all ui-state-default li-listview-base-<?php echo $listview_num;?>">
		<input type="hidden" name="listview_order_num-<?php echo $listview_num;?>" target-listview-num="<?php echo $listview_num;?>" class="listview_order_num" id="listview_order_num-<?php echo $listview_num;?>" value="" />

		<h3 class="ui-widget-header ui-corner-all" style="padding: 0.4em;"><a href="#" class="btn-listview-toggle" target-listview-num="<?php echo $listview_num;?>"><span class="icon-toggle-<?php echo $listview_num;?> ui-icon ui-icon-triangle-1-e d-inline-block"></span><span class="listview-h3-title-<?php echo $listview_num;?>"><?php echo esc_html($array_listview['listview_name']);?></span></a>　<a href="#" class="btn-listview-delete btn-mini"  target-listview-num="<?php echo $listview_num;?>"><?php echo esc_html__('delete', 'eyeta-biztool');?></a></h3>
		<div class="listview-toggle toggle-<?php echo $listview_num;?>" id="listview-<?php echo $listview_num;?>">
			<table class="pure-table pure-table-bordered w100">
				<tbody class=" listview-form-<?php echo $listview_num;?>">
					<tr>
						<td class="fbold w30"><?php echo esc_html__('Listview Name', 'eyeta-biztool');?></td>
						<td><input type="text" name="listview_name-<?php echo $listview_num;?>" id="listview_name-<?php echo $listview_num;?>" class="w100 listview_name" target-listview-num="<?php echo $listview_num;?>" value="<?php echo esc_attr($array_listview['listview_name']);?>" /></td>
					</tr>
					<tr>
						<td colspan="2" class="td-listviewcels">
							<p><?php echo esc_html__('Field', 'eyeta-biztool');?></p>
							<ul id="setting-table-listviewcels-ul-<?php echo $listview_num;?>" class="setting-table-listviewcels-ul-<?php echo $listview_num;?> sortable">
<?php
								$max_listviewcell_cnt = 0;
								foreach($array_listview['listviewcells'] as $listviewcell_key => $array_listviewcell) {
									$obj_listviewcell = Biztool_Listviewcell::factory( $array_listviewcell['listviewcell_type'], $post_type, $listview_num, $array_listviewcell['listviewcell_num'] );
									setting_table_listviewcell_base($post_type, $obj_listviewcell, $array_listviewcell['listview_num']);
									if($max_listviewcell_cnt < $array_listviewcell['listviewcell_num']) {
										$max_listviewcell_cnt = $array_listviewcell['listviewcell_num'];
									}
								}
								?>
								</ul>
							<input type="hidden" name="eyeta_biztool_max_listviewcell_num-<?php echo $listview_num;?>" id="eyeta_biztool_max_listviewcell_num-<?php echo $listview_num;?>" value="<?php echo $max_listviewcell_cnt;?>" />
							<p><input type="button" name="btn-add-listviewcell" class="btn-add-listviewcell" target-listview-num="<?php echo $listview_num;?>" value="<?php echo esc_html__('Add Field', 'eyeta-biztool');?>" /></p>
						</td>
					</tr>

					<tr>
						<td colspan="2" class="td-listviewcels">
							<p><?php echo esc_html__('Filter', 'eyeta-biztool');?></p>
							<ul id="setting-table-listviewfilters-ul-<?php echo $listview_num;?>" class="setting-table-listviewfilters-ul-<?php echo $listview_num;?> sortable">
<?php
								$max_listviewfilter_cnt = 0;
								foreach($array_listview['listviewfilters'] as $listviewfilter_key => $array_listviewfilter) {
									$obj_listviewfilter = Biztool_Listviewfilter::factory( $array_listviewfilter['listviewfilter_type'], $post_type, $listview_num, $array_listviewfilter['listviewfilter_num'] );
									setting_table_listviewfilter_base($post_type, $obj_listviewfilter, $array_listviewfilter['listview_num']);
									if($max_listviewfilter_cnt < $array_listviewfilter['listviewfilter_num']) {
										$max_listviewfilter_cnt = $array_listviewfilter['listviewfilter_num'];
									}
								}
								?>
								</ul>
							<input type="hidden" name="eyeta_biztool_max_listviewfilter_num-<?php echo $listview_num;?>" id="eyeta_biztool_max_listviewfilter_num-<?php echo $listview_num;?>" value="<?php echo $max_listviewfilter_cnt;?>" />
							<p><input type="button" name="btn-add-listviewcell" class="btn-add-listviewfilter" target-listview-num="<?php echo $listview_num;?>" value="<?php echo esc_html__('Add Filter', 'eyeta-biztool');?>" /></p>
						</td>
					</tr>
				</tbody>
			</table>

		</div>
	</li>
	<?php
}