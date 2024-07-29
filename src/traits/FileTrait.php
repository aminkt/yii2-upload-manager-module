<?php

namespace aminkt\uploadManager\traits;

use aminkt\uploadManager\UploadManager;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\imagine\Image;
use yii\web\UploadedFile;

/**
 * Trait FileTrait
 * Implement some usefull function for File active record that you should use it in you model.
 *
 * @package aminkt\uploadManager\traits
 *
 * @author Amin Keshavarz <ak_1596@yahoo.com>
 */
trait FileTrait
{

    /**
     * @var UploadedFile container of file uploaded
     */
    public $filesContainer;

    /**
     * @inheritdoc
     */
    public function rules($isMongo = false)
    {
        $userCalssName = UploadManager::getInstance()->userClass;
        $idColName = $isMongo ? '_id' : 'id';
        return [
            [['meta_data', 'extra_data'], 'string'],
            [['user_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => $userCalssName,
                'targetAttribute' => ['user_id' => $idColName]
            ],
            [['status'], 'in', 'range' => [static::STATUS_DISABLE, static::STATUS_ENABLE]],
            [['file_type'], 'in', 'range' => [
                static::FILE_TYPE_IMAGE,
                static::FILE_TYPE_VIDEO,
                static::FILE_TYPE_AUDIO,
                static::FILE_TYPE_ARCHIVE,
                static::FILE_TYPE_DOCUMENT,
                static::FILE_TYPE_APPLICATION,
                static::FILE_TYPE_UNDEFINED,
            ]],
            [['name', 'description', 'file'], 'string', 'max' => 255],
            [['extension'], 'string', 'max' => 20],
            [['file'], 'unique'],
            [['filesContainer',], 'file', 'skipOnEmpty' => true],
        ];
    }

    /**
     * Return a unique name for uploaded file.
     * @param UploadedFile $file
     * @param String $fileName
     * @return null|string
     */
    public static function getUploadedFileName($file, $fileName = null)
    {
        if ($file) {
            $unique = md5($file->baseName);
            $unique = substr($unique, 1, 5);
            if (!$fileName) {
                $fileName = md5($file->baseName);
            }
            return time() . "_" . $fileName . "_" . $unique . '.' . $file->extension;
        }
        return null;
    }

    /**
     * Return expected directory that file should upload in there.
     * @param string $dir
     * @return string|boolean
     */
    public static function getUploadedFileDir($dir)
    {
        $path = $dir . DIRECTORY_SEPARATOR . date('Y', time()) . DIRECTORY_SEPARATOR . date('n', time());
        if (FileHelper::createDirectory($path)) {
            return date('Y', time()) . '/' . date('n', time());
        }
        return false;
    }

    /**
     * Return model file type code.
     * @param UploadedFile $file
     * @return int
     */
    public static function getFileTypeCode($file)
    {
        $type = $file->type;
        switch (1) {
            case preg_match("/image/", $type):
                return static::FILE_TYPE_IMAGE;
                break;
            case preg_match("/video/", $type):
                return static::FILE_TYPE_VIDEO;
                break;
            case preg_match("/audio/", $type):
                return static::FILE_TYPE_AUDIO;
                break;
            case preg_match("/archive|rar|tar|zip/", $type):
                return static::FILE_TYPE_ARCHIVE;
                break;
            case preg_match("/document|pdf|text/", $type):
                return static::FILE_TYPE_DOCUMENT;
                break;
            case preg_match("/application/", $type):
                return static::FILE_TYPE_APPLICATION;
                break;
            default:
                return static::FILE_TYPE_UNDEFINED;
        }
    }

    /**
     * Serialize file metadata.
     * If you are using mongo db change this method to return just an array.
     *
     * @return mixed
     */
    protected function serializeMetaData()
    {
        return Json::encode([
            'name' => $this->filesContainer->name,
            'type' => $this->filesContainer->type,
            'size' => $this->filesContainer->size,
            'error' => $this->filesContainer->error,
        ]);
    }

    /**
     * Deserialize meta data that serialized in uploading.
     * If you are using mongo db change thi method to just return an array.
     * @return mixed
     */
    protected function deserializeMetaData()
    {
        return Json::decode($this->meta_data, true);
    }


    /**
     * Upload file to defined directory
     * @param string $dir
     * @param array $sizes
     * @return bool|string
     */
    public function upload($dir, $sizes = [])
    {
        if ($this->filesContainer) {
            $this->name = static::getUploadedFileName($this->filesContainer);
            $this->extension = $this->filesContainer->extension;
            $this->meta_data = $this->serializeMetaData();
            $this->status = static::STATUS_ENABLE;
            $this->file_type = static::getFileTypeCode($this->filesContainer);

            if ($directory = static::getUploadedFileDir($dir)) {
                $this->file = $directory . '/' . $this->name;
                if ($this->validate()) {
                    $file = false;

                    if (YII_ENV_TEST) {
                        if (copy($this->filesContainer->tempName, FileHelper::normalizePath($dir . DIRECTORY_SEPARATOR . $this->file))) {
                            $file = $this->file;
                        }
                    } else {
                        if ($this->filesContainer->saveAs(FileHelper::normalizePath($dir . DIRECTORY_SEPARATOR . $this->file))) {
                            $file = $this->file;
                        }
                    }


                    if ($this->file_type == self::FILE_TYPE_IMAGE and $file) {
                        foreach ($sizes as $key => $size) {
                            $Imagin = Image::thumbnail(FileHelper::normalizePath($dir . DIRECTORY_SEPARATOR . $this->file), $size[0], $size[1])
                                ->save(FileHelper::normalizePath($dir . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . $key . '_' . $this->name));
                        }
                    }
                    $this->filesContainer = null;
                    return $file;
                }
                return false;
            }
            throw new \RuntimeException("پوشه بندی برای آپلود فایل با مشکل رو به رو شد.");
        }
        throw new \InvalidArgumentException("فایل ارسال شده معتبر نیست.");
    }

    /**
     * @inheritdoc
     */
    public function getTypeLabel()
    {
        switch ($this->file_type) {
            case static::FILE_TYPE_IMAGE:
                return 'image';
            case static::FILE_TYPE_VIDEO:
                return 'video';
            case static::FILE_TYPE_AUDIO:
                return 'voice';
            case static::FILE_TYPE_ARCHIVE:
                return 'archive_file';
            case static::FILE_TYPE_DOCUMENT:
                return 'document';
            case static::FILE_TYPE_APPLICATION:
                return 'application';
            case static::FILE_TYPE_UNDEFINED:
                return 'undifined';
            default:
                return 'error_on_catch';
        }
    }

    /**
     * Return url address of file.
     *
     * @param null|string $size File size.
     *
     * @return string
     *
     * @author Amin Keshavarz <amin@keshavarz.pro>
     */
    public function getUrl($size = null)
    {
        $meta = $this->deserializeMetaData();
        $type = $meta['type'];
        $type = explode('/', $type);
        if ($size and $type[0] == 'image')
            $address = self::getFileDirectory($this->file) . '/' . $size . '_' . $this->name;
        else
            $address = self::getFileDirectory($this->file) . '/' . $this->name;

        $uploadUrl = UploadManager::getInstance()->uploadBaseUrl;
        return $uploadUrl . '/' . $address;
    }

    /**
     * Return file path.
     *
     * @param null $size
     * @param bool $returnNullIfNotExists
     *
     * @return string
     *
     * @author Amin Keshavarz <amin@keshavarz.pro>
     */
    public function getPath($size = null)
    {
        if ($size)
            $address = self::getFileDirectory($this->file) . '/' . $size . '_' . $this->name;
        else
            $address = self::getFileDirectory($this->file) . '/' . $this->name;

        $uploadPath = UploadManager::getInstance()->uploadPath;

        $p = FileHelper::normalizePath($uploadPath . '/' . $address);
        if (file_exists($p))
            return FileHelper::normalizePath($uploadPath . '/' . $address);
        else
            return null;
    }

    /**
     * Delete all instance of current file.
     *
     * @return void
     */
    public function deleteFiles()
    {
        $sizes = UploadManager::getInstance()->sizes;
        foreach ($sizes as $name => $size) {
            $path = $this->getPath($name, true);
            if ($path) {
                unlink($path);
            } else {
                Yii::warning("Can not delete file {$path}");
            }
        }

        $path = $this->getPath(null, true);
        if ($path) {
            unlink($path);
        } else {
            Yii::warning("Can not delete file {$path}");
        }
    }

    /**
     * @inheritdoc
     */
    public function setFilesContainer($file)
    {
        $this->filesContainer = $file;
    }

    /**
     * set uploader user id.
     *
     * @param $userId
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
    }

    /**
     * Return list of tumbnail of files.
     *
     * @return array
     */
    public function getTumbnailUrls()
    {
        if ($this->file_type != static::FILE_TYPE_IMAGE) {
            return [];
        }
        $sizes = UploadManager::getInstance()->sizes;
        $urls = [];
        foreach ($sizes as $name => $size) {
            $url = $this->getUrl($name);
            $urls[$name] = $url;
        }

        return $urls;
    }

    /**
     * Return orginal file name.
     *
     * @return string
     *
     * @author Amin Keshavarz <ak_1596@yahoo.com>
     */
    public function getFileName()
    {
        return $this->getMeta('name');
    }


    /**
     * Get meta data
     *
     * @param null|string $name Meta name.
     *
     * @return array|string
     */
    public function getMeta($name = null)
    {
        $meta = $this->deserializeMetaData();
        if ($name && $meta) {
            return $meta[$name];
        }
        return $meta;
    }

    /**
     * Return file directory
     *
     * @param $file
     *
     * @return string
     */
    public static function getFileDirectory($file)
    {
        $file = explode('/', $file);
        $f = "";
        $fSize = count($file);
        for ($i = 0; $i < $fSize - 1; $i++) {
            $f .= $file[$i];
            if ($i != $fSize - 2) {
                $f .= "/";
            }
        }
        return $f;
    }

    /**
     * Return api fields.
     */
    public function fields()
    {
        return [
            'id',
            'file_name' => 'fileName',
            'file_type' => 'typeLabel',
            'file_extension' => 'extension',
            'size' => function ($model) {
                return $model->getMeta('size');
            },
            'file_url' => 'url',
            'thumbnails' => 'tumbnailUrls'
        ];
    }

    /**
     * Return user class.
     *
     * @return null|static
     */
    public function getOwner()
    {
        /** @var ActiveRecord $userClass */
        $userClass = UploadManager::getInstance()->userClass;
        $user = $userClass::findOne($this->user_id);
        return $user;
    }
}