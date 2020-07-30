<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\activities;

use humhub\modules\activity\components\BaseActivity;
use humhub\modules\activity\interfaces\ConfigurableActivityInterface;
use humhub\modules\content\models\Content;
use Yii;

/**
 * Description of MemberRemoved
 *
 * @author luke
 */
class MemberRemoved extends BaseActivity implements ConfigurableActivityInterface
{

    /**
     * @inheritdoc
     */
    public $viewName = 'memberRemoved';

    /**
     * @inheritdoc
     */
    public $moduleId = 'page';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->visibility = Content::VISIBILITY_PRIVATE;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('PageModule.activities', 'Page member left');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('PageModule.activities', 'Whenever a member leaves one of your pages.');
    }

}
