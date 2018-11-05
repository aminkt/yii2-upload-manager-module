<?php

namespace aminkt\uploadManager\api\v1;

/**
 * Class Module
 * API version ۱
 *
 * @package aminkt\uploadManager\api\v1
 *
 * @author  Amin Keshavarz <amin@keshavarz.pro>
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'aminkt\uploadManager\api\v1\controllers';

    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }

    /**
     * @return self
     *
     * @author Amin Keshavarz <amin@keshavarz.pro>
     */
    public static function getInstance()
    {
        if (parent::getInstance())
            return parent::getInstance();

        return \Yii::$app->getModule('uploadManager')->getModule('apiV1');
    }
}