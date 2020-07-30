<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\assets;

use humhub\components\assets\AssetBundle;
use humhub\modules\user\assets\UserAsset;

class PageChooserAsset extends AssetBundle
{
    public $sourcePath = '@page/resources';

    public $js = [
        'js/humhub.page.chooser.js'
    ];

    public $depends = [
        PageAsset::class,
        UserAsset::class
    ];
}
