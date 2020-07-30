<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\notifications;

use humhub\modules\notification\components\NotificationCategory;
use humhub\modules\notification\targets\BaseTarget;
use humhub\modules\notification\targets\MailTarget;
use humhub\modules\notification\targets\MobileTarget;
use humhub\modules\notification\targets\WebTarget;
use Yii;

/**
 * PageMemberNotificationCategory
 *
 * @author buddha
 */
class PageMemberNotificationCategory extends NotificationCategory
{
    /**
     * @inheritdoc
     */
    public $id = 'page_member';

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t(
            'PageModule.notification',
            'Page Membership'
        );
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t(
            'PageModule.notification',
            'Receive Notifications of Page Membership events.'
        );
    }

    /**
     * @inheritdoc
     */
    public function getDefaultSetting(BaseTarget $target)
    {
        switch ($target->id) {
            case MailTarget::getId():
            case WebTarget::getId():
            case MobileTarget::getId():
                return true;
            default:
                return $target->defaultSetting;
        }
    }
}
