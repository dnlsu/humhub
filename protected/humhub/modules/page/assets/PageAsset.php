<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\assets;

use humhub\components\assets\AssetBundle;

class PageAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@page/resources';

    /**
     * @inheritdoc
     */
    public $js = [
        'js/humhub.page.js'
    ];
}
