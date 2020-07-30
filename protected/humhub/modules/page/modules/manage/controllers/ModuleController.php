<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\modules\manage\controllers;

use humhub\modules\page\modules\manage\components\Controller;
use Yii;

/**
 * Page module management
 *
 * @author Luke
 */
class ModuleController extends Controller
{
    /**
     * Modules Administration Action
     */
    public function actionIndex()
    {
        $page = $this->getPage();

        return $this->render('index', ['availableModules' => $page->getAvailableModules(), 'page' => $page]);
    }

    /**
     * Enables a page module
     *
     * @return string|array the output
     */
    public function actionEnable()
    {
        $this->forcePostRequest();

        $page = $this->getPage();

        $moduleId = Yii::$app->request->get('moduleId', '');

        if (!$page->isModuleEnabled($moduleId)) {
            $page->enableModule($moduleId);
        }

        if (!Yii::$app->request->isAjax) {
            return $this->redirect($page->createUrl('/page/manage/module'));
        } else {
            Yii::$app->response->format = 'json';
            return ['success' => true];
        }
    }

    /**
     * Disables a page module
     *
     * @return string|array the output
     */
    public function actionDisable()
    {
        $this->forcePostRequest();

        $page = $this->getPage();

        $moduleId = Yii::$app->request->get('moduleId', '');

        if ($page->isModuleEnabled($moduleId) && $page->canDisableModule($moduleId)) {
            $page->disableModule($moduleId);
        }

        if (!Yii::$app->request->isAjax) {
            return $this->redirect($page->createUrl('/page/manage/module'));
        } else {
            Yii::$app->response->format = 'json';
            return ['success' => true];
        }

    }

}
