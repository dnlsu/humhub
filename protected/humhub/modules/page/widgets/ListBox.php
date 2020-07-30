<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\widgets;

use yii\base\Widget;
use yii\data\Pagination;

/**
 * ListBox returns the content of the page list modal
 *
 * Example Action:
 *
 * ```php
 * public actionPageList() {
 *       $query = Page::find();
 *       $query->where(...);
 *
 *       $title = "Some Pages";
 *
 *       return $this->renderAjaxContent(ListBox::widget(['query' => $query, 'title' => $title]));
 * }
 * ```
 *
 * @since 1.1
 * @author luke
 */
class ListBox extends Widget
{

    /**
     * @var \yii\db\ActiveQuery
     */
    public $query;

    /**
     * @var string title of the box (not html encoded!)
     */
    public $title = 'Pages';

    /**
     * @var int displayed users per page
     */
    public $pageSize = 25;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $countQuery = clone $this->query;
        $pagination = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $this->pageSize]);
        $this->query->offset($pagination->offset)->limit($pagination->limit);

        return $this->render('listBox', [
                    'title' => $this->title,
                    'pages' => $this->query->all(),
                    'pagination' => $pagination
        ]);
    }

}
