<?php

namespace aminkt\uploadManager;

use aminkt\uploadManager\models\File;
use aminkt\uploadManager\models\FileSearch;
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
    public string $uploadPath;
    public string $fileIcon;
    public string $noImage;
    public string $uploadUrl = '/upload';
    public ?string $baseUrl = null;
    public string $acceptedFiles = "image/*,application/pdf,.psd";
    public array $sizes = [
        'thumb' => [150, 150],
        'small' => [250, 250],
        'normal' => [500, 500],
    ];

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

        return \Yii::$app->getModule('fileManager');
    }

    /**
     * @param $id
     * @param null $size
     * @param bool $path
     * @return string
     * @throws NotFoundHttpException
     */
    public function image($id, $size = null, $path = false)
    {
        $image = File::findOne($id);
        if (!$image or $image->file_type != $image::FILE_TYPE_IMAGE)
            return $this->getUploadBaseUrl() . '/' . $this->fileIcon;


        if ($size)
            $address = $image::getFileDirectory($image->file) . '/' . $size . '_' . $image->name;
        else
            $address = $image::getFileDirectory($image->file) . '/' . $image->name;

        $p = FileHelper::normalizePath($this->uploadPath . '/' . $address);
        if (!file_exists($p)) {
            return $this->getNoImage($path);
        } else if ($path) {
            return FileHelper::normalizePath($this->uploadPath . '/' . $address);
        }

        return $this->getUploadBaseUrl() . '/' . $address;
    }


    /**
     * Return file model.
     *
     * @param integer $id File id.
     *
     * @return null|File
     *
     * @throws \yii\web\NotFoundHttpException
     *
     * @author Amin Keshavarz <amin@keshavarz.pro>
     */
    public function getFile($id)
    {
        $file = File::findOne($id);
        if (!$file)
            throw new NotFoundHttpException("File not found");

        return $file;
    }

    /**
     * Return not found image.
     * @param bool $path
     * @return string
     */
    public function getNoImage($path = false)
    {
        if ($path)
            return FileHelper::normalizePath($this->uploadPath . '/' . $this->noImage);

        return $this->getUploadBaseUrl() . '/' . $this->noImage;
    }

    /**
     * Return base upload url.
     *
     * @return string
     */
    public function getUploadBaseUrl()
    {
        if (!$this->baseUrl) {
            $this->baseUrl = \Yii::$app->getUrlManager()->getHostInfo() . \Yii::$app->getUrlManager()->getBaseUrl();
        }

        return $this->baseUrl . $this->uploadUrl;
    }
}
