(function($){
	$(function() {

		/***
		 *
		 * 表の列の設定
		 *
		 */

		$('body').on("click", '.btn-field-toggle', function() {
			var target_field_num = $(this).attr('target-field-num');
			$('.icon-toggle-' + target_field_num).toggleClass('ui-icon-triangle-1-e');
			$('.icon-toggle-' + target_field_num).toggleClass('ui-icon-triangle-1-s');
			$('.toggle-' + target_field_num).toggle('blind');
		});

		/**
		 * 列を追加
		 */
		$('.btn-add-field').on("click", function() {
			$.blockUI();
			$.ajax({
				'url': eyeta_biztool_admin_url + '/admin-ajax.php',
				'method': 'POST',
				'dataType': 'html',
				'data': {
					'action': 'eyeta_biztool',
					'eyeta_biztool_action': 'get_field_base',
					'num': Number($('#eyeta_biztool_max_field_num').val())+1,
					'_wpnonce': eyeta_biztool_nonce
				},
				'success': function(html) {
					var $addhtml = $(html);
					$('#setting-table-field-ul').append($addhtml);
					$addhtml.find('.btn-field-toggle').trigger('click');

					var num = Number($('#eyeta_biztool_max_field_num').val());
					$('#eyeta_biztool_max_field_num').val(num+1);
				},
				'complete': function() {
					$.unblockUI();
				},
				'error': eyeta_ajax_error
			});
		});

		/**
		 * 列を削除
		 */
		$('body').on("click", '.btn-field-delete', function() {
			var target_field_num = $(this).attr('target-field-num');
			eyeta_show_lb_msg('Delete Field', 'Delete Field , Do you really want ?', {}, [
				{
					text: "Delete",
					click: function () {
						$('.li-field-base-' + target_field_num).remove();
						jQuery(this).dialog("close");
						return false;
					}
				},{
					text: "Cancel",
					click: function () {
						jQuery(this).dialog("close");
						return false;
					}

				}]);
			return false;
		});

		/**
		 * 列を複製
		 */
		$('body').on("click", '.btn-field-copy', function() {
			var target_field_num = $(this).attr('target-field-num');

			var num = Number($('#eyeta_biztool_max_field_num').val());
			var new_num = num + 1;
			$('#eyeta_biztool_max_field_num').val(new_num);


			var $new_li = $('.li-field-base-' + target_field_num).clone();
			$new_li.find('[target-field-num="' + target_field_num + '"]').attr('target-field-num', new_num);

			// select


			// その他field-num変更
			$new_li.removeClass('li-field-base-' + target_field_num).addClass('li-field-base-' + new_num);
			$new_li.find('.field_order_num-' + target_field_num).removeClass('field_order_num-' + target_field_num).addClass('field_order_num-' + new_num).attr('id', 'field_order_num-' + new_num).attr('name', 'field_order_num-' + new_num);
			$new_li.find('.icon-toggle-' + target_field_num).removeClass('icon-toggle-' + target_field_num).addClass('icon-toggle-' + new_num);
			$new_li.find('.field-h3-title-' + target_field_num).removeClass('field-h3-title-' + target_field_num).addClass('field-h3-title-' + new_num);
			$new_li.find('.toggle-' + target_field_num).removeClass('toggle-' + target_field_num).addClass('toggle-' + new_num).attr('id', 'field-' + new_num);
			$new_li.find('.field-form-' + target_field_num).removeClass('field-form-' + target_field_num).addClass('field-form-' + new_num);
			$new_li.find('.field_name-' + target_field_num).removeClass('field_name-' + target_field_num).addClass('field_name-' + new_num).attr('id', 'field_name-' + new_num).attr('name', 'field_name-' + new_num);
			$new_li.find('.field_type-' + target_field_num).removeClass('field_type-' + target_field_num).addClass('field_type-' + new_num).attr('id', 'field_type-' + new_num).attr('name', 'field_type-' + new_num).val($('.field_type-' + target_field_num).val());

			// タイプ別フィールド対策
			$new_li.find('.field-form-detail').each(function() {
				var $target_detail = $(this);
				$target_detail.find('*').each(function() {
					var $target_elm = $(this);
					var attr_name = $target_elm.attr('name');
					if(typeof attr_name !== typeof undefined && attr_name !== false) {

						var new_name = attr_name.replace(/\-[0-9]+$/,'-' + new_num);
						$target_elm.attr('name', new_name);
					}
				});
			});


			$('.li-field-base-' + target_field_num).after($new_li);

			return false;
		});

		/**
		 * 列名を編集した際にh3も変更
		 */
		$('body').on("change", '.field_name', function() {
			var $target = $(this);
			var field_num = $target.attr('target-field-num');
			$('.field-h3-title-' + field_num).text($target.val());
		});

		/**
		 * タイプの変更に併せて設定テーブルを変更する。
		 */
		$('body').on('change', '.field_type', function() {
			var target_select = $(this);
			var target_field_num = $(this).attr('target-field-num');

			$.blockUI();
			$.ajax({
				'url': eyeta_biztool_admin_url + '/admin-ajax.php',
				'method': 'POST',
				'dataType': 'html',
				'data': {
					'action': 'eyeta_biztool',
					'eyeta_biztool_action': 'get_field_form',
					'num': $(target_select).attr('target-field-num'),
					'post_type': $("#target_post_type").val(),
					'field_type': $(target_select).val(),
					'_wpnonce': eyeta_biztool_nonce
				},
				'success': function(html) {
					//
					$('.field-form-' + target_field_num).find('.field-form-detail').remove();
					$('.field-form-' + target_field_num).append(html);

				},
				'complete': function() {
					$.unblockUI();
				},
				'error':eyeta_ajax_error
			});

		});




		/**
		 * 保存
		 */
		$('.btn-save_table_setting').on("click", function(){


			eyeta_show_lb_msg('Save setting', 'Save Settings , Do you really want ?', {}, [
				{
					text: "保存",
					click: function () {
						$.blockUI();

						// 項目の並び順処理
						var order_num = 0;
						$('.field_order_num').each(function(){
							order_num++;
							$(this).val(order_num);
						});

						var listview_order_num = 0;
						$('.listview_order_num').each(function(){
							listview_order_num++;
							$(this).val(listview_order_num);
							var listview_num = $(this).attr('target-listview-num');
							var $current_listview = $('.li-listview-base-' + listview_num);


							var listviewcell_order_num = 0;
							$current_listview.find('.listviewcell_order_num-' + listview_num).each(function(){
								listviewcell_order_num++;
								$(this).val(listviewcell_order_num);

							});

							var listviewfilter_order_num = 0;
							$current_listview.find('.listviewfilter_order_num-' + listview_num).each(function(){
								listviewfilter_order_num++;
								$(this).val(listviewfilter_order_num);

							});

						});

						// submit
						$('#frm-setting-table').ajaxSubmit({
							'dataType': 'json',
							'success': function(data){
								if(data.rsl == 'OK') {
									// 成功の場合、画面をリロード
									eyeta_show_lb_msg('保存', data.msg, {}, [{
										text: "OK",
										click: function () {
											location.reload(true);
											return false;
										}
									}]);
								} else {
									eyeta_show_lb_msg('エラー', data.msg);
									return false;
								}
							},
							'complete': function() {
								$.unblockUI();
							},
							'error': eyeta_ajax_error
						});
					}
				},{
					text: "キャンセル",
					click: function () {
						jQuery(this).dialog("close");
						return false;
					}

				}]);


			return false;
		});


		/***
		 *
		 * 一覧表示の設定
		 *
		 */

		$('body').on("click", '.btn-listview-toggle', function() {
			var target_listview_key = $(this).attr('target-listview-num');
			$('.icon-toggle-' + target_listview_key).toggleClass('ui-icon-triangle-1-e');
			$('.icon-toggle-' + target_listview_key).toggleClass('ui-icon-triangle-1-s');
			$('.toggle-' + target_listview_key).toggle('blind');
			return false;
		});
		$('body').on("click", '.btn-listviewcell-toggle', function() {
			var target_listview_key = $(this).attr('target-listview-num');
			var target_listviewcell_key = $(this).attr('target-listviewcell-num');
			$('.icon-toggle-' + target_listview_key + '-' + target_listviewcell_key).toggleClass('ui-icon-triangle-1-e');
			$('.icon-toggle-' + target_listview_key + '-' + target_listviewcell_key).toggleClass('ui-icon-triangle-1-s');
			$('.toggle-' + target_listview_key + '-' + target_listviewcell_key).toggle('blind');
			return false;
		});
		$('body').on("click", '.btn-listviewfilter-toggle', function() {
			var target_listview_key = $(this).attr('target-listview-num');
			var target_listviewfilter_key = $(this).attr('target-listviewfilter-num');
			$('.icon-toggle-' + target_listview_key + '-' + target_listviewfilter_key).toggleClass('ui-icon-triangle-1-e');
			$('.icon-toggle-' + target_listview_key + '-' + target_listviewfilter_key).toggleClass('ui-icon-triangle-1-s');
			$('.toggle-filter-' + target_listview_key + '-' + target_listviewfilter_key).toggle('blind');
			return false;
		});

		/**
		 * 列名を編集した際にh3も変更
		 */
		$('body').on("change", '.listview_name', function() {
			var $target = $(this);
			var listview_num = $target.attr('target-listview-num');
			$('.listview-h3-title-' + listview_num).text($target.val());
		});
		$('body').on("change", '.listviewcell_name', function() {
			var $target = $(this);
			var listview_num = $target.attr('target-listview-num');
			var listviewcell_num = $target.attr('target-listviewcell-num');
			$('.listviewcell-h3-title-' + listview_num + '-' + listviewcell_num).text($target.val());
		});
		$('body').on("change", '.listviewfilter_name', function() {
			var $target = $(this);
			var listview_num = $target.attr('target-listview-num');
			var listviewcell_num = $target.attr('target-listviewfilter-num');
			$('.listviewfilter-h3-title-' + listview_num + '-' + listviewcell_num).text($target.val());
		});


		/**
		 * 表示列の組み合わせを追加
		 */
		$('.btn-add-listview').on("click", function() {

			$.blockUI();
			$.ajax({
				'url': eyeta_biztool_admin_url + '/admin-ajax.php',
				'method': 'POST',
				'dataType': 'html',
				'data': {
					'action': 'eyeta_biztool',
					'eyeta_biztool_action': 'get_listview_base',
					'post_type': $('#target_post_type').val(),
					'num': Number($('#eyeta_biztool_max_listview_num').val())+1,
					'_wpnonce': eyeta_biztool_nonce
				},
				'success': function(html) {
					var $addhtml = $(html);
					$('#setting-table-listview-ul').append($addhtml);
					$addhtml.find('.btn-listview-toggle').trigger('click');

					var num = Number($('#eyeta_biztool_max_listview_num').val());
					$('#eyeta_biztool_max_listview_num').val(num+1);
				},
				'complete': function() {
					$.unblockUI();
				},
				'error': eyeta_ajax_error
			});
		});

		$('body').on("click", '.btn-add-listviewcell', function() {
			var target_listview_num = $(this).attr('target-listview-num');
			$.blockUI();
			$.ajax({
				'url': eyeta_biztool_admin_url + '/admin-ajax.php',
				'method': 'POST',
				'dataType': 'html',
				'data': {
					'action': 'eyeta_biztool',
					'eyeta_biztool_action': 'get_listviewcell_base',
					'post_type': $('#target_post_type').val(),
					'listview_num': target_listview_num,
					'num': Number($('#eyeta_biztool_max_listviewcell_num-' + target_listview_num).val())+1,
					'_wpnonce': eyeta_biztool_nonce
				},
				'success': function(html) {
					var $addhtml = $(html);
					$('#setting-table-listviewcels-ul-' + target_listview_num).append($addhtml);
					$addhtml.find('.btn-listviewcell-toggle').trigger('click');

					var num = Number($('#eyeta_biztool_max_listviewcell_num-' + target_listview_num).val());
					$('#eyeta_biztool_max_listviewcell_num-' + target_listview_num).val(num+1);
				},
				'complete': function() {
					$.unblockUI();
				},
				'error': eyeta_ajax_error
			});
		});

		/**
		 * listviewcellタイプの変更に併せて設定テーブルを変更する。
		 */
		$('body').on('change', '.listviewcell_type', function() {
			var target_select = $(this);
			var target_listview_num = $(this).attr('target-listview-num');
			var target_listviewcell_num = $(this).attr('target-listviewcell-num');

			$.blockUI();
			$.ajax({
				'url': eyeta_biztool_admin_url + '/admin-ajax.php',
				'method': 'POST',
				'dataType': 'html',
				'data': {
					'action': 'eyeta_biztool',
					'eyeta_biztool_action': 'get_listviewcell_form',
					'num': target_listviewcell_num,
					'listview_num': target_listview_num,
					'post_type': $("#target_post_type").val(),
					'listviewcell_type': $(target_select).val(),
					'_wpnonce': eyeta_biztool_nonce
				},
				'success': function(html) {
					//
					$('.listviewcell-form-' + target_listview_num + '-' + target_listviewcell_num).find('.listviewcell-form-detail').remove();
					$('.listviewcell-form-' + target_listview_num + '-' + target_listviewcell_num).append(html);

				},
				'complete': function() {
					$.unblockUI();
				},
				'error': eyeta_ajax_error
			});

		});
		
		/**
		 * 表示列を削除
		 */
		$('body').on("click", '.btn-listviewcell-delete', function() {
			var target_lilstview_num = $(this).attr('target-listview-num');
			var target_lilstviewcell_num = $(this).attr('target-listviewcell-num');
			eyeta_show_lb_msg('Delete Column', 'Delete Column , Do you really want ?', {}, [
				{
					text: "表示列の削除",
					click: function () {
						$('.li-listviewcell-base-' + target_lilstview_num + '-' + target_lilstviewcell_num).remove();
						jQuery(this).dialog("close");
						return false;
					}
				},{
					text: "キャンセル",
					click: function () {
						jQuery(this).dialog("close");
						return false;
					}

				}]);
			return false;
		});

		$('body').on("click", '.btn-listview-delete', function() {
			var target_lilstview_num = $(this).attr('target-listview-num');
			eyeta_show_lb_msg('Delete Listview', 'Delete Listview , Do you really want ?', {}, [
				{
					text: "表示列の組み合せの削除",
					click: function () {
						$('.li-listview-base-' + target_lilstview_num).remove();
						jQuery(this).dialog("close");
						return false;
					}
				},{
					text: "キャンセル",
					click: function () {
						jQuery(this).dialog("close");
						return false;
					}

				}]);
			return false;
		});

		/**
		 * フィルタ追加
		 */
		$('body').on("click", '.btn-add-listviewfilter', function() {
			var target_listview_num = $(this).attr('target-listview-num');
			$.blockUI();
			$.ajax({
				'url': eyeta_biztool_admin_url + '/admin-ajax.php',
				'method': 'POST',
				'dataType': 'html',
				'data': {
					'action': 'eyeta_biztool',
					'eyeta_biztool_action': 'get_listviewfilter_base',
					'post_type': $('#target_post_type').val(),
					'listview_num': target_listview_num,
					'num': Number($('#eyeta_biztool_max_listviewfilter_num-' + target_listview_num).val())+1,
					'_wpnonce': eyeta_biztool_nonce
				},
				'success': function(html) {
					var $addhtml = $(html);
					$('#setting-table-listviewfilters-ul-' + target_listview_num).append($addhtml);
					$addhtml.find('.btn-listviewfilter-toggle').trigger('click');

					var num = Number($('#eyeta_biztool_max_listviewfilter_num-' + target_listview_num).val());
					$('#eyeta_biztool_max_listviewfilter_num-' + target_listview_num).val(num+1);
				},
				'complete': function() {
					$.unblockUI();
				},
				'error': eyeta_ajax_error
			});
		});

		// フィルタタイプ変更時
		$('body').on('change', '.listviewfilter_type', function() {
			var target_select = $(this);
			var target_listview_num = $(this).attr('target-listview-num');
			var target_listviewfilter_num = $(this).attr('target-listviewfilter-num');

			$.blockUI();
			$.ajax({
				'url': eyeta_biztool_admin_url + '/admin-ajax.php',
				'method': 'POST',
				'dataType': 'html',
				'data': {
					'action': 'eyeta_biztool',
					'eyeta_biztool_action': 'get_listviewfilter_form',
					'num': target_listviewfilter_num,
					'listview_num': target_listview_num,
					'post_type': $("#target_post_type").val(),
					'listviewfilter_type': $(target_select).val(),
					'_wpnonce': eyeta_biztool_nonce
				},
				'success': function(html) {
					//
					$('.listviewfilter-form-' + target_listview_num + '-' + target_listviewfilter_num).find('.listviewfilter-form-detail').remove();
					$('.listviewfilter-form-' + target_listview_num + '-' + target_listviewfilter_num).append(html);

				},
				'complete': function() {
					$.unblockUI();
				},
				'error': eyeta_ajax_error
			});

		});

		// フィルタを削除
		$('body').on("click", '.btn-listviewfilter-delete', function() {
			var target_lilstview_num = $(this).attr('target-listview-num');
			var target_listviewfilter_num = $(this).attr('target-listviewfilter-num');
			eyeta_show_lb_msg('Delete Filter', 'Delete Filter , Do you really want ?', {}, [
				{
					text: "フィルタの削除",
					click: function () {
						$('.li-listviewfilter-base-' + target_lilstview_num + '-' + target_listviewfilter_num).remove();
						jQuery(this).dialog("close");
						return false;
					}
				},{
					text: "キャンセル",
					click: function () {
						jQuery(this).dialog("close");
						return false;
					}

				}]);
			return false;
		});


		// 初期の読み込み
		var load_field_type = function(target_field_num, $target_select) {

			$.ajax({
				'url': eyeta_biztool_admin_url + '/admin-ajax.php',
				'method': 'POST',
				'dataType': 'html',
				'async': false,
				'data': {
					'action': 'eyeta_biztool',
					'eyeta_biztool_action': 'get_field_form',
					'num': target_field_num,
					'post_type': $("#target_post_type").val(),
					'field_type': $target_select.val(),
					'_wpnonce': eyeta_biztool_nonce
				},
				'success': function(html) {
					//
					$('.field-form-' + target_field_num).find('.field-form-detail').remove();
					$('.field-form-' + target_field_num).append(html);

				},
				'complete': function() {
					//$.unblockUI();
				},
				'error': eyeta_ajax_error
			});

		}
		var load_listviewcell_type = function(target_listview_num, target_listviewcell_num, target_listviewcell_type) {

			$.ajax({
				'url': eyeta_biztool_admin_url + '/admin-ajax.php',
				'method': 'POST',
				'dataType': 'html',
				'async': false,
				'data': {
					'action': 'eyeta_biztool',
					'eyeta_biztool_action': 'get_listviewcell_form',
					'num': target_listviewcell_num,
					'listview_num': target_listview_num,
					'post_type': $("#target_post_type").val(),
					'listviewcell_type': target_listviewcell_type,
					'_wpnonce': eyeta_biztool_nonce
				},
				'success': function (html) {
					//
					$('.listviewcell-form-' + target_listview_num + '-' + target_listviewcell_num).find('.listviewcell-form-detail').remove();
					$('.listviewcell-form-' + target_listview_num + '-' + target_listviewcell_num).append(html);

				},
				'complete': function () {
					//$.unblockUI();
				},
				'error': eyeta_ajax_error
			});
		}

		var load_listviewfilter_type = function(target_listview_num, target_listviewfilter_num, target_listviewfilter_type) {

			$.ajax({
				'url': eyeta_biztool_admin_url + '/admin-ajax.php',
				'method': 'POST',
				'dataType': 'html',
				'async': false,
				'data': {
					'action': 'eyeta_biztool',
					'eyeta_biztool_action': 'get_listviewfilter_form',
					'num': target_listviewfilter_num,
					'listview_num': target_listview_num,
					'post_type': $("#target_post_type").val(),
					'listviewfilter_type': target_listviewfilter_type,
					'_wpnonce': eyeta_biztool_nonce
				},
				'success': function(html) {
					//
					$('.listviewfilter-form-' + target_listview_num + '-' + target_listviewfilter_num).find('.listviewfilter-form-detail').remove();
					$('.listviewfilter-form-' + target_listview_num + '-' + target_listviewfilter_num).append(html);

				},
				'complete': function() {

				},
				'error': eyeta_ajax_error
			});
		};


		// アクセス権セット
		$('.table_acl_role_input').on("click", function() {
			var val = 0;
			if($(this).is(':checked')) {
				val = 1;
			}
			$.ajax({
				'url': eyeta_biztool_admin_url + '/admin-ajax.php',
				'method': 'POST',
				'dataType': 'json',
				'data': {
					'action': 'eyeta_biztool',
					'eyeta_biztool_action': 'update_table_acl',
					'post_type': $("#target_post_type").val(),
					'role': $(this).attr('target-role'),
					'cap': $(this).attr('target-cap'),
					'val': val,
					'_wpnonce': eyeta_biztool_nonce
				},
				'success': function(data) {
					if(data.rsl == 'NG') {
						eyeta_show_lb_msg('エラー', data.msg);
						return false;
					}
				},
				'complete': function() {

				},
				"error": function (XMLHttpRequest, status, errorThrown) {

					if (status == "error") {
						 // リクエスト失敗
						$(this).addClass('error');
						eyeta_show_lb_msg('通信エラー', "<span class='red'>通信に失敗しました。</span>");
					 } else if (status == "notmodified") {
						 // 更新されていない
						$(this).addClass('error');
						eyeta_show_lb_msg('通信エラー', "<span class='red'>通信に失敗しました。</span>");
					 } else if (status == "timeout") {
						 // タイムアウト
						$(this).addClass('error');
						eyeta_show_lb_msg('通信エラー', "<span class='red'>通信にタイムアウトしました。</span>");
					 } else if (status == "parsererror") {
						 // データパースエラー
						$(this).addClass('error');
						eyeta_show_lb_msg('通信エラー', "<span class='red'>システムエラーが発生しました。</span>");
					 }
				 }			
			 });
		});
		
		
		// フィールドアクセス権セット
		$('.table_acl_role_select').on("change", function() {
			var val = 0;
			if($(this).is(':checked')) {
				val = 1;
			}
			$.ajax({
				'url': eyeta_biztool_admin_url + '/admin-ajax.php',
				'method': 'POST',
				'dataType': 'json',
				'data': {
					'action': 'eyeta_biztool',
					'eyeta_biztool_action': 'update_field_acl',
					'post_type': $("#target_post_type").val(),
					'role': $(this).attr('target-role'),
					'field': $(this).attr('target-field'),
					'val': $(this).val(),
					'_wpnonce': eyeta_biztool_nonce
				},
				'success': function(data) {
					if(data.rsl == 'NG') {
						eyeta_show_lb_msg('エラー', data.msg);
						return false;
					}
				},
				'complete': function() {

				},
				"error": function (XMLHttpRequest, status, errorThrown) {

					if (status == "error") {
						 // リクエスト失敗
						$(this).addClass('error');
						eyeta_show_lb_msg('通信エラー', "<span class='red'>通信に失敗しました。</span>");
					 } else if (status == "notmodified") {
						 // 更新されていない
						$(this).addClass('error');
						eyeta_show_lb_msg('通信エラー', "<span class='red'>通信に失敗しました。</span>");
					 } else if (status == "timeout") {
						 // タイムアウト
						$(this).addClass('error');
						eyeta_show_lb_msg('通信エラー', "<span class='red'>通信にタイムアウトしました。</span>");
					 } else if (status == "parsererror") {
						 // データパースエラー
						$(this).addClass('error');
						eyeta_show_lb_msg('通信エラー', "<span class='red'>システムエラーが発生しました。</span>");
					 }
				 }			
			 });
		});		

	});
})(jQuery);