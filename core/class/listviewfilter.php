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
class Biztool_Listviewfilter {

	protected $_vars = array(
		'listviewfilter_name' => '',
		'order_num'          => 0,
		'listviewfilter_type'          => '',
		'post_type'          => '',
		'listview_num'       => 0,
		'listviewfilter_num'          => 0,
		'additional'          => array(),
		'additional_settings' => array()
	);

	public $is_new = true;


	static function factory( $filter_type, $post_type = null, $listview_num = null, $filter_num = null ) {
		// 対象のタイプのクラス取得
		$array_classes = apply_filters( 'eyeta_biztool/init/listviewfilter_types', array() );
		if ( ! isset( $array_classes[ $filter_type ] ) ) {
			return new Biztool_Listviewcell($post_type, $listview_num, $filter_num);
		}
		$class_name = $array_classes[ $filter_type ]['class'];

		$obj_field = new $class_name( $post_type, $listview_num, $filter_num);

		return $obj_field;
	}

	/**
	 * table constructor.
	 *
	 *
	 * @param null $post_type null の場合新規作成
	 */
	function __construct( $post_type = null, $listview_num = null, $filter_num = null ) {
		global $eyeta_biztool;
		if ( null != $filter_num && null != $listview_num && null != $post_type ) {
			$this->_vars['post_type'] = $post_type;
			$this->_vars['listviewfilter_num'] = $filter_num;
			$this->_vars['order_num'] = $filter_num;
			$this->_vars['listview_num'] = $listview_num;

			$this->is_new = false;

			// 登録内容を読み込む
			$tables_option = $eyeta_biztool->get_option( 'tables' );
			if ( ! isset( $tables_option[ $post_type ]['listviews']['listview_' . $listview_num]['listviewfilters'][ 'listviewfilter_' . $this->_vars['listviewfilter_num'] ] ) ) {
				// 生成前
			} else {
				$this->_vars = array_merge( $this->_vars, $tables_option[ $post_type ]['listviews']['listview_' . $listview_num]['listviewfilters'][ 'listviewfilter_' . $this->_vars['listviewfilter_num'] ] );
			}
		} elseif ( null != $filter_num ) {
			$this->_vars['filter_num'] = $filter_num;
		}
	}

	/**
	 * フィルタ処理
	 *
	 * @param $query
	 *
	 */
	function pre_get_posts($query) {

	}

	/**
	 * listview別のフィルタ表示
	 *
	 * @return string
	 */
	function get_restrict_manage_posts() {

		return '';
	}

	/**
	 * 管理画面フォーム生成
	 *
	 * @param string $html
	 */
	function get_listviewfilter_form($html = '') {
		//ob_start();
		?>

		<?php
		//$html .= ob_get_contents();
		//ob_end_clean();

		return $html;
	}

	/**
	 * フィールドの設定を保存
	 *
	 * @param $args
	 */
	function save_listviewfilter_setting($args) {

		// 検索項目名（Placeholderに表示）
		$this->_vars['listviewfilter_name'] = $args['listviewfilter_name'];
		// order_num
		$this->_vars['order_num'] = intval($args['listviewfilter_order_num']);

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