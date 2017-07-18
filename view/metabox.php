<?php
/**
 *
 *
 * Created by PhpStorm.
 * Author: Eyeta Co.,Ltd.(http://www.eyeta.jp)
 *
 */

namespace eyeta_biztool;


global $eyeta_biztool, $wpalchemy_media_access;

?>
<div class="my_meta_control">
	<?php
	$post_type = $mb->_get_current_post_type();

	$array_obj_tables = $eyeta_biztool->get_tables();
	if(isset($array_obj_tables[$post_type])) {
		$obj_table = $array_obj_tables[$post_type];
		$array_fields = $obj_table->get_fields();
		foreach($array_fields as $field_key => $array_field) {
			$obj_field = $obj_table->get_obj_field($array_field['field_key']);
			?>
			<?php

			echo $obj_field->get_field_edit_form($mb);
			?>
	<?php
		}


	}
	?>

</div>
