<?php
/*
Plugin Name: Eyeta-biztool
Description: easy-to-use custom fields.
Version: 0.1.2
Plugin URI:
Author: Eyeta Co.,Ltd.
Author URI: http://www.eyeta.jp/
License: GPLv2 or later
Text Domain: eyeta-biztool
*/
/*  Copyright 2016 Yuichiro ABE (email : y.abe@eyeta.jp)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
namespace eyeta_biztool;
/**
 *
 *
 * Created by PhpStorm.
 * Author: Eyeta Co.,Ltd.(http://www.eyeta.jp)
 *
 */



\register_activation_hook( __FILE__, '\eyeta_biztool\eyeta_biztool_activate' );
function eyeta_biztool_activate() {
	// プラグイン初期化

}

\register_deactivation_hook( __FILE__, '\eyeta_biztool\eyeta_biztool_deactivate' );
function eyeta_biztool_deactivate() {
	// プラグイン削除
}

class eyeta_biztool {

	protected $_plugin_dirname;
	protected $_plugin_url;
	protected $_plugin_path;

	/**
	 * 読み込まれた設定値
	 *
	 * @var bool
	 */
	protected $_array_options = false;

	/**
	 * 登録されている表インスタンス
	 * @var array
	 */
	protected $_array_obj_tables = array();

	protected $_version = '0.1.2';

	function get_ver() {
		return $this->_version;
	}

	// todo 一覧の項目カスタムから続き

	public function __construct() {
		// 初期パス等セット
		$this->init();

		require_once "init.php";
		init();

		// core class require
		require_once $this->get_plugin_path() . '/require.php';
		require_phps($this->get_plugin_path());

		// フックセット等
		\add_action("init", array(&$this, "init_action"));
		\add_action('wp_print_scripts', array(&$this, 'wp_print_scripts'));

		// 管理画面追加
		\add_action("admin_menu", array(&$this, "admin_menu"));

		// css, js
		\add_action('admin_enqueue_scripts', array(&$this, 'head_css'));
		\add_action('admin_enqueue_scripts', array(&$this, "head_js"));

		// ajax追加
		add_action('wp_ajax_eyeta_biztool', array(&$this, 'init_ajax'));
		add_action('wp_ajax_eyeta_biztool_img', array(&$this, 'eyeta_biztool_img'));


		// debug用
		//add_action('wp_ajax_eyeta_biztool_options', array(&$this, 'ajax_options'));

		// 投稿タイプセット
		require_once 'setup.php';
		\add_action("init", '\eyeta_biztool\setup');

		load_plugin_textdomain('eyeta-biztool', false, basename( dirname( __FILE__ ) ) . '/languages');


		// 一覧ページカスタマイズ
		require_once $this->get_plugin_path() . '/view/css-edit.php';
		\add_action('admin_head', "\eyeta_biztool\css_edit");
		
		// カスタムフィールド検索対象処理
		add_action( 'save_post', array(&$this, 'save_post'), 9999 );
		add_action( 'pre_get_posts', array(&$this, 'pre_get_posts'));

		// 画像セキュア処理
		add_filter('upload_dir', array(&$this, 'upload_dir') );
		add_filter( 'upload_post_params', array(&$this, 'upload_post_params') );
		add_action( 'init', array(&$this, 'init_for_secure_media') );
		add_filter( 'wp_insert_attachment_data', array(&$this, 'wp_insert_attachment_data'), 999, 2 );
		

	}

	
	

	/**
	 * セキュア画像ではタイトルに[secure]を追加
	 *
	 * @param $data
	 * @param $postarr
	 */
	function wp_insert_attachment_data($data, $postarr) {
		if(isset($_REQUEST['eyeta_biztool_img_secure']) && '' != $_REQUEST['eyeta_biztool_img_secure']) {
			$data['post_title'] = apply_filters('eyeta_biztool/edit/secure_file_title_pre', '[secure]') . $data['post_title'];
		}
		
		return $data;
	}

