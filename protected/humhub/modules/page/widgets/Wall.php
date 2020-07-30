<?php

namespace humhub\modules\page\widgets;

use yii\base\Widget;

class Wall extends Widget
{

    public $page;

    public function run()
    {
        return $this->render('pageWall', ['page' => $this->page]);
    }

}
