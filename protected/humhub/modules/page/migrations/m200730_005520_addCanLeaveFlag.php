<?php

use yii\db\Migration;
use yii\db\Schema;

class m200730_005520_addCanLeaveFlag extends Migration
{
    public function up()
    {
        $this->addColumn('page_membership', 'can_cancel_membership', Schema::TYPE_INTEGER. ' DEFAULT 1');
        $this->addColumn('page', 'members_can_leave', Schema::TYPE_INTEGER. ' DEFAULT 1');
    }

    public function down()
    {
        echo "m160217_161220_addCanLeaveFlag cannot be reverted.\n";

        return false;
    }
}
