<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\widgets;

use Yii;
use yii\base\Widget;

/**
 * This widget will added to the sidebar and show infos about the current selected page
 *
 * @author Andreas Strobel
 * @since 0.5
 */
class Header extends Widget
{

    /**
     * @var \humhub\modules\page\models\Page the Page which this header belongs to
     */
    public $page;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('header', [
            'page' => $this->page,
            // Deprecated variables below (will removed in future versions)
            'followingEnabled' => !Yii::$app->getModule('page')->disableFollow,
            'postCount' => -1
        ]);
    }

}
