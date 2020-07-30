<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\permissions;

use humhub\libs\BasePermission;
use humhub\modules\page\models\Page;
use Yii;

/**
 * Invite new users to page permission
 */
class InviteUsers extends BasePermission
{

    /**
     * @inheritdoc
     */
    public $defaultAllowedGroups = [
        Page::USERGROUP_OWNER,
        Page::USERGROUP_ADMIN,
        Page::USERGROUP_MODERATOR,
        Page::USERGROUP_MEMBER,
    ];

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [
        Page::USERGROUP_USER,
        Page::USERGROUP_GUEST,
    ];

    /**
     * @inheritdoc
     */
    protected $title = 'Invite users';

    /**
     * @inheritdoc
     */
    protected $description = 'Allows the user to invite new members to the page';

    /**
     * @inheritdoc
     */
    protected $moduleId = 'page';

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->title = Yii::t('PageModule.permissions', 'Invite users');
        $this->description = Yii::t('PageModule.permissions', 'Allows the user to invite new members to the page');
    }

}