	/**
	 * セキュア画像を呼び出そうとしているときはタブを制限
	 *
	 */
	function init_for_secure_media() {
		if(isset($_REQUEST['eyeta_biztool_img_secure']) && '' != $_REQUEST['eyeta_biztool_img_secure']) {
			add_filter( 'media_upload_tabs', array(&$this, 'media_upload_tabs'), 9999 );
		}

	}
	function media_upload_tabs($_default_tabs = array()) {
		$_default_tabs = array(
			'type' => __('From Computer')
		);
		return $_default_tabs;
	}


	/**
	 * 投稿タイプの一覧取得
	 *
	 * @param $post_types
	 * @param array $exclude
	 * @param array $include
	 *
	 * @return array
	 */
	function get_post_types( $post_types = array(), $exclude = array(), $include = array() ) {
		// get all custom post types
		$post_types = array_merge($post_types, get_post_types());


		// core include / exclude
		$includes = array_merge( array(), $include );
		$excludes = array_merge( array( 'revision', 'nav_menu_item' ), $exclude );


		// include
		foreach( $includes as $p )
		{
			if( post_type_exists($p) )
			{
				$post_types[ $p ] = $p;
			}
		}


		// exclude
		foreach( $excludes as $p )
		{
			unset( $post_types[ $p ] );
		}

		foreach($post_types as $key => $post_type_name) {
			if(isset($this->_array_obj_tables[$key])) {
				$post_types[$key] = $this->_array_obj_tables[$key]->table_name;
			}
		}


		return $post_types;

	}


	/**
	 * セキュア画像表示Ajax　
	 * （この関数が呼ばれる時点でログインしているものとする）
	 *
	 */
	function eyeta_biztool_img() {
		$path = urldecode($_REQUEST['path']);
		if('' == $path) {
			wp_die('no path');
			exit;
		}
                

		$uploads = wp_upload_dir();
		$secure_subpath = apply_filters('eyeta_biztool/edit/secure_upload_dir', '/biztool');
		$path = $uploads['basedir'] . $secure_subpath . '/' . $path;
		$filename = basename($path);

		$filetype = wp_check_filetype( $filename );

		header( "Content-Type: " . $filetype['type'] );//ダウンロードの指示
		//$d = new \DateTime('now', new \DateTimeZone("Asia/Tokyo"));
		//header( 'Content-Disposition: attachment; filename*=UTF-8\'\'' . rawurlencode( $filename ) );
		header( "Content-Length: " . filesize( $path ) );//ダウンロードするファイルのサイズ
		ob_end_clean();//ファイル破損エラー防止
		readfile( $path );

		exit;
	}
	/**
	 * 画像保存先セキュア処理
	 */
	/**
	 * アップロードパラメーターにセキュア引数を追加
	 *
	 * @param $post_params
	 */
	function upload_post_params($post_params) {
		if($_REQUEST['eyeta_biztool_img_secure']) {
			$post_params['eyeta_biztool_img_secure'] = $_REQUEST['eyeta_biztool_img_secure'];
		}

		return $post_params;
	}

	/**
	 * フォルダをセキュアな領域へ変更
	 *
	 * @param $array_uploads
	 *
	 * @return mixed
	 */
	function upload_dir($array_uploads) {

		// dir変更
		if($_REQUEST['eyeta_biztool_img_secure']) {
			if(isset($_FILES) && count($_FILES) != 0) {
				$secure_subpath = apply_filters('eyeta_biztool/edit/secure_upload_dir', '/biztool');
				if(!file_exists($array_uploads['basedir'] . $secure_subpath)) {
					if(!mkdir($array_uploads['basedir'] . $secure_subpath, 0777, true)) {
						wp_die('アップロードフォルダの作成に失敗しました。');
					}
					// index.php .htaccess
					if(!($fp = fopen($array_uploads['basedir'] . $secure_subpath . '/index.php', 'w'))) {
						wp_die('.htaccessファイルの作成に失敗しました。');
					}
					fwrite($fp, ' ');
					fclose($fp);
					if(!($fp = fopen($array_uploads['basedir'] . $secure_subpath . '/.htaccess', 'w'))) {
						wp_die('.htaccessファイルの作成に失敗しました。');
					}
					fwrite($fp, '<IfModule mod_rewrite.c>' . "\n");
					fwrite($fp, 'RewriteEngine On' . "\n");
					fwrite($fp, 'RewriteRule ^index\.php$ - [L]' . "\n");
					fwrite($fp, 'RewriteRule (.*) ' . admin_url('admin-ajax.php') . '?action=eyeta_biztool_img&path=$1' . "\n");
					fwrite($fp, '</IfModule>' . "\n");
					fclose($fp);

				}
				$array_uploads['subdir'] = $secure_subpath . $array_uploads['subdir'];
				$array_uploads['path'] = $array_uploads['basedir'] . $array_uploads['subdir'];
				$array_uploads['url'] = $array_uploads['baseurl'] . $array_uploads['subdir'];
			}
		}

		return $array_uploads;
	}


