<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\modules\manage\controllers;

use humhub\modules\content\components\ContentContainerControllerAccess;
use Yii;
use yii\web\HttpException;
use humhub\modules\page\models\Page;
use humhub\modules\page\modules\manage\components\Controller;
use humhub\modules\page\modules\manage\models\MembershipSearch;
use humhub\modules\page\notifications\ChangedRolesMembership;
use humhub\modules\user\models\User;
use humhub\modules\page\models\Membership;
use humhub\modules\page\modules\manage\models\ChangeOwnerForm;

/**
 * Member Controller
 *
 * @author Luke
 */
class MemberController extends Controller
{
    /**
     * @inheritdoc
     */
    protected function getAccessRules() {
        return [
            ['login'],
            [ContentContainerControllerAccess::RULE_USER_GROUP_ONLY => [Page::USERGROUP_ADMIN], 'actions' => [
                'index', 'pending-invitations', 'pending-approvals', 'reject-applicant', 'approve-applicant', 'remove']],
            [ContentContainerControllerAccess::RULE_USER_GROUP_ONLY => [Page::USERGROUP_OWNER], 'actions' => ['change-owner']]
        ];
    }

    /**
     * Members Administration Action
     */
    public function actionIndex()
    {
        $page = $this->getPage();
        $searchModel = new MembershipSearch();
        $searchModel->page_id = $page->id;
        $searchModel->status = Membership::STATUS_MEMBER;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // User Group Change
        if (Yii::$app->request->post('dropDownColumnSubmit')) {
            Yii::$app->response->format = 'json';
            $membership = Membership::findOne(['page_id' => $page->id, 'user_id' => Yii::$app->request->post('user_id')]);
            if ($membership === null) {
                throw new HttpException(404, 'Could not find membership!');
            }

            if ($membership->load(Yii::$app->request->post()) && $membership->save()) {

                ChangedRolesMembership::instance()
                    ->about($membership)
                    ->from(Yii::$app->user->identity)
                    ->send($membership->user);

                return Yii::$app->request->post();
            }

            return $membership->getErrors();
        }

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
                    'page' => $page
        ]);
    }

    /**
     * Members Administration Action
     */
    public function actionPendingInvitations()
    {
        $page = $this->getPage();
        $searchModel = new MembershipSearch();
        $searchModel->page_id = $page->id;
        $searchModel->status = Membership::STATUS_INVITED;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('pending-invitations', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
                    'page' => $page
        ]);
    }

    /**
     * Members Administration Action
     */
    public function actionPendingApprovals()
    {
        $page = $this->getPage();
        $searchModel = new MembershipSearch();
        $searchModel->page_id = $page->id;
        $searchModel->status = Membership::STATUS_APPLICANT;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('pending-approvals', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
                    'page' => $page
        ]);
    }

    /**
     * User Manage Users Page, Reject Member Request Link
     */
    public function actionRejectApplicant()
    {
        $this->forcePostRequest();

        $page = $this->getPage();
        $userGuid = Yii::$app->request->get('userGuid');
        $user = User::findOne(['guid' => $userGuid]);

        if ($user != null) {
            $page->removeMember($user->id);
        }

        return $this->redirect($page->getUrl());
    }

    /**
     * User Manage Users Page, Approve Member Request Link
     */
    public function actionApproveApplicant()
    {
        $this->forcePostRequest();

        $page = $this->getPage();
        $userGuid = Yii::$app->request->get('userGuid');
        $user = User::findOne(['guid' => $userGuid]);

        if ($user != null) {
            $membership = $page->getMembership($user->id);
            if ($membership != null && $membership->status == Membership::STATUS_APPLICANT) {
                $page->addMember($user->id);
            }
        }

        return $this->redirect($page->getUrl());
    }

    /**
     * Removes a Member
     */
    public function actionRemove()
    {
        $this->forcePostRequest();

        $page = $this->getPage();
        $userGuid = Yii::$app->request->get('userGuid');
        $user = User::findOne(['guid' => $userGuid]);

        if ($page->isPageOwner($user->id)) {
            throw new HttpException(500, 'Owner cannot be removed!');
        }

        $page->removeMember($user->id);

        // Redirect  back to Administration page
        return $this->htmlRedirect($page->createUrl('/page/manage/member'));
    }

    /**
     * Change owner
     */
    public function actionChangeOwner()
    {
        $page = $this->getPage();

        $model = new ChangeOwnerForm([
            'page' => $page,
            'ownerId' => $page->getPageOwner()->id
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $page->setPageOwner($model->ownerId);
            return $this->redirect($page->getUrl());
        }

        return $this->render('change-owner', [
                    'page' => $page,
                    'model' => $model
        ]);
    }

}
