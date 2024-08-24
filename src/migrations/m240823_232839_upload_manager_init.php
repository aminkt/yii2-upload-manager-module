<?php

use aminkt\uploadManager\models\File;
use aminkt\uploadManager\UploadManager;
use yii\db\Migration;
use yii\helpers\Inflector;

/**
 * Class m240823_232839_upload_manager_init
 */
class m240823_232839_upload_manager_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(File::tableName(), [
            'id'=>$this->primaryKey(),
            'user_id' => $this->integer(),
            'name'=>$this->string(191),
            'description'=>$this->string(191),
            'file'=>$this->string(191)->unique(),
            'extension'=>$this->string(20),
            'meta_data'=>$this->text(),
            'extra_data'=>$this->text(),
            'status'=>$this->smallInteger(1),
            'file_type'=>$this->smallInteger(2),
            'update_at'=>$this->dateTime(),
            'create_at'=>$this->dateTime()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable(File::tableName());
        $this->dropTable(File::tableName());
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240823_232839_upload_manager_init cannot be reverted.\n";

        return false;
    }
    */
}
