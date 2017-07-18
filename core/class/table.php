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
class Biztool_Table {

	/**
	 * 
	 * listviews['listview_num'] = array(
	 * 'listview_name'
	 * 'listview_num'
	 * 'order_num'
	 * 'listviewcells'
	 * 'listviewfilters'
	 * )
	 * 
	 * @var array
	 * 
	 * 
	 */
	protected $_vars = array(
		'post_type' => '',
		'h3_title' => '新規追加',
		'table_name' => '',
		'table_type' => 'simple',
		'max_field_num' => 0,
		'fields' => array(),
		'listviews' => array(),
		'listviewfilters' => array()
	);

	/**
	 * 登録されているフィールドインスタンス
	 * @var array
	 */
	protected $_array_obj_fields = array();
	protected $_array_obj_listviewcells = array();
	protected $_array_obj_listviewfilters = array();
	public $is_new = true;
	public $array_js_vars = array();

	static function factory($table_type, $post_type = null) {
		// 対象のタイプのクラス取得
		$array_classes = apply_filters('eyeta_biztool/init/table_types', array());
		if (!isset($array_classes[$table_type])) {
			return null;
		}
		$class_name = $array_classes[$table_type]['class'];

		$obj_field = new $class_name($post_type);

		return $obj_field;
	}

	/**
	 * table constructor.
	 *
	 *
	 * @param null $post_type null の場合新規作成
	 */
	function __construct($post_type = null) {
		global $eyeta_biztool;
		if (null != $post_type) {
			$this->_vars['post_type'] = $post_type;
			$this->is_new = false;

			// 登録内容を読み込む
			$table_option = $eyeta_biztool->get_option('tables');
			if (!isset($table_option[$post_type])) {
				eyeta_biztool_log('Biztool_Table: __construct: no post type in option: ' . $post_type, 'ERROR');
				wp_die('post_typeが見つかりません');
			}

			$this->_vars = array_merge($this->_vars, $table_option[$post_type]);

			$this->init_post_type();

			// フィールドクラスインスタンス化
			//foreach($this->_vars['fields'] as $key => $array_field_vals) {
			//$this->_array_obj_fields[$key] = Biztool_Field::factory($array_field_vals['field_type'], $post_type, $array_field_vals['field_num']);
			//}
		} else {
			// 新規
		}

		if ('' == $this->_vars['h3_title']) {
			$this->_vars['h3_title'] = $this->_vars['table_name'];
		}
	}

