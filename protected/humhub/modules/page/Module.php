<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page;

use humhub\modules\user\models\User;
use Yii;

/**
 * PageModule provides all page related classes & functions.
 *
 * @author Luke
 * @since 0.5
 */
class Module extends \humhub\components\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'humhub\modules\page\controllers';

    /**
     * @var boolean Allow global admins (super admin) access to private content also when no member
     */
    public $globalAdminCanAccessPrivateContent = false;

    /**
     *
     * @var boolean Do not allow multiple pages with the same name
     */
    public $useUniquePageNames = true;

    /**
     * @var boolean defines if the page following is disabled or not.
     * @since 1.2
     */
    public $disableFollow = true;

    /**
     * @var int maximum page url length
     * @since 1.3
     */
    public $maximumPageUrlLength = 45;

    /**
     * @var int minimum page url length
     * @since 1.3
     */
    public $minimumPageUrlLength = 2;

    /**
     * @inheritdoc
     */
    public function getPermissions($contentContainer = null)
    {
        if ($contentContainer instanceof models\Page) {
            return [
                new permissions\InviteUsers(),
            ];
        } elseif ($contentContainer instanceof User) {
            return [];
        }

        return [
            new permissions\CreatePrivatePage(),
            new permissions\CreatePublicPage(),
        ];
    }

    public function getName()
    {
        return Yii::t('PageModule.base', 'Page');
    }

    /**
     * @inheritdoc
     */
    public function getNotifications()
    {
       return [
           'humhub\modules\page\notifications\ApprovalRequest',
           'humhub\modules\page\notifications\ApprovalRequestAccepted',
           'humhub\modules\page\notifications\ApprovalRequestDeclined',
           'humhub\modules\page\notifications\Invite',
           'humhub\modules\page\notifications\InviteAccepted',
           'humhub\modules\page\notifications\InviteDeclined'
       ];
    }

}
