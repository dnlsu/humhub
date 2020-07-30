<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\page\tests\codeception\functional;

use humhub\modules\page\models\Page;
use FunctionalTester;

class DeletePageCest
{
    public function testPageDeleteAccess(FunctionalTester $I)
    {
        $I->assertPageAccessFalse(Page::USERGROUP_MEMBER, '/page/manage/default/delete');
        $I->assertPageAccessFalse(Page::USERGROUP_USER, '/page/manage/default/delete');
        $I->assertPageAccessFalse(Page::USERGROUP_MODERATOR, '/page/manage/default/delete');
        $I->assertPageAccessFalse(Page::USERGROUP_ADMIN, '/page/manage/default/delete');
        $I->assertPageAccessTrue(Page::USERGROUP_OWNER, '/page/manage/default/delete');
        $I->assertPageAccessStatus(Page::USERGROUP_OWNER, 302, '/page/manage/default/delete', [], ['DeleteForm[confirmPageName]' => 'Page 2']);
        $I->assertPageAccessFalse(Page::USERGROUP_OWNER, '/page/page');
    }

    public function testSystemAdminDeletion(FunctionalTester $I)
    {
        $I->assertPageAccessTrue('root', '/page/manage/default/delete', [],  ['DeleteForm[confirmPageName]' => 'Page 2']);
    }
}
