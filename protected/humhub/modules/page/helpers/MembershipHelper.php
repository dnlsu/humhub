<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\helpers;

use Yii;
use humhub\modules\user\models\User;
use humhub\modules\page\models\Membership;
use humhub\modules\page\models\Page;

/**
 * MembershipHelper
 *
 * @since 1.3
 * @author Luke
 */
class MembershipHelper
{

    /**
     * Returns an array of pages where the given user is owner.
     *
     * @param User|null $user the user or null for current user
     * @param boolean $useCache use cached result if available
     * @return Page[] the list of pages
     */
    public static function getOwnPages(User $user = null, $useCache = true)
    {
        if ($user === null) {
            $user = Yii::$app->user->getIdentity();
        }

        $pages = [];
        foreach (Membership::GetUserPages($user->id, $useCache) as $page) {
            if ($page->isPageOwner($user->id)) {
                $pages[] = $page;
            }
        }
        return $pages;
    }

}
