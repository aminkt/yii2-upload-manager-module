<?php

namespace aminkt\uploadManager\api\v1\controllers;

use aminkt\uploadManager\components\Upload;
use aminkt\uploadManager\models\FileSearch;
use aminkt\uploadManager\models\UploadmanagerFiles;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;


/**
 * Class UploadController
 *
 * Handle upload actions.
 *
 * @package aminkt\uploadManager\api\v1\controllers
 */
class UploadController extends ActiveController
{
    public $modelClass = Business::class;
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'data',
    ];

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
                'methods' => ['*']
            ],
            'actions' => [
                'login' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'login-authed' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'revoke-token' => [
                    'Access-Control-Allow-Credentials' => true,
                ]
            ]
        ];

        // re-add authentication filter
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
            'except' => ['options'],
            'optional' => ['view']
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
        $searchModel = new FileSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
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
        $file = UploadmanagerFiles::findOne($id);
        if(!$file){
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
        $file = UploadmanagerFiles::findOne($id);
        if(!$file){
            throw new NotFoundHttpException("File not found");
        }
        if($file->delete()){
            return [
                'message' => 'File deleted.'
            ];
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
        foreach ($_FILES as $name => $file){
            $files[] = Upload::directUpload($name, $isBase64);
        }
        if(count($files) > 0){
            return $files;
        }
        throw new BadRequestHttpException("File not found.");
    }

    /**
     * Load file from server by id.
     *
     * @param integer   $id
     *
     * @return bool|string|NotFoundHttpException
     * @throws NotFoundHttpException
     *
     * @author Amin Keshavarz <Amin@keshavarz.pro>
     */
    public function actionLoad($id){
        $file = UploadmanagerFiles::findOne($id);
        if(!$file){
            return new NotFoundHttpException("File not found in db");
        }

        $path = $file->getPath(null , true);
        if(!$path){
            throw new NotFoundHttpException("File not found");
        }

        $handle = fopen($path, 'r');
        $content = fread($handle, filesize($path));
        fclose($handle);

        // Return file
        // Allow to read Content Disposition (so we can read the file name on the client side)
        header('Access-Control-Expose-Headers: Content-Disposition');
        header('Content-Type: ' . $file->tags['type']);
        header('Content-Length: ' . $file->tags['size']);
        header('Content-Disposition: inline; filename="' . $file['name'] . '"');

        echo $content;
        exit();
    }
}