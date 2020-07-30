<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page;

use humhub\modules\content\widgets\WallEntry;
use humhub\modules\user\events\UserEvent;
use humhub\modules\page\models\Page;
use humhub\modules\page\models\Membership;
use humhub\modules\page\helpers\MembershipHelper;
use Yii;
use yii\base\BaseObject;
use yii\web\HttpException;

/**
 * Events provides callbacks for all defined module events.
 *
 * @author luke
 */
class Events extends BaseObject
{

    /**
     * On rebuild of the search index, rebuild all page records
     *
     * @param type $event
     */
    public static function onSearchRebuild($event)
    {
        foreach (Page::find()->each() as $page) {
            Yii::$app->search->add($page);
        }
    }

    public static function onWallEntryInit($event)
    {
        /** @var WallEntry $application */
        $application = $event->sender;
        $application->wallEntryLayout = '@page/widgets/views/wallEntry.php';
    }

    /**
     * Callback on user soft deletion
     *
     * @param UserEvent $event
     */
    public static function onUserSoftDelete(UserEvent $event)
    {
        $user = $event->user;

        // Delete pages which this user owns
        foreach (MembershipHelper::getOwnPages($user) as $ownedPage) {
            $ownedPage->delete();
        }

        // Cancel all page memberships
        foreach (Membership::findAll(['user_id' => $user->id]) as $membership) {
            // Avoid activities
            $membership->delete();
        }

        // Cancel all page invites by the user
        foreach (Membership::findAll([
            'originator_user_id' => $user->id, 'status' => Membership::STATUS_INVITED,
        ]) as $membership) {
            // Avoid activities
            $membership->delete();
        }
    }

    public static function onConsoleApplicationInit($event)
    {
        $application = $event->sender;
        $application->controllerMap['page'] = commands\PageController::class;
    }

    /**
     * Callback to validate module database records.
     *
     * @param Event $event
     */
    public static function onIntegrityCheck($event)
    {
        $integrityController = $event->sender;

        $integrityController->showTestHeadline("Page Module - Pages (" . Page::find()->count() . " entries)");
        foreach (Page::find()->each() as $page) {
            foreach ($page->applicants as $applicant) {
                if ($applicant->user == null) {
                    if ($integrityController->showFix("Deleting applicant record id " . $applicant->id . " without existing user!")) {
                        $applicant->delete();
                    }
                }
            }
        }

        $integrityController->showTestHeadline("Page Module - Memberships (" . models\Membership::find()
                ->count() . " entries)");
        foreach (models\Membership::find()->joinWith('page')->each() as $membership) {
            if ($membership->page == null) {
                if ($integrityController->showFix("Deleting page membership " . $membership->page_id . " without existing page!")) {
                    $membership->delete();
                }
            }
            if ($membership->user == null) {
                if ($integrityController->showFix("Deleting page membership " . $membership->user_id . " without existing user!")) {
                    $membership->delete();
                }
            }
        }
    }

}
