<?php

use yii\db\Schema;
use yii\db\Migration;

class m200730_005513_foreign_keys extends Migration
{

    public function up()
    {
        // Cleanup orphaned records
        $this->db->createCommand('DELETE page_module FROM page_module LEFT JOIN page s ON s.id=page_module.page_id WHERE s.id IS NULL and page_module.page_id != 0')->execute();

        try {
            $this->addForeignKey('fk_page_membership-user_id', 'page_membership', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');
        } catch (Exception $ex) {
            Yii::error($ex->getMessage());
        }

        try {
            $this->addForeignKey('fk_page_membership-page_id', 'page_membership', 'page_id', 'page', 'id', 'CASCADE', 'CASCADE');
        } catch (Exception $ex) {
            Yii::error($ex->getMessage());
        }

        try {
            $this->alterColumn('page_module', 'page_id', $this->integer()->null());
            $this->update('page_module', ['page_id' => new yii\db\Expression('NULL')], ['page_id' => 0]);

            # Not required in 1.3
            #$this->addForeignKey('fk_page_module-page_id', 'page_module', 'page_id', 'page', 'id', 'CASCADE', 'CASCADE');
        } catch (Exception $ex) {
            Yii::error($ex->getMessage());
        }

        try {
            $this->addForeignKey('fk_page-wall_id', 'page', 'wall_id', 'wall', 'id', 'CASCADE', 'CASCADE');
        } catch (Exception $ex) {
            Yii::error($ex->getMessage());
        }

        try {
            # Not required in 1.3
            # $this->addForeignKey('fk_page_module-module_id', 'page_module', 'module_id', 'module_enabled', 'module_id', 'CASCADE', 'CASCADE');
        } catch (Exception $ex) {
            Yii::error($ex->getMessage());
        }
    }

    public function down()
    {
        $this->dropForeignKey('fk_page_membership-user_id', 'page_membership');
        $this->dropForeignKey('fk_page_membership-page_id', 'page_membership');
        $this->dropForeignKey('fk_page_module-page_id', 'page_module');
        $this->dropForeignKey('fk_page-wall_id', 'page');
        $this->dropForeignKey('fk_page_module-module_id', 'page_module');

        return true;
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