	/**
	 * カスタムフィールドを全文検索対象にする処理
	 *
	 * @var bool
	 */
	protected $_post_saved = false;
	protected $_post_content_str = '';
	function save_post($target_post_id) {
		$this->_post_content_str = '';
		if($this->_post_saved) {
			return true;
		}

		$target_post = get_post($target_post_id);
		$array_obj_tables = $this->get_tables();
		if(isset($array_obj_tables[$target_post->post_type])) {
			// 本プラグイン処理対象の投稿タイプ
			$obj_target_table = $array_obj_tables[$target_post->post_type];
			//$array_fields = get_post_custom($target_post_id);
			foreach($obj_target_table->fields as $field) {
				$obj_field = $obj_target_table->get_obj_field($field['field_num']);
				if($obj_field->for_search_field) {
					$this->_post_content_str .= (string)$obj_field->get_search_str($target_post_id) . "\n";
					//array_walk_recursive ( $obj_field->get_search_str($target_post_id), array(&$this, 'save_content_array'));
				}
			}
		}

		$this->_post_saved = true;
		update_post_meta((int)$target_post_id, '_biztool_for_search', $this->_post_content_str);
		/*wp_update_post(array(
			'ID' => (int)$target_post_id,
			'post_content' => $this->_post_content_str
		));*/

		return true;
	}
	function save_content_array($item, $key) {
		if(!is_array(maybe_unserialize($item))) {
			$this->_post_content_str .= (string)$item . "\n";
		} else {
			array_walk_recursive ( maybe_unserialize($item), array(&$this, 'save_content_array'));
		}
	}

	/**
	 * フィルタ処理
	 *
	 * @param $query
	 *
	 */
	function pre_get_posts($query) {

		$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : NULL ;
		$uri_parts = parse_url($uri);
		$file = basename($uri_parts['path']);
		$array_obj_tables = $this->get_tables();

		// 一覧画面
		if(is_admin() && 'edit.php' == $file && isset($array_obj_tables[$_REQUEST['post_type']])) {
			// 対象
			$s = $query->get('s');
			if('' != $s) {
				$meta_query = $query->get('meta_query');
				$meta_query[] = array(
					'key'=> '_biztool_for_search',
					'value' => $s,
					'compare' => 'LIKE'
				);
				$query->set('meta_query', $meta_query);
				$query->set('s', '');
			}
		}


	}

	/**
	 * デバック用オプション表示
	 */
	protected $_array_keys = array();
	function ajax_options() {
		//update_option('eyeta-biztool_options', array());
		$options = get_option('eyeta-biztool_options');
		$this->_array_keys = array();
		if($options) {
			$this->show_options($options);
		}
	}

	function show_options($current_options) {
		if(is_array($current_options)) {
			foreach($current_options as $key => $value) {
				array_push($this->_array_keys, $key);
				$this->show_options($current_options[$key]);
				array_pop($this->_array_keys);
			}

		} else {
			foreach($this->_array_keys as $key) {
				echo $key . ' -> ';
			}
			echo $current_options . '<br />';
		}
	}

	/**
	 * tableクラスを保持
	 *
	 * @param $key
	 * @param $table
	 */
	function set_table($key, $obj_table) {
		$this->_array_obj_tables[$key] = $obj_table;
	}

	function get_table($key) {
		if(isset($this->_array_obj_tables[$key])) {
			return $this->_array_obj_tables[$key];
		} else {
			return false;
		}
	}

	/**
	 * テーブルオブジェクトの配列を返す
	 * @return mixed
	 */
	function get_tables() {
		return $this->_array_obj_tables;
	}

