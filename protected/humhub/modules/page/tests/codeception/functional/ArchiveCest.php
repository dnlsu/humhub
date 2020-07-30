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


class ArchiveCest
{
    public function testPageArchiveAccess(FunctionalTester $I)
    {
        $I->assertPageAccessFalse(Page::USERGROUP_GUEST, '/page/manage/default/archive');
        $I->assertPageAccessFalse(Page::USERGROUP_MEMBER, '/page/manage/default/archive');
        $I->assertPageAccessFalse(Page::USERGROUP_USER, '/page/manage/default/archive');
        $I->assertPageAccessFalse(Page::USERGROUP_MODERATOR, '/page/manage/default/archive');
        $I->assertPageAccessFalse(Page::USERGROUP_ADMIN, '/page/manage/default/archive');
        $I->assertPageAccessFalse(Page::USERGROUP_OWNER, '/page/manage/default/archive');
        $I->assertPageAccessTrue(Page::USERGROUP_OWNER, '/page/manage/default/archive', true);
    }

    public function testPageArchivePage(FunctionalTester $I)
    {
        $page = $I->loginByPageUserGroup(Page::USERGROUP_OWNER);
        $I->amOnPage($page, '/page/manage/default/archive', true);
        $I->amOnPage($page);
        $I->see('Archived');
    }
}
