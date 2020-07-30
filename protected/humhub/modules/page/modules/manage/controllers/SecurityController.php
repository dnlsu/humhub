<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\modules\manage\controllers;

use Yii;
use humhub\modules\page\modules\manage\components\Controller;
use humhub\modules\page\models\Page;
use yii\web\HttpException;

/**
 * SecurityController
 *
 * @since 1.1
 * @author Luke
 */
class SecurityController extends Controller
{

    public function actionIndex()
    {
        $page = $this->contentContainer;
        $page->scenario = Page::SCENARIO_SECURITY_SETTINGS;

        if ($page->load(Yii::$app->request->post()) && $page->save()) {
            $this->view->saved();
            return $this->redirect($page->createUrl('index'));
        } else if(Yii::$app->request->post()) {
            $this->view->error(Yii::t('PageModule.base', 'Settings could not be saved!'));
        }

        return $this->render('index', ['model' => $page]);
    }

    /**
     * Shows page permessions
     */
    public function actionPermissions()
    {
        $page = $this->getPage();

        $groups = $page->getUserGroups();
        $groupId = Yii::$app->request->get('groupId', Page::USERGROUP_MEMBER);
        if (!array_key_exists($groupId, $groups)) {
            throw new HttpException(500, 'Invalid group id given!');
        }

        // Handle permission state change
        if (Yii::$app->request->post('dropDownColumnSubmit')) {
            Yii::$app->response->format = 'json';
            $permission = $page->permissionManager->getById(Yii::$app->request->post('permissionId'), Yii::$app->request->post('moduleId'));
            if ($permission === null) {
                throw new HttpException(500, 'Could not find permission!');
            }
            $page->permissionManager->setGroupState($groupId, $permission, Yii::$app->request->post('state'));
            return [];
        }

        return $this->render('permissions', [
                    'page' => $page,
                    'groups' => $groups,
                    'groupId' => $groupId
        ]);
    }

}
