<?php

namespace aminkt\uploadManager\interfaces;
use yii\web\UploadedFile;

/**
 * Class FileInterface
 * The model calss that want integrate with upload manager module should implement this interface.
 *
 * @package aminkt\uploadManager\interfaces
 */
interface FileInterface
{
    /**
     * Return a unique name for uploaded file.
     * @param UploadedFile $file
     * @param String $fileName
     * @return null|string
     */
    public static function getUploadedFileName($file, $fileName = null);

    /**
     * Return expected directory that file should upload in there.
     * @param string $dir
     * @return string|boolean
     */
    public static function getUploadedFileDir($dir);


    /**
     * In upload time upload module will get file from request and set them into files container in model.
     * this method handle this.
     *
     * @param UploadedFile $file
     *
     * @return void
     */
    public function setFilesContainer($file);

    /**
     * Set id of user who uploaded current file.
     * @param $userId
     * @return void
     */
    public function setUserId($userId);

    /**
     * Return model file type code.
     * @param UploadedFile $file
     * @return int
     */
    public static function getFileTypeCode($file);

    /**
     * Upload file to defined directory
     * @param string $dir
     * @param array $sizes
     * @return bool|string
     */
    public function upload($dir, $sizes = []);

    /**
     * Return label to show file type.
     *
     * @return mixed
     */
    public function getTypeLabel();

    /**
     * Return url address of file.
     *
     * @param null|string $size File size.
     *
     * @return string
     *
     * @author Amin Keshavarz <amin@keshavarz.pro>
     */
    public function getUrl($size = null);

    /**
     * Return file path.
     *
     * @param null $size
     * @param bool  $returnNullIfNotExists
     *
     * @return string
     *
     * @author Amin Keshavarz <amin@keshavarz.pro>
     */
    public function getPath($size = null);


    /**
     * Get meta data of file.
     *
     * @param null|string $name Meta name.
     *
     * @return array|string
     */
    public function getMeta($metaName);

    /**
     * Return orginal file name.
     *
     * @return string
     *
     * @author Amin Keshavarz <ak_1596@yahoo.com>
     */
    public function getFileName();

    /**
     * Delete all instance of current file from server.
     *
     * @return void
     */
    public function deleteFiles();

    /**
     * Return id of file.
     *
     * @return mixed
     */
    public function getId();
}