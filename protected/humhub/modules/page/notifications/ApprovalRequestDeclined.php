<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\notifications;

use Yii;
use yii\bootstrap\Html;
use humhub\modules\notification\components\BaseNotification;

/**
 * PageApprovalRequestDeclinedNotification
 *
 * @since 0.5
 */
class ApprovalRequestDeclined extends BaseNotification
{

    /**
     * @inheritdoc
     */
    public $moduleId = 'page';

    /**
     * @inheritdoc
     */
    public $viewName = 'approvalDeclined';

    /**
     *  @inheritdoc
     */
    public function category()
    {
        return new PageMemberNotificationCategory;
    }

    /**
     *  @inheritdoc
     */
    public function getMailSubject()
    {
        return strip_tags($this->html());
    }

    /**
     * @inheritdoc
     */
    public function html()
    {
        return Yii::t('PageModule.notification', '{displayName} declined your membership request for the page {spaceName}', [
                    '{displayName}' => Html::tag('strong', Html::encode($this->originator->displayName)),
                    '{spaceName}' => Html::tag('strong', Html::encode($this->source->name))
        ]);
    }

}
