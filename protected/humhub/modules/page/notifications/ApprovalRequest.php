<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\notifications;

use humhub\modules\notification\components\BaseNotification;
use Yii;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

/**
 * PageApprovalRequestNotification
 *
 * @since 0.5
 */
class ApprovalRequest extends BaseNotification
{

    /**
     * @inheritdoc
     */
    public $moduleId = 'page';

    /**
     * @inheritdoc
     */
    public $viewName = 'approval';
    public $message;

    /**
     * @inheritdoc
     */
    public $markAsSeenOnClick = false;

    /**
     * Sets the approval request message for this notification.
     *
     * @param string $message
     */
    public function withMessage($message)
    {
        if ($message) {
            $this->message = $message;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getViewParams($params = [])
    {
        return ArrayHelper::merge(parent::getViewParams(['message' => $this->message]), $params);
    }

    /**
     * @inheritdoc
     */
    public function getMailSubject()
    {
        return Yii::t('PageModule.notification', '{displayName} requests membership for the page {spaceName}', [
                    '{displayName}' => Html::encode($this->originator->displayName),
                    '{spaceName}' => Html::encode($this->source->name)
        ]);
    }

    /**
     *  @inheritdoc
     */
    public function category()
    {
        return new PageMemberNotificationCategory;
    }

    /**
     * @inheritdoc
     */
    public function html()
    {
        return Yii::t('PageModule.notification', '{displayName} requests membership for the page {spaceName}', [
                    '{displayName}' => Html::tag('strong', Html::encode($this->originator->displayName)),
                    '{spaceName}' => Html::tag('strong', Html::encode($this->source->name))
        ]);
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return serialize(['source' => $this->source, 'originator' => $this->originator, 'message' => $this->message]);
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        $this->init();
        $unserializedArr = unserialize($serialized);
        $this->from($unserializedArr['originator']);
        $this->about($unserializedArr['source']);
        $this->withMessage($unserializedArr['message']);
    }

}
