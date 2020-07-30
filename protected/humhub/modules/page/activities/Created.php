<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\activities;

use humhub\modules\activity\components\BaseActivity;
use humhub\modules\content\models\Content;

/**
 * Description of PageCreated
 *
 * @author luke
 */
class Created extends BaseActivity
{

    /**
     * @inheritdoc
     */
    public $moduleId = 'page';

    /**
     * @inheritdoc
     */
    public $clickable = false;

    /**
     * @inheritdoc
     */
    public $viewName = 'created';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->visibility = Content::VISIBILITY_PUBLIC;
        parent::init();
    }

}
