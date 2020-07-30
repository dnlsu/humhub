<?php

namespace page\acceptance;

use page\AcceptanceTester;

class CreatePageCest
{

    /**
     * Create private page
     *
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    public function testCreatePage(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->wantToTest('the creation of a page');
        $I->amGoingTo('create a new page and invite another user');

        $I->click('#space-menu');
        $I->waitForText('Create new page');
        $I->click('Create new page');

        $I->waitForText('Create new page', 30, '#globalModal');
        $I->fillField('Page[name]', 'Page 1');
        $I->fillField('Page[description]', 'PageDescription');

        $I->click('#access-settings-link');
        $I->waitForElementVisible('.field-space-join_policy');

        // Only by invite
        $I->jsClick('#space-join_policy [value="0"]');

        // Private visibility
        $I->jsClick('#space-visibility [value="0"]');

        $I->click('Next', '#globalModal');

        $I->waitForText('Name "Page 1" has already been taken.', 20, '#globalModal');
        $I->fillField('Page[name]', 'MyPage');
        $I->click('Next', '#globalModal');

        // Fresh test environments (travis) won't have any preinstalled modules.
        // Perhaps we should fetch an module manually by default.
        try {
            $I->waitForText('Add Modules', 5, '#globalModal');
            $I->click('Next', '#globalModal');
        } catch (\Exception $e) {
            // Do this if it's not present.
        }

        $I->waitForText('Invite members', 10, '#globalModal');
        $I->selectUserFromPicker('#space-invite-user-picker', 'Peter Tester');
        $I->wait(1);

        $I->click('Done', '#globalModal');
        $I->waitForText('MyPage');
        $I->waitForText('This page is still empty!');

        $I->amUser1(true);
        $I->seeInNotifications('invited you to the page MyPage');

        //TODO: Test private page
        // User Approval
    }

    // User Approval
    // Page settings
}
