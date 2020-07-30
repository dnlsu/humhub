<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2014 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\widgets;

use humhub\modules\page\models\Page;
use humhub\modules\page\permissions\InviteUsers;
use yii\base\Widget;

/**
 * InviteButton class
 *
 * @author luke
 * @since 0.11
 */
class InviteButton extends Widget
{
    /**
     * @var Page
     */
    public $page;

    /**
     * @inheritDoc
     */
    public function run()
    {
        if (!$this->page->getPermissionManager()->can(new InviteUsers())) {
            return;
        }

        return $this->render('inviteButton', ['page' => $this->page]);
    }

}
