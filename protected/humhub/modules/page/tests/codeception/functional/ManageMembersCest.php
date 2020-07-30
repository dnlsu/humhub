<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 19.07.2018
 * Time: 21:30
 */

namespace humhub\modules\page\tests\codeception\functional;

use FunctionalTester;
use humhub\modules\page\models\Page;


class ManageMembersCest
{
    public function testPageManageMembersAccess(FunctionalTester $I)
    {
        $I->assertPageAccessFalse(Page::USERGROUP_MEMBER, '/page/manage/member');
        $I->assertPageAccessFalse(Page::USERGROUP_USER, '/page/manage/member');
        $I->assertPageAccessFalse(Page::USERGROUP_MODERATOR, '/page/manage/member');
        $I->assertPageAccessTrue(Page::USERGROUP_ADMIN, '/page/manage/member');
        $I->assertPageAccessTrue(Page::USERGROUP_OWNER, '/page/manage/member');
    }

    public function testChangeOwnerAccess(FunctionalTester $I)
    {
        $I->assertPageAccessFalse(Page::USERGROUP_MEMBER, '/page/manage/member/change-owner');
        $I->assertPageAccessFalse(Page::USERGROUP_USER, '/page/manage/member/change-owner');
        $I->assertPageAccessFalse(Page::USERGROUP_MODERATOR, '/page/manage/member/change-owner');
        $I->assertPageAccessFalse(Page::USERGROUP_ADMIN, '/page/manage/member/change-owner');
        $I->assertPageAccessTrue(Page::USERGROUP_OWNER, '/page/manage/member/change-owner');

        $I->amAdmin();
        $I->amOnPage4('/page/manage/member/change-owner', [], ['ChangeOwnerForm[ownerId]' => 2]);
        $I->seeSuccessResponseCode();

        $page = Page::findOne(4);

        if(!$page->ownerUser->id === 2) {
            $I->see('Change owner did not work');
        }
    }

    public function testApprovalAccess(FunctionalTester $I)
    {
        $I->assertPageAccessFalse(Page::USERGROUP_MEMBER, '/page/manage/member/pending-invitations');
        $I->assertPageAccessFalse(Page::USERGROUP_USER, '/page/manage/member/pending-invitations');
        $I->assertPageAccessFalse(Page::USERGROUP_MODERATOR, '/page/manage/member/pending-invitations');
        $I->assertPageAccessTrue(Page::USERGROUP_ADMIN, '/page/manage/member/pending-invitations');
        $I->assertPageAccessTrue(Page::USERGROUP_OWNER, '/page/manage/member/pending-invitations');

        $I->assertPageAccessFalse(Page::USERGROUP_MEMBER, '/page/manage/member/pending-approvals');
        $I->assertPageAccessFalse(Page::USERGROUP_USER, '/page/manage/member/pending-approvals');
        $I->assertPageAccessFalse(Page::USERGROUP_MODERATOR, '/page/manage/member/pending-approvals');
        $I->assertPageAccessTrue(Page::USERGROUP_ADMIN, '/page/manage/member/pending-approvals');
        $I->assertPageAccessTrue(Page::USERGROUP_OWNER, '/page/manage/member/pending-approvals');
    }
}
