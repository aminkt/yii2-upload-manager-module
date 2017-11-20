<?php

namespace aminkt\uploadManager\components;


use yii\web\View;

class Assets extends \yii\web\AssetBundle
{
    public $sourcePath = __DIR__ . "/assets";
    public $css = [
        'image-picker.css',
        'upload-manager.css'
    ];

    public $js = [
        'image-picker.min.js',
        'upload-manager.js',
    ];

    public $jsOptions = ['position'=>View::POS_END];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}