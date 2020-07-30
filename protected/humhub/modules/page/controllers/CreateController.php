<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\controllers;

use humhub\components\Controller;
use humhub\components\behaviors\AccessControl;
use humhub\modules\page\models\Page;
use humhub\modules\page\permissions\CreatePrivatePage;
use humhub\modules\page\permissions\CreatePublicPage;
use humhub\modules\page\models\forms\InviteForm;
use Colors\RandomColor;
use humhub\modules\user\helpers\AuthHelper;
use Yii;
use yii\base\Exception;
use yii\web\HttpException;

/**
 * CreateController is responsible for creation of new pages
 *
 * @author Luke
 * @since 0.5
 */
class CreateController extends Controller
{

    /**
     * @inheritdoc
     */
    public $defaultAction = 'create';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'acl' => [
                'class' => AccessControl::class,
            ]
        ];
    }

    public function actionIndex()
    {
        return $this->redirect(['create']);
    }

    /**
     * Creates a new Page
     * @throws HttpException
     * @throws Exception
     */
    public function actionCreate($visibility = null, $skip = 0)
    {
        // User cannot create pages (public or private)
        if (!Yii::$app->user->permissionmanager->can(new CreatePublicPage) && !Yii::$app->user->permissionmanager->can(new CreatePrivatePage)) {
            throw new HttpException(400, 'You are not allowed to create pages!');
        }

        $model = $this->createPageModel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($skip) {
                return $this->htmlRedirect($model->getUrl());
            }
            return $this->actionModules($model->id);
        }

        $visibilityOptions = [];
        if (AuthHelper::isGuestAccessEnabled() && Yii::$app->user->permissionmanager->can(new CreatePublicPage)) {
            $visibilityOptions[Page::VISIBILITY_ALL] = Yii::t('PageModule.base', 'Public (Members & Guests)');
        }
        if (Yii::$app->user->permissionmanager->can(new CreatePublicPage)) {
            $visibilityOptions[Page::VISIBILITY_REGISTERED_ONLY] = Yii::t('PageModule.base', 'Public (Members only)');
        }
        if (Yii::$app->user->permissionmanager->can(new CreatePrivatePage)) {
            $visibilityOptions[Page::VISIBILITY_NONE] = Yii::t('PageModule.base', 'Private (Invisible)');
        }

        if ($visibility !== null && isset($visibilityOptions[$visibility])) {
            // allow setting pre-selected visibility
            $model->visibility = $visibility;
        } elseif (!isset($visibilityOptions[$model->visibility])) {
            if (!function_exists('array_key_first')) {
                // TEMPORARY until min. version raised to PHP 7.3+
                foreach ($visibilityOptions as $key => $unused) {
                    $model->visibility = $key;
                    break;
                }
            } else {
                $model->visibility = array_key_first($visibilityOptions);
            }
        }

        $joinPolicyOptions = [
            Page::JOIN_POLICY_NONE => Yii::t('PageModule.base', 'Only by invite'),
            Page::JOIN_POLICY_APPLICATION => Yii::t('PageModule.base', 'Invite and request'),
            Page::JOIN_POLICY_FREE => Yii::t('PageModule.base', 'Everyone can enter')
        ];

        return $this->renderAjax('create', ['model' => $model, 'visibilityOptions' => $visibilityOptions, 'joinPolicyOptions' => $joinPolicyOptions]);
    }

    /**
     * Creates an empty page model
     *
     * @return Page the preconfigured page object
     */
    protected function createPageModel()
    {
        /* @var \humhub\modules\page\Module $module */
        $module = Yii::$app->getModule('page');

        $model = new Page();
        $model->scenario = Page::SCENARIO_CREATE;
        $model->visibility = $module->settings->get('defaultVisibility', Page::VISIBILITY_REGISTERED_ONLY);
        $model->join_policy = $module->settings->get('defaultJoinPolicy', Page::JOIN_POLICY_APPLICATION);
        $model->color = RandomColor::one(['luminosity' => 'dark']);

        return $model;
    }

    /**
     * Activate / deactivate modules
     * @throws Exception
     */
    public function actionModules($page_id)
    {
        $page = Page::find()->where(['id' => $page_id])->one();

        if (count($page->getAvailableModules()) == 0) {
            return $this->actionInvite($page);
        } else {
            return $this->renderAjax('modules', ['page' => $page, 'availableModules' => $page->getAvailableModules()]);
        }
    }

    /**
     * Invite user
     *
     * @throws Exception
     */
    public function actionInvite($page = null, $pageId = null)
    {
        $page = ($page == null) ? Page::findOne(['id' => $pageId]) : $page;

        if (!$page) {
            throw new HttpException(404);
        }

        $model = new InviteForm(['page' => $page]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->htmlRedirect($page->getUrl());
        }

        return $this->renderAjax('invite', [
            'canInviteExternal' => $model->canInviteExternal(),
            'model' => $model,
            'page' => $page
        ]);
    }

}
