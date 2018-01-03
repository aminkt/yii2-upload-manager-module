<?php

namespace aminkt\uploadManager\models;

use common\components\YiiJDF;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\imagine\Image;
use yii\web\UploadedFile;
use yii\db\Expression;

/**
 * This is the model class for table "{{%uploadmanager_files}}".
 *
 * @property integer $id
 * @property integer $userId
 * @property string $name
 * @property string $description
 * @property string $file
 * @property string $extension
 * @property string $metaData
 * @property string $extraData
 * @property integer $status
 * @property integer $fileType
 * @property string $updateTime
 * @property string $createTime
 *
 *
 * @property array $tags
 * @property array $type
 */
class UploadmanagerFiles extends \yii\db\ActiveRecord
{
    const FILE_TYPE_IMAGE = 1;
    const FILE_TYPE_VIDEO = 2;
    const FILE_TYPE_AUDIO = 3;
    const FILE_TYPE_ARCHIVE = 4;
    const FILE_TYPE_DOCUMENT = 5;
    const FILE_TYPE_APPLICATION = 6;
    const FILE_TYPE_UNDEFINED = 7;

    const STATUS_DISABLE = 0;
    const STATUS_ENABLE = 1;


    /**
     * @var UploadedFile container of file uploaded
     */
    public $filesContainer;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return "{{%uploadManager_files}}";
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['createTime', 'updateTime'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updateTime'],
                ],
                // if you're using datetime instead of UNIX timestamp:
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['metaData', 'extraData'], 'string'],
            [['status', 'fileType', 'userId'], 'integer'],
            [['name', 'description', 'file'], 'string', 'max' => 255],
            [['extension'], 'string', 'max' => 20],
            [['file'], 'unique'],
            [['filesContainer',], 'file', 'skipOnEmpty'=>true],
        ];
    }

    /**
     * Return a unique name for uploaded file.
     * @param UploadedFile $file
     * @param String $fileName
     * @return null|string
     */
    public static function getUploadedFileName($file, $fileName = null){
        if($file){
            $unique = md5($file->baseName);
            $unique = substr($unique, 1,5);
            if(!$fileName){
                $fileName = md5($file->baseName);
            }
            return time()."_".$fileName."_".$unique.'.'.$file->extension;
        }
        return null;
    }

    /**
     * Return expected directory that file should upload in there.
     * @param string $dir
     * @return string|boolean
     */
    public static function getUploadedFileDir($dir){
        $path = $dir.DIRECTORY_SEPARATOR.date('Y', time()).DIRECTORY_SEPARATOR.date('n', time());
        if(FileHelper::createDirectory($path)){
            return date('Y', time()).'/'.date('n', time());
        }
        return false;
    }

    /**
     * Return model file type code.
     * @param UploadedFile $file
     * @return int
     */
    public static function getFileTypeCode($file){
        $type = $file->type;
        switch (1){
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
     * Upload file to defined directory
     * @param string $dir
     * @param array $sizes
     * @return bool|string
     */
    public function upload($dir, $sizes = []){
        if($this->filesContainer){
            $this->name = static::getUploadedFileName($this->filesContainer);
            $this->extension = $this->filesContainer->extension;
            $this->metaData = Json::encode($this->filesContainer);
            $this->status = static::STATUS_ENABLE;
            $this->fileType = static::getFileTypeCode($this->filesContainer);

            if($directory = static::getUploadedFileDir($dir)){
                $this->file = $directory.'/'.$this->name;
                if ($this->validate()){
                    $file = false;
                    if($this->filesContainer->saveAs(FileHelper::normalizePath($dir.DIRECTORY_SEPARATOR.$this->file))){
                        $file = $this->file;
                    }

                    if($this->fileType == self::FILE_TYPE_IMAGE and $file) {
                        foreach ($sizes as $key=>$size){
                            $Imagin = Image::thumbnail(FileHelper::normalizePath($dir.DIRECTORY_SEPARATOR.$this->file), $size[0], $size[1])
                                ->save(FileHelper::normalizePath($dir.DIRECTORY_SEPARATOR.$directory.DIRECTORY_SEPARATOR.$key.'_'.$this->name));
                        }
                    }
                    $this->filesContainer = null;
                    return  $file;
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
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'نام',
            'description' => 'توضیحات',
            'file' => 'File',
            'extension' => 'پسوند',
            'metaData' => 'اطلاعات الصاقی',
            'extraData' => 'Extra Data',
            'status' => 'وضعیت',
            'fileType' => 'نوع فایل',
            'createTime' => 'زمان ایجاد',
            'type' => 'نوع فایل',
        ];
    }

    public function getTags(){
        return Json::decode($this->metaData);
    }

    public function getType(){
        switch ($this->fileType){
            case static::FILE_TYPE_IMAGE:
                return 'تصویر';
            case static::FILE_TYPE_VIDEO:
                return 'ویدئو';
            case static::FILE_TYPE_AUDIO:
                return 'صدا';
            case static::FILE_TYPE_ARCHIVE:
                return 'فایل فشرده';
            case static::FILE_TYPE_DOCUMENT:
                return 'اسناد';
            case static::FILE_TYPE_APPLICATION:
                return 'نرم افزار';
            case static::FILE_TYPE_UNDEFINED:
                return 'تعیین نشده';
            default:
                return 'خطا در دریافت اطلاعات';
        }
    }

    public static function getFileDirectory($file){
        $file = explode('/', $file);
        $f = "";
        $fSize = count($file);
        Yii::trace($file);
        for ($i=0; $i<$fSize-1; $i++){
            $f.=$file[$i];
            if($i != $fSize-2){
                $f.="/";
            }
        }
        return $f;
    }
}
