<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\notifications;

use humhub\modules\page\models\Membership;
use humhub\modules\user\models\User;
use Yii;
use yii\bootstrap\Html;
use humhub\modules\notification\components\BaseNotification;


/**
 * PageInviteDeclinedNotification is sent to the originator of the invite to
 * inform him about the decline.
 *
 * @since 0.5
 * @author Luke
 */
class InviteRevoked extends BaseNotification
{

    /**
     * @inheritdoc
     */
    public $moduleId = 'page';

    /**
     * @inheritdoc
     */
    public $viewName = 'inviteDeclined';

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

    public function getMailSubject()
    {
        return strip_tags($this->html());
    }

    /**
     * @inheritdoc
     */
    public function html()
    {
        return Yii::t('PageModule.notification', '{displayName} revoked your invitation for the page {spaceName}', [
            '{displayName}' => Html::tag('strong', Html::encode($this->originator->displayName)),
            '{spaceName}' => Html::tag('strong', Html::encode($this->getPage()->name))
        ]);
    }

}
