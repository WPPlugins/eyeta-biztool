(function ($) {
	$(function () {

		/**
		 * 表の追加
		 */
		$('.btn-add_table').on('click', function () {
			// 入力チェック
			if ($('#table-name-new').val() == '') {
				eyeta_show_lb_msg('エラー', '<span style="color: red;">Please input Posttype name</span>');
				return false;
			}

			eyeta_show_lb_msg('Add Posttype', 'Add Posttype , Do you really want ?', {}, [
				{
					text: "Add",
					click: function () {
						$.blockUI();

						$('#frm-setting-base-table-new').ajaxSubmit({
							'dataType': 'json',
							'success': function (data) {
								if ('OK' == data.rsl) {
									eyeta_show_lb_msg('追加', data.msg, {}, [{
											text: "OK",
											click: function () {
												location.href = eyeta_biztool_admin_url + '/admin.php?page=eyeta-biztool&biztool_page=setting%2Ftable&post_type=' + data.data.post_type;
												return false;
											}
										}]);
								} else {
									// error
									eyeta_show_lb_msg('エラー', data.msg);
								}
							},
							'complete': function () {
								$.unblockUI();
							},
							'error': eyeta_ajax_error

						});
						jQuery(this).dialog("close");
					}
				},
				{
					text: "Cancel",
					click: function () {
						jQuery(this).dialog("close");
						return false;
					}
				}
			]);


		});


		/**
		 * 表の名前の更新
		 */
		$('.btn-save_table').on('click', function () {
			var target_post_type = $(this).attr('target_post_type');
			// 入力チェック
			if ($('#table-name-' + target_post_type).val() == '') {
				eyeta_show_lb_msg('エラー', '<span style="color: red;">Please input Posttype name</span>');
				return false;
			}

			eyeta_show_lb_msg('Save Posttype', 'Save Posttype name, Do you really want ?', {}, [
				{
					text: "Save",
					click: function () {
						$.blockUI();

						$('#frm-setting-base-table-' + target_post_type).ajaxSubmit({
							'dataType': 'json',
							'success': function (data) {
								if ('OK' == data.rsl) {
									eyeta_show_lb_msg('保存', data.msg);
									$('#h3-' + target_post_type).text($('#table-name-' + target_post_type).val());
								} else {
									// error
									eyeta_show_lb_msg('エラー', data.msg);
								}
							},
							'complete': function () {
								$.unblockUI();
							}

						});
						jQuery(this).dialog("close");
					}
				},
				{
					text: "Cancel",
					click: function () {
						jQuery(this).dialog("close");
						return false;
					}
				}
			]);


		});


		/**
		 * 表の詳細へ
		 */
		$('.btn-go_setting_table').on('click', function () {
			var target_post_type = $(this).attr('target_post_type');

			location.href = eyeta_biztool_admin_url + 'admin.php?page=eyeta-biztool&biztool_page=setting%2Ftable&post_type=' + target_post_type;
			return false;
		});

		/**
		 * グループの追加
		 */
		$('.btn-add_group').on('click', function () {
			// 入力チェック
			if ($('#group-name-new').val() == '') {
				eyeta_show_lb_msg('エラー', '<span style="color: red;">Please input Role name</span>');
				return false;
			}

			eyeta_show_lb_msg('Add role', 'Add Role , Do you really want ?', {}, [
				{
					text: "Add",
					click: function () {
						$.blockUI();

						$('#frm-setting-base-group-new').ajaxSubmit({
							'dataType': 'json',
							'success': function (data) {
								if ('OK' == data.rsl) {
									eyeta_show_lb_msg('追加', data.msg, {}, [{
											text: "OK",
											click: function () {
												location.reload(true);
												return true;
											}
										}]);
								} else {
									// error
									eyeta_show_lb_msg('エラー', data.msg);
								}
							},
							'complete': function () {
								$.unblockUI();
							}

						});
						jQuery(this).dialog("close");
					}
				},
				{
					text: "Cancel",
					click: function () {
						jQuery(this).dialog("close");
						return false;
					}
				}
			]);

		});
		
		// 
		/**
		 * グループの名前の更新
		 */
		$('.btn-save_group').on('click', function () {
			var target_group = $(this).attr('target_group');
			// 入力チェック
			if ($('#table-name-' + target_group).val() == '') {
				eyeta_show_lb_msg('エラー', '<span style="color: red;">Please input Role name</span>');
				return false;
			}

			eyeta_show_lb_msg('Save Role', 'Save Role name , Do you really want ?', {}, [
				{
					text: "Save",
					click: function () {
						$.blockUI();

						$('#frm-setting-base-group-' + target_group).ajaxSubmit({
							'dataType': 'json',
							'success': function (data) {
								if ('OK' == data.rsl) {
									eyeta_show_lb_msg('保存', data.msg);
									$('#h3-' + target_group).text($('#table-name-' + target_group).val());
								} else {
									// error
									eyeta_show_lb_msg('エラー', data.msg);
								}
							},
							'complete': function () {
								$.unblockUI();
							}

						});
						jQuery(this).dialog("close");
					}
				},
				{
					text: "Cancel",
					click: function () {
						jQuery(this).dialog("close");
						return false;
					}
				}
			]);


		});

	});

})(jQuery);
