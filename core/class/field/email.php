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
class Biztool_Field_Email extends Biztool_Field {


	function __construct($post_type = null, $field_num = null) {
		parent::__construct($post_type, $field_num);
		// インライン編集を許可
		$this->can_edit_inline = true;

		$this->_vars['field_type'] = 'email';

		$this->_vars['additional'] = array(
			'field_note' => array( 'type' => 'textarea', 'default' => '', 'required' => false),
			'is_required' => array( 'type' => 'checkbox', 'default' => '', 'required' => false),
			'default_value' => array( 'type' => 'text', 'default' => '', 'required' => false),
			'placeholder' => array( 'type' => 'text', 'default' => '', 'required' => false)
		);

	}
	
	/**
	 *  表内編集用フィールド
	 * @param type $target_post_id
	 * @return string
	 */
	function get_inlineedit_column($target_post_id) {
		$str_html = parent::get_inlineedit_column($target_post_id);

		$str_field = get_post_meta($target_post_id, $this->_vars['field_key'], true);
		$str_html .= '<input type="email" org="' . esc_attr($str_field)
			. '" name="' . esc_attr($this->_vars['field_key']) . '-'
			. $target_post_id . '" id="' . esc_attr($this->_vars['field_key']) . '-'
			. $target_post_id . '" class="diff-check no-form w100" value="'
			. esc_attr($str_field) . '" target-post_id="'
			. $target_post_id . '" target-field-key="' . esc_attr($this->_vars['field_key']) . '" />';

		// js
		$this->wp_add_inline_script_vars_for_list($this->_vars['post_type'], $target_post_id);

		return $str_html;

	}

	/**
	 * 一覧画面用のJSに書き出す配列を生成
	 *
	 * @return mixed
	 */
	function wp_add_inline_script_vars_for_list($post_type, $target_post_id) {
		parent::wp_add_inline_script_vars_for_list($post_type, $target_post_id);
		global $eyeta_biztool;
		$obj_table = $eyeta_biztool->get_table($post_type);

		$array_rule = array();
		if($this->_vars['additional_settings']['is_required']) {
			$array_rule['required'] = true;
		}
		$array_rule['email'] = true;
		if($array_rule) {
			$obj_table->array_js_vars['validate']['rules'][$this->_vars['field_key'] . '-' . $target_post_id] = $array_rule;
		}

		return ;
	}
	/**
	 * JSに書き出す配列を生成
	 *
	 * @return mixed
	 */
	function wp_add_inline_script_vars($post_type, $array_js_vars = array()) {
		$array_js_vars = parent::wp_add_inline_script_vars($post_type, $array_js_vars);

		$array_rule = array();
		if($this->_vars['additional_settings']['is_required']) {
			$array_rule['required'] = true;
		}
		$array_rule['email'] = true;
		if($array_rule) {
			$array_js_vars['validate']['rules']['_' . $post_type . '[' . $this->_vars['field_key'] . ']'] = $array_rule;
		}


		return $array_js_vars;
	}


	/**
	 * フィールドの設定を保存
	 *
	 * @param $args
	 */
	function save_field_setting($args) {
		
		parent::save_field_setting($args);

		return $this->_vars;
	}

	/**
	 * 入力画面用フォーム
	 * 
	 * @param string $html]
	 */
	function get_field_edit_form($mb, $html = '') {
		?>
		<p>
			<?php
		$mb->the_field($this->field_key);
		$html = parent::get_field_edit_form($mb, $html);
		?>
		<label><?php echo esc_html($this->field_name);?></label>
		<input type="email" name="<?php $mb->the_name(); ?>" id="<?php echo esc_attr($this->_vars['field_key']); ?>" placeholder="<?php echo esc_attr($this->_vars['additional_settings']['placeholder']);?>" value="<?php if($mb->have_value()) { $mb->the_value(); } else { echo esc_attr($this->_vars['additional_settings']['default_value']); } ?>" />
		<span><?php echo esc_html($this->field_note);?></span>
		</p>
		<?php
		
		return $html;
	}
	
	/**
	 * 単純な列出力のためのテキスト
	 *
	 * @param $target_post_id
	 *
	 * @return mixed
	 */
	function get_simple_column($target_post_id) {

		$str_field = esc_html(get_post_meta($target_post_id, $this->_vars['field_key'], true));

		return $str_field;
	}

	/**
	 * フィールド独自の設定項目のフォームを返す。
	 *
	 */
	function get_field_form($html = '') {

		$html .= parent::get_field_form($html);

		ob_start();
		?>
		<tr class="field-form-detail">
			<td class="fbold"><?php echo esc_html__('Description', 'eyeta-biztool');?><br /><span class="description"><?php echo esc_html__('It will be displayed on the post screen', 'eyeta-biztool');?></span></td>
			<td class=""><textarea class="w100" name="field_note-<?php echo $this->field_num;?>"><?php echo esc_html($this->get_additional_values_for_form('field_note'));?></textarea></td>
		</tr>
		<tr class="field-form-detail">
			<td class="fbold"><?php echo esc_html__('Required', 'eyeta-biztool');?></td>
			<td class=""><input type="checkbox" name="is_required-<?php echo $this->field_num;?>" value="1" <?php echo $this->get_additional_values_for_form('is_required', array('value' => 1));?> /><?php echo esc_html__('Required', 'eyeta-biztool');?></td>
		</tr>
		<tr class="field-form-detail">
			<td class="fbold"><?php echo esc_html__('Default value', 'eyeta-biztool');?><br /><span class="description"><?php echo esc_html__('It will be displayed when creating a new', 'eyeta-biztool');?></span></td>
			<td class=""><input type="text" class="w100" name="default_value-<?php echo $this->field_num;?>" value="<?php echo esc_attr($this->get_additional_values_for_form('default_value'));?>" /></td>
		</tr>
		<tr class="field-form-detail">
			<td class="fbold"><?php echo esc_html__('Text placeholders', 'eyeta-biztool');?><br /><span class="description"><?php echo esc_html__('It will be displayed in the input field', 'eyeta-biztool');?></span></td>
			<td class=""><input type="text" class="w100" name="placeholder-<?php echo $this->field_num;?>" value="<?php echo esc_attr($this->get_additional_values_for_form('placeholder'));?>" /></td>
		</tr>


		<?php
		$html .= ob_get_contents();
		ob_end_clean();

		
		return $html;
	}

}