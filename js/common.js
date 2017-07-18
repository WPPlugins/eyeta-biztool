var $eyeta_msgbox_div;
(function($){
	$(function() {
		var refresh_accordion = function($target){
			var attr = $target.attr('active_num');
			if(typeof attr !== typeof undefined && attr !== false) {
				$target.accordion({
					collapsible: true,
					'active': $(this).attr('active_num')
				});
			} else {
				$target.accordion({
					collapsible: true
				});
			}
		};
		
		if($( ".tabs").size() != 0) {
			$( ".tabs" ).tabs({
				'activate': function(e, ui) {
					ui.newPanel.find('.accordion').each(function() {
						// todo アコーディオンを閉じた状態でスタートさせたい
						$(this).accordion('refresh');
					});
				}
			});
		}
		if($( ".accordion").size() != 0) {
			$( ".accordion" ).each(function() {
				refresh_accordion($(this));
			});
		}
		if($('.sortable').size() != 0) {
			$( ".sortable" ).sortable();
		}
		if($('.datepicker').size() != 0) {
			$( ".datepicker" ).datepicker({dateFormat: "yy/mm/dd"});
		}

		
		$eyeta_msgbox_div = jQuery("<div />").attr("id", "eyeta_div_msgbox").css("diplay", "none");
		$("body").append($eyeta_msgbox_div);
		$eyeta_msgbox_div.dialog({
			"autoOpen": false
		});

	});
	
	
})(jQuery);

var eyeta_show_lb_msg = function (title, msg, options, buttons) {
	if(buttons == undefined) {
		buttons = [{
      text: "Ok",
      click: function() {
        jQuery( this ).dialog( "close" );
      }
		}];
	}

  var dialog_options = jQuery.extend({
        "autoOpen": false,
          "title": title,
            "modal": true,
            "closeOnEscape": false,
            "closeText": "",
            "buttons": buttons
      }, options);

	$eyeta_msgbox_div.html(msg);
	$eyeta_msgbox_div.dialog(dialog_options);
	$eyeta_msgbox_div.dialog("open");

}

/**
 * ajaxのエラー処理をデフォルトセット
 */
var eyeta_ajax_error = function (XMLHttpRequest, status, errorThrown) {

			if (status == "error") {
				// リクエスト失敗
        eyeta_show_lb_msg('通信エラー', "通信に失敗しました。");
			} else if (status == "notmodified") {
				// 更新されていない
        eyeta_show_lb_msg('通信エラー', "通信に失敗しました。");
			} else if (status == "timeout") {
				 // タイムアウト
        eyeta_show_lb_msg('通信エラー', "通信にタイムアウトしました。");
			} else if (status == "parsererror") {
				 // データパースエラー
        eyeta_show_lb_msg('通信エラー', "システムエラーが発生しました。");
			}
		};
