<?php

namespace aminkt\uploadManager;

use aminkt\uploadManager\models\File;
use aminkt\uploadManager\models\FileSearch;
use aminkt\uploadManager\models\UploadmanagerFiles;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;

/**
 * uploadManager module definition class
 *
 * @property string uploadBaseUrl
 */
class UploadManager extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'aminkt\uploadManager\controllers';

    public $uploadPath;
    public $uploadUrl = '/upload';
    public $baseUrl;

    /**
     * @var array $allowedFiles Allowed file extenssions.
     * @since v1.2.0
     */
    public $allowedFiles = ['jpg', 'jpeg', 'png', 'mp4', 'pdf'];

    /** @deprecated  */
    public $acceptedFiles = "image/*,application/pdf,.psd";

    public $sizes = [
        'thumb'=>[150, 150],
        'small'=>[250, 250],
        'normal'=>[500, 500],
    ];

    /**
     * Max upload size allowed by module and php in megabyte.
     * @var string $maxUploadFileSize
     * @since v1.2.0
     */
    public $maxUploadFileSize = 2;

    /** @var integer Admin user id to get some extra accesses */
    public $adminId;

    /** @var string Namespace of user model class. */
    public $userClass;

    /** @var string Namespace of file model class. */
    public $fileClass = File::class;

    /** @var string Namespace of file search model class. */
    public $fileSearchClass = FileSearch::class;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        ini_set('upload_max_filesize', $this->maxUploadFileSize.'M');
        ini_set('post_max_size', $this->maxUploadFileSize.'M');
        ini_set('max_input_time', 3000);
        ini_set('max_execution_time', 3000);
        $this->modules = [
            'v1' => [
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

    /**
     * Return base upload url.
     *
     * @return string
     */
    public function getUploadBaseUrl(){
        if(!$this->baseUrl){
            $this->baseUrl =  \Yii::$app->getUrlManager()->getHostInfo() . \Yii::$app->getUrlManager()->getBaseUrl();
        }

        return $this->baseUrl . $this->uploadUrl;
    }
}
