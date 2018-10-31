<?php

namespace aminkt\uploadManager\models;

use aminkt\uploadManager\interfaces\FileConstantsInterface;
use aminkt\uploadManager\interfaces\FileInterface;
use aminkt\uploadManager\traits\FileTrait;
use aminkt\uploadManager\UploadManager;
use common\components\YiiJDF;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\imagine\Image;
use yii\web\UploadedFile;

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
 * @property string $fileName
 */
class File extends \yii\db\ActiveRecord implements FileInterface, FileConstantsInterface
{
    use FileTrait {
        FileTrait::rules as traitRules;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return "{{%uploadmanager_files}}";
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
        return $this->traitRules();
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

    /**
     * Get meta data of file.
     *
     * @deprecated Use getMeta() instead of this method.
     *
     * @return array
     */
    public function getTags(){
        return $this->getMeta();
    }

    /**
     * Get meta data
     *
     * @param null|string $name Meta name.
     *
     * @return array|string
     */
    public function getMeta($name = null){
        $meta = Json::decode($this->metaData);
        if($name){
            return $meta[$name];
        }
        return $meta;
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        $this->deleteFiles();
        return parent::beforeDelete();
    }

    /**
     * Return user class.
     *
     * @return null|static
     */
    public function getOwner(){
        /** @var ActiveRecord $userClass */
        $userClass = UploadManager::getInstance()->userClass;
        $user = $userClass::findOne($this->userId);
        return $user;
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'id',
            'fileName',
            'name',
            'description',
            'extension',
            'type',
            'owner',
            'url'
        ];
    }
}
