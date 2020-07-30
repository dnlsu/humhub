<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\widgets;

use humhub\widgets\BaseSidebar;

/**
 * Sidebar implements the default page sidebar.
 *
 * @author Luke
 * @since 0.5
 */
class Sidebar extends BaseSidebar
{

    /**
     * @var \humhub\modules\page\models\Page the page this sidebar is in
     */
    public $page;

}