	/**
	 * 表示列設定を保存
	 *
	 * @param $args
	 */
	function save_listviews_setting($args) {

		$max_num = intval($args['eyeta_biztool_max_listview_num']);
		$array_for_order_listviews = array();
		$array_listviews_vars = array();

		for ($current_listview_num = 1; $current_listview_num <= $max_num; $current_listview_num++) {
			if (isset($args['listview_order_num-' . $current_listview_num])) {
				$array_current_listview = array();
				$array_current_listview['listview_num'] = $current_listview_num;
				$array_current_listview['listview_name'] = $args['listview_name-' . $current_listview_num];
				$array_current_listview['order_num'] = intval($args['listview_order_num-' . $current_listview_num]);
				$array_current_listview['listviewcells'] = array();
				$array_current_listview['listviewfilters'] = array();

				// 表示列
				$max_listviewcell_num = intval($args['eyeta_biztool_max_listviewcell_num-' . $current_listview_num]);
				$array_for_order_listviewcells = array();
				$array_listviewcells_vars = array();

				for ($current_listviewcell_num = 1; $current_listviewcell_num <= $max_listviewcell_num; $current_listviewcell_num++) {
					$str_nums = '-' . $current_listview_num . '-' . $current_listviewcell_num;
					if (isset($args['listviewcell_order_num' . $str_nums])) {

						// 引数配列生成
						$array_param = array();
						foreach ($args as $key => $val) {
							if (substr($key, ( - 1 ) * strlen($str_nums)) == $str_nums) {
								$array_param[substr($key, 0, ( - 1 ) * strlen($str_nums))] = $val;
							}
						}
						//print_r($array_param);die;

						$obj_listviewcell = Biztool_Listviewcell::factory($array_param['listviewcell_type'], $this->_vars['post_type'], $current_listview_num, $current_listviewcell_num);

						$array_for_order_listviewcells['listviewcell_' . $current_listviewcell_num] = $array_param['listviewcell_order_num'];
						$array_listviewcells_vars['listviewcell_' . $current_listviewcell_num] = $obj_listviewcell->save_listviewcell_setting($array_param);
					}

					//
				}
				// フィールド並べ替え
				array_multisort($array_for_order_listviewcells, $array_listviewcells_vars);
				$array_current_listview['listviewcells'] = $array_listviewcells_vars;

				// フィルター
				$max_listviewfilter_num = intval($args['eyeta_biztool_max_listviewfilter_num-' . $current_listview_num]);
				$array_for_order_listviewfilters = array();
				$array_listviewfilters_vars = array();

				for ($current_listviewfilter_num = 1; $current_listviewfilter_num <= $max_listviewfilter_num; $current_listviewfilter_num++) {
					$str_nums = '-' . $current_listview_num . '-' . $current_listviewfilter_num;
					if (isset($args['listviewfilter_order_num' . $str_nums])) {

						// 引数配列生成
						$array_param = array();
						foreach ($args as $key => $val) {
							if (substr($key, ( - 1 ) * strlen($str_nums)) == $str_nums) {
								$array_param[substr($key, 0, ( - 1 ) * strlen($str_nums))] = $val;
							}
						}
						//print_r($array_param);die;

						$obj_listviewfilter = Biztool_Listviewfilter::factory($array_param['listviewfilter_type'], $this->_vars['post_type'], $current_listview_num, $current_listviewfilter_num);

						$array_for_order_listviewfilters['listviewfilter_' . $current_listviewfilter_num] = $array_param['listviewfilter_order_num'];
						$array_listviewfilters_vars['listviewfilter_' . $current_listviewfilter_num] = $obj_listviewfilter->save_listviewfilter_setting($array_param);
					}

					//
				}
				// フィールド並べ替え
				array_multisort($array_for_order_listviewfilters, $array_listviewfilters_vars);
				$array_current_listview['listviewfilters'] = $array_listviewfilters_vars;

				$array_for_order_listviews['listview_' . $current_listview_num] = $array_current_listview['order_num'];
				$array_listviews_vars['listview_' . $current_listview_num] = $array_current_listview;
			}
			array_multisort($array_for_order_listviews, $array_listviews_vars);
		}

		$this->_vars['listviews'] = $array_listviews_vars;

		$this->save();
	}

	/**
	 * テーブルのフィールドを保存していく
	 *
	 * @param $args
	 */
	function save_fields_setting($args) {
		$max_num = intval($args['eyeta_biztool_max_field_num']);
		$array_for_order = array();
		$array_obj_fields = array();
		$array_field_vars = array();
		for ($current_num = 1; $current_num <= $max_num; $current_num++) {
			if (isset($args['field_order_num-' . $current_num])) {
				$field_key = Biztool_Field::get_field_key_from_cnt($this->_vars['post_type'], $current_num);
				// 対象のフィールド定義有り、保存処理開始
				$field_type = $args['field_type-' . $current_num];
				$post_type = $this->_vars['post_type'];
				$obj_field = Biztool_Field::factory($field_type, $post_type, $current_num);
				// 引数配列生成
				$array_param = array();
				foreach ($args as $key => $val) {
					if (substr($key, (-1) * strlen('-' . $current_num)) == '-' . $current_num) {
						$array_param[substr($key, 0, (-1) * strlen('-' . $current_num))] = $val;
					}
				}

				// フィールドの設定内容保存
				$field_vars = $obj_field->save_field_setting($array_param);

				$array_for_order[$field_key] = $field_vars['order_num'];
				$array_obj_fields[$field_key] = $obj_field;

				$array_field_vars[$field_key] = $field_vars;
			}
		}
		// フィールド並べ替え
		array_multisort($array_for_order, $array_obj_fields, $array_field_vars);

		$this->_vars['fields'] = $array_field_vars;
		$this->_vars['max_field_num'] = $max_num;
		$this->_array_obj_fields = $array_obj_fields;

		$this->save();

		return true;
	}

