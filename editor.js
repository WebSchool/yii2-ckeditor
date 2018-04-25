/**
 * Собственные обработчики для работы с плагином
 */

if (typeof webschool == "undefined" || !webschool) {
	var webschool = {};
}

webschool.ckEditor =
{
    /**
	 * для textarea, которому назначен ckeditor, вызываем тригер "change",
	 * скорей всего для поля ckEditor вызывается такой же тригер
     */
	registerOnChange: function(id)
	{
		CKEDITOR.instances[id] && CKEDITOR.instances[id].on('change', function () {
			CKEDITOR.instances[id].updateElement();
			$('#' + id).trigger('change');

			return false;
		});
	},

	/**
	 * При переключении в вкладку файлового менеджера зарегистрировать 1 раз csrfToken в форме загрузки файла
	 * todo: для переключения вкладки (или кнопки) файлового менеджера поменять название класса "cke_dialog_tabs" на подходящий класс (хотя и так норм)
	 */
	isRegisteredCsrf: false,
	registerCsrf: function()
	{
		if (this.isRegisteredCsrf)
			return;

		this.isRegisteredCsrf = true;

		yii && $(document)
		.off('click', '.cke_dialog_tabs a:eq(2)')
		.on('click', '.cke_dialog_tabs a:eq(2)', function () {
			var form = $('.cke_dialog_ui_input_file iframe').contents().find('form');
			var csrfName = yii.getCsrfParam();

			if (!form.find('input[name=' + csrfName + ']').length) {
				var csrfTokenInput = $('<input/>').attr({'type': 'hidden', 'name': csrfName}).val(yii.getCsrfToken());
				form.append(csrfTokenInput);
			}
		});
	}
};