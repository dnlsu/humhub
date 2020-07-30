<?php


use yii\db\Migration;

/**
 * Fix broken notification with invalid originator_user_ids
 */
class m200730_005510_fix_notifications extends Migration
{

    public function up()
    {
        $query = new \yii\db\Query();
        $query->select('notification.id')->from('notification');
        $query->andWhere(['or',
            ['class' => 'humhub\modules\page\notifications\Invite'],
            ['class' => 'humhub\modules\page\notifications\InviteAccepted'],
            ['class' => 'humhub\modules\page\notifications\InviteDeclined'],
            ['class' => 'humhub\modules\page\notifications\ApprovalRequest'],
            ['class' => 'humhub\modules\page\notifications\ApprovalRequestAccepted'],
            ['class' => 'humhub\modules\page\notifications\ApprovalRequestDeclined']
        ]);

        $query->leftJoin('user', 'notification.originator_user_id=user.id');
        $query->andWhere('user.id IS NULL');

        foreach ($query->all() as $notification) {
            $this->delete('notification', ['id' => $notification['id']]);
        }
    }

    public function down()
    {
        echo "m151223_171310_fix_notifications cannot be reverted.\n";

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
