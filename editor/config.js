/**
 * @license Copyright (c) 2003-2018, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/**
 * TODO: После изменений очистить кеш браузера (если не используется фраемворк)
 */

// запрещаем автоматически удалять span и i
//CKEDITOR.dtd.$removeEmpty.span = 0;
//CKEDITOR.dtd.$removeEmpty.i = 0;

CKEDITOR.editorConfig = function( config ) {
    // Define changes to default configuration here. For example:
    // config.language = 'fr';
    // config.uiColor = '#AADC6E';

    //config.height = 500;
    //config.width = 'auto';
    //config.skin = 'kama';

    // удаляем плагин "вспомогательного окна", который срабатывает при клике на правую кнопку мыши
    //config.removePlugins = 'liststyle,tabletools,contextmenu';

    // вместо тега "<p>" и тега "<br />" ставим "<div>"
    //config.enterMode = CKEDITOR.ENTER_BR;

    // автоматом подставляются теги "html", "head", "body"
    //config.fullPage = true;

    // вставить автоматом чистый текст без стилей
    //config.forcePasteAsPlainText = true;

    //config.autoParagraph = false;

    //config.filebrowserBrowseUrl = '../ckeditor/plugins/ckfinder/ckfinder.html?' + (Math.random() * (100000 - 1) + 100000);

    /*config.toolbar = [
        { name: '1', items: [ 'Preview', 'Source', '-', 'NewPage', 'Print', '-', 'Templates' ] },
        { name: '2', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
        { name: '3', items: [ 'Find', 'Replace', '-', 'SelectAll'/!*, '-', 'Scayt'*!/ ] },
        //{ name: '4', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
        '/',
        { name: '5', items: [ 'Bold', 'Italic', 'Underline', 'Strike', /!*'Subscript', 'Superscript', '-',*!/ 'RemoveFormat' ] },
        { name: '6', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', /!*'Language'*!/ ] },
        { name: '7', items: [ 'Link', 'Unlink', 'Anchor' ] },
        { name: '8', items: [ 'Image', 'Flash', 'Table', /!*'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak',*!/ 'Iframe' ] },
        { name: '9', items: [ /!*'Styles',*!/ 'Format'/!*, 'Font', 'FontSize'*!/ ] },
        //{ name: '10', items: [ 'TextColor', 'BGColor' ] },
        { name: '11', items: [ "Maximize", "ShowBlocks" ] }
    ];*/

    // изменть цвет фона и размер шрифта для отображения
    //config.contentsCss = "/css/ckEditor.css";

    // чтобы не заменял базовые теги на свои
    //config.allowedContent = true;
};

/**
 * Здесь можно указывать для разных диалоговых окон дефолтные значения
 */
/*
CKEDITOR.on('dialogDefinition', function(ev) {
    try {
        var dialogName = ev.data.name;
        var dialogDefinition = ev.data.definition;

        // для ссылок выставляем по умолчанию target="_blank"
        if(dialogName == 'link') {
            var informationTab = dialogDefinition.getContents('target');
            var targetField = informationTab.get('linkTargetType');
            targetField['default'] = '_blank';
        }

        // для изображений делаем обязательным поле "альтернативный текст"
        if (dialogName == 'image') {
            var onOk = dialogDefinition.onOk;

            dialogDefinition.onOk = function (e) {
                var input = this.getContentElement('info', 'txtAlt');
                var imageAlt = input.getValue().trim();

                if (!imageAlt) {
                    alert('Введите альтернативный текст.');
                    input.focus();
                    return false;
                }

                onOk && onOk.apply(this, e);
            };
        }
    } catch(exception) {
        alert('Error ' + ev.message);
    }
});
*/