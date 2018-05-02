CKEditor Расширение для Yii 2
===========================

CKEditor — a free WYSIWYG editor that can be used on web pages.


## Installation

The easiest way to install this extension is via [composer] (http://getcomposer.org/download/).

Either run

```
composer require webschool/yii2-ckeditor
```

or add

```json
"webschool/yii2-ckeditor": "^1.0"
```

in the `require` section of your composer.json file.

## Using

```php
use webschool\ckeditor\CKEditor;
use yii\helpers\Html;

CKEditor::widget([
    'editorOptions' => [
        'preset' => 'full', // basic, standard, full
    ]
]);

//или c ActiveForm

echo $form->field($model, 'content')->widget(CKEditor::className(),[
    'editorOptions' => [
        'preset' => 'full', // basic, standard, full
    ],
]);
```

## Configuring CkFinder

In order to use your settings, copy the file "vendor/webschool/yii2-ckeditor/editor/plugins/ckfinder/config.php" to "backend/config/ckfinder/config.php".

In "backend/config/ckfinder/config.php", remove all references to the variable "localConfig".

## Useful links

CKEditor Api - http://docs.ckeditor.com/

CKEditor Примеры - http://nightly.ckeditor.com/
