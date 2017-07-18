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
class Biztool_Field_Post extends Biztool_Field {


	function __construct($post_type = null, $field_num = null) {
		parent::__construct($post_type, $field_num);
		// インライン編集を許可
		$this->can_edit_inline = true;


		$this->_vars['field_type'] = 'post';

		$this->_vars['additional'] = array(
			'field_note' => array( 'type' => 'textarea', 'default' => '', 'required' => false),
			'is_required' => array( 'type' => 'checkbox', 'default' => '', 'required' => false),
			'target_post_type' => array( 'type' => 'select', 'default' => '', 'required' => false),
			'show_field' => array( 'type' => 'select', 'default' => '', 'required' => false)
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
		if(!$str_field) {
			return '';
		}
		$target_post = get_post(intval($str_field));
		if('title' == $this->_vars['additional_settings']['show_field'])  {
			$str_html = ($target_post->post_title);
		} else {
			$meta_val = get_post_meta($target_post->ID, $this->_vars['additional_settings']['show_field'], true);
			$str_html = ($meta_val);
		}
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

		$str_field = get_post_meta($target_post_id, $this->_vars['field_key'], true);
		if(!$str_field) {
			return '';
		}
		$target_post = get_post(intval($str_field));
		if('title' == $this->_vars['additional_settings']['show_field'])  {
			$str_html = esc_html($target_post->post_title);
		} else {
			$meta_val = get_post_meta($target_post->ID, $this->_vars['additional_settings']['show_field'], true);
			$str_html = esc_html($meta_val);
		}
		return $str_html;
	}

	/**
	 *  表内編集用フィールド
	 * @param type $target_post_id
	 * @return string
	 */
	function get_inlineedit_column($target_post_id) {
		global $eyeta_biztool;

		$str_html = parent::get_inlineedit_column($target_post_id);

		$str_field = get_post_meta($target_post_id, $this->_vars['field_key'], true);
		$str_html .= '
				<select org="' . esc_attr($str_field)
			. '" name="' . esc_attr($this->_vars['field_key']) . '-'
			. $target_post_id . '" id="' . esc_attr($this->_vars['field_key']) . '-'
			. $target_post_id . '" class="w100 diff-check no-form"  target-post_id="'
			. $target_post_id . '" target-field-key="' . esc_attr($this->_vars['field_key']) . '" >
			<option value="">選択してください。</option>';

			if('title' == $this->_vars['additional_settings']['show_field'])  {
				$target_posts = new \WP_Query(
					array(
						'post_type' => $this->_vars['additional_settings']['target_post_type'],
						'posts_per_page' => apply_filters('eyeta_biztool/edit/max_select_option_count', 1000),
						'orderby' => 'title'
					)
				);
			} else {
				// 対象のフィールドが数字かどうかをチェック
				$obj_table = $eyeta_biztool->get_table($this->_vars['additional_settings']['target_post_type']);
				$obj_field = $obj_table->get_obj_field($this->_vars['additional_settings']['show_field']);
				if($obj_field->is_number) {
					$target_posts = new \WP_Query(
						array(
							'post_type' => $this->_vars['additional_settings']['target_post_type'],
							'posts_per_page' => apply_filters('eyeta_biztool/edit/max_select_option_count', 1000),
							'meta_key' => $this->_vars['additional_settings']['show_field'],
							'orderby' => 'meta_value_num'
						)
					);
				} else {
					$target_posts = new \WP_Query(
						array(
							'post_type' => $this->_vars['additional_settings']['target_post_type'],
							'posts_per_page' => apply_filters('eyeta_biztool/edit/max_select_option_count', 1000),
							'meta_key' => $this->_vars['additional_settings']['show_field'],
							'orderby' => 'meta_value'
						)
					);
				}
			}
			foreach($target_posts->posts as $obj_post) {
				$str_selected = '';
				if($str_field == $obj_post->ID) {
					$str_selected= ' selected ';
				}
				$str_html .='
				<option value="' . $obj_post->ID . '" ' . $str_selected . ' >';
				if('title' == $this->_vars['additional_settings']['show_field'])  {
					$str_html .= esc_html($obj_post->post_title);
				} else {
					$meta_val = get_post_meta($obj_post->ID, $this->_vars['additional_settings']['show_field'], true);
					$str_html .= esc_html($meta_val);
				}
				$str_html .= '</option>';
			}
				$str_html .= '</select>';

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
		global $eyeta_biztool;
		?>
			<?php
		$mb->the_field($this->field_key);
		$html = parent::get_field_edit_form($mb, $html);
		?>
		<label><?php echo esc_html($this->field_name);?></label>
		<select name="<?php $mb->the_name(); ?>" id="<?php echo esc_attr($this->_vars['field_key']); ?>" >
			<option value="">選択してください。</option>
			<?php
			if('title' == $this->_vars['additional_settings']['show_field'])  {
				$target_posts = new \WP_Query(
					array(
						'post_type' => $this->_vars['additional_settings']['target_post_type'],
						'posts_per_page' => apply_filters('eyeta_biztool/edit/max_select_option_count', 1000),
						'orderby' => 'title'
					)
				);
			} else {
				// 対象のフィールドが数字かどうかをチェック
				$obj_table = $eyeta_biztool->get_table($this->_vars['additional_settings']['target_post_type']);
				$obj_field = $obj_table->get_obj_field($this->_vars['additional_settings']['show_field']);
				if($obj_field->is_number) {
					$target_posts = new \WP_Query(
						array(
							'post_type' => $this->_vars['additional_settings']['target_post_type'],
							'posts_per_page' => apply_filters('eyeta_biztool/edit/max_select_option_count', 1000),
							'meta_key' => $this->_vars['additional_settings']['show_field'],
							'orderby' => 'meta_value_num'
						)
					);
				} else {
					$target_posts = new \WP_Query(
						array(
							'post_type' => $this->_vars['additional_settings']['target_post_type'],
							'posts_per_page' => apply_filters('eyeta_biztool/edit/max_select_option_count', 1000),
							'meta_key' => $this->_vars['additional_settings']['show_field'],
							'orderby' => 'meta_value'
						)
					);
				}
			}
			foreach($target_posts->posts as $obj_post) {
				?>
				<option value="<?php echo $obj_post->ID; ?>" <?php $mb->the_select_state($obj_post->ID); ?> ><?php
					if('title' == $this->_vars['additional_settings']['show_field'])  {
						echo esc_html($obj_post->post_title);
					} else {
						$meta_val = get_post_meta($obj_post->ID, $this->_vars['additional_settings']['show_field'], true);
						echo esc_html($meta_val);
					}
					?></option>

				<?php
			}

			?>
		 </select>
		<span><?php echo esc_html($this->field_note);?></span>
		<?php
		
		return $html;
	}
	

	/**
	 * フィールド独自の設定項目のフォームを返す。
	 *
	 */
	function get_field_form($html = '') {
		global $eyeta_biztool;

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
			<td class="fbold"><?php echo esc_html__('Post Type', 'eyeta-biztool');?><br /><span class="description"><?php echo esc_html__('Select the posttype you want to select', 'eyeta-biztool');?></span></td>
			<td class=""><select name="target_post_type-<?php echo $this->field_num;?>" class="select-target_post_type" target-field-num="<?php echo $this->field_num;?>">
					<?php
					$array_post_types = $eyeta_biztool->get_post_types();
					foreach($array_post_types as $post_type_key => $post_type_name) {
						?>
						<option value="<?php echo esc_attr($post_type_key);?>" <?php echo $this->get_additional_values_for_form('target_post_type', array('value' => $post_type_key));?> ><?php echo esc_html($post_type_name);?></option>
						<?php
					}
					?>
				</select></td>
		</tr>
		<tr class="field-form-detail">
			<td class="fbold"><?php echo esc_html__('Field to display', 'eyeta-biztool');?><br /><span class="description"><?php echo esc_html__('Columns to display in the select box', 'eyeta-biztool');?></span></td>
			<td class=""><select name="show_field-<?php echo $this->field_num;?>" id="show_field-<?php echo $this->field_num;?>" class="select-show_field select-show_field-<?php echo $this->field_num;?>">

					<?php
					if($this->_vars['additional_settings']['target_post_type'] != '') {
						// 値有り
						if($this->get_additional_values_for_form('show_field') != '') {
							echo Biztool_Field_Post::get_post_type_field_options($this->_vars['additional_settings']['target_post_type'], $this->get_additional_values_for_form('show_field'));
						} else {
							echo Biztool_Field_Post::get_post_type_field_options($this->_vars['additional_settings']['target_post_type']);
						}
					}
					?>
				</select></td>
		</tr>

		<?php
		$html .= ob_get_contents();
		ob_end_clean();

		
		return $html;
	}

	
	
	static function get_post_type_field_options($post_type, $selected = "") {
		global $eyeta_biztool;
		// 表定義があるPostTypeかどうか
		$obj_table = $eyeta_biztool->get_table($post_type);
		$str_rsl = "";
		if($obj_table) {
			$array_fields = $obj_table->get_fields();
			foreach($array_fields as $field_key => $array_field) {
				$str_rsl .= '<option value="' . esc_attr($field_key) . '" ';
				if($field_key == $selected) {
					$str_rsl .= ' selected ';
				}
				$str_rsl .= ' >' . esc_html($array_field['field_name']) . '</option>';
			}
		} else {
			$str_rsl .= '<option value="title">' . esc_html__('Post title', 'eyeta-biztool') . '</option>';
		}
	
		return $str_rsl;
	}
}