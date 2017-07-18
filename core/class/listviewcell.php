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
class Biztool_Listviewcell {

	protected $_vars = array(
		'order_num'          => 0,
		'listviewcell_type'          => '',
		'post_type'          => '',
		'listview_num'       => 0,
		'listviewcell_name'       => '',
		'listviewcell_num'          => 0,
		'listviewcell_key_for_column' => false,
		'sortable' => false, // 継承したクラスでセット
		'additional'          => array(),
		'additional_settings' => array(),
		'show_mobile' => false,
		'width' => '',
		'width_type' => 'px',
		'show_searched' => false
	);

	public $is_new = true;


	static function factory( $cell_type, $post_type = null, $listview_num = null, $cell_num = null ) {
		// 対象のタイプのクラス取得
		$array_classes = apply_filters( 'eyeta_biztool/init/listviewcell_types', array() );
		if ( ! isset( $array_classes[ $cell_type ] ) ) {
			return new Biztool_Listviewcell($post_type, $listview_num, $cell_num);
		}
		$class_name = $array_classes[ $cell_type ]['class'];

		$obj_field = new $class_name( $post_type, $listview_num, $cell_num);

		return $obj_field;
	}

	/**
	 * table constructor.
	 *
	 *
	 * @param null $post_type null の場合新規作成
	 */
	function __construct( $post_type = null, $listview_num = null, $cell_num = null ) {
		global $eyeta_biztool;
		if ( null != $cell_num && null != $listview_num && null != $post_type ) {
			$this->_vars['post_type'] = $post_type;
			$this->_vars['listviewcell_num'] = $cell_num;
			$this->_vars['cell_num'] = $cell_num;
			$this->_vars['order_num'] = $cell_num;
			$this->_vars['listview_num'] = $listview_num;

			$this->is_new = false;

			// 登録内容を読み込む
			$tables_option = $eyeta_biztool->get_option( 'tables' );
			if ( ! isset( $tables_option[ $post_type ]['listviews']['listview_' . $listview_num]['listviewcells'][ 'listviewcell_' . $this->_vars['listviewcell_num'] ] ) ) {
				// 生成前
			} else {
				$this->_vars = array_merge( $this->_vars, $tables_option[ $post_type ]['listviews']['listview_' . $listview_num]['listviewcells'][ 'listviewcell_' . $this->_vars['cell_num'] ] );
			}
		} elseif ( null != $cell_num ) {
			$this->_vars['cell_num'] = $cell_num;
		}
	}

	/**
	 * 一覧表への値を返す
	 *
	 * @param $target_post_id
	 */
	function get_manage_posts_custom_column($target_post_id) {

		return "";
	}

	/**
	 * 管理画面フォーム生成
	 *
	 * @param string $html
	 */
	function get_listviewcell_form($html = '') {
		ob_start();
		?>
		<!--<tr class="listviewcell-form-detail">
			<td class="fbold">スマホで表示</td>
			<td class="show_mobile-<?php echo $this->_vars['listview_num'];?>-<?php echo $this->_vars['cell_num'];?>">
				<input type="checkbox" name="show_mobile-<?php echo $this->_vars['listview_num'];?>-<?php echo $this->_vars['cell_num'];?>" value="1" <?php if($this->_vars['show_mobile']) echo 'checked';?> />表示
			</td>
		</tr>
		<tr class="listviewcell-form-detail">
			<td class="fbold">全文検索対象</td>
			<td class="show_searched-<?php echo $this->_vars['listview_num'];?>-<?php echo $this->_vars['cell_num'];?>">
				<input type="checkbox" name="show_searched-<?php echo $this->_vars['listview_num'];?>-<?php echo $this->_vars['cell_num'];?>" value="1" <?php if($this->_vars['show_searched']) echo 'checked';?> />表示
			</td>
		</tr>
		<tr class="listviewcell-form-detail">
			<td class="fbold">セル幅</td>
			<td class="width-<?php echo $this->_vars['listview_num'];?>-<?php echo $this->_vars['cell_num'];?>">
				<input type="text" name="width-<?php echo $this->_vars['listview_num'];?>-<?php echo $this->_vars['cell_num'];?>" value="<?php echo esc_attr($this->_vars['width']);?>"  style="width: 5em;" />
				<input type="radio" name="width_type-<?php echo $this->_vars['listview_num'];?>-<?php echo $this->_vars['cell_num'];?>" value="" <?php if($this->_vars['width_type'] != 'per' ) echo 'checked';?> />&nbsp;px&nbsp;&nbsp;
				<input type="radio" name="width_type-<?php echo $this->_vars['listview_num'];?>-<?php echo $this->_vars['cell_num'];?>" value="per" <?php if($this->_vars['width_type'] == 'per' ) echo 'checked';?> />&nbsp;%
			</td>
		</tr>-->

		<?php
		$html .= ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * フィールドの設定を保存
	 *
	 * @param $args
	 */
	function save_listviewcell_setting($args) {

		// フィールド名
		$this->_vars['listviewcell_name'] = $args['listviewcell_name'];
		// order_num
		$this->_vars['order_num'] = intval($args['listviewcell_order_num']);
		// show_mobile
		if(isset($args['show_mobile'])) {
			$this->_vars['show_mobile'] = intval($args['show_mobile']);
		} else {
			$this->_vars['show_mobile'] = false;
		}
		// width
		if(is_numeric($args['width'])) {
			$this->_vars['width'] = intval($args['width']);
		} else {
			$this->_vars['width'] = '';
		}
		// width_type
		if(isset($args['width_type']) && '' != isset($args['width_type'])) {
			$this->_vars['width_type'] = $args['width_type'];
		} else {
			$this->_vars['width_type'] = '';
		}
		// show_searched
		if(isset($args['show_searched'])) {
			$this->_vars['show_searched'] = intval($args['show_searched']);
		} else {
			$this->_vars['show_searched'] = false;
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
					} else if(is_array($this->_vars['additional_settings'][$key]) && array_search($args['value'], $this->_vars['additional_settings'][$key])) {
						return ' selected';
					} else {
						return "";
					}
					break;
				case "checkbox":
					if(isset($this->_vars['additional_settings'][$key]) && !is_array(isset($this->_vars['additional_settings'][$key])) && $this->_vars['additional_settings'][$key] == $args['value']) {
						return ' checked';
					} elseif(isset($this->_vars['additional_settings'][$key]) && is_array(isset($this->_vars['additional_settings'][$key])) && array_search($args['value'], $this->_vars['additional_settings'][$key])) {
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