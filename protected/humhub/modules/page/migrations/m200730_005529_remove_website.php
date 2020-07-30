<?php


use yii\db\Migration;

class m200730_005529_remove_website extends Migration
{
    public function up()
    {
        $this->dropColumn('page', 'website');
    }

    public function down()
    {
        echo "m160225_180229_remove_website cannot be reverted.\n";

        return false;
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
