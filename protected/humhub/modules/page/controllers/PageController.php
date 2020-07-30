<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\components\behaviors\AccessControl;
use humhub\modules\page\models\Page;
use humhub\modules\user\models\User;
use humhub\modules\user\widgets\UserListBox;
use humhub\modules\stream\actions\ContentContainerStream;
use humhub\modules\page\widgets\Menu;
use humhub\modules\post\permissions\CreatePost;
use Yii;
use yii\web\HttpException;
use yii\db\Expression;

/**
 * PageController is the main controller for pages.
 *
 * It show the page itself and handles all related tasks like following or
 * memberships.
 *
 * @property-read Page $contentContainer
 *
 * @author Luke
 * @package humhub.modules_core.space.controllers
 * @since 0.5
 */
class PageController extends ContentContainerController
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'acl' => [
                'class' => AccessControl::class,
                'guestAllowedActions' => ['index', 'home', 'stream']
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'stream' => [
                'class' => ContentContainerStream::class,
                'contentContainer' => $this->contentContainer
            ],
        ];
    }

    /**
     * Generic Start Action for Profile
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $page = $this->getPage();

        if (Yii::$app->request->get('tour') || Yii::$app->request->get('contentId')) {
            return $this->actionHome();
        }

        if (!$page->isMember()) {
            $defaultPageUrl = Menu::getGuestsDefaultPageUrl($page);
            if ($defaultPageUrl != null) {
                return $this->redirect($defaultPageUrl);
            }
        }

        $defaultPageUrl = Menu::getDefaultPageUrl($page);
        if ($defaultPageUrl != null) {
            return $this->redirect($defaultPageUrl);
        }

        return $this->actionHome();
    }

    /**
     * Default page homepage
     *
     * @return string the rendering result.
     * @throws \yii\base\InvalidConfigException
     */
    public function actionHome()
    {
        $page = $this->contentContainer;
        $canCreatePosts = $page->permissionManager->can(new CreatePost());
        $isMember = $page->isMember();

        return $this->render('home', [
                    'page' => $page,
                    'canCreatePosts' => $canCreatePosts,
                    'isMember' => $isMember
        ]);
    }

    /**
     * Follows a Page
     */
    public function actionFollow()
    {
        if (Yii::$app->getModule('page')->disableFollow) {
            throw new HttpException(403, Yii::t('ContentModule.base', 'This action is disabled!'));
        }

        $this->forcePostRequest();
        $page = $this->getPage();

        $success = false;

        if (!$page->isMember()) {
            // follow without notifications by default
            $success = $page->follow(null, false);
        }

        if (Yii::$app->request->isAjax) {
            return $this->asJson(['success' => $success]);
        }

        return $this->redirect($page->getUrl());
    }

    /**
     * Unfollows a Page
     */
    public function actionUnfollow()
    {
        $this->forcePostRequest();
        $page = $this->getPage();

        $success = $page->unfollow();

        if (Yii::$app->request->isAjax) {
            return $this->asJson(['success' => $success]);
        }

        return $this->redirect($page->getUrl());
    }

    /**
     * Modal to  list followers of a page
     */
    public function actionFollowerList()
    {
        $query = User::find();
        $query->leftJoin('user_follow', 'user.id=user_follow.user_id AND object_model=:userClass AND user_follow.object_id=:spaceId', [':userClass' => Page::class, ':spaceId' => $this->getPage()->id]);
        $query->orderBy(['user_follow.id' => SORT_DESC]);
        $query->andWhere(['IS NOT', 'user_follow.id', new Expression('NULL')]);
        $query->visible();

        $title = Yii::t('PageModule.base', '<strong>Page</strong> followers');

        return $this->renderAjaxContent(UserListBox::widget(['query' => $query, 'title' => $title]));
    }

}
