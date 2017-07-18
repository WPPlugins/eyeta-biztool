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
class Biztool_Field_Image extends Biztool_Field {

// http://www.webopixel.net/wordpress/779.html

	function __construct($post_type = null, $field_num = null) {
		parent::__construct($post_type, $field_num);

		$this->_vars['field_type'] = 'image';

		$this->_vars['additional'] = array(
			'field_note' => array( 'type' => 'textarea', 'default' => '', 'required' => false),
			'is_required' => array( 'type' => 'checkbox', 'default' => '', 'required' => false),
			'is_secure' => array( 'type' => 'checkbox', 'default' => '', 'required' => false)
		);

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


		if($this->_vars['additional_settings']['size']) {
			$array_rule['size'] = $this->_vars['additional_settings']['size'];
		}

		if($array_rule) {
			$array_js_vars['validate']['rules']['_' . $post_type . '[' . $this->_vars['field_key'] . ']'] = $array_rule;
		}


		return $array_js_vars;
	}


	/*
	 * URLから画像IDを取得する。
	 */
	static function get_image_id_from_url($url) {
		global $wpdb;

		$parse_url = parse_url($url);
		$path = $parse_url['path'];

		$upload_dir = wp_upload_dir();
		$parse_dir = parse_url($upload_dir['baseurl']);
		$upload_path = $parse_dir['path'];

		$target_path = substr($path, strlen($upload_path));
		$basename = basename($target_path);
		$dirname = dirname($target_path);
		$filetype = wp_check_filetype( $basename );
		// $filetype['ext']
		$filename_body = substr($basename, 0, -1 * (1+strlen($filetype['ext'])));
		$filename_body = preg_replace('/\-[0-9]*x[0-9]*$/', '', $filename_body);

		$target_str = substr($target_path, 1, -1 * strlen($basename)) . $filename_body . '.' . $filetype['ext'];
		
		$sql = $wpdb->prepare("select meta_id, post_id from " . $wpdb->postmeta . " where meta_key='_wp_attached_file' and meta_value=%s", $target_str );

		$rsl = $wpdb->get_results($sql);
		if($rsl) {
			return $rsl[0]->post_id;
		} else {
			return false;
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

		$img_id = Biztool_Field_Image::get_image_id_from_url($str_field);
		if($img_id) {
			$array_src = wp_get_attachment_image_src( $img_id, apply_filters('eyeta_biztool/list/thum_size', 'eyeta_biztool_thum'));
			return '<img src="' . $array_src[0] . '" class="eyeta_biztool_thum" target-image-id="" />';
			
		} else {
			return '<img src="' . $str_field . '" class="eyeta_biztool_thum" target-image-id="" />';
		}
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
		global $wpalchemy_media_access;
		?>
		<p style="margin-bottom: 0;">
			<?php
		$html = parent::get_field_edit_form($mb, $html);

		$is_secure = $this->_vars['additional_settings']['is_secure'];
		$str_secure = "";
		if($is_secure) {
			$str_secure = 'type&eyeta_biztool_img_secure=1';
		}
		?>

		<label><?php echo esc_html($this->field_name);?></label>
		<?php $mb->the_field($this->_vars['field_key']); ?>
		<?php $wpalchemy_media_access->setGroupName($this->_vars['field_key'])->setInsertButtonLabel('追加'); ?>
		<div class="media-box">

		    <?php if ($mb->have_value()): ?>
			    <div class="pre-image">
		        <img src="<?php echo $mb->get_the_value(); ?>" class="w30" />
		        <div class="img-edit">
		            <a href="<?php echo $wpalchemy_media_access->getButtonLink($str_secure); ?>>" class="mediabutton-<?php echo $this->_vars['field_key'];?> thickbox {label:'<?php echo esc_html__('Edit', 'eyeta-biztool');?>'}"><?php echo esc_html__('Edit', 'eyeta-biztool');?></a>
		        </div>
		        </div>
		    <?php else: ?>
		        <a href="<?php echo $wpalchemy_media_access->getButtonLink($str_secure); ?>" class="mediabutton-<?php echo $this->_vars['field_key'];?> button thickbox {label:'<?php echo esc_html__('Add', 'eyeta-biztool');?>'}"><?php echo esc_html__('Add', 'eyeta-biztool');?></a>
		    <?php endif; ?>

		    <?php echo $wpalchemy_media_access->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value())); ?>
		</div>
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
			<td class="fbold"><?php echo esc_html__('Required', 'eyeta-biztool');?></td>
			<td class=""><input type="checkbox" name="is_required-<?php echo $this->field_num;?>" value="1" <?php echo $this->get_additional_values_for_form('is_required', array('value' => 1));?> /><?php echo esc_html__('Required', 'eyeta-biztool');?></td>
		</tr>
		<tr class="field-form-detail">
			<td class="fbold"><?php echo esc_html__('Private file', 'eyeta-biztool');?></td>
			<td class=""><input type="checkbox" name="is_secure-<?php echo $this->field_num;?>" value="1" <?php echo $this->get_additional_values_for_form('is_secure', array('value' => 1));?> /><?php echo esc_html__('Private file', 'eyeta-biztool');?><br /><?php echo esc_html__('It can not be seen as not logged in', 'eyeta-biztool');?></td>
		</tr>

		<?php
		$html .= ob_get_contents();
		ob_end_clean();

		
		return $html;
	}

}