	/**
	 * テーブルの基本情報を保存する。
	 *
	 * @param $args
	 */
	function save_basic_setting($args) {
		// 表のタイプ
		if ($args['table_type'] != $this->_vars['table_type']) {
			// クラスを作り直し
			return Biztool_Table::factory($args['table_type'], $args['post_type']);
		} else {
			return $this;
		}
	}

	/**
	 * 番号からポストタイプ名を取得
	 *
	 * @param $cnt
	 */
	static function get_post_type_from_cnt($cnt) {
		return 'eyeta_biztool_' . $cnt;
	}

	/**
	 * post type register
	 */
	function init_post_type() {

		$labels = array(
			"name" => $this->_vars['table_name'],
			"singular_name" => $this->_vars['table_name'],
			/*"menu_name" => __($this->_vars['table_name'], ''),
			"all_items" => __('すべての' . $this->_vars['table_name'], ''),
			"search_items" => __($this->_vars['table_name'] . 'を検索', ''),
			'add_new_item' => __('新規' . $this->_vars['table_name'] . 'を追加', ''),
			'edit_item' => __($this->_vars['table_name'] . 'を編集', ''),
			'new_item' => __('新規' . $this->_vars['table_name'], ''),
			'view_item' => __($this->_vars['table_name'] . 'を表示', ''),
			'search_items' => __($this->_vars['table_name'] . 'を検索', ''),
			'not_found' => __($this->_vars['table_name'] . 'が見つかりませんでした。', ''),
			'not_found_in_trash' => __('ゴミ箱内に' . $this->_vars['table_name'] . 'が見つかりませんでした。', ''),*/
		);

		$args = array(
			"label" => $this->_vars['table_name'],
			"labels" => $labels,
			"description" => "",
			"public" => true,
			"publicly_queryable" => false,
			"show_ui" => true,
			"show_in_rest" => false,
			"rest_base" => "",
			"has_archive" => false,
			"show_in_menu" => true,
			"exclude_from_search" => true,
			"capability_type" => $this->_vars['post_type'],
			"map_meta_cap" => false,
			"hierarchical" => false,
			"rewrite" => array("slug" => $this->_vars['post_type'], "with_front" => false),
			"query_var" => false,
			"menu_position" => 5, // todo 表示位置調整機能実装
			"supports" => array("title", "editor", "author"),
		);
		register_post_type($this->_vars['post_type'], apply_filters( 'eyeta_biztool/register_post_type/args', $args, $this->_vars['post_type']));

		// リストマネジメント
		add_action('manage_posts_extra_tablenav', array(&$this, 'manage_posts_extra_tablenav'));
		add_action('manage_posts_extra_tablenav', array(&$this, 'manage_posts_extra_tablenav_bottom'));
		add_filter('manage_' . $this->_vars['post_type'] . '_posts_columns', array(&$this, 'manage_posts_columns'), 10, 2);
		add_filter('manage_edit-' . $this->_vars['post_type'] . '_sortable_columns', array(&$this, 'manage_sortable_column'), 10, 1);
		add_action('manage_' . $this->_vars['post_type'] . '_posts_custom_column', array(&$this, 'manage_posts_custom_column'), 10, 2);
		add_filter('post_row_actions', array(&$this, 'post_row_actions'), 10, 2);
		//add_action('restrict_manage_posts', array(&$this, 'restrict_manage_posts'), 10);
		add_action('pre_get_posts', array(&$this, 'pre_get_posts'));

		// 編集画面対応
		add_action('admin_enqueue_scripts', array(&$this, "admin_print_scripts"), 20);
		
		// テーブルアクセス権対応
		$this->wpinit();
		
	}
	
