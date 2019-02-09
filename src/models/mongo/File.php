<?php

namespace aminkt\yii2\uploadmanager\models\mongo;

use aminkt\yii2\uploadmanager\interfaces\FileConstantsInterface;
use aminkt\yii2\uploadmanager\interfaces\FileInterface;
use aminkt\yii2\uploadmanager\traits\FileTrait;
use aminkt\yii2\uploadmanager\UploadManager;
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
      fields as traitFields;
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['create_at', 'update_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['update_at'],
                ],
                // if you're using datetime instead of UNIX timestamp:
                'value' => new \MongoDB\BSON\UTCDateTime(time() + 1000),
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
        return $this->traitFields();
    }
}
