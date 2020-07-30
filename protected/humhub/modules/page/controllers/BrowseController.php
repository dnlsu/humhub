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
use humhub\modules\page\widgets\Chooser;
use Yii;
use yii\data\Pagination;

/**
 * BrowseController
 *
 * @author Luke
 * @package humhub.modules_core.space.controllers
 * @since 0.5
 */
class BrowseController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'acl' => [
                'class' => AccessControl::class,
                'guestAllowedActions' => ['search-json']
            ]
        ];
    }

    /**
     * Returns a workspace list by json
     *
     * It can be filtered by by keyword.
     */
    public function actionSearchJson()
    {
        Yii::$app->response->format = 'json';

        $query = Page::find()->visible();
        $query->search(Yii::$app->request->get('keyword'));

        $countQuery = clone $query;
        $pagination = new Pagination(['totalCount' => $countQuery->count(), 'pageSizeParam' => 'limit']);

        $query->offset($pagination->offset)->limit($pagination->limit);

        return $this->asJson($this->prepareResult($query->all()));
    }

    /**
     * @param $pages Page[] array of pages
     * @return array
     */
    protected function prepareResult($pages)
    {
        $target = Yii::$app->request->get('target');

        $json = [];
        $withChooserItem = ($target === 'chooser');
        foreach ($pages as $page) {
            $json[] = Chooser::getPageResult($page, $withChooserItem);
        }

        return $json;
    }
}
