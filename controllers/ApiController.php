<?php

namespace aminkt\uploadManager\controllers;

use aminkt\uploadManager\components\Upload;
use aminkt\uploadManager\models\UploadmanagerFiles;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Api controller for the `uploadManager` module to upload files into server, fetch it or remove.
 *
 * @author Amin Keshavarz <Amin@keshavarz.pro>
 */
class ApiController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * Upload file and get id of file in system.
     *
     * @author Amin Keshavarz <Amin@keshavarz.pro>
     */
    public function actionUpload(){
        /** @var UploadmanagerFiles[] $files */
        $files = [];
        foreach ($_FILES as $name => $file){
            $files[] = Upload::directUpload($name);
        }
        \Yii::warning($files);
        for ($i=0; $i<count($files); $i++){
            if($i>0){
                echo ",";
            }
            echo $files[$i]->id;
        }
    }

    /**
     * Delete file from server.
     *
     * @author Amin Keshavarz <Amin@keshavarz.pro>
     */
    public function actionDelete(){
        $fileId = \Yii::$app->getRequest()->getRawBody();
        if(!$fileId){
            throw new BadRequestHttpException("File id is not valid");
        }

        $file = UploadmanagerFiles::findOne($fileId);
        if(!$file){
            return new NotFoundHttpException("File not found in db");
        }

        if(!$file->delete()){
            throw new ServerErrorHttpException("Can not delete file `{$file->id}``");
        }

        // no content to return
        \Yii::$app->getResponse()->setStatusCode(204);
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

    /**
     * Fetch file from another server by url and download it into server.
     *
     * @author Amin Keshavarz <Amin@keshavarz.pro>
     */
    public function actionFetch(){
        throw new BadRequestHttpException("Not implemented yet");
    }

    /**
     * FilePond uses the restore end point to restore temporary server files. This might be useful in a situation where the user closes the browser window but has not finished completing the form. Temporary files can be set with the files property.
     *
     * Step one and two now look like this.
     *
     * * client requests restore of file with id 12345 using a GET request
     * * server returns a file object with header `Content-Disposition: inline; filename=my-file.jpg`
     *
     * @param integer $id
     *
     * @return bool|string|NotFoundHttpException
     * @throws NotFoundHttpException
     *
     * @author Amin Keshavarz <Amin@keshavarz.pro>
     */
    public function actionRestore($id){
        return $this->actionLoad($id);
    }
}
