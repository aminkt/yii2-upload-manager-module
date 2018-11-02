<?php

namespace aminkt\uploadManager\interfaces;

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
    public function getPath($size = null, $returnNullIfNotExists=false);

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