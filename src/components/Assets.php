<?php

namespace aminkt\yii2\uploadmanager\components;


use yii\web\View;

class Assets extends \yii\web\AssetBundle
{
    public $sourcePath = __DIR__ . "/assets";
    public $css = [
        
    ];

    public $js = [

    ];

    public $jsOptions = ['position'=>View::POS_END];
}