	/**
	 * アクションの実行権限チェック
	 *
	 * @param $action
	 */
	function current_user_can($action) {
		$cap = $this->get_cap_by_action($action);
		if($cap) {
			if(current_user_can('administrator')) {
				// 管理者はbiztoolアクセス権はすべてOK
				return true;
			} else {
				return current_user_can($cap);
			}
		} else {
			return current_user_can($action);
		}
	}

	/**
	 * 指定アクションの権限cap名取得
	 *
	 * @param $action
	 *
	 * @return bool
	 */
	function get_cap_by_action($action) {
		$actions = apply_filters('eyeta_biztool/init/acl', array());
		$post_type = false;
		$field_key = false;
		if(strpos($action, '|') !== false) {
			// post_type指定あり
			$post_type = substr($action, strpos($action, '|') + 1);
			$action = substr($action, 0, strpos($action, '|'));
			if(strpos($post_type, '.') !== false) {
				// field_key指定あり 
				$field_key = substr($post_type, strpos($post_type, '.') + 1);
				$post_type = substr($post_type, 0, strpos($post_type, '.'));
			}
		}
		if(isset($actions[$action])) {
			if(!isset($actions[$action]['cap'])) {
				$array_caps = array();
				foreach($actions[$action] as $key => $biztool_action) {
					if($post_type) {
						$cap = str_replace('%%post_type%%', $post_type, $biztool_action['cap']);
						if($field_key) {
							$cap = str_replace('%%field%%', $field_key, $cap);
						}
						$array_caps[] = $cap;
					} else {
						$array_caps[] = $biztool_action['cap'];
					}
				}
				return $array_caps;
			} else {
				if($post_type) {
					$cap = str_replace('%%post_type%%', $post_type, $actions[$action]['cap']);
					if($field_key) {
						$cap = str_replace('%%field%%', $field_key, $cap);
					}
					return $cap;
				} else {
					return $actions[$action]['cap'];
				}
			}
			
		} else {
			return false;
		}
	}

	/**
	 * ajaxフック追加
	 *
	 * eyeta_biztool/add_table
	 */
	function init_ajax() {
		eyeta_biztool_log('init_ajax start action: ' . $_REQUEST['action']);

		$ajaxes = apply_filters('eyeta_biztool/init/ajaxs', array());
		foreach($ajaxes as $key => $array_ajax) {
			if($_REQUEST['eyeta_biztool_action'] == $key) {
				if("" != $array_ajax['require']) {
					require_once $array_ajax['require'];
				}
				call_user_func($array_ajax['function']);
				die;
			}
		}

		wp_die('no ajax');
	}

	/*
	 * 管理画面追加
	 */
	function admin_menu () {
		
		// 設定画面追加
		
		add_menu_page(
			__('BizTool Setting', 'eyeta-biztool'),
			__('BizTool Setting', 'eyeta-biztool'),
			$this->get_cap_by_action('eyeta-biztool/setting/base'),
			'eyeta-biztool',
			array(&$this, 'admin_menu_router'), apply_filters( 'eyeta-biztool/setting/position', '', 80.999) );

	}

	function admin_menu_router() {
		$str_page = $_REQUEST['biztool_page'];
		if(!isset($_REQUEST['biztool_page']) || '' == $_REQUEST['biztool_page']) {
			$str_page = 'setting/base';
		}
		if(!$this->current_user_can('eyeta-biztool/' . $str_page)) {
			wp_die('no permission');
		}

		$array_path = explode('/', $str_page);

		$target_file = $this->get_plugin_path() . '/view/' . implode('/', $array_path);
		$target_file .= '.php';
		$target_func = __NAMESPACE__ . "\\" . implode('_', $array_path);

		if(file_exists($target_file)) {
			require_once $target_file;
		}
		if(function_exists($target_func)) {
			call_user_func($target_func);
		}

	}

	/**
	 * 本プラグイン用のオプションを取得
	 *
	 * @param $key
	 * @param string $default
	 *
	 * @return mixed|string
	 */
	function get_option($key, $default = "") {
		if(!$this->_array_options) {
			$this->_array_options = get_option('eyeta-biztool_options');
			if(!$this->_array_options) {
				$this->_array_options = array();
			}
		}

		if(isset($this->_array_options[$key])) {
			return $this->_array_options[$key];
		} else {
			return $default;
		}
	}

