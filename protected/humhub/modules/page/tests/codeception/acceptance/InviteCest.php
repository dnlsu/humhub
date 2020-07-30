<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace page\acceptance;

use page\AcceptanceTester;

class InviteCest
{
    /**
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    public function testPageUserInviteAccept(AcceptanceTester $I)
    {
        $I->wantTo('ensure that accepting an users invitation to a page works.');

        $I->amUser1();
        $I->amOnPage2();
        $I->click('Invite', '.panel-profile');
        $I->waitForText('Invite members', null, '#globalModal');
        $I->selectUserFromPicker('#space-invite-user-picker', 'Sara Tester');
        $I->click('Send', '#globalModal');

        $I->amUser2(true);
        $I->seeInNotifications('Peter Tester invited you to the page Page 2', true);
        $I->waitForText('Accept Invite', null, '.controls-header');
        $I->dontSee('Admin Page 2 Post Private', '#wallStream');
        $I->click('Accept Invite', '.controls-header');
        $I->waitForText('Admin Page 2 Post Private', null, '#wallStream');

        $I->amUser1(true);
        $I->seeInNotifications('Sara Tester accepted your invite for the page Page 2', true);
        $I->waitForText('Sara Tester joined this page.');
    }

    /**
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    public function testPageUserInviteDecline(AcceptanceTester $I)
    {
        $I->wantTo('ensure that declining an user invitation to a page works.');

        $I->amUser1();
        $I->amOnPage2();
        $I->click('Invite', '.panel-profile');
        $I->waitForText('Invite members', null, '#globalModal');
        $I->selectUserFromPicker('#space-invite-user-picker', 'Sara Tester');
        $I->click('Send', '#globalModal');

        $I->amUser2(true);
        $I->seeInNotifications('Peter Tester invited you to the page Page 2', true);
        $I->waitForText('Accept Invite', null, '.controls-header');

        $I->click('.dropdown-toggle', '.controls-header');
        $I->waitForText('Decline Invite', null,'.controls-header');
        $I->click('Decline Invite');
        $I->waitForElementVisible('[data-menu-id="dashboard"].active');

        $I->amUser1(true);
        $I->seeInNotifications('Sara Tester declined your invite for the page Page 2');
    }

    /**
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    public function testPageUserInviteRevoke(AcceptanceTester $I)
    {
        $I->wantTo('ensure that declining an user invitation to a page works.');

        $I->amUser1();
        $I->amOnPage2();
        $I->click('Invite', '.panel-profile');
        $I->waitForText('Invite members', null, '#globalModal');
        $I->selectUserFromPicker('#space-invite-user-picker', 'Sara Tester');
        $I->click('Send', '#globalModal');

        $I->waitForElementNotVisible('#globalModal');

        $I->click('.dropdown-navigation', '.controls-header');
        $I->waitForText('Members', null, '.controls-header');
        $I->click('Members', '.controls-header');

        $I->waitForText('Pending Invites');
        $I->click('Pending Invites');

        $I->waitForText('Cancel', null, '.layout-content-container');
        $I->click('Cancel', '.layout-content-container');
        $I->acceptPopup();
        $I->waitForText('Member since', null, '.layout-content-container');

        $I->amUser2(true);
        $I->seeInNotifications('Peter Tester revoked your invitation for the page Page 2');

    }
}
