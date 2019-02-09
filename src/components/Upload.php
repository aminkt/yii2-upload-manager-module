<?php

namespace aminkt\yii2\uploadmanager\components;

use aminkt\yii2\uploadmanager\classes\UploadedBase64File;
use aminkt\yii2\uploadmanager\interfaces\FileInterface;
use yii\base\Component;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

/**
 * Class Upload
 *
 * Upload file to server.
 *
 * @package aminkt\uploadManager\components
 *
 * @author  Amin Keshavarz <amin@keshavarz.pro>
 */
class Upload extends Component
{
    /** @var array Keep errors. */
    public static $errors = [];

    /**
     * @param string $input    Input name.
     * @param bool   $isBase64 Upload an base64 encoded file.
     *
     * @return \aminkt\uploadManager\interfaces\FileInterface
     *
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\ServerErrorHttpException
     *
     * @author Amin Keshavarz <amin@keshavarz.pro>
     */
    public static function directUpload($input = 'file', $isBase64 = false)
    {
        static::$errors = [];
        $module = \aminkt\yii2\uploadmanager\UploadManager::getInstance();
        $uploadPath = $module->uploadPath;
        $size = $module->sizes;

        $fileModelName = \aminkt\yii2\uploadmanager\UploadManager::getInstance()->fileClass;

        // Clean last loaded file.
        UploadedFile::reset();
        /** @var FileInterface $model */
        if($isBase64){
            $model = new $fileModelName();
            $model->setFilesContainer(UploadedBase64File::uploadBase64File($input));
        }elseif($file = UploadedFile::getInstanceByName($input)){
            $model = new $fileModelName();
            $model->setFilesContainer($file);
        }elseif($files = UploadedFile::getInstancesByName($input)){
            $count = count($files);
            if($count == 1){
                $model = new $fileModelName();
                $model->setFilesContainer($files[0]);
            }else{
                throw new BadRequestHttpException("Just one file can uploaded directly");
            }
        }else{
            throw new BadRequestHttpException("File not send to server.");
        }


        if ($userId = \Yii::$app->getUser()->getId()) {
            $model->setUserId($userId);
            if ($model->upload($uploadPath, $size)) {
                //Now save file data to database
                $model->save();
                return $model;
            }else{
                \Yii::error($model->getErrors());
                static::$errors = $model->getFirstErrors();
                throw new BadRequestHttpException("File not saved in server.");
            }
        } else {
            throw new ForbiddenHttpException("You have not correct access to upload file.");
        }

    }
}