(function($){
	$(function() {

		$('#post').validate({
			'rules': eyeta_biztool_table_validate_rules
		});

		$('#publish').on("click", function() {
			var rsl = $('#post').valid();

			return rsl;
		});

	});


})(jQuery);