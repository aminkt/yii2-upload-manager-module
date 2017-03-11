<?php

namespace uploadManager;
use uploadManager\models\UploadmanagerFiles;
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
    public $controllerNamespace = 'uploadManager\controllers';

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
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
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
        if(!$image)
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
}
