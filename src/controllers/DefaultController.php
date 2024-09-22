<?php

namespace aminkt\uploadManager\controllers;

use aminkt\uploadManager\components\Upload;
use aminkt\uploadManager\models\File;
use aminkt\uploadManager\models\FileSearch;
use aminkt\uploadManager\UploadManager;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Default controller for the `uploadManager` module
 */
class DefaultController extends Controller
{
    /** @var UploadManager $uploadManager */
    public $uploadManager = null;

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

    public function init()
    {
        parent::init();
        $this->uploadManager = UploadManager::getInstance();
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $model = new FileSearch();
        $dataProvider = $model->search(\Yii::$app->getRequest()->get());

        if (\Yii::$app->request->isAjax) {
            return $this->renderAjax('index', [
                'model' => $model,
                'dataProvider' => $dataProvider,
            ]);
        }

        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $dataProvider,
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
        if (\Yii::$app->request->isPost) {
            $file = Upload::directUpload();
            echo Json::encode($file->getMetaData());
            return true;
        } elseif (\Yii::$app->request->isGet)
            return $this->render('upload');
        elseif (\Yii::$app->request->isAjax)
            return $this->renderAjax('upload');
        throw new ForbiddenHttpException("شما دسترسی های مجاز برای مرور این صفحه را ندارید.");
    }


    /**
     * Show uploadManager options as a ajax.
     * @param bool $multiple
     * @param null $counter
     * @return string
     */
    public function actionAjax($multiple = false, $counter = null)
    {
        $model = new FileSearch();

        $dataProvider = $model->search(\Yii::$app->getRequest()->post());

        $selected = \Yii::$app->getRequest()->post('selected');
        $selectedArray = $selected ? explode(',', $selected) : [];

        return $this->renderAjax('ajax', [
            'dataProvider' => $dataProvider,
            'model' => $model,
            'multiple' => $multiple,
            'counter' => $counter,
            'selectedItems' => $selectedArray
        ]);
    }

    /**
     * Delete file
     *
     * @param $id
     */
    public function actionDelete($id)
    {
        $file = File::findOne($id);
        if (!$file) {
            throw new NotFoundHttpException("File not found");
        }

        if ($file->userId != \Yii::$app->getUser()->getId() and
            !in_array(\Yii::$app->getUser()->getId(), UploadManager::getInstance()->adminId)) {
            throw new ForbiddenHttpException("You just can delete your files.");
        }

        $file->delete();

        return $this->redirect(['index']);
    }

}