	/**
	 * WordPressのinitフック
	 */
	function wpinit() {
		global $eyeta_biztool;
		$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : NULL ;
		$uri_parts = parse_url($uri);
		$file = basename($uri_parts['path']);
		$tables = $eyeta_biztool->get_option('tables', array());
		
		if('post-new.php' == $file && isset($_REQUEST['post_type']) && isset($tables[$_REQUEST['post_type']])) {
			// 新規追加画面
			if(!$eyeta_biztool->current_user_can('eyeta-biztool/table/add_post|' . $this->_vars['post_type'])) {
				wp_die('アクセス権がありません。');
			}
		}

	}
	
	/**
	 * 指定したロールへ全権追加
	 * 
	 * @param type $role
	 */
	function add_all_cap($role) {
		$role->add_cap( 'edit_' . $this->_vars['post_type']);
		$role->add_cap( 'read_' . $this->_vars['post_type']);
		$role->add_cap( 'delete_' . $this->_vars['post_type']);
		$role->add_cap( 'edit_' . $this->_vars['post_type'] . 's');
		$role->add_cap( 'edit_others_' . $this->_vars['post_type'] . 's');
		$role->add_cap( 'publish_' . $this->_vars['post_type'] . 's');
		$role->add_cap( 'read_private_' . $this->_vars['post_type'] . 's');
		$role->add_cap( 'delete_' . $this->_vars['post_type'] . 's');
		$role->add_cap( 'delete_private_' . $this->_vars['post_type'] . 's');
		$role->add_cap( 'delete_published_' . $this->_vars['post_type'] . 's');
		$role->add_cap( 'delete_others_' . $this->_vars['post_type'] . 's');
		$role->add_cap( 'edit_private_' . $this->_vars['post_type'] . 's');
		$role->add_cap( 'edit_published_' . $this->_vars['post_type'] . 's');
	}

	function admin_print_scripts() {
		global $eyeta_biztool;

		$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : NULL ;
		$uri_parts = parse_url($uri);
		$file = basename($uri_parts['path']);
		$array_obj_tables = $eyeta_biztool->get_tables();
		
		// 共通
		if(!$eyeta_biztool->current_user_can('eyeta-biztool/table/add_post|' . $this->_vars['post_type'])) {
			$css = '.menu-icon-' . $this->_vars['post_type'] . ' .wp-submenu { display: none; }' . "\n";
			$css .= '#wp-admin-bar-new-' . $this->_vars['post_type'] . ' { display: none; }' . "\n";
			wp_add_inline_style('eyeta-biztool-style', $css);
		}

		// 編集画面
		if (is_admin() && \WPAlchemy_MetaBox::_get_current_post_type() == $this->_vars['post_type']) {
			// 対象
			$array_fields = $this->_vars['fields'];
			$array_js_vars = array(
				'validate' => array(
					'rules' => array()
				)
			);
			foreach ($array_fields as $field_key => $array_field) {
				$obj_field = $this->get_obj_field($array_field['field_num']);
				$array_js_vars = $obj_field->wp_add_inline_script_vars($this->_vars['post_type'], $array_js_vars);
			}
			$js = '';
			$js .= 'var eyeta_biztool_table_validate_rules = ' . json_encode($array_js_vars["validate"]["rules"]) . ';';
			wp_add_inline_script('eyeta-biztool-post', $js);
		} else if(is_admin() && 'edit.php' == $file && isset($array_obj_tables[$_REQUEST['post_type']]) && $this->_vars['post_type'] == $_REQUEST['post_type']) {
			// 一覧画面
			if(!$eyeta_biztool->current_user_can('eyeta-biztool/table/add_post|' . $this->_vars['post_type'])) {
				$css ='.page-title-action { display: none; }' . "\n";
				wp_add_inline_style('eyeta-biztool-style', $css);
			}
		}

		// todo 編集画面にも新規追加ボタンがある
		if(is_admin() && 'post.php' == $file && isset($_REQUEST['post']) && '' != $_REQUEST['post']) {
			$target_post = get_post(intval($_REQUEST['post']));
			if($target_post && isset($array_obj_tables[$target_post->post_type]) && !$eyeta_biztool->current_user_can('eyeta-biztool/table/add_post|' . $target_post->post_type) ) {
				$css ='.page-title-action { display: none; }' . "\n";
				wp_add_inline_style('eyeta-biztool-style', $css);
			}
		}
	}
	
	
	function manage_posts_extra_tablenav_bottom($witch) {
		if('bottom' == $witch) {
			$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : NULL ;
			$uri_parts = parse_url($uri);
			$file = basename($uri_parts['path']);
			if(is_admin() && 'edit.php' == $file && $_REQUEST['post_type'] == $this->post_type) {
			// 一覧画面
			
				if(count($this->array_js_vars) != 0) {
					$js = '<script type="text/javascript">';
					$js .= 'var eyeta_biztool_table_validate_rules = ' . json_encode($this->array_js_vars['validate']['rules']) . ';';
					$js .= '</script>';
					echo $js;
				} else {
					$js = '<script type="text/javascript">';
					$js .= 'var eyeta_biztool_table_validate_rules = {};';
					$js .= '</script>';
					echo $js;
				}
			}
		}
	}

