<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\modules\manage\components;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\content\components\ContentContainerControllerAccess;
use humhub\modules\page\models\Page;

/**
 * Default Page Manage Controller
 *
 * @author luke
 */
class Controller extends ContentContainerController
{

    protected function getAccessRules() {
        return [
            ['login'],
            [ContentContainerControllerAccess::RULE_USER_GROUP_ONLY => [Page::USERGROUP_ADMIN]]
        ];
    }
}
