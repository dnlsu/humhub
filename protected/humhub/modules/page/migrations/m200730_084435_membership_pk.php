<?php

use yii\db\Migration;

class m200730_084435_membership_pk extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        try {
            $this->dropForeignKey('fk_page_membership-page_id', 'page_membership');
            $this->dropForeignKey('fk_page_membership-user_id', 'page_membership');
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
        }

        $this->dropPrimaryKey('PRIMARY', 'page_membership');
        $this->addColumn('page_membership', 'id', $this->primaryKey());
        $this->addForeignKey(
            'fk_page_membership-page_id',
            'page_membership',
            'page_id',
            'page',
            'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk_page_membership-user_id',
            'page_membership',
            'user_id',
            'user',
            'id',
            'CASCADE', 'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_page_membership-page_id', 'page_membership');
        $this->dropForeignKey('fk_page_membership-user_id', 'page_membership');
        $this->dropPrimaryKey('PRIMARY', 'page_membership');
        $this->addPrimaryKey('PRIMARY', 'page_membership', ['page_id', 'user_id']);
        $this->dropColumn('page_membership', 'id');
        $this->addForeignKey(
            'fk_page_membership-page_id',
            'page_membership',
            'page_id',
            'page',
            'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk_page_membership-user_id',
            'page_membership',
            'user_id',
            'user',
            'id',
            'CASCADE', 'CASCADE'
        );
    }
}
