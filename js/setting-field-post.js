(function($){
	$(function() {

		// 列を生成
		$('body').on('change', '.select-target_post_type', function() {
			var field_num = $(this).attr('target-field-num');
			
			
			$.ajax({
				'url': eyeta_biztool_admin_url + '/admin-ajax.php',
				'method': 'POST',
				'dataType': 'html',
				'data': {
					'action': 'eyeta_biztool',
					'eyeta_biztool_action': 'ajax_get_post_type_field_options',
					'post_type': $(this).val(),
					'_wpnonce': eyeta_biztool_nonce
				},
				'success': function(html) {
					//
					$('.show_field-option-' + field_num).remove();
					$('#show_field-' + field_num).append(html);

				},
				'complete': function() {

				},
				'error': eyeta_ajax_error
			});
			//  class="show_field-options-<?php echo $this->field_num;?>"
		});

	});


})(jQuery);