	function update_option($key, $value) {
		if(!$this->_array_options) {
			$this->_array_options = get_option('eyeta-biztool_options');
		}
		if(!$this->_array_options) {
			$this->_array_options = array();
		}

		$this->_array_options[$key] = $value;

		update_option('eyeta-biztool_options', $this->_array_options);
	}

	/**
	 * 管理画面CSS追加
	 */
	function head_css () {
		if(is_admin() && isset($_REQUEST['page'])) {
			if('eyeta-biztool' == $_REQUEST['page']) {
				// jquery uiのバージョンでCSSを切り替えたい
				$scripts = wp_scripts();
				$jquery_ui_tabs = $scripts->query( 'jquery-ui-tabs' );
				if($jquery_ui_tabs) {
					wp_enqueue_style( 'eyeta-jquery-ui', 'https://code.jquery.com/ui/' . $jquery_ui_tabs->ver . '/themes/smoothness/jquery-ui.css');
				} else {
					wp_die('jquery uiのバージョンが取得出来ません。');
				}

				wp_enqueue_style( 'eyeta-biztool-pure', '//yui.yahooapis.com/pure/0.6.0/pure-min.css', '0.6.0');

			}

		}

		if(is_admin()) {
			wp_enqueue_style('wpalchemy-metabox', $this->get_plugin_url() . '/includes/wpalchemy/metaboxes/meta.css');
			wp_enqueue_style( 'eyeta-biztool-style', $this->get_plugin_url() . '/assets/css/screen.css');
		}

		$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : NULL ;
		$uri_parts = parse_url($uri);
		$file = basename($uri_parts['path']);
		$array_obj_tables = $this->get_tables();

		// 一覧画面
		if(is_admin() && 'edit.php' == $file && isset($array_obj_tables[$_REQUEST['post_type']])) {
			$scripts = wp_scripts();
			$jquery_ui = $scripts->query( 'jquery-ui-tabs' );
			if($jquery_ui) {
				wp_enqueue_style( 'eyeta-jquery-ui', 'https://code.jquery.com/ui/' . $jquery_ui->ver . '/themes/smoothness/jquery-ui.css');
			} else {
				wp_die('jquery uiのバージョンが取得出来ません。');
			}
			
			wp_enqueue_style( 'eyeta-biztool-style', $this->get_plugin_url() . '/assets/css/screen.css');
			wp_add_inline_style( 'eyeta-biztool-style', '.wp-list-table { display: none; }' );


		}
		// 投稿画面
		if(is_admin() && isset($array_obj_tables[\WPAlchemy_MetaBox::_get_current_post_type()]) ) {
			$scripts = wp_scripts();
			$jquery_ui = $scripts->query( 'jquery-ui-datepicker' );
			if($jquery_ui) {
				wp_enqueue_style( 'eyeta-jquery-ui', 'https://code.jquery.com/ui/' . $jquery_ui->ver . '/themes/smoothness/jquery-ui.css');
			} else {
				wp_die('jquery uiのバージョンが取得出来ません。');
			}
			wp_enqueue_style( 'eyeta-biztool-style', $this->get_plugin_url() . '/assets/css/screen.css');
		}

	}

