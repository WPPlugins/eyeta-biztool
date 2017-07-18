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
class Biztool_Listviewfilter_Checkbox extends Biztool_Listviewfilter {

	protected $_array_target_field_types = array(
		'checkbox'
	);

	function __construct($post_type = null, $listview_num = null, $cell_num = null) {
		parent::__construct($post_type, $listview_num, $cell_num);

		$this->_vars['listviewfilter_type'] = 'checkbox';

		$this->_vars['additional'] = array(
			'filter_target_field' => array( 'type' => 'select', 'default' => '', 'required' => false)
		);

	}

	
	/**
	 * フィルタ処理
	 *
	 * @param $query
	 *
	 */
	function pre_get_posts($query) {
		parent::pre_get_posts($query);

		$target_request_key = 'listviewfilter_' . $this->_vars['listviewfilter_num'];
		if(isset($_REQUEST[$target_request_key]) && '' != $_REQUEST[$target_request_key]) {
			$meta_query = $query->get('meta_query');
			$meta_query[] = array(
				'key'=> $this->_vars['additional_settings']['filter_target_field'] . '_array',
				'value' => $_REQUEST[$target_request_key]
			);
			$query->set('meta_query', $meta_query);
		}
	}
	
	/**
	 * listview別のフィルタ表示
	 *
	 * @return string
	 */
	function get_restrict_manage_posts() {
		global $eyeta_biztool;

		$target_request_key = 'listviewfilter_' . $this->_vars['listviewfilter_num'];


		$obj_table = $eyeta_biztool->get_table($this->post_type);
		$obj_target_field = $obj_table->get_obj_field($target_request_key);
		$array_values = $obj_target_field->get_selections_as_array();
		
		// todo ここから続き
		?>

		<input type="text" name="<?php echo $target_request_key;?>" placeholder="<?php echo esc_attr($this->_vars['listviewfilter_name']);?>" value="<?php echo (isset($_REQUEST[$target_request_key])?esc_attr($_REQUEST[$target_request_key]):'');?>" />

		<?php
		return '';
	}


	/**
	 * 設定を保存
	 *
	 * @param $args
	 */
	function save_listviewfilter_setting($args) {

		parent::save_listviewfilter_setting($args);

		return $this->_vars;
	}



	function get_listviewfilter_form($html = "") {
		global $eyeta_biztool;

		$html .= parent::get_listviewfilter_form($html);

		ob_start();
		?>
		<tr class="listviewcell-form-detail">
			<td class="fbold">対象フィールド</td>
			<td class=""><select class="filter_target_field" name="filter_target_field-<?php echo $this->_vars['listview_num'];?>-<?php echo $this->_vars['listviewfilter_num'];?>">
				<?php
				$obj_tables = $eyeta_biztool->get_table($this->_vars['post_type']);
				$fields = $obj_tables->get_fields();
				foreach( $fields as $field_key => $array_field) {
					$obj_field = $obj_tables->get_obj_field($field_key);
					if(array_search($obj_field->field_type, $this->_array_target_field_types) !== false) {
						?>
						<option
							value="<?php echo $field_key; ?>" <?php echo $this->get_additional_values_for_form( 'filter_target_field', array( 'value' => $field_key ) ); ?> ><?php echo esc_html( $obj_field->field_name ); ?></option>
						<?php
					}
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