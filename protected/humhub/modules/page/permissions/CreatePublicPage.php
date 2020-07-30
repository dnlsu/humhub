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
 * CreatePublicPage Permission
 */
class CreatePublicPage extends BasePermission
{

    /**
     * @inheritdoc
     */
    protected $id = 'create_public_page';

    /**
     * @inheritdoc
     */
    protected $title = 'Create public page';

    /**
     * @inheritdoc
     */
    protected $description = 'Can create public visible pages. (Listed in directory)';

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

        $this->title = Yii::t('PageModule.permissions', 'Create public page');
        $this->description = Yii::t('PageModule.permissions', 'Can create public visible pages. (Listed in directory)');
    }

}
