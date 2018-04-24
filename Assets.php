<?php
/**
 * Date: 24.04.18
 * Time: 11:36
 */

namespace webschool\ckeditor;

use yii\web\AssetBundle;

class Assets extends AssetBundle
{
	public $sourcePath = '@webschool/ckeditor/editor';

    public $js = [
        'ckeditor.js',
		'js.js',
    ];

	public $depends = [
		'yii\web\YiiAsset',
	];
}