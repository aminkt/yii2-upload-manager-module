<?php

namespace aminkt\uploadManager;

use aminkt\uploadManager\models\UploadmanagerFiles;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;

/**
 * uploadManager module definition class
 */
class UploadManager extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'aminkt\uploadManager\controllers';

    public $uploadPath;
    public $uploadUrl;
    public $fileIcon;
    public $noImage;
    public $acceptedFiles = "image/*,application/pdf,.psd";
    public $sizes = [
        'thumb'=>[150, 150],
        'small'=>[250, 250],
        'normal'=>[500, 500],
    ];

    /** @var integer Admin user id to get some extra accesses */
    public $adminId;

    /** @var string Namespace of user model class. */
    public $userClass;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->modules = [
            'apiV1' => [
                'class' => 'aminkt\uploadManager\api\v1\Module',
            ],
        ];
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

        return \Yii::$app->getModule('uploadManager');
    }

    /**
     * @param $id
     * @param null $size
     * @param bool $path
     * @return string
     * @throws NotFoundHttpException
     */
    public function image($id, $size = null, $path=false){
        $image = UploadmanagerFiles::findOne($id);
        if(!$image or $image->fileType != $image::FILE_TYPE_IMAGE)
            return $this->fileIcon;


         if($size)
            $address = $image::getFileDirectory($image->file).'/'.$size.'_'.$image->name;
        else
            $address = $image::getFileDirectory($image->file).'/'.$image->name;

        if($path){
            $p = FileHelper::normalizePath($this->uploadPath.'/'.$address);
            if(file_exists($p))
                return FileHelper::normalizePath($this->uploadPath.'/'.$address);
            else
                return FileHelper::normalizePath($this->uploadPath.'/'.$this->noImage);
        }
        return $this->uploadUrl.'/'.$address;
    }


    /**
     * Return file model.
     *
     * @param integer $id File id.
     *
     * @return null|UploadmanagerFiles
     *
     * @throws \yii\web\NotFoundHttpException
     *
     * @author Amin Keshavarz <amin@keshavarz.pro>
     */
    public function getFile($id)
    {
        $file = UploadmanagerFiles::findOne($id);
        if (!$file)
            throw new NotFoundHttpException("File not found");

        return $file;
    }

    /**
     * Return not found image.
     * @param bool $path
     * @return string
     */
    public function getNoImage($path=false){
        if($path)
            return FileHelper::normalizePath($this->uploadPath.'/'.$this->noImage);

        return $this->uploadUrl.'/'.$this->noImage;
    }
}
