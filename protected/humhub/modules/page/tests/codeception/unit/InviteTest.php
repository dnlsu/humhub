<?php

namespace tests\codeception\unit\modules\space;

use Yii;
use tests\codeception\_support\HumHubDbTestCase;
use Codeception\Specify;
use humhub\modules\page\models\Page;

class InviteTest extends HumHubDbTestCase
{

    use Specify;

    public function testInviteAccept()
    {
        $this->becomeUser('Admin');

        // Page 1 is approval page
        $page = Page::findOne(['id' => 1]);
        $page->inviteMember(2, Yii::$app->user->id);

        $this->assertMailSent(1, 'Approval notification admin mail');
        $this->assertHasNotification(\humhub\modules\page\notifications\Invite::class, $page, Yii::$app->user->id, 'Invite Request Notification');

        $membership = \humhub\modules\page\models\Membership::findOne(['page_id' => 1, 'user_id' => 2]);
        $this->assertNotNull($membership);
        $this->assertEquals($membership->status, \humhub\modules\page\models\Membership::STATUS_INVITED);

        $this->becomeUser('User1');

        $page->addMember(2);
        $this->assertMailSent(2, 'Approval notification admin mail');
        $this->assertHasNotification(\humhub\modules\page\notifications\InviteAccepted::class, $page, 2, 'Approval Accepted Invite Notificatoin');
    }

    public function testInviteDecline()
    {
        $this->becomeUser('Admin');

        // Page 1 is approval page
        $page = Page::findOne(['id' => 1]);
        $page->inviteMember(2, Yii::$app->user->id);

        $this->assertMailSent(1, 'Approval notification admin mail');
        $this->assertHasNotification(\humhub\modules\page\notifications\Invite::class, $page, Yii::$app->user->id, 'Invite Request Notification');

        $membership = \humhub\modules\page\models\Membership::findOne(['page_id' => 1, 'user_id' => 2]);
        $this->assertNotNull($membership);
        $this->assertEquals($membership->status, \humhub\modules\page\models\Membership::STATUS_INVITED);

        $this->becomeUser('User1');

        $page->removeMember();
        $this->assertMailSent(2, 'Approval notification admin mail');
        $this->assertHasNotification(\humhub\modules\page\notifications\InviteDeclined::class, $page, 2, 'Declined Invite Notificatoin');
    }

}
