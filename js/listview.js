(function($){
	$(function() {

		$('.wp-list-table').fadeIn();

		$('.biztool_listview').on('change', function() {
			$('#posts-filter').submit();
			return true;
		});

		if($( ".accordion_close").size() != 0) {
			$( ".accordion_close" ).each(function() {
				$(this).accordion({
					collapsible: true,
					active: $(this).attr('accordion-target')
				});
			});
		}
		
		$('.btn-biztool_filter').on('click', function() {
			
			// リスト内編集のために対応
			$('.no-form').attr('disabled', true);
			
			// todo biztool_listview配列の個別JS実行に対応

			$('#posts-filter').submit();
			return false;

		});
		
		// インライン編集用
		// no-form
		$('#posts-filter').on('submit', function() {

			$('.no-form').attr('disabled', true);

			return true;
		});
		// diff check
		$('body').on('keyup', '.diff-check', function() {
			var org = $(this).attr('org');
			if($(this).val() != org) {
				$(this).addClass('biztool-chenged');
			} else {
				$(this).removeClass('biztool-chenged');
			}
			return true;
		});
		/* form submitキャンセル用、フィールド保存 */
		function biztool_submit_stop(e){
			if(e.target.nodeName != "textarea" && e.target.nodeName != "TEXTAREA") {
				if (!e) var e = window.event;
		
				if(e.keyCode == 13) {
					// 差分があれば保存
					save_field($(e.target));

					return false;
				}
			} else {
				return true;
			}
		}
		$('body').on('keydown', biztool_submit_stop);


		/**
		 * チェックボックスのインライン編集
		 *
		 */
		$('.diff-check-checkbox').on('click', function() {
			// チェックボックスのチェックされている物を抽出する
			var checkbox_class = '.checkbox-' + $(this).attr('target-field-key') + '-' + $(this).attr('target-post_id');
			var array_checked = [];
			$(checkbox_class).each(function(){
				if($(this).is(':checked')) {
					array_checked[array_checked.length] = $(this).val();
				}
			});
			save_field($(this), array_checked);
		});

		/**
		 * ラジオボタンのインライン編集
		 *
		 */
		$('.diff-check-radio').on('click', function() {
			// チェックボックスのチェックされている物を抽出する
			var checkbox_class = '.radio-' + $(this).attr('target-field-key') + '-' + $(this).attr('target-post_id');
			var checked_val = '';
			$(checkbox_class).each(function(){
				if($(this).is(':checked')) {
					checked_val = $(this).val();
				}
			});
			save_field($(this), checked_val);
		});

		$('.diff-check-yn').on('click', function(){
			var val = '';
			if($(this).is(':checked')) {
				val='1';
			}
			save_field($(this), val);
		});

		$('.diff-check').on('change', function() {
			save_field($(this), $(this).val());
		});
		function save_field($target, val) {
			$target.removeClass('error');
			
			if(false == $('#posts-filter').valid()) {
				return false;
			}

			$.ajax(
				{
					'url': eyeta_biztool_admin_url + '/admin-ajax.php',
					'dataType': 'json',
					'data': {
						'action': 'eyeta_biztool',
						'_wpnonce': eyeta_biztool_nonce,
						'eyeta_biztool_action': 'ajax_save_field',
						'target_post_id': $target.attr('target-post_id'),
						'target_field_key': $target.attr('target-field-key'),
						'new_value': val
					},
					'context': $target.get(0),
					'success': function(data) {
						if(data.rsl == 'NG') {
							eyeta_show_lb_msg('保存エラー', data.msg);
							$(this).addClass('biztool-error');
						} else {
							$(this).removeClass('biztool-error');
						}
					},
					"error": eyeta_ajax_error

				}
			);

		}
		
		if(eyeta_biztool_table_validate_rules.length != 0) {
			$('#posts-filter').validate({
				'rules': eyeta_biztool_table_validate_rules
			});
		}
		
		
		$('#posts-filter').on('submit', function() {
			$.removeData( $(this).get(0), "validator" );
			return true;
		});

		

		
	});


})(jQuery);