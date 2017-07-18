<?php
/**
 *
 *
 * Created by PhpStorm.
 * Author: Eyeta Co.,Ltd.(http://www.eyeta.jp)
 *
 */

 
namespace eyeta_biztool;

function css_edit() {
	global $eyeta_biztool;

	$post_type = \WPAlchemy_MetaBox::_get_current_post_type();
	$array_obj_tables = $eyeta_biztool->get_tables();
	if(apply_filters('eyeta_biztool/edit/filter_css_on', isset($array_obj_tables[$post_type]), $post_type)) {
		// 不要項目非表示
		$html = '#titlediv {display: none;} ';
		$html = '#post-body-content {display: none;} ';
		$html .= '#edit-slug-box {display: none;} ';
		$html .= '#postdivrich {display: none;} ';
		$html .= '#postexcerpt, #screen-meta label[for=postexcerpt-hide] {display: none;} ';
		$html .= '#postcustom, #screen-meta label[for=postcustom-hide] { display: none; } ';
		$html .= '#commentstatusdiv, #screen-meta label[for=commentstatusdiv-hide] {display: none;} ';
		$html .= '#commentsdiv, #screen-meta label[for=commentsdiv-hide] {display: none;} ';
		$html .= '#slugdiv, #screen-meta label[for=slugdiv-hide] {display: none;} ';
		$html .= '#authordiv, #screen-meta label[for=authordiv-hide] {display: none;} ';
		$html .= '#formatdiv, #screen-meta label[for=formatdiv-hide] {display: none;} ';
		$html .= '#postimagediv, #screen-meta label[for=postimagediv-hide] {display: none;} ';
		$html .= '#revisionsdiv, #screen-meta label[for=revisionsdiv-hide] {display: none;} ';
		$html .= '#categorydiv, #screen-meta label[for=categorydiv-hide] {display: none;} ';
		$html .= '#tagsdiv-post_tag, #screen-meta label[for=tagsdiv-post_tag-hide] {display: none;} ';
		$html .= '#trackbacksdiv, #screen-meta label[for=trackbacksdiv-hide] {display: none;} ';
		$html = apply_filters('eyeta_biztool/edit/css', $html, $post_type);

		echo '<style type="text/css">';
		echo $html;
		echo '</style>';
	}


}