	/*
	 * 管理画面JS追加
	 */
	function head_js () {
		if(is_admin() && isset($_REQUEST['page'])) {
			if(isset($_REQUEST['biztool_page'])) {
				$str_path = $_REQUEST['biztool_page'];
			} else {
				$str_path = 'setting/base';
			}
			if('eyeta-biztool' == $_REQUEST['page']) {
				wp_enqueue_script( 'jquery-ui-tabs');
				wp_enqueue_script( 'jquery-ui-accordion');
				wp_enqueue_script( 'jquery-ui-dialog');
				wp_enqueue_script( 'jquery-ui-sortable');
				wp_enqueue_script('jquery-ui-datepicker');
				//wp_enqueue_script( 'jquery-ui-');
				wp_enqueue_script( 'jquery-form');

				wp_enqueue_script( 'eyeta-biztool-jquery-validation', $this->get_plugin_url() . '/js/jquery-validation-1.15.0/dist/jquery.validate.js', array( 'jquery' ), "1.15.0" );
				wp_enqueue_script( 'eyeta-biztool-jquery-validation-ja', $this->get_plugin_url() . '/js/additional-methods-ja.js', array( 'jquery', 'eyeta-biztool-jquery-validation' ), "1.0" );

				wp_enqueue_script( 'eyeta-biztool-jquery-blockUI', $this->get_plugin_url() . '/js/jquery.blockUI.js', array( 'jquery' ) );


				wp_enqueue_script( 'eyeta-biztool-common', $this->get_plugin_url() . '/js/common.js', array( 'jquery', 'jquery-ui-tabs', 'jquery-ui-dialog', 'jquery-ui-accordion', 'jquery-ui-sortable', 'jquery-form', 'eyeta-biztool-jquery-validation-ja', 'eyeta-biztool-jquery-blockUI' ), $this->_version );

				switch($str_path) {
					case '':
					case 'setting/base':
						wp_enqueue_script( 'eyeta-biztool-setting-base', $this->get_plugin_url() . '/js/setting-base.js', array( 'eyeta-biztool-common' ), $this->_version);
						break;
					case 'setting/table':
						wp_enqueue_script( 'eyeta-biztool-setting-table', $this->get_plugin_url() . '/js/setting-table.js', array( 'eyeta-biztool-common' ), $this->_version);
						$array_jses = apply_filters('eyeta_biztool/init/field_js', array());
						foreach($array_jses as $key => $array_vals) {
							wp_enqueue_script( $array_vals['key'], $array_vals['url'], $array_vals['deps'], $array_vals['ver']);
						}

						break;
				}
			}

		}

		$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : NULL ;
		$uri_parts = parse_url($uri);
		$file = basename($uri_parts['path']);
		$array_obj_tables = $this->get_tables();

		// 一覧画面
		if(is_admin() && 'edit.php' == $file && isset($array_obj_tables[$_REQUEST['post_type']])) {
			// 一覧画面
			wp_enqueue_script( 'jquery-ui-tabs');
			wp_enqueue_script( 'jquery-ui-accordion');
			wp_enqueue_script( 'jquery-ui-dialog');
			wp_enqueue_script( 'jquery-ui-sortable');
			wp_enqueue_script('jquery-ui-datepicker');
			//wp_enqueue_script( 'jquery-ui-');
			wp_enqueue_script( 'jquery-form');
			wp_enqueue_script( 'eyeta-biztool-maskinput', $this->get_plugin_url() . '/js/jquery.maskedinput.js', array( 'jquery' ), '1.4.1');

			wp_enqueue_script( 'eyeta-biztool-jquery-validation', $this->get_plugin_url() . '/js/jquery-validation-1.15.0/dist/jquery.validate.js', array( 'jquery' ), "1.15.0" );
			wp_enqueue_script( 'eyeta-biztool-jquery-validation-ja', $this->get_plugin_url() . '/js/additional-methods-ja.js', array( 'jquery', 'eyeta-biztool-jquery-validation' ), "1.0" );
			wp_enqueue_script( 'eyeta-biztool-jquery-blockUI', $this->get_plugin_url() . '/js/jquery.blockUI.js', array( 'jquery' ) );
			wp_enqueue_script( 'eyeta-biztool-common', $this->get_plugin_url() . '/js/common.js', array( 'jquery', 'jquery-ui-tabs', 'jquery-ui-dialog', 'jquery-ui-accordion', 'jquery-ui-sortable', 'jquery-form', 'eyeta-biztool-jquery-validation-ja', 'eyeta-biztool-jquery-blockUI' ), $this->_version );
			wp_enqueue_script( 'eyeta-biztool-listview', $this->get_plugin_url() . '/js/listview.js', array( 'eyeta-biztool-common' ), $this->_version);
		}

		// 投稿編集画面
		if(is_admin() && isset($array_obj_tables[\WPAlchemy_MetaBox::_get_current_post_type()]) ) {
			// 一覧画面
			wp_enqueue_script( 'jquery-ui-tabs');
			wp_enqueue_script( 'jquery-ui-accordion');
			wp_enqueue_script( 'jquery-ui-dialog');
			wp_enqueue_script( 'jquery-ui-sortable');
			wp_enqueue_script('jquery-ui-datepicker');

			//wp_enqueue_script( 'jquery-ui-');
			wp_enqueue_script( 'jquery-form');

			wp_enqueue_script( 'eyeta-biztool-jquery-validation', $this->get_plugin_url() . '/js/jquery-validation-1.15.0/dist/jquery.validate.js', array( 'jquery' ), "1.15.0" );
			wp_enqueue_script( 'eyeta-biztool-jquery-validation-ja', $this->get_plugin_url() . '/js/additional-methods-ja.js', array( 'jquery', 'eyeta-biztool-jquery-validation' ), "1.0" );
			wp_enqueue_script( 'eyeta-biztool-jquery-blockUI', $this->get_plugin_url() . '/js/jquery.blockUI.js', array( 'jquery' ) );
			wp_enqueue_script( 'eyeta-biztool-common', $this->get_plugin_url() . '/js/common.js', array( 'jquery', 'jquery-ui-tabs', 'jquery-ui-dialog', 'jquery-ui-accordion', 'jquery-ui-sortable', 'jquery-form', 'eyeta-biztool-jquery-validation-ja', 'eyeta-biztool-jquery-blockUI' ), $this->_version );
			wp_enqueue_script( 'eyeta-biztool-post', $this->get_plugin_url() . '/js/post.js', array( 'eyeta-biztool-common' ), $this->_version);

			wp_enqueue_script( 'eyeta-biztool-maskinput', $this->get_plugin_url() . '/js/jquery.maskedinput.js', array( 'jquery' ), '1.4.1');

		}

	}

