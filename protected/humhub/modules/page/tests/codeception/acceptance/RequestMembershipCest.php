<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace page\acceptance;

use page\AcceptanceTester;

class RequestMembershipCest
{
    /**
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    public function testRequestMembershipAccept(AcceptanceTester $I)
    {
        $I->wantTo('ensure that accepting an users page membership works.');

        $I->amUser1();
        $I->amOnPage1();
        $I->seeElement('#requestMembershipButton');
        $I->click('#requestMembershipButton');

        $I->waitForText('Request page membership', null,'#globalModal');
        $I->fillField('#request-message', 'Hi, I want to join this page.');
        $I->click('Send', '#globalModal');
        $I->waitForText('Your request was successfully submitted to the page administrators.');
        $I->click('Close', '#globalModal');

        $I->waitForText('Cancel pending membership application');

        $I->amAdmin(true);
        $I->seeInNotifications('Peter Tester requests membership for the page Page 1', true);

        $I->waitForText('New member request',null, '.panel-danger');
        $I->see('Hi, I want to join this page.', '.panel-danger');
        $I->click('Accept', '.panel-danger');

        $I->wait(1);

        $I->amUser1(true);

        $I->seeInNotifications('Admin Tester approved your membership for the page Page 1', true);
        $I->waitForText('User 1 Page 1 Post Private', null, '#wallStream');
    }

    /**
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    public function testRequestMembershipDecline(AcceptanceTester $I)
    {
        $I->wantTo('ensure that declining an users page membership works.');

        $I->amUser1();
        $I->amOnPage1();
        $I->seeElement('#requestMembershipButton');
        $I->click('#requestMembershipButton');

        $I->waitForText('Request page membership', null,'#globalModal');
        $I->fillField('#request-message', 'Hi, I want to join this page.');
        $I->click('Send', '#globalModal');
        $I->waitForText('Your request was successfully submitted to the page administrators.');
        $I->click('Close', '#globalModal');

        $I->waitForText('Cancel pending membership application');

        $I->amAdmin(true);
        $I->seeInNotifications('Peter Tester requests membership for the page Page 1', true);

        $I->waitForText('New member request', null, '.panel-danger');

        $I->click('.dropdown-navigation', '.controls-header');
        $I->waitForText('Members', null, '.controls-header');
        $I->click('Members', '.controls-header');

        $I->waitForText('Manage members');
        $I->see('Pending Approvals');
        $I->click('Pending Approvals');

        $I->waitForText('Reject');
        $I->click('Reject');

        $I->waitForElementVisible('#wallStream');
        $I->dontSeeInNotifications('Peter Tester requests membership for the page Page 1');

        $I->amUser1(true);

        $I->seeInNotifications('Admin Tester declined your membership request for the page Page 1', true);
        $I->waitForElementVisible('#requestMembershipButton');
    }

    /**
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    public function testRequestMembershipRevoke(AcceptanceTester $I)
    {
        $I->wantTo('ensure that revoking an users page membership works.');

        $I->amUser1();
        $I->amOnPage1();
        $I->seeElement('#requestMembershipButton');
        $I->click('#requestMembershipButton');

        $I->waitForText('Request page membership', null,'#globalModal');
        $I->fillField('#request-message', 'Hi, I want to join this page.');
        $I->click('Send', '#globalModal');
        $I->waitForText('Your request was successfully submitted to the page administrators.');
        $I->click('Close', '#globalModal');

        $I->waitForText('Cancel pending membership application');
        $I->click('Cancel pending membership application');
        $I->waitForText('Admin Page 2 Post Private', null,'#wallStream'); // Back to dashboard
        $I->amOnPage1();
        $I->waitForText('Request membership', null,'#requestMembershipButton');

        $I->amAdmin(true);
        $I->dontSeeInNotifications('Peter Tester requests membership for the page Page 1');
        $I->amOnPage1();
        $I->dontSeeElement('.panel-danger');

        $I->click('.dropdown-navigation', '.controls-header');
        $I->waitForText('Members', null, '.controls-header');
        $I->click('Members', '.controls-header');

        $I->waitForText('Manage members');
        $I->dontSee('Pending Approvals');
    }
}
