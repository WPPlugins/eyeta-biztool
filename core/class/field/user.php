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
class Biztool_Field_User extends Biztool_Field {


	function __construct($post_type = null, $field_num = null) {
		parent::__construct($post_type, $field_num);

		// インライン編集を許可
		$this->can_edit_inline = true;

		$this->_vars['field_type'] = 'user';

		$this->_vars['additional'] = array(
			'field_note' => array( 'type' => 'textarea', 'default' => '', 'required' => false),
			'is_required' => array( 'type' => 'checkbox', 'default' => '', 'required' => false),
			'target_role' => array( 'type' => 'select', 'default' => '', 'required' => false)
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
		$obj_user = get_user_by('ID', intval($str_field));
		if($obj_user) {
			return ($obj_user->display_name);
		} else {
			return "";
		}

	}
	/**
	 *  表内編集用フィールド
	 * @param type $target_post_id
	 * @return string
	 */
	function get_inlineedit_column($target_post_id) {
		$str_html = parent::get_inlineedit_column($target_post_id);

		$str_html .= '<input type="text" org="' . esc_attr($str_field)
			. '" name="' . esc_attr($this->_vars['field_key']) . '-'
			. $target_post_id . '" id="' . esc_attr($this->_vars['field_key']) . '-'
			. $target_post_id . '" class="diff-check no-form w100" value="'
			. esc_attr($str_field) . '" target-post_id="'
			. $target_post_id . '" target-field-key="' . esc_attr($this->_vars['field_key']) . '" />';


		$str_field = get_post_meta($target_post_id, $this->_vars['field_key'], true);
		$str_html = '<select name="' . esc_attr($this->_vars['field_key']) . '-'
			. $target_post_id . '"  org="' . esc_attr($str_field)
			. '" class="w100 diff-check no-form w100" id="' . esc_attr($this->_vars['field_key']) . '-'
			. $target_post_id . '" target-post_id="'
			. $target_post_id . '" target-field-key="' . esc_attr($this->_vars['field_key']) . '" >
			<option value="">選択してください。</option>';

		$editable_roles = get_editable_roles();
		if($this->_vars['additional_settings']['target_role'] ) {
			foreach( $editable_roles as $role => $role_info ) {
				if( !in_array($role, $this->_vars['additional_settings']['target_role']) )
				{
					unset( $editable_roles[ $role ] );
				}
			}
		}
		$users = get_users();
		$array_users = array();
		if( !empty($users) && !empty($editable_roles) ) {
			$array_users = array();

			foreach( $editable_roles as $role => $role_info ) {
				// vars
				$this_users = array();
				$this_json = array();


				// loop over users
				$keys = array_keys($users);
				foreach( $keys as $key )
				{
					if( in_array($role, $users[ $key ]->roles) )
					{
						$this_users[] = $users[ $key ];
						unset( $users[ $key ] );
					}
				}


				// bail early if no users for this role
				if( empty($this_users) )
				{
					continue;
				}


				// label
				$label = translate_user_role( $role_info['name'] );


				// append to choices
				$array_users[ $label ] = array();

				foreach( $this_users as $user )
				{
					$array_users[ $label ][ $user->ID ] = ucfirst( $user->display_name );
				}

			}
		}

		foreach($array_users as $label => $array_users) {
			$str_html .='
			<optgroup label="' . esc_attr($label) . '">';
			foreach($array_users as $user_id => $display_name) {
				$str_selected = '';
				if($str_field == $user_id) {
					$str_selected = ' selected ';
				}
				$str_html .= '<option value="' . $user_id . '" ' . $str_selected . ' >' . esc_html($display_name) . '</option>';
			}
		}
		$str_html .= '</select>';
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
	 * 単純な列出力のためのテキスト
	 *
	 * @param $target_post_id
	 *
	 * @return mixed
	 */
	function get_simple_column($target_post_id) {

		$str_field = get_post_meta($target_post_id, $this->_vars['field_key'], true);
		$obj_user = get_user_by('ID', intval($str_field));
		if($obj_user) {
			return esc_html($obj_user->display_name);
		} else {
			return "";
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
			$editable_roles = get_editable_roles();
			if($this->_vars['additional_settings']['target_role'] ) {
				foreach( $editable_roles as $role => $role_info ) {
					if( !in_array($role, $this->_vars['additional_settings']['target_role']) )
					{
						unset( $editable_roles[ $role ] );
					}
				}
			}
			$users = get_users();
			$array_users = array();
			if( !empty($users) && !empty($editable_roles) ) {
				$array_users = array();

				foreach( $editable_roles as $role => $role_info ) {
					// vars
					$this_users = array();
					$this_json = array();


					// loop over users
					$keys = array_keys($users);
					foreach( $keys as $key )
					{
						if( in_array($role, $users[ $key ]->roles) )
						{
							$this_users[] = $users[ $key ];
							unset( $users[ $key ] );
						}
					}


					// bail early if no users for this role
					if( empty($this_users) )
					{
						continue;
					}


					// label
					$label = translate_user_role( $role_info['name'] );


					// append to choices
					$array_users[ $label ] = array();

					foreach( $this_users as $user )
					{
						$array_users[ $label ][ $user->ID ] = ucfirst( $user->display_name );
					}

				}
			}

			foreach($array_users as $label => $array_users) {
				?>
				<optgroup label="<?php echo esc_attr($label);?>">
				<?php
				foreach($array_users as $user_id => $display_name) {
					?>
					<option value="<?php echo $user_id; ?>" <?php $mb->the_select_state($user_id); ?> ><?php echo esc_html($display_name);?></option>
					<?php
				}

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
			<td class="fbold"><?php echo esc_html__('Target role', 'eyeta-biztool');?><br /><span class="description"></span></td>
			<td class=""><select name="target_role-<?php echo $this->field_num;?>[]" multiple="multiple" size="5">
					<option value=""><?php echo esc_html__('ALL', 'eyeta-biztool');?></option>
					<?php
					$array_roles =  get_editable_roles(); // $choices[$role] = translate_user_role( $details['name'] );

					foreach($array_roles as $role => $details) {
						$role_name = translate_user_role( $details['name'] );
						?>
						<option value="<?php echo esc_attr($role);?>" <?php echo $this->get_additional_values_for_form('target_role', array('value' => $role));?> ><?php echo esc_html($role_name);?></option>
						<?php
					}
					?>
				</select></td>
		</tr>

		<?php
		$html .= ob_get_contents();
		ob_end_clean();

		
		return $html;
	}


}