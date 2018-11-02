<?php

namespace aminkt\uploadManager\components;

use aminkt\uploadManager\classes\UploadedBase64File;
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
        $module = \aminkt\uploadManager\UploadManager::getInstance();
        $uploadPath = $module->uploadPath;
        $size = $module->sizes;

        $fileModelName = \aminkt\uploadManager\UploadManager::getInstance()->fileClass;

        if($isBase64){
            $model = new $fileModelName();
            $model->filesContainer = UploadedBase64File::uploadBase64File($input);
        }elseif($file = UploadedFile::getInstanceByName($input)){
            $model = new $fileModelName();
            $model->filesContainer = $file;
        }elseif($files = UploadedFile::getInstancesByName($input)){
            $count = count($files);
            if($count == 1){
                $model = new $fileModelName();
                $model->filesContainer = $files[0];
            }else{
                throw new BadRequestHttpException("Just one file can uploaded directly");
            }
        }else{
            throw new BadRequestHttpException("فایل به سرور ارسال نشد.");
        }


        if ($userId = \Yii::$app->getUser()->getId()) {
            $model->userId = $userId;
            if ($model->upload($uploadPath, $size)) {
                //Now save file data to database
                $model->save();
                return $model;
            }else{
                \Yii::error($model->getErrors());
                throw new ServerErrorHttpException("فایل در سرور ذخیره نشد.");
            }
        } else {
            throw new ForbiddenHttpException("شما دسترسی مجاز برای ارسال فایل را ندارید.");
        }

    }
}