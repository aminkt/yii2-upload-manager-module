<?php

use yii\db\Migration;

class m170217_094950_add_user_data extends Migration
{
    public function up()
    {
        $this->addColumn('{{%uploadManager_files}}', 'userId', $this->integer()." AFTER id");
    }

    public function down()
    {
        $this->dropColumn('{{%uploadManager_files}}', 'userId');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
