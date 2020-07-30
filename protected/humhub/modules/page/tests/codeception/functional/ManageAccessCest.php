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


class ManageAccessCest
{
    public function testPageAccessManage(FunctionalTester $I)
    {
        $I->assertPageAccessFalse(Page::USERGROUP_MEMBER, '/page/manage');
        $I->assertPageAccessFalse(Page::USERGROUP_USER, '/page/manage');
        $I->assertPageAccessFalse(Page::USERGROUP_MODERATOR, '/page/manage');
        $I->assertPageAccessTrue(Page::USERGROUP_ADMIN, '/page/manage');
        $I->assertPageAccessTrue(Page::USERGROUP_OWNER, '/page/manage');
    }
}
