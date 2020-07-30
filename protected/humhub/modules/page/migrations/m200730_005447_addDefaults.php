<?php


use yii\db\Migration;

class m200730_005447_addDefaults extends Migration
{

    public function up()
    {
        $this->insert('setting', ['name'=>'defaultVisibility', 'module_id'=>'page', 'value'=>'1']);
        $this->insert('setting', ['name'=>'defaultJoinPolicy', 'module_id'=>'page', 'value'=>'1']);
    }

    public function down()
    {
        echo "m141022_094635_addDefaults does not support migration down.\n";
        return false;
    }

    /*
      // Use safeUp/safeDown to do migration with transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
