/**
 * jquery validate 用日本語拡張
 *
 * Created by JetBrains PhpStorm.
 * Author: Eyeta Co.,Ltd.(http://www.eyeta.jp)
 *
 */

/*
 * Translated default messages for the jQuery validation plugin.
 * Locale: JA (Japanese; 日本語)
 */
jQuery.extend(jQuery.validator.messages, {
	required: "このフィールドは必須です。",
	remote: "このフィールドを修正してください。",
	email: "有効なEメールアドレスを入力してください。",
	url: "有効なURLを入力してください。",
	date: "有効な日付を入力してください。",
	dateISO: "有効な日付（ISO）を入力してください。",
	number: "有効な数字を入力してください。",
	digits: "数字のみを入力してください。",
	creditcard: "有効なクレジットカード番号を入力してください。",
	equalTo: "同じ値をもう一度入力してください。",
	extension: "有効な拡張子を含む値を入力してください。",
	maxlength: jQuery.validator.format("{0} 文字以内で入力してください。"),
	minlength: jQuery.validator.format("{0} 文字以上で入力してください。"),
	rangelength: jQuery.validator.format("{0} 文字から {1} 文字までの値を入力してください。"),
	range: jQuery.validator.format("{0} から {1} までの値を入力してください。"),
	max: jQuery.validator.format("{0} 以下の値を入力してください。"),
	min: jQuery.validator.format("{0} 以上の値を入力してください。")
});

//全角ひらがな･カタカナのみ
jQuery.validator.addMethod("kana", function(value, element) {
	return this.optional(element) || /^([ァ-ヶーぁ-ん　]+)$/.test(value);
	}, "全角ひらがな･カタカナを入力してください"
);

//全角ひらがなのみ
jQuery.validator.addMethod("hiragana", function(value, element) {
	return this.optional(element) || /^([ぁ-んー　]+)$/.test(value);
	}, "全角ひらがなを入力してください"
);

//全角カタカナのみ
jQuery.validator.addMethod("katakana", function(value, element) {
	return this.optional(element) || /^([ァ-ヶー　]+)$/.test(value);
	}, "全角カタカナを入力してください"
);

//半角カタカナのみ
jQuery.validator.addMethod("hankana", function(value, element) {
	return this.optional(element) || /^([ｧ-ﾝﾞﾟ\s]+)$/.test(value);
	}, "半角カタカナを入力してください"
);

//半角アルファベット（大文字･小文字）のみ
jQuery.validator.addMethod("alphabet", function(value, element) {
	return this.optional(element) || /^([a-zA-z\s]+)$/.test(value);
	}, "半角英字を入力してください"
);

//半角アルファベット（大文字･小文字）もしくは数字のみ
jQuery.validator.addMethod("alphanum", function(value, element) {
	return this.optional(element) || /^([a-zA-Z0-9]+)$/.test(value);
	}, "半角英数字を入力してください"
);

//半角アルファベット（大文字･小文字）もしくは数字、記号
jQuery.validator.addMethod("alphanum2", function(value, element) {
	return this.optional(element) || /^([a-zA-Z0-9\!\"\#\$\%\&\'\(\)\=\{\}\@\;\:\>\<\_\-\^]+)$/.test(value);
	}, "半角英数字または記号（\!\"\#\$\%\&\'\(\)\=\{\}\@\;\:\>\<\_\-\^）を入力してください"
);

//半角アルファベット（大文字･小文字）もしくは数字、記号
jQuery.validator.addMethod("alphanum3", function(value, element) {
		return this.optional(element) || /^([a-zA-Z0-9\.]+)$/.test(value);
	}, "半角英数字または記号（\.）を入力してください"
);

//半角アルファベット（大文字･小文字）もしくは数字のみ
jQuery.validator.addMethod("alphanum4", function(value, element) {
	return this.optional(element) || /^([a-zA-Z0-9\s]+)$/.test(value);
	}, "半角英数字を入力してください"
);


//郵便番号（例:012-3456）
jQuery.validator.addMethod("postnum", function(value, element) {
	return this.optional(element) || /^\d{3}\-\d{4}$/.test(value);
	}, "郵便番号を入力してください（例:123-4567）"
);

//携帯番号（例:010-2345-6789）
jQuery.validator.addMethod("mobilenum", function(value, element) {
	return this.optional(element) || /^0\d0-\d{4}-\d{4}$/.test(value);
	}, "携帯番号を入力してください（例:010-2345-6789）"
);

//電話番号（例:012-345-6789）
jQuery.validator.addMethod("telnum", function(value, element) {
	return this.optional(element) || /^[\-0-9]+$/.test(value);
	}, "電話番号を入力してください（例:012-345-6789）"
);

// 全角チェック
jQuery.validator.addMethod("zenkaku", function(value, element) {
	var str = value;
	for (var i = 0; i < str.length; i++) {
		var c = str.charCodeAt(i);
		// Shift_JIS: 0x0 〜 0x80, 0xa0 , 0xa1 〜 0xdf , 0xfd 〜 0xff
		// Unicode : 0x0 〜 0x80, 0xf8f0, 0xff61 〜 0xff9f, 0xf8f1 〜 0xf8f3
		if ( (c >= 0x0 && c < 0x81) || (c == 0xf8f0) || (c >= 0xff61 && c < 0xffa0) || (c >= 0xf8f1 && c < 0xf8f4)) {
				return this.optional(element) || false;
		}
	}
	return true;
	}, "全角文字を入力してください"
);

//指定文字数
jQuery.validator.addMethod("justlength", function(value, element, param) {
	return this.optional(element) || (value.length == param);
	}, jQuery.validator.format("{0} 文字で入力してください")
);

//指定文字数
jQuery.validator.addMethod("date_-", function(value, element, param) {
		try {
			jQuery.datepicker.parseDate('yy-mm-dd', value);
			return this.optional(element) || true;
		} catch(e) {
			return this.optional(element) || false;
		}
	}, jQuery.validator.format("yyyy-mm-dd形式で入力してください")
);

jQuery.validator.addMethod("date_-2", function(value, element, param) {
		try {
			jQuery.datepicker.parseDate('yy/mm/dd', value);
			return this.optional(element) || true;
		} catch(e) {
			return this.optional(element) || false;
		}
	}, jQuery.validator.format("yyyy/mm/dd形式で入力してください")
);
