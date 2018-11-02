<?php

namespace aminkt\uploadManager\models\mongo;

use aminkt\uploadManager\interfaces\FileConstantsInterface;
use aminkt\uploadManager\interfaces\FileInterface;
use aminkt\uploadManager\traits\FileTrait;
use aminkt\uploadManager\UploadManager;
use common\components\YiiJDF;
use MongoDB\BSON\ObjectId;
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
 * @property ObjectId $_id
 * @property mixed $id
 * @property integer $user_id
 * @property string $name
 * @property string $description
 * @property string $file
 * @property string $extension
 * @property string $meta_data
 * @property string $extra_data
 * @property integer $status
 * @property integer $file_type
 * @property string $update_at
 * @property string $create_at
 *
 *
 * @property array $typeLabel
 * @property string $fileName
 */
class File extends \yii\mongodb\ActiveRecord implements FileInterface, FileConstantsInterface
{
    use FileTrait {
      rules as traitRules;
    }

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'files';
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
    public function attributes()
    {
        return [
            '_id',
            'user_id',
            'name',
            'description',
            'file',
            'extension',
            'meta_data',
            'extra_data',
            'status',
            'file_type',
            'update_at',
            'create_at'
        ];
    }

    public function rules()
    {
        return $this->traitRules(true);
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
     * @inheritdoc
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'id',
            'file_name' => 'fileName',
            'file_type' => 'fileTypeCode',
            'file_extension',
            'file_url',
            'thumbnails' => 'thumbnailUrls'
        ];
    }
}
