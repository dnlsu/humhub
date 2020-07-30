<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\modules\manage\controllers;

use humhub\modules\content\components\ContentContainerControllerAccess;
use humhub\modules\page\components\UrlRule;
use Yii;
use humhub\modules\page\models\Page;
use humhub\modules\page\modules\manage\models\AdvancedSettingsPage;
use humhub\modules\page\widgets\Menu;
use humhub\modules\page\widgets\Chooser;
use humhub\modules\page\modules\manage\components\Controller;
use humhub\modules\page\modules\manage\models\DeleteForm;
use humhub\modules\page\activities\PageArchived;
use humhub\modules\page\activities\PageUnArchived;
use yii\helpers\Url;

/**
 * Default page admin action
 *
 * @author Luke
 */
class DefaultController extends Controller
{

    /**
     * @inheritdoc
     */
    protected function getAccessRules() {
        return [
            ['login'],
            [ContentContainerControllerAccess::RULE_USER_GROUP_ONLY => [Page::USERGROUP_ADMIN], 'actions' => ['index', 'advanced']],
            [ContentContainerControllerAccess::RULE_USER_GROUP_ONLY => [Page::USERGROUP_OWNER], 'actions' => ['archive', 'unarchive', 'delete']],
            [ContentContainerControllerAccess::RULE_POST => ['archive', 'unarchive']]
        ];
    }

    /**
     * General page settings
     */
    public function actionIndex()
    {
        $page = $this->contentContainer;
        $page->scenario = 'edit';

        if ($page->load(Yii::$app->request->post()) && $page->validate() && $page->save()) {
            $this->view->saved();
            return $this->redirect($page->createUrl('index'));
        }

        return $this->render('index', ['model' => $page]);
    }

    public function actionAdvanced()
    {
        $page = AdvancedSettingsPage::findOne(['id' => $this->contentContainer->id]);
        $page->scenario = 'edit';
        $page->indexUrl = Yii::$app->getModule('page')->settings->page()->get('indexUrl');
        $page->indexGuestUrl = Yii::$app->getModule('page')->settings->page()->get('indexGuestUrl');

        if ($page->load(Yii::$app->request->post()) && $page->validate() && $page->save()) {
            $this->view->saved();
            unset(UrlRule::$pageUrlMap[$page->guid]);
            return $this->redirect($page->createUrl('advanced'));
        }

        $indexModuleSelection = Menu::getAvailablePages();
        unset($indexModuleSelection[Url::to(['/page/home', 'container' => $page])]);

        // To avoid infinit redirects of actionIndex we remove the stream value and set an empty selection instead
        $indexModuleSelection = ['' => Yii::t('PageModule.manage', 'Stream (Default)')] + $indexModuleSelection;

        return $this->render('advanced', ['model' => $page, 'indexModuleSelection' => $indexModuleSelection]);
    }

    /**
     * Archives the page
     */
    public function actionArchive()
    {
        $page = $this->getPage();
        $page->archive();

        // Create Activity when the page in archived
        PageArchived::instance()->from(Yii::$app->user->getIdentity())->about($page->owner)->save();

        return $this->asJson( [
            'success' => true,
            'page' => Chooser::getPageResult($page, true, ['isMember' => true])
        ]);
    }

    /**
     * Unarchives the page
     */
    public function actionUnarchive()
    {
        $page = $this->getPage();
        $page->unarchive();

        // Create Activity when the page in unarchieved
        PageUnArchived::instance()->from(Yii::$app->user->getIdentity())->about($page->owner)->save();

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = 'json';
            return [
                'success' => true,
                'page' => Chooser::getPageResult($page, true, ['isMember' => true])
            ];
        }

        return $this->redirect($page->createUrl('/page/manage'));
    }

    /**
     * Deletes the page
     */
    public function actionDelete()
    {
        $model = new DeleteForm();
        $model->pageName = $this->getPage()->name;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $this->getPage()->delete();
            return $this->goHome();
        }

        return $this->render('delete', ['model' => $model, 'page' => $this->getPage()]);
    }
}
