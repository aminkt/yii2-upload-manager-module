<?php

namespace aminkt\uploadManager\api\v1\controllers;

use aminkt\uploadManager\components\Upload;
use aminkt\uploadManager\interfaces\FileInterface;
use aminkt\uploadManager\models\FileSearch;
use aminkt\uploadManager\models\UploadmanagerFiles;
use aminkt\uploadManager\UploadManager;
use yii\data\ActiveDataProvider;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;


/**
 * Class UploadController
 *
 * Handle upload actions.
 *
 * @package aminkt\uploadManager\api\v1\controllers
 */
class UploadController extends ActiveController
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'data',
    ];

    public function init()
    {
        $this->modelClass = UploadManager::getInstance()->fileClass;
        parent::init();
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                // restrict access to
                'Origin' => ['*'],
                'methods' => ['GET', 'POST', 'DELETE', 'OPTIONS', 'HEAD']
            ],
            'actions' => [
                '*' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
            ]
        ];

        // re-add authentication filter
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBearerAuth::className(),
                HttpBasicAuth::className(),
                QueryParamAuth::className(),
            ],
            'except' => ['options'],
            'optional' => ['view', 'load']
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        // disable the default actions
        unset($actions['index'], $actions['view'], $actions['delete'], $actions['create'], $actions['update']);

        return $actions;
    }

    /**
     * List of all files
     *
     * @return \yii\data\ActiveDataProvider
     *
     * @author Amin Keshavarz <ak_1596@yahoo.com>
     */
    public function actionIndex()
    {
        $fileSearchModel = UploadManager::getInstance()->fileSearchClass;
        if ($fileSearchModel) {
            $searchModel = new $fileSearchModel();
            $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        } else {
            $fileModel = UploadManager::getInstance()->fileClass;
            $dataProvider = new ActiveDataProvider([
                'query' => $fileModel::find()
            ]);
        }

        return $dataProvider;
    }


    /**
     * View a single file.
     *
     * @param integer $id Business id.
     *
     * @return Business Business model.
     *
     * @throws NotFoundHttpException
     * @throws ForbiddenHttpException
     *
     * @author Amin Keshavarz <ak_1596@yahoo.com>
     */
    public function actionView($id)
    {
        $modelCalssName = UploadManager::getInstance()->fileClass;
        $file = $modelCalssName::findOne($id);
        if (!$file) {
            throw new NotFoundHttpException("File not found");
        }
        return $file;
    }

    /**
     * Delete a model from database. and remove it from server.
     *
     * @param integer $id Business Id.
     *
     * @return void
     *
     * @author Amin Keshavarz <ak_1596@yahoo.com>
     *
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $modelCalssName = UploadManager::getInstance()->fileClass;
        $file = $modelCalssName::findOne($id);
        if (!$file) {
            throw new NotFoundHttpException("File not found");
        }
        if ($file->delete()) {
            \Yii::$app->getResponse()->setStatusCode(204);

        } else {
            throw new ServerErrorHttpException("File did not delete.");
        }
    }


    /**
     * Upload new file.
     *
     * @param bool $isBase64
     *
     * @return UploadmanagerFiles[]|array
     * @throws BadRequestHttpException
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\ServerErrorHttpException
     * @author Saghar Mojdehi <saghar.mojdehi@gmail.com>
     */
    public function actionCreate($isBase64 = false)
    {
        /** @var UploadmanagerFiles[] $files */
        $files = [];
        foreach ($_FILES as $name => $file) {
            $files[] = Upload::directUpload($name, $isBase64);
        }
        if (count($files) > 0) {
            return $files;
        }
        throw new BadRequestHttpException("There is no file to upload.");
    }

    /**
     * Load file from server by id.
     *
     * @param integer $id
     *
     * @return bool|string|NotFoundHttpException
     * @throws NotFoundHttpException
     *
     * @author Amin Keshavarz <Amin@keshavarz.pro>
     */
    public function actionLoad($id)
    {
        $modelCalssName = UploadManager::getInstance()->fileClass;
        /** @var FileInterface $file */
        $file = $modelCalssName::findOne($id);
        if (!$file) {
            throw new NotFoundHttpException("File not found in db.");
        }

        $path = $file->getPath(null, true);
        if (!$path) {
            throw new NotFoundHttpException("File not found");
        }

        $handle = fopen($path, 'r');
        $content = fread($handle, filesize($path));
        fclose($handle);

        // Return file
        // Allow to read Content Disposition (so we can read the file name on the client side)
        header('Access-Control-Expose-Headers: Content-Disposition');
        header('Content-Type: ' . $file->getMeta('type'));
        header('Content-Length: ' . $file->getMeta('size'));
        header('Content-Disposition: inline; filename="' . $file['name'] . '"');

        echo $content;
        exit();
    }
}