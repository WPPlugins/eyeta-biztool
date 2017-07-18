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
 * プラグイン初期設定
 * （初期配列等セット）
 */
function init() {

	// 表の種別マスタ
	add_filter('eyeta_biztool/init/table_types', '\eyeta_biztool\init_table_types');

	// ajaxの一覧
	add_filter('eyeta_biztool/init/ajaxs', '\eyeta_biztool\init_ajaxs');

	// 権限設定
	add_filter('eyeta_biztool/init/acl', '\eyeta_biztool\init_acl');

	// 列の種別マスタ
	add_filter('eyeta_biztool/init/field_types', '\eyeta_biztool\init_field_types');

	// 一覧表のセルの種別マスタ
	add_filter('eyeta_biztool/init/listviewcell_types', '\eyeta_biztool\init_listviewcell_types');

	// 一覧表のフィルタの種別マスタ
	add_filter('eyeta_biztool/init/listviewfilter_types', '\eyeta_biztool\init_listviewfilter_types');
	
	// 編集画面のCSS追加
	// 'eyeta_biztool/edit/filter_css_on'
	// 'eyeta_biztool/edit/css'
	
	// セキュア画像サブフォルダ名
	// 'eyeta_biztool/edit/secure_upload_dir'

	// セキュア画像のタイトルの頭につける文字列
	// 'eyeta_biztool/edit/secure_file_title_pre'

	// セキュア画像のタイトルの頭につける文字列
	// 'eyeta_biztool/register_post_type/args'

	// フィールド別で追加するJS（あれば）
	add_filter('eyeta_biztool/init/field_js', '\eyeta_biztool\init_field_js');

	// クラス等のrequireリスト
	add_filter('eyeta_biztool/init/require', '\eyeta_biztool\init_require');

	// 投稿からのSELECTボックスなどの表示上限（保険）
	// 'eyeta_biztool/edit/max_select_option_count'

	// 一覧表示の際の画像サイズ
	// 'eyeta_biztool/list/thum_size'

	add_image_size( 'eyeta_biztool_thum', 150, 150, false );
}


function init_require($array_requires=array()) {

	$path = dirname(__FILE__);
	$array_requires[] = $path . '/core/class/table.php';
	$array_requires[] = $path . '/core/class/table/simple.php';
	$array_requires[] = $path . '/core/class/field.php';
	$array_requires[] = $path . '/core/class/field/text.php';
	$array_requires[] = $path . '/core/class/field/date.php';
	$array_requires[] = $path . '/core/class/field/textarea.php';
	$array_requires[] = $path . '/core/class/field/number.php';
	$array_requires[] = $path . '/core/class/field/email.php';
	$array_requires[] = $path . '/core/class/field/password.php';
	$array_requires[] = $path . '/core/class/field/image.php';
	$array_requires[] = $path . '/core/class/field/file.php';
	$array_requires[] = $path . '/core/class/field/select.php';
	$array_requires[] = $path . '/core/class/field/checkbox.php';
	$array_requires[] = $path . '/core/class/field/radio.php';
	$array_requires[] = $path . '/core/class/field/true_false.php';
	$array_requires[] = $path . '/core/class/field/post.php';
	$array_requires[] = $path . '/core/class/field/user.php';

	$array_requires[] = $path . '/core/class/listviewcell.php';
//	$array_requires[] = $path . '/core/class/listviewcell/simpletext.php';
	$array_requires[] = $path . '/core/class/listviewcell/simple.php';
	$array_requires[] = $path . '/core/class/listviewcell/inlineedit.php';


	$array_requires[] = $path . '/core/class/listviewfilter.php';
	$array_requires[] = $path . '/core/class/listviewfilter/textfield.php';
	$array_requires[] = $path . '/core/class/listviewfilter/checkbox.php';

	$array_requires[] = $path . '/includes/wpalchemy/MetaBox.php';
	$array_requires[] = $path . '/includes/wpalchemy/MediaAccess.php';

	require_once $path . '/functions.php';


	return $array_requires;
}


function init_field_js($array_field_jses = array()) {
	global $eyeta_biztool;

	// 投稿タイプフィールド
	$array_field_jses['post_field'] = array(
		'key' => 'eyeta-biztool-setting-field-post',
		'url' => $eyeta_biztool->get_plugin_url() . '/js/setting-field-post.js',
		'deps' => array( 'eyeta-biztool-common' ),
		'ver' => $eyeta_biztool->get_ver()
	);

	return $array_field_jses;
}


