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
class Biztool_Field_Checkbox extends Biztool_Field {


	function __construct($post_type = null, $field_num = null) {
		parent::__construct($post_type, $field_num);
		// インライン編集を許可
		$this->can_edit_inline = true;

		$this->_vars['field_type'] = 'checkbox';

		$this->_vars['additional'] = array(
			'field_note' => array( 'type' => 'textarea', 'default' => '', 'required' => false),
			'selection' => array( 'type' => 'textarea', 'default' => '', 'required' => false)
		);

	}
	

	/**
	 * 検索本文へ投入する値を返す
	 *
	 * @param $target_post_id
	 *
	 */
	function get_search_str($target_post_id) {
		$rsl = $this->get_simple_column_array($target_post_id);

		if(is_array($rsl)) {
			return implode("\n", $rsl);
		} else {
			return $rsl;
		}

	}


	/**
	 *  表内編集用フィールド
	 * @param type $target_post_id
	 * @return string
	 */
	function get_inlineedit_column($target_post_id) {
		$str_html = parent::get_inlineedit_column($target_post_id);

		$selections = $this->get_selections_as_array();

		$str_field = get_post_meta($target_post_id, $this->_vars['field_key'], true);
		$array_field = maybe_unserialize($str_field);
		if(!is_array($array_field)) {
			$array_field = array($array_field);
		}
//print_r($array_field);echo 'aaa';print_r($selections);
		foreach ( $selections as $i => $item ) {
			// checked or not
			$org_val = '';
			if(array_search($i, $array_field) !== false) {
				// checked
				$org_val = 'checked';
			}
			$str_html .= '
			<input type="checkbox" ' . $org_val . ' org="' . $org_val . '" name="' . esc_attr($this->_vars['field_key']) . '-' . $target_post_id . '[]"
			       class="diff-check-checkbox checkbox-' . esc_attr($this->_vars['field_key']) . '-' . $target_post_id . ' no-form" target-post_id="' . $target_post_id . '" target-field-key="' . esc_attr($this->_vars['field_key']) . '"
			       value="' . esc_attr($i) . '"  /> ' . esc_html($item) . '
			<br/>';
		}

		// js
		$this->wp_add_inline_script_vars_for_list($this->_vars['post_type'], $target_post_id);

		return $str_html;

	}

	/**
	 * 単純な列出力のためのテキスト
	 *
	 * @param $target_post_id
	 *
	 * @return mixed
	 */
	function get_simple_column($target_post_id) {

		$rsl = $this->get_simple_column_array($target_post_id);

		if(is_array($rsl)) {
			return implode('<br />', $rsl);
		} else {
			return esc_html($rsl);
		}

	}



	/**
	 * 単純な列出力のためのテキスト
	 *
	 * @param $target_post_id
	 *
	 * @return mixed
	 */
	function get_simple_column_array($target_post_id) {

		$str_field = get_post_meta($target_post_id, $this->_vars['field_key'], true);
		$str_field = maybe_unserialize($str_field);

		$array_selections = $this->get_selections_as_array();

		$array_vals = array();
		if(is_array($str_field)) {
			foreach($str_field as $val) {
				if(isset($array_selections[$val])) {
					$array_vals[] = $array_selections[$val];
				}
			}

			return $array_vals;
		} else {
			if(isset($array_selections[$str_field])) {
				return ($array_selections[$str_field]);
			} else {
				return "";
			}
		}

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
	 * 選択肢を配列として返す。
	 */
	function get_selections_as_array() {
		$target_str = $this->_vars['additional_settings']['selection'];
		$target_str = str_replace("\r\n", "\n", $target_str);
		$array_lines = explode("\n", $target_str);
		$array_rsl = array();
		foreach($array_lines as $str_line) {
			$array_line = explode(" : ", $str_line);
			if("" != $array_line[0]) {
				$array_rsl[$array_line[0]] = $array_line[1];
			}
		}
		return $array_rsl;
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
		$selections = $this->get_selections_as_array();

		$html = parent::get_field_edit_form( $mb, $html );
		?>
		<label><?php echo esc_html( $this->field_name ); ?></label>

		<?php

		foreach ( $selections as $i => $item ) {
			$mb->the_field($this->field_key);
			?>
			<input type="checkbox" name="<?php $mb->the_name(); ?>[]"
			       value="<?php echo $i; ?>" <?php $mb->the_checkbox_state( $i ); ?> /> <?php echo $item; ?>
			<br/>
			<?php
		}

			?>
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
		<tr class="field-form-detail">
			<td class="fbold"><?php echo esc_html__('Choices', 'eyeta-biztool');?><br /><span class="description"><?php echo esc_html__('Once described as the following , you can control both the value and the label .', 'eyeta-biztool');?><br />
red : 赤<br />
blue : 青</span></td>
			<td class=""><textarea class="w100" name="selection-<?php echo $this->field_num;?>" ><?php echo esc_html($this->get_additional_values_for_form('selection'));?></textarea></td>
		</tr>

		<?php
		$html .= ob_get_contents();
		ob_end_clean();

		
		return $html;
	}

}