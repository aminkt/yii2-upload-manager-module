<?php

namespace aminkt\uploadManager\traits;

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
    public function rules()
    {

        return [
            [['metaData', 'extraData'], 'string'],
            [['userId'], 'integer'],
            [['status'], 'in', 'range' => [static::STATUS_DISABLE, static::STATUS_ENABLE]],
            [['fileType'], 'in', 'range' => [
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
            $this->metaData = Json::encode([
                'name' => $this->filesContainer->name,
                'type' => $this->filesContainer->type,
                'size' => $this->filesContainer->size,
                'error' => $this->filesContainer->error,
            ]);
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
    public function getTypeLabel(){
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
        $meta = Json::decode($this->metaData, true);
        $type = $meta['type'];
        $type = explode('/', $type);
        if ($size and $type[0] == 'image')
            $address = self::getFileDirectory($this->file) . '/' . $size . '_' . $this->name;
        else
            $address = self::getFileDirectory($this->file) . '/' . $this->name;

        $uploadUrl = Yii::$app->getModule('uploadManager')->uploadUrl;
        return $uploadUrl . '/' . $address;
    }

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
    public function getPath($size = null, $returnNullIfNotExists=false)
    {
        if ($size)
            $address = self::getFileDirectory($this->file) . '/' . $size . '_' . $this->name;
        else
            $address = self::getFileDirectory($this->file) . '/' . $this->name;

        $uploadPath = Yii::$app->getModule('uploadManager')->uploadPath;
        $noImage = Yii::$app->getModule('uploadManager')->noImage;

        $p = FileHelper::normalizePath($uploadPath . '/' . $address);
        if (file_exists($p))
            return FileHelper::normalizePath($uploadPath . '/' . $address);
        else
            return $returnNullIfNotExists? null : FileHelper::normalizePath($uploadPath . '/' . $noImage);
    }

    /**
     * Delete all instance of current file.
     *
     * @return void
     */
    public function deleteFiles(){
        $sizes = UploadManager::getInstance()->sizes;
        foreach ($sizes as $name=>$size){
            $path = $this->getPath($name, true);
            if($path){
                unlink($path);
            }else{
                Yii::warning("Can not delete file {$path}");
            }
        }

        $path = $this->getPath(null, true);
        if($path){
            unlink($path);
        }else{
            Yii::warning("Can not delete file {$path}");
        }
    }

    /**
     * Return orginal file name.
     *
     * @return string
     *
     * @author Amin Keshavarz <ak_1596@yahoo.com>
     */
    public function getFileName(){
        return $this->getMeta('name');
    }

    /**
     * Return file directory
     *
     * @param $file
     *
     * @return string
     */
    public static function getFileDirectory($file){
        $file = explode('/', $file);
        $f = "";
        $fSize = count($file);
        for ($i=0; $i<$fSize-1; $i++){
            $f.=$file[$i];
            if($i != $fSize-2){
                $f.="/";
            }
        }
        return $f;
    }
}