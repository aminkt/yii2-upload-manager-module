<?php

use yii\db\Migration;

class m160915_111512_init extends Migration
{
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
//        $this->createTable("{{%uploadManager_albums}}", [
//            'id'=>$this->primaryKey(),
//            'name'=>$this->string()->notNull(),
//            'slug'=>$this->string(),
//            'description'=>$this->string(),
//            'coverId'=>$this->integer(),
//            'status'=>$this->smallInteger(1),
//            'createTime'=>$this->integer(20),
//
//        ]);

        $this->createTable("{{%files}}", [
            'id'=>$this->primaryKey(),
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
        ], $tableOptions);


//        $this->addForeignKey('uploadManager_file_album_fk', "{{%uploadManager_files}}", 'albumId', "{{%uploadManager_albums}}", 'id', 'CASCADE', 'CASCADE');
//        $this->addForeignKey('uploadManager_album_file_fk', "{{%uploadManager_albums}}", 'coverId', "{{%uploadManager_files}}", 'id', 'SET NULL', 'CASCADE');
    }

    public function safeDown()
    {
//        $this->dropForeignKey('uploadManager_album_file_fk', '{{%uploadManager_albums}}');
//        $this->dropForeignKey('uploadManager_file_album_fk', '{{%uploadManager_files}}');

        $this->truncateTable("{{%files}}");
//        $this->truncateTable("{{%uploadManager_albums}}");

        $this->dropTable("{{%files}}");
//        $this->dropTable("{{%uploadManager_albums}}");
    }
}