	function pre_get_posts($query) {
		if ($this->_vars['post_type'] == $_REQUEST['post_type']) {
			$current_listview_key = $this->get_current_listview();
			foreach ($this->_vars['listviews'][$current_listview_key]['listviewfilters'] as $listviewfilter_key => $array_listviewfilter) {
				$obj_listviewfilter = $this->get_obj_listviewfilter($current_listview_key, $listviewfilter_key);
				echo $obj_listviewfilter->pre_get_posts($query);
			}
		}
	}

	function post_row_actions($actions, $post) {
		if (isset($_REQUEST['post_type']) && $this->_vars['post_type'] == $_REQUEST['post_type']) {
			unset($actions['inline hide-if-no-js']);
		}
		return $actions;
	}

	/**
	 * 表示列フィルタ追加
	 */
	protected $_shown_extra_tablenav = false;

	function manage_posts_extra_tablenav($witch) {
		// ビュー切替
		if ('top' == $witch) {
			if ($this->_vars['post_type'] == $_REQUEST['post_type'] && false == $this->_shown_extra_tablenav) {
				$this->_shown_extra_tablenav = true;

				// 対象の投稿タイプ
				?>
				<div class="alignleft actions ">
					<select name="biztool_listview" class="biztool_listview">

						<?php
						foreach ($this->_vars['listviews'] as $listview_key => $array_listviewcells) {
							?>
							<option value="<?php echo esc_attr($listview_key); ?>" <?php if ($listview_key == $_REQUEST['biztool_listview']) echo 'selected'; ?> ><?php echo esc_html($array_listviewcells['listview_name']); ?></option>
							<?php
						}
						?>
					</select>

				</div>
				<?php

			// ビュー後とのフィルタ
			$current_listview_key = $this->get_current_listview();
			if (isset($this->_vars['listviews'][$current_listview_key]['listviewfilters']) && is_array($this->_vars['listviews'][$current_listview_key]['listviewfilters'])) {
				// フィルタ有り
				?>
				<script type="text/javascript">
					var biztool_filter_on_submit = {};
				</script>

				<?php
				if(count($this->_vars['listviews'][$current_listview_key]['listviewfilters']) != 0) {
					?>
					<div class="alignleft actions " style="width: 100%; margin-bottom: 1em;">
						<div class="accordion_close biztool_detail_filter_accordion" accordion-target="2">
							<h3><?php echo esc_html__('Additional search', 'eyeta-biztool');?></h3>
							<div>
								<?php
								foreach ( $this->_vars['listviews'][ $current_listview_key ]['listviewfilters'] as $listviewfilter_key => $array_listviewfilter ) {
									$obj_listviewfilter = $this->get_obj_listviewfilter( $current_listview_key, $listviewfilter_key );
									echo $obj_listviewfilter->get_restrict_manage_posts();
								}
								?>
								<br/><br/>
								<input type="button" class="btn-biztool_filter button" name="biztool_filter"
								       value="<?php echo esc_html__('Search', 'eyeta-biztool');?>"/>
							</div>
						</div>
					</div>
					<?php
				}
			}
						}

		}
	}

