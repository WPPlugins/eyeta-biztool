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
class Biztool_Field {

	protected $_vars = array(
		'field_key' => 'new',
		'field_num' => 0,  // テーブル単位でのフィールド番号
		'h3_title' => '',
		'field_name' => '',
		'field_type' => '',
		'for_search_field' => false,
		'order_num' => 0,
		'additional' => array(),
		'additional_settings' => array()
	);

	public $is_new = true;
	
	// 数字として表示の際にフォーマット
	public $is_number = false;
	
	// インライン編集を許可
	public $can_edit_inline = false;


	static function factory($field_type, $post_type = null, $field_num = null ) {
		// 対象のタイプのクラス取得
		$array_classes = apply_filters('eyeta_biztool/init/field_types', array());
		if(!isset($array_classes[$field_type])) {
			return "";
		}
		$class_name = $array_classes[$field_type]['class'];

		$obj_field = new $class_name($post_type, $field_num);

		return $obj_field;
	}

	/**
	 * table constructor.
	 *
	 *
	 * @param null $post_type null の場合新規作成
	 */
	function __construct($post_type = null, $field_num = null) {
		global $eyeta_biztool;
		if(null != $field_num && null != $post_type) {
			$this->_vars['field_key'] = Biztool_Field::get_field_key_from_cnt($post_type, $field_num);
			$this->_vars['field_num'] = $field_num;
			$this->_vars['order_num'] = $field_num;

			$this->is_new = false;

			// 登録内容を読み込む
			$tables_option = $eyeta_biztool->get_option('tables');
			if(!isset($tables_option[$post_type]['fields'][$this->_vars['field_key']])) {
				// 生成前
			} else {
				$this->_vars = array_merge($this->_vars, $tables_option[$post_type]['fields'][$this->_vars['field_key']]);
			}
		} elseif(null != $field_num) {
			$this->_vars['field_num'] = $field_num;
		}

		if('' == $this->_vars['h3_title']) {
			$this->_vars['h3_title'] = $this->_vars['table_name'];
		}

	}

	/**
	 * 検索本文へ投入する値を返す
	 * 
	 * @param $target_post_id
	 * 
	 */
	function get_search_str($target_post_id) {
		$val = get_post_meta($target_post_id, $this->_vars['field_key'], true);
		
		return $val;
	}

	/**
	 * 列への単純表示
	 */
	function get_simple_column($target_post_id) {
		
		$target_post = get_post(intval($target_post_id));
		$this->_vars['post_type'] = $target_post->post_type;

		return "";
	}
	
	/**
	 * フィールドの値変更（Ajaxによる変更を想定）
	 * 
	 * @param type $target_post_id
	 * @param type $new_value
	 * @param type $args
	 */
	function save_field_value($target_post_id, $new_value, $args = array()) {
		$rsl =update_post_meta($target_post_id, $this->_vars['field_key'], $new_value);
		if($rsl) {
			return true;
		} else {
			return new WP_Error('error', 'フィールドの更新に失敗しました。');
		}
	}

	/**
	 *  表内編集用フィールド
	 * @param type $target_post_id
	 * @return string
	 */
	function get_inlineedit_column($target_post_id) {
		
		$target_post = get_post(intval($target_post_id));
		$this->_vars['post_type'] = $target_post->post_type;

		return "";
	}

	/**
	 * 編集画面用のJSに書き出す配列を生成
	 *
	 * @return mixed
	 */
	function wp_add_inline_script_vars($post_type, $array_js_vars = array()) {
		if(!isset($array_js_vars['validate'])) $array_js_vars['validate'] = array();
		if(!isset($array_js_vars['validate']['rules'])) $array_js_vars['validate']['rules'] = array();

		return $array_js_vars;
	}

	/**
	 * 一覧画面用のJSに書き出す配列を生成
	 *
	 * @return mixed
	 */
	function wp_add_inline_script_vars_for_list($post_type, $target_post_id) {
		global $eyeta_biztool;
		$obj_table = $eyeta_biztool->get_table($post_type);
		if(!isset($obj_table->array_js_vars['validate'])) $obj_table->array_js_vars['validate'] = array();
		if(!isset($obj_table->array_js_vars['validate']['rules'])) $obj_table->array_js_vars['validate']['rules'] = array();

		return ;
	}