function init_listviewfilter_types($args = array()) {
	$array_listviewfilters = array();

	// テキストフィールド
	/*$array_listviewfilters['body_text'] = array(
		'group' => '全体検索',
		'name' => 'テキスト曖昧検索',
		//'description' => 'テキストとして単純表示',
		'class' => '\eyeta_biztool\Biztool_Listviewfilter_Textbody'
	);*/
	$array_listviewfilters['text_field'] = array(
		'group' => __('In the column Search', 'eyeta-biztool'),
		'name' => __('Fuzzy search for the specified column', 'eyeta-biztool'),
		//'description' => 'テキストとして単純表示',
		'class' => '\eyeta_biztool\Biztool_Listviewfilter_Textfield'
	);

	/*$array_listviewfilters['checkbox'] = array(
		'group' => '列内検索',
		'name' => 'チェックボックス検索',
		//'description' => 'テキストとして単純表示',
		'class' => '\eyeta_biztool\Biztool_Listviewfilter_Checkbox'
	);*/


	return array_merge($args, $array_listviewfilters);

}


function init_listviewcell_types($args = array()) {
	$array_listviewcells = array();

	// テキストフィールド
/*	$array_listviewcells['simple_text'] = array(
		'group' => '表示',
		'name' => 'テキスト',
		'orderby' => true,
		//'description' => 'テキストとして単純表示',
		'class' => '\eyeta_biztool\Biztool_Listviewcell_SimpleText'
	);*/

	$array_listviewcells['simple'] = array(
		'group' =>  __('dipslay', 'eyeta-biztool'),
		'name' => __('Simple display ( specified simply display one column )', 'eyeta-biztool'),
		'orderby' => true,
		//'description' => 'テキストとして単純表示',
		'class' => '\eyeta_biztool\Biztool_Listviewcell_Simple'
	);

	$array_listviewcells['inline_edit'] = array(
		'group' => __('Edit in the list', 'eyeta-biztool'),
		'name' => 'Edit in the list',
		'orderby' => true,
		//'description' => 'テキストとして単純表示',
		'class' => '\eyeta_biztool\Biztool_Listviewcell_Inlineedit'
	);


	return array_merge($args, $array_listviewcells);

}

/**
 * 表の列の種別マスタ
 */
function init_field_types($args = array()) {
	global $eyeta_biztool;

	$array_fields = array();

	// テキストフィールド
	$array_fields['text'] = array(
		'group' => __('Basic', 'eyeta-biztool'),
		'name' => __('Text', 'eyeta-biztool'),
		'description' => __('Textbox', 'eyeta-biztool'),
		'class' => '\eyeta_biztool\Biztool_Field_Text'
	);
	
	// テキストエリアフィールド
	$array_fields['textarea'] = array(
		'group' => __('Basic', 'eyeta-biztool'),
		'name' => __('Textarea', 'eyeta-biztool'),
		'description' => __('Multiline text', 'eyeta-biztool'),
		'class' => '\eyeta_biztool\Biztool_Field_Textarea'
	);

	// 数値
	$array_fields['number'] = array(
		'group' => __('Basic', 'eyeta-biztool'),
		'name' => __('Numeric', 'eyeta-biztool'),
		'description' => '',
		'class' => '\eyeta_biztool\Biztool_Field_Number'
	);

	// メールアドレス
	$array_fields['email'] = array(
		'group' => __('Basic', 'eyeta-biztool'),
		'name' => __('Email', 'eyeta-biztool'),
		'description' => '',
		'class' => '\eyeta_biztool\Biztool_Field_Email'
	);

	// メールアドレス
	$array_fields['password'] = array(
		'group' => __('Basic', 'eyeta-biztool'),
		'name' => __('Password', 'eyeta-biztool'),
		'description' => '',
		'class' => '\eyeta_biztool\Biztool_Field_Password'
	);

	// 日付
	$array_fields['date'] = array(
		'group' => __('Basic', 'eyeta-biztool'),
		'name' => __('Date', 'eyeta-biztool'),
		'description' => '日付設定（DB内はyyyymmddで保持）',
		'class' => '\eyeta_biztool\Biztool_Field_Date'
	);

	// 画像
	$array_fields['image'] = array(
		'group' => __('File', 'eyeta-biztool'),
		'name' => __('Image', 'eyeta-biztool'),
		'description' => '',
		'class' => '\eyeta_biztool\Biztool_Field_Image'
	);

	// ファイル
	$array_fields['file'] = array(
		'group' => __('File', 'eyeta-biztool'),
		'name' => __('File', 'eyeta-biztool'),
		'description' => '',
		'class' => '\eyeta_biztool\Biztool_Field_File'
	);

	// selectbox
	$array_fields['select'] = array(
		'group' => __('Choices', 'eyeta-biztool'),
		'name' => __('Selectbox', 'eyeta-biztool'),
		'description' => '',
		'class' => '\eyeta_biztool\Biztool_Field_Select'
	);

	// checkbox
	$array_fields['checkbox'] = array(
		'group' => __('Choices', 'eyeta-biztool'),
		'name' => __('Checkbox', 'eyeta-biztool'),
		'description' => '',
		'class' => '\eyeta_biztool\Biztool_Field_Checkbox'
	);

	// radio
	$array_fields['radio'] = array(
		'group' => __('Choices', 'eyeta-biztool'),
		'name' => __('Radio button', 'eyeta-biztool'),
		'description' => '',
		'class' => '\eyeta_biztool\Biztool_Field_Radio'
	);

	// truefalse
	$array_fields['truefalse'] = array(
		'group' => __('Choices', 'eyeta-biztool'),
		'name' => __('Yes/No', 'eyeta-biztool'),
		'description' => '',
		'class' => '\eyeta_biztool\Biztool_Field_Truefalse'
	);

	// post
	$array_fields['post'] = array(
		'group' => __('Relation', 'eyeta-biztool'),
		'name' => __('Post', 'eyeta-biztool'),
		'description' => __('You can link the post of the other Posttype', 'eyeta-biztool'),
		'class' => '\eyeta_biztool\Biztool_Field_Post'
	);

	// user
	$array_fields['user'] = array(
		'group' => __('Relation', 'eyeta-biztool'),
		'name' => __('User', 'eyeta-biztool'),
		'description' => '',
		'class' => '\eyeta_biztool\Biztool_Field_User'
	);



	return array_merge($args, $array_fields);

}

