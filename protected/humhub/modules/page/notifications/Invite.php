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
 * If an user was invited to a workspace, this notification is fired.
 *
 * @since 0.5
 */
class Invite extends BaseNotification
{

    /**
     * @inheritdoc
     */
    public $moduleId = 'page';

    /**
     * @inheritdoc
     */
    public $viewName = 'invite';

    /**
     * @inheritdoc
     */
    public $markAsSeenOnClick = false;

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
    public function getPage()
    {
        return $this->source;
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
        return Yii::t('PageModule.notification', '{displayName} invited you to the page {spaceName}', [
                    '{displayName}' => Html::tag('strong', Html::encode($this->originator->displayName)),
                    '{spaceName}' => Html::tag('strong', Html::encode($this->getPage()->name))
        ]);
    }

}
