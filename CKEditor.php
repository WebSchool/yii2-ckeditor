<?php
/**
 * Date: 24.04.18
 * Time: 11:36
 */

namespace webschool\ckeditor;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\Json;
use yii\widgets\InputWidget;

class CKEditor extends InputWidget
{
    public $editorOptions = [];
    public $containerOptions = [];

    public function init()
    {
        parent::init();

        if (array_key_exists('preset', $this->editorOptions)) {
            if ($this->editorOptions['preset'] == 'basic') {
                $this->presetBasic();
            } elseif($this->editorOptions['preset'] == 'standard') {
                $this->presetStandard();
            } elseif($this->editorOptions['preset'] == 'full') {
                $this->presetFull();
            }

            unset($this->editorOptions['preset']);
        }
    }

    public function run()
    {
        Assets::register($this->getView());

        echo Html::beginTag('div', $this->containerOptions);

        if ($this->hasModel()) {
            echo Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textarea($this->name, $this->value, $this->options);
        }

        echo Html::endTag('div');

        $js = [
            'webschool.ckEditor.registerOnChange('.Json::encode($this->options['id']).');'
        ];

        if (isset($this->editorOptions['filebrowserUploadUrl']))
            $js[] = "webschool.ckEditor.registerCsrf();";

        if (!isset($this->editorOptions['on']['instanceReady']))
            $this->editorOptions['on']['instanceReady'] = new JsExpression("function( ev ){".implode(' ', $js)."}");

        $JavaScript = "CKEDITOR.replace(";
        $JavaScript .= Json::encode($this->options['id']);
        $JavaScript .= empty($this->editorOptions) ? '' : ', '.Json::encode($this->editorOptions);
        $JavaScript .= ");";

        $this->getView()->registerJs($JavaScript, View::POS_END);
    }

    private function presetBasic()
    {
        $options['height'] = 100;

        $options['toolbarGroups'] = [
            ['name' => 'undo'],
            ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
            ['name' => 'colors'],
            ['name' => 'links', 'groups' => ['links', 'insert']],
            ['name' => 'others','groups' => ['others', 'about']],
        ];

        $options['removeButtons'] = 'Subscript,Superscript,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe';
        $options['removePlugins'] = 'elementspath';
        $options['resize_enabled'] = false;

        $this->editorOptions = ArrayHelper::merge($options, $this->editorOptions);
    }

    private function presetStandard()
    {
        $options['height'] = 400;
        $options['width'] = 'auto';
        $options['autoParagraph'] = false;
        $options['allowedContent'] = true; // чтобы не заменял базовые теги на свои

        $options['toolbar'] = [
            ['name' => '1', 'items' => ['Preview','Source', '-', 'NewPage', 'Print', '-', 'Templates']],
            ['name' => '2', 'items'=> ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']],
            ['name' => '3', 'items' => ['Find', 'Replace', '-', 'SelectAll']],
            '/',
            ['name' => '5', 'items' => ['Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat']],
            ['name' => '6', 'items' => ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl']],
            ['name' => '7', 'items' => ['Link', 'Unlink', 'Anchor']],
            ['name' => '8', 'items' => ['Image', 'Flash', 'Table']],
            ['name' => '9', 'items' => ['Format']],
            ['name' => '10', 'items' => ['Maximize', 'ShowBlocks']],
        ];

        $this->editorOptions = ArrayHelper::merge($options, $this->editorOptions);
    }

    private function presetFull()
    {
        $options['height'] = 500;

        $options['toolbarGroups'] = [
            ['name' => 'clipboard', 'groups' => ['mode','undo', 'selection', 'clipboard', 'doctools']],
            ['name' => 'editing', 'groups' => ['find', 'spellchecker', 'tools', 'about']],
            '/',
            ['name' => 'paragraph', 'groups' => ['templates', 'list', 'indent', 'align']],
            ['name' => 'forms'],
            '/',
            ['name' => 'styles'],
            ['name' => 'blocks'],
            '/',
            ['name' => 'basicstyles', 'groups' => ['basicstyles', 'colors','cleanup']],
            ['name' => 'links', 'groups' => ['links', 'insert']],
            ['name' => 'others'],
        ];

        $this->editorOptions = ArrayHelper::merge($options, $this->editorOptions);
    }
}