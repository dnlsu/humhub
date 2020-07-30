<?php


use yii\db\Migration;

class m200730_005417_membership extends Migration {

    public function up() {


        $this->createTable('page_membership', [
            'page_id' => 'int(11) NOT NULL',
            'user_id' => 'int(11) NOT NULL',
            'originator_user_id' => 'varchar(45) DEFAULT NULL',
            'status' => 'tinyint(4) DEFAULT NULL',
            'request_message' => 'text DEFAULT NULL',
            'last_visit' => 'datetime DEFAULT NULL',
            'invite_role' => 'tinyint(4) DEFAULT NULL',
            'admin_role' => 'tinyint(4) DEFAULT NULL',
            'share_role' => 'tinyint(4) DEFAULT NULL',
            'created_at' => 'datetime DEFAULT NULL',
            'created_by' => 'int(11) DEFAULT NULL',
            'updated_at' => 'datetime DEFAULT NULL',
            'updated_by' => 'int(11) DEFAULT NULL',
        ], '');

        $this->addPrimaryKey('pk_user_page_membership', 'page_membership', 'page_id,user_id');
        $this->createIndex('index_status', 'page_membership', 'status', false);

    }

    public function down() {
        echo "m140324_170617_membership does not support migration down.\n";
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
