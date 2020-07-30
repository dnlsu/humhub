<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\modules\manage\controllers;

use humhub\modules\content\components\ContentContainerControllerAccess;
use humhub\modules\content\controllers\ContainerImageController;
use humhub\modules\page\models\Page;

/**
 * ImageControllers handles page profile and banner image
 *
 * @author Luke
 */
class ImageController extends ContainerImageController
{
    public $validContentContainerClasses = [Page::class];

    public function getAccessRules()
    {
        return [
            [ContentContainerControllerAccess::RULE_USER_GROUP_ONLY => [Page::USERGROUP_ADMIN]],
        ];
    }

    public $imageUploadName = 'pagefiles';
    public $bannerUploadName = 'bannerfiles';

}
