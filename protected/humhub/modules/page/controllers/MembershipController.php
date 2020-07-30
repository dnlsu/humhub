<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\controllers;

use humhub\components\behaviors\AccessControl;
use humhub\modules\admin\permissions\ManageUsers;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\content\components\ContentContainerControllerAccess;
use humhub\modules\page\jobs\AddUsersToPageJob;
use humhub\modules\page\models\forms\InviteForm;
use humhub\modules\page\models\forms\RequestMembershipForm;
use humhub\modules\page\models\Membership;
use humhub\modules\page\models\Page;
use humhub\modules\page\permissions\InviteUsers;
use humhub\modules\user\models\UserPicker;
use humhub\modules\user\widgets\UserListBox;
use humhub\widgets\ModalClose;
use Yii;
use yii\web\HttpException;

/**
 * PageController is the main controller for pages.
 *
 * It show the page itself and handles all related tasks like following or
 * memberships.
 *
 * @author Luke
 * @package humhub.modules_core.space.controllers
 * @since 0.5
 */
class MembershipController extends ContentContainerController
{
    public function getAccessRules()
    {
        return [
            ['permission' => [InviteUsers::class], 'actions' => ['invite']],
            [ContentContainerControllerAccess::RULE_LOGGED_IN_ONLY => ['revoke-membership']],
            [ContentContainerControllerAccess::RULE_USER_GROUP_ONLY => [Page::USERGROUP_MEMBER],
                'actions' => [
                    'revoke-notifications',
                    'receive-notifications',
                    'search-invite',
                    'switch-dashboard-display'
                    ]
            ]
        ];
    }

    /**
     * Provides a searchable user list of all workspace members in json.
     *
     */
    public function actionSearch()
    {
        Yii::$app->response->format = 'json';

        $page = $this->getPage();
        $visibility = (int)$page->visibility;
        if ($visibility === Page::VISIBILITY_NONE && !$page->isMember() ||
            ($visibility === Page::VISIBILITY_REGISTERED_ONLY && Yii::$app->user->isGuest)
        ) {
            throw new HttpException(404, Yii::t('PageModule.base',
                'This action is only available for workspace members!'));
        }

        return UserPicker::filter([
            'query' => $page->getMembershipUser(),
            'keyword' => Yii::$app->request->get('keyword'),
            'fillUser' => true,
            'disabledText' => Yii::t('PageModule.base',
                'This user is not a member of this page.'),
        ]);
    }

    /**
     * Requests Membership for this Page
     */
    public function actionRequestMembership()
    {
        $this->forcePostRequest();
        $page = $this->getPage();

        if (!$page->canJoin(Yii::$app->user->id)) {
            throw new HttpException(500,
                Yii::t('PageModule.base', 'You are not allowed to join this page!'));
        }

        $page->addMember(Yii::$app->user->id);

        return $this->htmlRedirect($page->getUrl());
    }

    /**
     * Requests Membership Form for this Page
     * (If a message is required.)
     *
     */
    public function actionRequestMembershipForm()
    {
        $page = $this->getPage();

        // Check if we have already some sort of membership
        if (Yii::$app->user->isGuest || $page->getMembership(Yii::$app->user->id) != null) {
            throw new HttpException(500,
                Yii::t('PageModule.base', 'Could not request membership!'));
        }

        $model = new RequestMembershipForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $page->requestMembership(Yii::$app->user->id, $model->message);
            return $this->renderAjax('requestMembershipSave', ['page' => $page]);
        }

        return $this->renderAjax('requestMembership', ['model' => $model, 'page' => $page]);
    }

    public function actionRevokeNotifications()
    {
        $page = $this->getPage();
        Yii::$app->notification->setPageSetting(Yii::$app->user->getIdentity(), $page, false);

        return $this->redirect($page->getUrl());
    }

    public function actionReceiveNotifications()
    {
        $page = $this->getPage();
        Yii::$app->notification->setPageSetting(Yii::$app->user->getIdentity(), $page, true);

        return $this->redirect($page->getUrl());
    }

    /**
     * Revokes Membership for this workspace
     * @return \yii\web\Response
     * @throws HttpException
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRevokeMembership()
    {
        $this->forcePostRequest();
        $page = $this->getPage();

        if ($page->isPageOwner()) {
            throw new HttpException(500,
                Yii::t('PageModule.base', 'As owner you cannot revoke your membership!'));
        } elseif (!$page->canLeave()) {
            throw new HttpException(500,
                Yii::t('PageModule.base', 'Sorry, you are not allowed to leave this page!'));
        }

        $page->removeMember();

        return $this->goHome();
    }

    /**
     * Provides a searchable user list of all workspace members in json.
     *
     */
    public function actionSearchInvite()
    {
        $page = $this->getPage();

        return $this->asJson(UserPicker::filter([
            'query' => $page->getNonMembershipUser(),
            'keyword' => Yii::$app->request->get('keyword'),
            'fillUser' => true,
            'disabledText' => Yii::t('PageModule.base',
                'This user is already a member of this page.'),
        ]));
    }


    /**
     * Invite New Members to this workspace
     */
    public function actionInvite()
    {
        $model = new InviteForm(['page' => $this->getPage()]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if($model->isQueuedJob()) {
                $success = ($model->withoutInvite)
                    ? Yii::t( 'PageModule.base', 'User memberships have been added to the queue')
                    : Yii::t( 'PageModule.base', 'User invitations have been added to the queue');
            } else {
                $success = Yii::t('PageModule.base', 'Users has been invited.');
            }

            return ModalClose::widget([
                'success' => $success
            ]);
        }

        return $this->renderAjax('invite', ['model' => $model, 'page' => $model->page]);
    }

    /**
     * When a user clicks on the Accept Invite Link, this action is called.
     * After this the user should be member of this workspace.
     */
    public function actionInviteAccept()
    {
        $this->forcePostRequest();
        $page = $this->getPage();

        // Load Pending Membership
        $membership = $page->getMembership();
        if ($membership == null) {
            throw new HttpException(404, Yii::t('PageModule.base', 'There is no pending invite!'));
        }

        // Check there are really an Invite
        if ($membership->status == Membership::STATUS_INVITED) {
            $page->addMember(Yii::$app->user->id);
        }

        return $this->redirect($page->getUrl());
    }

    /**
     * Toggle page content display at dashboard
     *
     * @throws HttpException
     */
    public function actionSwitchDashboardDisplay($show = 0)
    {
        $this->forcePostRequest();
        $page = $this->getPage();

        $membership = $page->getMembership();
        $membership->show_at_dashboard = ($show) ? 1 : 0;
        $membership->save();

        return $this->redirect($page->getUrl());
    }

    /**
     * Returns an user list which are page members
     */
    public function actionMembersList()
    {
        return $this->renderAjaxContent(UserListBox::widget([
            'query' => Membership::getPageMembersQuery($this->getPage())->visible(),
            'title' => Yii::t('PageModule.manage', "<strong>Members</strong>"),
        ]));
    }

}
