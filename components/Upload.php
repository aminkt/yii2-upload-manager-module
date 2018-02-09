<?php

namespace aminkt\uploadManager\components;

use aminkt\uploadManager\classes\UploadedBase64File;
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
     * @param string $input    Input name.
     * @param bool   $isBase64 Upload an base64 encoded file.
     *
     * @return \aminkt\uploadManager\models\UploadmanagerFiles
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\ServerErrorHttpException
     * @author Amin Keshavarz <amin@keshavarz.pro>
     */
    public static function directUpload($input = 'file', $isBase64 = false)
    {
        $module = \aminkt\uploadManager\UploadManager::getInstance();
        $uploadPath = $module->uploadPath;
        $size = $module->sizes;
        $model = new UploadmanagerFiles();
        if ($userId = \Yii::$app->getUser()->getId()) {
            $model->userId = $userId;
            if (isset($_FILES[$input]) or $isBase64) {
                $model->filesContainer = $isBase64 ?
                    UploadedBase64File::uploadBase64File($input) :
                    UploadedFile::getInstanceByName($input);
                if ($model->upload($uploadPath, $size)) {
                    //Now save file data to database
                    $model->save();
                    return $model;
                }
                throw new ServerErrorHttpException("فایل در سرور ذخیره نشد.");
            }
            throw new BadRequestHttpException("فایل به سرور ارسال نشد.");
        } else {
            throw new ForbiddenHttpException("شما دسترسی مجاز برای ارسال فایل را ندارید.");
        }

    }
}