	/**
	 * フィールドの設定を保存
	 *
	 * @param $args
	 */
	function save_field_setting($args) {

		// フィールド名
		$this->_vars['field_name'] = $args['field_name'];
		$this->_vars['h3_title'] = $args['field_name'];
		// フィールドタイプ
		$this->_vars['field_type'] = $args['field_type'];
		// order_num
		$this->_vars['order_num'] = intval($args['field_order_num']);

		if(isset($args['for_search_field'])) {
			$this->_vars['for_search_field'] = intval($args['for_search_field']);
		} else {
			$this->_vars['for_search_field'] = false;
		}

		// 追加項目設定
		foreach($this->_vars['additional'] as $key => $array_additional) {
			if(isset($args[$key])) {
				switch($array_additional['type']) {
					case 'number':
						if('' != $args[$key] && !is_numeric($args[$key])) {
							$this->_vars['additional_settings'][$key] = $array_additional['default'];
						} else {
							$this->_vars['additional_settings'][$key] = $args[$key];
						}
						break;
					case "checkbox":
						$this->_vars['additional_settings'][$key] = $args[$key];
						break;
					case "select":
						if(is_array($args[$key])) {
							if(count($args[$key]) == 1 && current($args[$key]) == '') {
								$this->_vars['additional_settings'][$key] = '';
							} else {
								$this->_vars['additional_settings'][$key] = $args[$key];
							}
						} else {
							$this->_vars['additional_settings'][$key] = $args[$key];
						}
						break;
					default;
						$this->_vars['additional_settings'][$key] = $args[$key];
						break;
				}
				if($array_additional['required'] && '' == $this->_vars['additional_settings'][$key]) {
					$this->_vars['additional_settings'][$key] = $array_additional['default'];
				}

			} else {
				if($array_additional['required']) {
					$this->_vars['additional_settings'][$key] = $array_additional['default'];
				} else {
					$this->_vars['additional_settings'][$key] = $array_additional['default'];
				}
			}

		}



		return $this->_vars;
	}

	/**
	 * フィールドタイプ別の追加情報に対して、
	 * フォーム生成のために適切な戻り値を返す。
	 *
	 * @param $key
	 */
	function get_additional_values_for_form($key, $args = array()) {
		if(isset($this->_vars['additional'][$key]) && isset($this->_vars['additional_settings'][$key])) {
			switch($this->_vars['additional'][$key]['type']) {
				case "radio":
					if($this->_vars['additional_settings'][$key] == $args['value']) {
						return ' checked';
					} else {
						return "";
					}
					break;
				case "select":
					if(!is_array($this->_vars['additional_settings'][$key]) && $this->_vars['additional_settings'][$key] == $args['value']) {
						return ' selected';
					} else if(is_array($this->_vars['additional_settings'][$key]) && array_search($args['value'], $this->_vars['additional_settings'][$key]) !== false) {
						return ' selected';
					} else {
						return "";
					}
					break;
				case "checkbox":
					if(isset($this->_vars['additional_settings'][$key]) && !is_array(isset($this->_vars['additional_settings'][$key])) && $this->_vars['additional_settings'][$key] == $args['value']) {
						return ' checked';
					} elseif(isset($this->_vars['additional_settings'][$key]) && is_array(isset($this->_vars['additional_settings'][$key])) && array_search($args['value'], $this->_vars['additional_settings'][$key]) !== false) {
						return " checked";
					} else {
						return "";
					}
					break;
				default:
					return $this->_vars['additional_settings'][$key];
			}
			
		}

		// 設定に無い場合は空を返す
		return "";
	}

	/**
	 * 入力画面用フォーム
	 *
	 * @param string $html]
	 */
	function get_field_edit_form($mb, $html = '') {
		

		return $html;
	}

	/**
	 * フィールド独自の設定項目のフォームを返す。
	 *
	 */
	function get_field_form($html = '') {

		ob_start();
		?>
		<tr class="field-form-detail">
			<td class="fbold"><?php echo esc_html__('meta_key', 'eyeta-biztool');?></td>
			<td class="field_key-<?php echo $this->field_num;?>"><input type="text" class="w100" name="field_key-<?php echo $this->field_num;?>" readonly value="<?php echo $this->field_key;?>" /></td>
		</tr>
		<tr class="field-form-detail">
			<td class="fbold"><?php echo esc_html__('for search', 'eyeta-biztool');?></td>
			<td class="for_search_field-<?php echo $this->field_num;?>">
				<input type="checkbox" name="for_search_field-<?php echo $this->field_num;?>" value="1" <?php if($this->_vars['for_search_field']) echo 'checked';?> />対象

			</td>
		</tr>

		<?php
		$html .= ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * 番号からポストタイプ名を取得
	 *
	 * @param $cnt
	 */
	static function get_field_key_from_cnt($post_type, $cnt) {
		return 'biztool_field_' . $post_type . '_' . $cnt;
	}

	function __get($key) {
		if(array_key_exists($key, $this->_vars)) {
			return $this->_vars[$key];
		} else {
			if(array_key_exists($key, $this->_vars['additional_settings'])) {
				return $this->_vars['additional_settings'][$key];
			} else {
				eyeta_biztool_log('Biztool_Field:__get: no key: ' . $key);
				return null;
			}
		}
	}

	function __set($key, $value) {
		$this->_vars[$key] = $value;
	}


}
