<?php

use yii\db\Migration;

class m170217_094950_add_user_data extends Migration
{
    public function up()
    {
        $this->addColumn('{{%files}}', 'user_id', $this->integer() . " AFTER id");
    }

    public function down()
    {
        $this->dropColumn('{{%files}}', 'user_id');
    }
}