	/*
	 * initフック
	 */
	function init_action() {
		// 各種フックセット


	}

	/**
	 * javascriptへ変数類をセット
	 */
	function wp_print_scripts() {
		echo '<script type="text/javascript">
			var eyeta_biztool_plugin_url = "' . $this->get_plugin_url() . '";
			var eyeta_biztool_nonce = "' . $this->get_nonce() . '";
			var eyeta_biztool_admin_url = "' . admin_url() . '";
		</script>';
	}

	/*
	 * 初期化
	**/
	function init() {

		$array_tmp = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
		$this->_plugin_dirname = plugin_basename(__FILE__);
		$this->_plugin_url = plugin_dir_url(__FILE__);
		$this->_plugin_path = plugin_dir_path(__FILE__);

	}
	function get_plugin_url() {
		return $this->_plugin_url;
	}

	function get_plugin_dirname() {
		return $this->_plugin_dirname;
	}

	function get_plugin_path() {
		return $this->_plugin_path;
	}

	/**
	 * nonce取得
	 *
	 * @return string
	 */
	function get_nonce() {
		return \wp_create_nonce(\plugin_basename(__FILE__));
	}

	function verify_nonce($nonce) {
		return wp_verify_nonce($nonce, \plugin_basename(__FILE__));
	}

}

/*
 * エラー関数
 */
function eyeta_biztool_log($msg, $level = "DEBUG") {

	$level_array = array(
		"DEBUG" => 0,
		"DETAIL" => 1,
		"INFO" => 2,
		"ERROR" => 3
	);



	if($level_array[apply_filters("eyeta_biztool_log_level", "DEBUG")] <= $level_array[$level]) {
		if(mb_strlen($msg)< 800) {
			error_log($_SERVER["SERVER_NAME"] . " : " . $level . " : " . $msg);
		} else {
			$size = mb_strlen($msg);
			for($i=0; $i < $size; $i+=800) {
				error_log($_SERVER["SERVER_NAME"] . " : " . $level . " : " . mb_substr($msg, $i, 800));
			}
		}
	}


	$error_email = apply_filters("eyeta_biztool_error_mail_to", false);
	if($level == "ERROR" && $error_email !== false) {
		wp_mail( $error_email, 'eyeta_biztoolでエラー：' . $_SERVER["SERVER_NAME"], $_SERVER["SERVER_NAME"] . " : " . $level . " : " . $msg, 'From: ' . $error_email );
	}

}


global $eyeta_biztool;
$eyeta_biztool = new eyeta_biztool();
