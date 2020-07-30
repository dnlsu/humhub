<?php


use yii\db\Migration;

class m200730_005448_notifications extends Migration
{

    public function up()
    {
        $this->update('notification', ['class' => 'humhub\modules\page\notifications\ApprovalRequest'], ['class' => 'PageApprovalRequestNotification']);
        $this->update('notification', ['class' => 'humhub\modules\page\notifications\ApprovalRequestAccepted'], ['class' => 'PageApprovalRequestAcceptedNotification']);
        $this->update('notification', ['class' => 'humhub\modules\page\notifications\ApprovalRequestDeclined'], ['class' => 'PageApprovalRequestDeclinedNotification']);
        $this->update('notification', ['class' => 'humhub\modules\page\notifications\Invite'], ['class' => 'PageInviteNotification']);
        $this->update('notification', ['class' => 'humhub\modules\page\notifications\InviteAccepted'], ['class' => 'PageInviteAcceptedNotification']);
        $this->update('notification', ['class' => 'humhub\modules\page\notifications\InviteDeclined'], ['class' => 'PageInviteDeclinedNotification']);


        $this->update('notification', ['source_pk' => new yii\db\Expression('page_id'), 'source_class' => 'humhub\modules\page\models\Page'], ['class' => 'humhub\modules\page\notifications\ApprovalRequest', 'source_class' => 'humhub\modules\user\models\User']);
        $this->update('notification', ['source_pk' => new yii\db\Expression('page_id'), 'source_class' => 'humhub\modules\page\models\Page'], ['class' => 'humhub\modules\page\notifications\ApprovalRequestAccepted', 'source_class' => 'humhub\modules\user\models\User']);
        $this->update('notification', ['source_pk' => new yii\db\Expression('page_id'), 'source_class' => 'humhub\modules\page\models\Page'], ['class' => 'humhub\modules\page\notifications\ApprovalRequestDeclined', 'source_class' => 'humhub\modules\user\models\User']);
        $this->update('notification', ['source_pk' => new yii\db\Expression('page_id'), 'source_class' => 'humhub\modules\page\models\Page'], ['class' => 'humhub\modules\page\notifications\Invite', 'source_class' => 'humhub\modules\user\models\User']);
        $this->update('notification', ['source_pk' => new yii\db\Expression('page_id'), 'source_class' => 'humhub\modules\page\models\Page'], ['class' => 'humhub\modules\page\notifications\InviteAccepted', 'source_class' => 'humhub\modules\user\models\User']);
        $this->update('notification', ['source_pk' => new yii\db\Expression('page_id'), 'source_class' => 'humhub\modules\page\models\Page'], ['class' => 'humhub\modules\page\notifications\InviteDeclined', 'source_class' => 'humhub\modules\user\models\User']);
    }

    public function down()
    {
        echo "m150831_061628_notifications cannot be reverted.\n";

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
