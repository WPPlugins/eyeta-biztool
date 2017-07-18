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
class Biztool_Field_Truefalse extends Biztool_Field {


	function __construct($post_type = null, $field_num = null) {
		parent::__construct($post_type, $field_num);

		// インライン編集を許可
		$this->can_edit_inline = true;

		$this->_vars['field_type'] = 'truefalse';

		$this->_vars['additional'] = array(
			'field_note' => array( 'type' => 'textarea', 'default' => '', 'required' => false),
			//'default_value' => array( 'type' => 'checkbox', 'default' => '', 'required' => false)
		);

	}
	
		/**
	 * 検索本文へ投入する値を返す
	 *
	 * @param $target_post_id
	 *
	 */
	function get_search_str($target_post_id) {

		$str_field = get_post_meta($target_post_id, $this->_vars['field_key'], true);
		if($str_field) {
			return apply_filters('eyeta_biztool/field/true_false/yes_word', 'YES');
		} else {
			return '';
		}
	}
	/**
	 * 単純な列出力のためのテキスト
	 *
	 * @param $target_post_id
	 *
	 * @return mixed
	 */
	function get_simple_column($target_post_id) {

		$str_field = get_post_meta($target_post_id, $this->_vars['field_key'], true);
		if($str_field) {
			return apply_filters('eyeta_biztool/field/true_false/yes_word', 'YES');
		} else {
			return '';
		}
	}
	/**
	 *  表内編集用フィールド
	 * @param type $target_post_id
	 * @return string
	 */
	function get_inlineedit_column($target_post_id) {
		$str_html = parent::get_inlineedit_column($target_post_id);

		$str_field = get_post_meta($target_post_id, $this->_vars['field_key'], true);
		$str_checked = '';
		if($str_field) {
			$str_checked = ' checked ';
		}
		$str_html .= '<input type="checkbox" ' . $str_checked . ' org="" name="' . esc_attr($this->_vars['field_key']) . '-'
			. $target_post_id . '" id="' . esc_attr($this->_vars['field_key']) . '-'
			. $target_post_id . '" class="diff-check-yn no-form w100" value="1" target-post_id="'
			. $target_post_id . '" target-field-key="' . esc_attr($this->_vars['field_key']) . '" />';

		// js
		$this->wp_add_inline_script_vars_for_list($this->_vars['post_type'], $target_post_id);

		return $str_html;

	}


	/**
	 * JSに書き出す配列を生成
	 *
	 * @return mixed
	 */
	function wp_add_inline_script_vars($post_type, $array_js_vars = array()) {
		$array_js_vars = parent::wp_add_inline_script_vars($post_type, $array_js_vars);

		$array_rule = array();
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
		

		$html = parent::get_field_edit_form( $mb, $html );
		?>
		<label><?php echo esc_html( $this->field_name ); ?></label>

		<?php

		
			$mb->the_field($this->field_key);

			?>
						<input type="checkbox" name="<?php $mb->the_name(); ?>"
			       value="1" <?php $mb->the_checkbox_state( 1 ); ?> />
			<br/>
		<span><?php echo esc_html($this->field_note);?></span>
		</p>
		<?php
		
		return $html;
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

		<?php
		$html .= ob_get_contents();
		ob_end_clean();

		
		return $html;
	}

}