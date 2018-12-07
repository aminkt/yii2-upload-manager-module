<?php

namespace aminkt\uploadManager\classes;

use yii\helpers\BaseFileHelper;

/**
 * Class UploadedBase64File
 *
 * @package aminkt\uploadManager\classes
 *
 * @author  Amin Keshavarz <amin@keshavarz.pro>
 */
class UploadedBase64File extends \yii\web\UploadedFile
{
    private static $_files;

    public static function uploadBase64File($inputName, $method = 'post')
    {
        $file = \Yii::$app->getRequest()->$method($inputName);
        if (!$file) {
            return null;
        }
        $fileDecoded = base64_decode($file);

        $f = finfo_open();

        $mimeType = finfo_buffer($f, $fileDecoded, FILEINFO_MIME_TYPE);
        $sizes = strlen($fileDecoded);
        $ext = BaseFileHelper::getExtensionsByMimeType($mimeType);

        if (!self::$_files) {
            self::$_files = [];
        }

        self::$_files[$inputName] = [
            'name' => "{$inputName}.{$ext[0]}",
            'tempName' => $file,
            'type' => $mimeType,
            'size' => $sizes,
            'error' => UPLOAD_ERR_OK,
        ];

        return isset(self::$_files[$inputName]) ? new static(self::$_files[$inputName]) : null;
    }

    /**
     * Saves the uploaded file.
     * Note that this method uses php's move_uploaded_file() method. If the target file `$file`
     * already exists, it will be overwritten.
     *
     * @param string $file           the file path used to save the uploaded file
     * @param bool   $deleteTempFile whether to delete the temporary file after saving.
     *                               If true, you will not be able to save the uploaded file again in the current
     *                               request.
     *
     * @return bool true whether the file is saved successfully
     * @see error
     */
    public function saveAs($file, $deleteTempFile = true)
    {
        if ($this->error == UPLOAD_ERR_OK) {
            $file = file_put_contents($file, base64_decode($this->tempName));
            $this->tempName = $this->name;
            return $file;
        }

        return false;
    }
}