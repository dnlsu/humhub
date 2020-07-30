<?php


use yii\db\Migration;

class m200730_005446_addState extends Migration
{

    public function up()
    {
        $this->addColumn('page_module', 'state', 'int(4)');
        $this->dropColumn('page_module', 'created_at');
        $this->dropColumn('page_module', 'created_by');
        $this->dropColumn('page_module', 'updated_at');
        $this->dropColumn('page_module', 'updated_by');

        $this->update('page_module', ['state' => 1]);
    }

    public function down()
    {
        echo "m140901_112246_addState does not support migration down.\n";
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