/**
 * 表の種別マスタ
 */
function init_table_types($args = array()) {
	return array_merge($args, array(
		'simple' => array(
			'name' => __('Simple table', 'eyeta-biztool'),
			'description' => '',
			'class' => '\eyeta_biztool\Biztool_Table_Simple'
		)
	));
}

/**
 * ajaxの一覧
 */
function init_ajaxs($args = array()) {
	$array_ajaxes = array();


	// テーブル追加
	$array_ajaxes['add_table'] = array(
		'action' => 'add_table',
		'function' => '\eyeta_biztool\ajax_add_table',
		'require' => dirname(__FILE__) . '/core/ajax/add_table.php'
	);
	
	// テーブル名の更新　
	$array_ajaxes['update_table_name'] = array(
		'action' => 'update_table_name',
		'function' => '\eyeta_biztool\ajax_update_table_name',
		'require' => dirname(__FILE__) . '/core/ajax/update_table_name.php'
	);

	// 設定画面のフィールド欄ベース側を返す
	$array_ajaxes['get_field_base'] = array(
		'action' => 'get_field_base',
		'function' => '\eyeta_biztool\ajax_get_field_base',
		'require' => dirname(__FILE__) . '/core/ajax/get_field_base.php'
	);

	// 設定画面のフィールド欄フィールドタイプ別のフォームを返す
	$array_ajaxes['get_field_form'] = array(
		'action' => 'get_field_form',
		'function' => '\eyeta_biztool\ajax_get_field_form',
		'require' => dirname(__FILE__) . '/core/ajax/get_field_form.php'
	);

	// 設定画面のフィールド欄フィールドタイプ別のフォームを返す
	$array_ajaxes['save_table_setting'] = array(
		'action' => 'save_table_setting',
		'function' => '\eyeta_biztool\ajax_save_table_setting',
		'require' => dirname(__FILE__) . '/core/ajax/save_table_setting.php'
	);

	// 表示列の組み合わせ欄ベース側を返す
	$array_ajaxes['get_listview_base'] = array(
		'action' => 'get_listview_base',
		'function' => '\eyeta_biztool\ajax_get_listview_base',
		'require' => dirname(__FILE__) . '/core/ajax/get_listview_base.php'
	);

	// 表示列欄ベースを返す
	$array_ajaxes['get_listviewcell_base'] = array(
		'action' => 'get_listviewcell_base',
		'function' => '\eyeta_biztool\ajax_get_listviewcell_base',
		'require' => dirname(__FILE__) . '/core/ajax/get_listviewcell_base.php'
	);

	// 設定画面の表示列欄タイプ別のフォームを返す
	$array_ajaxes['get_listviewcell_form'] = array(
		'action' => 'get_listviewcell_form',
		'function' => '\eyeta_biztool\ajax_get_listviewcell_form',
		'require' => dirname(__FILE__) . '/core/ajax/get_listviewcell_form.php'
	);

	// フィルター欄ベースを返す
	$array_ajaxes['get_listviewfilter_base'] = array(
		'action' => 'get_listviewfilter_base',
		'function' => '\eyeta_biztool\ajax_get_listviewfilter_base',
		'require' => dirname(__FILE__) . '/core/ajax/get_listviewfilter_base.php'
	);


	// フィルタータイプ別フォーム
	$array_ajaxes['get_listviewfilter_form'] = array(
		'action' => 'get_listviewfilter_form',
		'function' => '\eyeta_biztool\ajax_get_listviewfilter_form',
		'require' => dirname(__FILE__) . '/core/ajax/get_listviewfilter_form.php'
	);


	// 投稿タイプ型フィールドのSELECTボックス内に出すフィールドの選択肢optionタグ
	$array_ajaxes['ajax_get_post_type_field_options'] = array(
		'action' => 'ajax_get_post_type_field_options',
		'function' => '\eyeta_biztool\ajax_get_post_type_field_options',
		'require' => dirname(__FILE__) . '/core/ajax/get_post_type_field_options.php'
	);

	// インライン編集フィールド保存
	$array_ajaxes['ajax_save_field'] = array(
		'action' => 'ajax_save_field',
		'function' => '\eyeta_biztool\ajax_save_field',
		'require' => dirname(__FILE__) . '/core/ajax/save_field.php'
	);

	// グループの追加
	$array_ajaxes['add_group'] = array(
		'action' => 'add_group',
		'function' => '\eyeta_biztool\ajax_add_group',
		'require' => dirname(__FILE__) . '/core/ajax/add_group.php'
	);

	// グループの追加
	$array_ajaxes['update_group_name'] = array(
		'action' => 'update_group_name',
		'function' => '\eyeta_biztool\ajax_update_group_name',
		'require' => dirname(__FILE__) . '/core/ajax/update_group_name.php'
	);
	
	// 表アクセス権変更
	$array_ajaxes['update_table_acl'] = array(
		'action' => 'update_table_acl',
		'function' => '\eyeta_biztool\ajax_update_table_acl',
		'require' => dirname(__FILE__) . '/core/ajax/update_table_acl.php'
	);
	
	// 列アクセス権変更
	$array_ajaxes['update_field_acl'] = array(
		'action' => 'update_field_acl',
		'function' => '\eyeta_biztool\ajax_update_field_acl',
		'require' => dirname(__FILE__) . '/core/ajax/update_field_acl.php'
	);
	
	
	
	
	return array_merge($args, $array_ajaxes);
}


