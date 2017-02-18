<?php

namespace uploadManager\controllers;

use uploadManager\models\UploadmanagerFiles;
use uploadManager\UploadManager;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

/**
 * Default controller for the `uploadManager` module
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /** @var UploadManager $uploadManager */
    public $uploadManager = null;

    public function init()
    {
        parent::init();
        $this->uploadManager = \Yii::$app->getModule('uploadManager');
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $model = new UploadmanagerFiles();

        $dataProvider = new ActiveDataProvider([
            'query'=>$model::find()->where(['userId'=>\Yii::$app->getUser()->getId()])
        ]);
        if(\Yii::$app->request->isAjax)
            return $this->renderAjax('index', [
                'model'=>$model,
                'dataProvider'=>$dataProvider,
            ]);
        return $this->render('index', [
            'model'=>$model,
            'dataProvider'=>$dataProvider,
        ]);
    }

    /**
     * Renders the upload view for the module
     * @return string
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws ServerErrorHttpException
     */
    public function actionUpload()
    {
        if(\Yii::$app->request->isPost){
            $fileName = 'file';
            $uploadPath = $this->uploadManager->uploadPath;
            $size = $this->uploadManager->sizes;
            $model = new UploadmanagerFiles();
            $model->userId = \Yii::$app->getUser()->getId();
            if (isset($_FILES[$fileName])) {
                $model->filesContainer = UploadedFile::getInstanceByName($fileName);
                if ($model->upload($uploadPath, $size)) {
                    //Now save file data to database
                    $model->save();
                    echo Json::encode($model->metaData);
                    return true;
                }
                throw new ServerErrorHttpException("فایل در سرور ذخیره نشد.");
            }
            throw new BadRequestHttpException("فایل به سرور ارسال نشد.");
        }
        elseif(\Yii::$app->request->isGet)
            return $this->render('upload');
        elseif(\Yii::$app->request->isAjax)
            return $this->renderAjax('upload');
        throw new ForbiddenHttpException("شما دسترسی های مجاز برای مرور این صفحه را ندارید.");
    }


    /**
     * Show uploadManager options as a ajax.
     * @param bool $multiple
     * @param null $counter
     * @return string
     */
    public function actionAjax($multiple = false, $counter = null){
        $model = new UploadmanagerFiles();

        $dataProvider = new ActiveDataProvider([
            'query'=>$model::find()->where(['userId'=>\Yii::$app->getUser()->getId()])
        ]);

        return $this->renderAjax('ajax', [
            'dataProvider'=>$dataProvider,
            'multiple'=>$multiple,
            'counter'=>$counter,
        ]);
    }

}
