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
 * post_typeを利用した表領域
 *
 * Class biztool_table
 * @package eyeta_biztool
 */
class Biztool_Listviewcell_Simple extends Biztool_Listviewcell {

	// todo スマホで表示、横幅指定などに対応する。

	function __construct($post_type = null, $listview_num = null, $cell_num = null) {
		parent::__construct($post_type, $listview_num, $cell_num);

		$this->_vars['listviewcell_type'] = 'simple';
		$this->_vars['sortable'] = true;

		$this->_vars['additional'] = array(
			'target_field' => array( 'type' => 'select', 'default' => '', 'required' => false)
		);

	}



	/**
	 * 一覧表への値を返す
	 *
	 * @param $target_post_id
	 */
	function get_manage_posts_custom_column($target_post_id) {
		global $eyeta_biztool;
		//$html = parent::get_manage_posts_custom_column($target_post_id);

		$field_key = $this->_vars['additional_settings']['target_field'];
		$obj_table = $eyeta_biztool->get_table($this->_vars['post_type']);
		$obj_field = $obj_table->get_obj_field($field_key);
		
		$str_field = $obj_field->get_simple_column($target_post_id);

		return $str_field;
	}


	/**
	 * 設定を保存
	 *
	 * @param $args
	 */
	function save_listviewcell_setting($args) {

		parent::save_listviewcell_setting($args);

		return $this->_vars;
	}



	function get_listviewcell_form($html = "") {
		global $eyeta_biztool;

		$html .= parent::get_listviewcell_form($html);

		ob_start();
		?>
		<tr class="listviewcell-form-detail">
			<td class="fbold"><?php echo esc_html__('Display column', 'eyeta-biztool');?></td>
			<td class=""><select class="target_field" name="target_field-<?php echo $this->_vars['listview_num'];?>-<?php echo $this->_vars['cell_num'];?>">
				<?php
				$obj_tables = $eyeta_biztool->get_table($this->_vars['post_type']);
				$fields = $obj_tables->get_fields();
				foreach( $fields as $field_key => $array_field) {
					$obj_field = $obj_tables->get_obj_field($field_key);
					?>
					<option value="<?php echo $field_key;?>" <?php echo $this->get_additional_values_for_form('target_field', array('value' => $field_key));?> ><?php echo esc_html($obj_field->field_name);?></option>
					<?php
				}
				?></select>
			</td>
		</tr>

		<?php
		$html .= ob_get_contents();
		ob_end_clean();


		return $html;
	}

}