<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\widgets;

use humhub\components\Widget;

/**
 * MembershipButton shows various membership related buttons in page header.
 *
 * @author luke
 * @since 0.11
 */
class MembershipButton extends Widget
{

    /**
     * @var \humhub\modules\page\models\Page
     */
    public $page;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $membership = $this->page->getMembership();

        return $this->render('membershipButton', [
                    'page' => $this->page,
                    'membership' => $membership
        ]);
    }

}