/**
 * 権限設定
 */
function init_acl($args = array()) {
	$array_acls = array();


	$array_acls['eyeta-biztool/setting/base'] = array(
		'caption' => '設定画面利用',
		'cap' => 'administrator'
	);


	$array_acls['eyeta-biztool/setting/base/table'] = array(
		'caption' => '設定画面：表の管理',
		'cap' => 'administrator'
	);

	$array_acls['eyeta-biztool/setting/table'] = array(
		'caption' => '表の詳細の編集',
		'cap' => 'administrator'
	);

	$array_acls['eyeta-biztool/setting/base/group'] = array(
		'caption' => 'グループの設定',
		'cap' => 'administrator'
	);

	$array_acls['eyeta-biztool/table/show_posts'] = array( // |post_typeをつける 
		'caption' => '一覧表示',
		'cap' => 'edit_%%post_type%%s'
	);

	$array_acls['eyeta-biztool/table/edit_post'] = array(
		array( // |post_typeをつける 
			'caption' => '他者投稿編集',
			'cap' => 'edit_others_%%post_type%%s'
		),
		array( // |post_typeをつける 
			'caption' => '編集',
			'cap' => 'edit_%%post_type%%'
		)
	);

	$array_acls['eyeta-biztool/table/add_post'] = array( // |post_typeをつける 
		'caption' => '投稿追加',
		'cap' => 'publish_%%post_type%%s'
	);

	$array_acls['eyeta-biztool/table/delete_post'] = array(
		array( // |post_typeをつける 
			'caption' => '投稿削除',
			'cap' => 'delete_%%post_type%%s'
		),
		array( // |post_typeをつける 
			'caption' => '投稿削除',
			'cap' => 'delete_%%post_type%%'
		),
		array( // |post_typeをつける 
			'caption' => '投稿削除',
			'cap' => 'delete_published_%%post_type%%s'
		),
	);
	

	$array_acls['eyeta-biztool/table/show_field'] = array( // |post_typeをつける 
		'caption' => 'フィールド表示権限',
		'cap' => 'biztool_show_field_%%field%%'
	);

	$array_acls['eyeta-biztool/table/edit_field'] = array( // |post_typeをつける 
		'caption' => 'フィールド編集権限',
		'cap' => 'biztool_edit_field_%%field%%'
	);

	// そのたACL
	// テーブルリスト表示権限

	return array_merge($args, $array_acls);
}

