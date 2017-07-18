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
 * 登録されている画像サイズ一覧
 *
 * @param $sizes
 *
 *
 * @return array
 */
function get_image_sizes()
{
	// find all sizes
	$all_sizes = get_intermediate_image_sizes();


	// define default sizes
	$sizes = array(
		'thumbnail'	=>	__("Thumbnail"),
		'medium'	=>	__("Medium"),
		'large'		=>	__("Large"),
		'full'		=>	__("Full")
	);


	// add extra registered sizes
	foreach( $all_sizes as $size )
	{
		if( !isset($sizes[ $size ]) )
		{
			$sizes[ $size ] = ucwords( str_replace('-', ' ', $size) );
		}
	}


	// return array
	return $sizes;
}