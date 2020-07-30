<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\tests\codeception\fixtures;

use yii\test\ActiveFixture;

class PageFixture extends ActiveFixture
{

    public $modelClass = 'humhub\modules\page\models\Page';
    public $depends = [
        'humhub\modules\content\tests\codeception\fixtures\ContentContainerFixture'
    ];

}