	function manage_posts_custom_column($column_name, $target_post_id) {
		$target_listview_key = $this->get_current_listview();

		$obj_listviewcell = $this->get_obj_listviewcell($target_listview_key, $column_name);
		if (is_wp_error($obj_listviewcell)) {
			// 対象のセルなし
			return true;
		}
		echo ($obj_listviewcell->get_manage_posts_custom_column($target_post_id));
	}

	function manage_sortable_column($sortable_column) {
		$post_type = $_REQUEST['post_type'];
		$array_listviews = $this->_vars['listviews'];

		$target_listview_key = $this->get_current_listview();

		foreach ($array_listviews[$target_listview_key]['listviewcells'] as $listviewcell_key => $array_listviewcell) {
			$obj_listviewcell = $this->get_obj_listviewcell('listview_' . $array_listviewcell['listview_num'], 'listviewcell_' . $array_listviewcell['listviewcell_num']);

			if ($obj_listviewcell->sortable) {
				$sortable_column[$listviewcell_key] = $listviewcell_key;
			}
		}

		return $sortable_column;
	}

	/**
	 * 一覧画面の列の定義
	 * 
	 * @param $columns
	 *
	 * @return mixed
	 */
	function manage_posts_columns($columns) {
		$post_type = $_REQUEST['post_type'];
		$array_listviews = $this->_vars['listviews'];

		$target_listview_key = $this->get_current_listview();

		// 一旦削除
		unset($columns['title']);
		unset($columns['date']);
		unset($columns['author']);
		foreach ($array_listviews[$target_listview_key]['listviewcells'] as $listviewcell_key => $array_listviewcell) {
			$obj_listviewcell = $this->get_obj_listviewcell('listview_' . $array_listviewcell['listview_num'], 'listviewcell_' . $array_listviewcell['listviewcell_num']);

			if ($array_listviewcell['listviewcell_key_for_column']) {
				$columns[$listviewcell_key] = $obj_listviewcell->listviewcell_key_for_column;
			} else {
				$columns[$listviewcell_key] = $obj_listviewcell->listviewcell_name;
			}
		}

		return apply_filters( 'eyeta_biztool/manage_posts_columns/args', $columns, $this->_vars['post_type']);
	}

	/**
	 * 現在表示中のリストビュー
	 * @return mixed|string
	 */
	function get_current_listview() {
		$array_listviews = $this->_vars['listviews'];

		$target_listview_key = '';
		if (isset($_REQUEST['biztool_listview']) && isset($array_listviews[$_REQUEST['biztool_listview']])) {
			$target_listview_key = $_REQUEST['biztool_listview'];
		}
		if ('' == $target_listview_key) {
			if (is_array($array_listviews)) {
				$target_listview_key = key(array_slice($array_listviews, 0, 1));
			}
		}
		return $target_listview_key;
	}

	/**
	 * @return array
	 */
	function get_vars() {
		return $this->_vars;
	}

	/**
	 * @return array
	 */
	function set_vars($vars) {
		$this->_vars = $vars;
	}

