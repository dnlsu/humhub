<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page;

use yii\base\Event;

/**
 * MemberEvent
 *
 * @since 1.2
 * @author Luke
 */
class MemberEvent extends Event
{

    /**
     * @var models\Page
     */
    public $page;

    /**
     * @var \humhub\modules\user\models\User
     */
    public $user;

}
