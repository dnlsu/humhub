<?php

namespace tests\codeception\unit\modules\space;

use humhub\modules\page\models\Membership;
use humhub\modules\page\models\Page;
use humhub\modules\user\models\User;
use tests\codeception\_support\HumHubDbTestCase;
use Yii;

class MembershipTest extends HumHubDbTestCase
{
    public function testJoinPolicityApprovalApprove()
    {
        $this->becomeUser('User1');

        $user1 = Yii::$app->user->getIdentity();

        // Request Membership for Page 1 (approval join policity)
        $page = Page::findOne(['id' => 1]);
        $page->requestMembership(Yii::$app->user->id, 'Let me in!');

        // Check approval mails are send and notification
        $this->assertSentEmail(1); // Approval notification admin mail
        $this->assertHasNotification(\humhub\modules\page\notifications\ApprovalRequest::class, $page,
            Yii::$app->user->id, 'Approval Request Notification');

        $membership = Membership::findOne(['page_id' => 1, 'user_id' => Yii::$app->user->id]);
        $this->assertNotNull($membership);
        $this->assertEquals($membership->status, Membership::STATUS_APPLICANT);

        $this->becomeUser('Admin');

        $page->addMember(2);
        $this->assertSentEmail(2); //Approval notification admin mail
        $this->assertHasNotification(\humhub\modules\page\notifications\ApprovalRequestAccepted::class, $page, 1,
            'Approval Accepted Notification');

        $memberships = Membership::findByUser($user1)->all();
        $this->assertNotEmpty($memberships, 'get all memberships of user query.');
        $match = null;

        foreach ($memberships as $membership) {
            if ($membership->user_id == $user1->id) {
                $match = $membership;
            }
        }

        $this->assertNotNull($match);
    }

    public function testJoinPolicityApprovalDecline()
    {
        $this->becomeUser('User1');

        // Page 1 is approval page
        $page = Page::findOne(['id' => 1]);
        $page->requestMembership(Yii::$app->user->id, 'Let me in!');

        $this->assertSentEmail(1); // Approval notification admin mail
        $this->assertHasNotification(\humhub\modules\page\notifications\ApprovalRequest::class, $page,
            Yii::$app->user->id, 'Approval Request Notification');

        $membership = Membership::findOne(['page_id' => 1, 'user_id' => Yii::$app->user->id]);
        $this->assertNotNull($membership);
        $this->assertEquals($membership->status, Membership::STATUS_APPLICANT);

        $this->becomeUser('Admin');

        $page->removeMember(2);
        $this->assertSentEmail(2); // Rejection notification admin mail
        $this->assertHasNotification(\humhub\modules\page\notifications\ApprovalRequestDeclined::class, $page, 1,
            'Approval Accepted Notification');
    }

    public function testChangeRoleMembership()
    {
        $membership = Membership::findOne(['page_id' => 3, 'user_id' => 2]);

        \humhub\modules\page\notifications\ChangedRolesMembership::instance()
            ->about($membership)
            ->from(User::findOne(['id' => 1]))
            ->send($membership->user);

        $this->assertSentEmail(1);
    }
}
