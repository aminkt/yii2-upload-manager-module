<?php

namespace aminkt\uploadManager\models;

use aminkt\uploadManager\interfaces\FileConstantsInterface;
use aminkt\uploadManager\interfaces\FileInterface;
use aminkt\uploadManager\traits\FileTrait;
use aminkt\uploadManager\UploadManager;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "{{%files}}".
 *
 * @property integer $id
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
 * @property string $file_name
 */
class File extends \yii\db\ActiveRecord implements FileInterface, FileConstantsInterface
{
    use FileTrait {
        rules as traitRuels;
        fields as traitFields;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $moduleName = UploadManager::getInstance()->id;
        return "{{%{$moduleName}_files}}";
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
                'value' => new Expression('NOW()'),
            ],
        ];
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
    public function fields()
    {
        return $this->traitFields();
    }

    public function rules()
    {
        return $this->traitRuels();
    }

    public function getId()
    {
        return $this->id;
    }
}
