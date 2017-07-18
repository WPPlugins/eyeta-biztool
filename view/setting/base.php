<?php
/**
 *
 *
 * Created by PhpStorm.
 * Author: Eyeta Co.,Ltd.(http://www.eyeta.jp)
 *
 */

 
namespace eyeta_biztool;

require_once "base-table.php";
require_once "base-table-from-excel.php";
require_once "base-group.php";

/**
 * メイン設定画面
 */
function setting_base() {
	global $eyeta_biztool;

	?>
		<div class="wrap">
			<h2><?php echo esc_html__('BizTool setting', 'eyeta-biztool');?></h2>
			<div class="tabs" id="setting-base-tabs">
				<ul>
					<?php if($eyeta_biztool->current_user_can('eyeta-biztool/setting/base/table')) {?>
					<li><a href="#setting-base-tables"><?php echo esc_html__('Posttype Management', 'eyeta-biztool');?></a></li>
					<?php } ?>
					<?php if($eyeta_biztool->current_user_can('eyeta-biztool/setting/base/group')) {?>
					<li><a href="#setting-base-groups"><?php echo esc_html__('Role Management', 'eyeta-biztool');?></a></li>
					<?php } ?>
					</ul>

				<?php if($eyeta_biztool->current_user_can('eyeta-biztool/setting/base/table')) {?>
				<div id="setting-base-tables">
					<div class="accordion" id="setting-base-tables-accordion">
						<?php
						setting_base_table();
						?>

						<?php
						//setting_base_table_from_excel();
						?>


						<?php
						$tables = $eyeta_biztool->get_option('tables', array());
						foreach($tables as $post_type => $table) {
							setting_base_table($post_type);
						}
						?>


					</div>
				</div>
				<?php } ?>

				<?php if($eyeta_biztool->current_user_can('eyeta-biztool/setting/base/group')) {?>

				<div id="setting-base-groups">
					<div class="accordion" id="setting-base-groups-accordion">
						<?php
						setting_base_group();
						?>

						<?php
						$array_roles = wp_roles()->roles;
						foreach($array_roles as $role_key => $array_role) {
							if('administrator' == $role_key) {
								continue;
							}
							setting_base_group($role_key, $array_role);
						}
						?>
					</div>
				</div>
				<?php } ?>




				</div>




<?php
	
}