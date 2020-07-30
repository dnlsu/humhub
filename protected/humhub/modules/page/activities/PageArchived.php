<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\activities;

use Yii;
use humhub\modules\activity\components\BaseActivity;
use humhub\modules\activity\interfaces\ConfigurableActivityInterface;

/**
 * Description of MemberAdded
 *
 * @author luke
 */
class PageArchived extends BaseActivity implements ConfigurableActivityInterface
{
    /**
     * @inheritdoc
     */
    public $viewName = 'pageArchived';

    /**
     * @inheritdoc
     */
    public $moduleId = 'page';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->visibility = \humhub\modules\content\models\Content::VISIBILITY_PRIVATE;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('PageModule.activities', 'Page has been archived');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('PageModule.activities', 'Whenever a page is archived.');
    }
}
