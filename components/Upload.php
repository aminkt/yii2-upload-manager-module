<?php

namespace aminkt\uploadManager\components;

use aminkt\uploadManager\models\UploadmanagerFiles;
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
     * @param string $input
     *
     * @return UploadmanagerFiles
     *
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\ServerErrorHttpException
     *
     * @author Amin Keshavarz <amin@keshavarz.pro>
     */
    public static function directUpload($input = 'file')
    {
        $module = \aminkt\uploadManager\UploadManager::getInstance();
        $uploadPath = $module->uploadPath;
        $size = $module->sizes;
        $model = new UploadmanagerFiles();
        if (!($userId = \Yii::$app->getUser()->getId())) {
            throw new ForbiddenHttpException("شما دسترسی مجاز برای ارسال فایل را ندارید.");
        }
        $model->userId = $userId;
        if (isset($_FILES[$input])) {
            $model->filesContainer = UploadedFile::getInstanceByName($input);
            if ($model->upload($uploadPath, $size)) {
                //Now save file data to database
                $model->save();
                return $model;
            }
            throw new ServerErrorHttpException("فایل در سرور ذخیره نشد.");
        }
        throw new BadRequestHttpException("فایل به سرور ارسال نشد.");
    }
}