	/**
	 * @param $field_num
	 */
	function get_obj_field($field_num) {
		if (is_numeric($field_num)) {
			$field_key = Biztool_Field::get_field_key_from_cnt($this->_vars['post_type'], $field_num);
		} else {
			$field_key = $field_num;
		}
		if (!isset($this->_vars['fields'][$field_key])) {
			return new WP_Error('', 'フィールドがタイプが登録されていません。:' . $field_key);
		}
		if (isset($this->_array_obj_fields[$field_key])) {
			return $this->_array_obj_fields[$field_key];
		} else {
			$this->_array_obj_fields[$field_key] = Biztool_Field::factory($this->_vars['fields'][$field_key]['field_type'], $this->_vars['post_type'], $this->_vars['fields'][$field_key]['field_num']);
			return $this->_array_obj_fields[$field_key];
		}
	}

	/**
	 * listviewcellオブジェクトを取得
	 *
	 * @param $listviewcell_num
	 *
	 * @return WP_Error|mixed
	 */
	function get_obj_listviewcell($listview_key, $listviewcell_key) {


		if (!isset($this->_vars['listviews'][$listview_key]['listviewcells'][$listviewcell_key])) {
			return new WP_Error('', 'フィールドがタイプが登録されていません。:' . $listview_key . ' ' . $listviewcell_key);
		}
		$array_listviewcell = $this->_vars['listviews'][$listview_key]['listviewcells'][$listviewcell_key];
		if (isset($this->_array_obj_listviewcells[$listview_key][$listviewcell_key])) {
			return $this->_array_obj_listviewcells[$listview_key][$listviewcell_key];
		} else {
			if (!isset($this->_array_obj_listviewcells[$listview_key]))
				$this->_array_obj_listviewcells[$listview_key] = array();

			$this->_array_obj_listviewcells[$listview_key][$listviewcell_key] = Biztool_Listviewcell::factory($array_listviewcell['listviewcell_type'], $this->_vars['post_type'], $array_listviewcell['listview_num'], $array_listviewcell['listviewcell_num']);

			return $this->_array_obj_listviewcells[$listview_key][$listviewcell_key];
		}
	}

	/**
	 * listviewcellオブジェクトを取得
	 *
	 * @param $listviewcell_num
	 *
	 * @return WP_Error|mixed
	 */
	function get_obj_listviewfilter($listview_key, $listviewfilter_key) {


		if (!isset($this->_vars['listviews'][$listview_key]['listviewfilters'][$listviewfilter_key])) {
			return new WP_Error('', 'フィールドがタイプが登録されていません。:' . $listview_key . ' ' . $listviewfilter_key);
		}
		$array_listviewfilter = $this->_vars['listviews'][$listview_key]['listviewfilters'][$listviewfilter_key];
		if (isset($this->_array_obj_listviewfilters[$listview_key][$listviewfilter_key])) {
			return $this->_array_obj_listviewfilters[$listview_key][$listviewfilter_key];
		} else {
			if (!isset($this->_array_obj_listviewfilters[$listview_key]))
				$this->_array_obj_listviewfilters[$listview_key] = array();

			$this->_array_obj_listviewfilters[$listview_key][$listviewfilter_key] = Biztool_Listviewfilter::factory($array_listviewfilter['listviewfilter_type'], $this->_vars['post_type'], $array_listviewfilter['listview_num'], $array_listviewfilter['listviewfilter_num']);

			return $this->_array_obj_listviewfilters[$listview_key][$listviewfilter_key];
		}
	}

	/**
	 * @param $field_key
	 */
	function get_fields() {

		return $this->_vars['fields'];
	}

	function __get($key) {
		if (array_key_exists($key, $this->_vars)) {
			return $this->_vars[$key];
		} else {
			//eyeta_biztool_log('Biztool_Table:__get: no key: ' . $key);
			return null;
		}
	}

	function __set($key, $value) {
		$this->_vars[$key] = $value;
	}

	function save() {
		global $eyeta_biztool;

		$tables = $eyeta_biztool->get_option('tables');
		$tables[$this->_vars['post_type']] = $this->_vars;

		$eyeta_biztool->update_option('tables', $tables);

		$eyeta_biztool->set_table($this->_vars['post_type'], $this);

		return true;
	}

}
