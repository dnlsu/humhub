<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\permissions;

use humhub\libs\BasePermission;
use Yii;

/**
 * CreatePrivatePage Permission
 */
class CreatePrivatePage extends BasePermission
{

    /**
     * @inheritdoc
     */
    protected $id = 'create_private_page';

    /**
     * @inheritdoc
     */
    protected $title = 'Create private page';

    /**
     * @inheritdoc
     */
    protected $description = 'Can create hidden (private) pages.';

    /**
     * @inheritdoc
     */
    protected $moduleId = 'page';

    /**
     * @inheritdoc
     */
    protected $defaultState = self::STATE_ALLOW;

    public function __construct($config = []) {
        parent::__construct($config);

        $this->title = Yii::t('PageModule.permissions', 'Create private page');
        $this->description = Yii::t('PageModule.permissions', 'Can create hidden (private) pages.');
    }
}
