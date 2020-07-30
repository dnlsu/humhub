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


class UserAddedNotification extends BaseNotification
{
    /**
     * @inheritdoc
     */
    public $moduleId = 'page';

    /**
     * @inheritdoc
     */
    public $viewName = 'userAdded';


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
        return Yii::t('PageModule.notification', 'You were added to Page {spaceName}', [
                    '{spaceName}' => Html::tag('strong', Html::encode($this->getPage()->name))
        ]);
    }

}
