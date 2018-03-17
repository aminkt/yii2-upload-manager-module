<?php

namespace aminkt\uploadManager\components;


use yii\web\View;

class FilePondAssets extends \yii\web\AssetBundle
{
    public $css = [
        'filepond-plugin-image-preview.min.css',
        'filepond.min.css',
    ];

    public $js = [
        'filepond-plugin-file-encode.min.js',
        'filepond-plugin-image-exif-orientation.min.js',
        'filepond-plugin-image-preview.min.js',
        'filepond-plugin-file-validate-size.min.js',
        'filepond.min.js',
    ];

    public $jsOptions = ['position'=>View::POS_END];

    public $depends = [
        'yii\web\JqueryAsset'
    ];


    public function init()
    {
        parent::init();
        $this->sourcePath = __DIR__ . "/assets/filepond";
    }
}