<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace page\functional;

use humhub\modules\page\models\Page;
use page\FunctionalTester;

class ProfileImageAccessCest
{
    public function testUploadAccessForPageAdmin(FunctionalTester $I)
    {
        $I->wantTo('ensure that page admins can access profile imag eupload');
        $I->assertPageAccessTrue(Page::USERGROUP_ADMIN, 'page/manage/image/upload');
    }

    public function testUploadAccessForGuest(FunctionalTester $I)
    {
        $I->wantTo('ensure that page admins can access profile imag eupload');
        $I->assertPageAccessFalse(Page::USERGROUP_GUEST, 'page/manage/image/upload');
    }

    public function testUploadAccessForMember(FunctionalTester $I)
    {
        $I->wantTo('ensure that page admins can access profile imag eupload');
        $I->assertPageAccessFalse(Page::USERGROUP_MEMBER, 'page/manage/image/upload');
    }

    public function testUploadAccessForUser(FunctionalTester $I)
    {
        $I->wantTo('ensure that page admins can access profile imag eupload');
        $I->assertPageAccessFalse(Page::USERGROUP_USER, 'page/manage/image/upload');
    }

    public function testUploadAccessForModerator(FunctionalTester $I)
    {
        $I->wantTo('ensure that page admins can access profile imag eupload');
        $I->assertPageAccessFalse(Page::USERGROUP_MODERATOR, 'page/manage/image/upload');
    }
}
