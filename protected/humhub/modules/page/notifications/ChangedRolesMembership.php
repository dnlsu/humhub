<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\notifications;

use humhub\modules\notification\components\BaseNotification;
use Yii;
use yii\bootstrap\Html;

/**
 * @property \humhub\modules\page\models\Membership $source
 * @since 1.3
 */
class ChangedRolesMembership extends BaseNotification
{
    /**
     * @inheritdoc
     */
    public $moduleId = 'page';

    /**
     * @inheritdoc
     */
    public $viewName = 'membershipRolesChanged';

    /**
     * @inheritdoc
     */
    public function category()
    {
        return new PageMemberNotificationCategory;
    }

    /**
     * @inheritdoc
     */
    public function getMailSubject()
    {
        return strip_tags($this->html());
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->source->page->getUrl();
    }

    /**
     * @inheritdoc
     */
    public function html()
    {
        $groups = $this->source->page->getUserGroups();

        if (!isset($groups[$this->source->group_id])) {
            throw new \Exception('The role ' . $this->source->group_id . ' is wrong for Membership');
        }

        return Yii::t(
            'PageModule.notification',
            '{displayName} changed your role to {roleName} in the page {spaceName}.',
            [
                '{displayName}' => Html::tag('strong', Html::encode($this->originator->displayName)),
                '{roleName}' => Html::tag('strong', $groups[$this->source->group_id]),
                '{spaceName}' => Html::tag('strong', Html::encode($this->source->page->getDisplayName())),
            ]);
    }
}
