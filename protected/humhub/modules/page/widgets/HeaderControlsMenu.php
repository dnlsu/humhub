<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\widgets;

use humhub\modules\page\models\Page;
use humhub\modules\ui\menu\DropdownDivider;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\ui\menu\widgets\DropdownMenu;
use Yii;

/**
 * The Admin Navigation for pages
 *
 * @author Luke
 * @package humhub.modules_core.space.widgets
 * @since 0.5
 */
class HeaderControlsMenu extends DropdownMenu
{
    /**
     * @var Page
     */
    public $page;

    /**
     * @inheritdoc
     */
    public $label = '<i class="fa fa-cog"></i>';

    /**
     * @inheritdoc
     */
    public $id = 'page-header-controls-menu';

    /**
     * @inheritdoc
     */
    public function init()
    {

        if ($this->template === '@humhub/widgets/views/dropdownNavigation') {
            $this->template = '@ui/menu/widgets/views/dropdown-menu.php';
        }


        // check user rights
        if ($this->page->isAdmin()) {
            $this->addEntry(new MenuLink([
                'label' => Yii::t('PageModule.base', 'Settings'),
                'url' => $this->page->createUrl('/page/manage'),
                'icon' => 'cogs',
                'sortOrder' => 100
            ]));

            $this->addEntry(new MenuLink([
                'label' => Yii::t('PageModule.manage', 'Security'),
                'url' => $this->page->createUrl('/page/manage/security'),
                'icon' => 'lock',
                'sortOrder' => 200,
            ]));

            $this->addEntry(new MenuLink([
                'label' => Yii::t('PageModule.manage', 'Members'),
                'url' => $this->page->createUrl('/page/manage/member'),
                'icon' => 'group',
                'sortOrder' => 300
            ]));

            $this->addEntry(new MenuLink([
                'label' => Yii::t('PageModule.manage', 'Modules'),
                'url' => $this->page->createUrl('/page/manage/module'),
                'icon' => 'rocket',
                'sortOrder' => 400,
            ]));

            $this->addEntry(new DropdownDivider(['sortOrder' => 500]));
        }

        if ($this->page->isMember()) {
            $membership = $this->page->getMembership();

            if (!$membership->send_notifications) {
                $this->addEntry(new MenuLink([
                    'label' => Yii::t('PageModule.manage', 'Receive Notifications for new content'),
                    'url' => $this->page->createUrl('/page/membership/receive-notifications'),
                    'icon' => 'bell',
                    'sortOrder' => 600,
                    'htmlOptions' => ['data-method' => 'POST']
                ]));
            } else {
                $this->addEntry(new MenuLink([
                    'label' => Yii::t('PageModule.manage', 'Don\'t receive notifications for new content'),
                    'url' => $this->page->createUrl('/page/membership/revoke-notifications'),
                    'icon' => 'bell-o',
                    'sortOrder' => 600,
                    'htmlOptions' => ['data-method' => 'POST']
                ]));
            }

            if (!$this->page->isPageOwner() && $this->page->canLeave()) {
                $this->addEntry(new MenuLink([
                    'label' => Yii::t('PageModule.manage', 'Cancel Membership'),
                    'url' => $this->page->createUrl('/page/membership/revoke-membership'),
                    'icon' => 'times',
                    'sortOrder' => 700,
                    'htmlOptions' => ['data-method' => 'POST']
                ]));
            }

            if ($membership->show_at_dashboard) {
                $this->addEntry(new MenuLink([
                    'label' => Yii::t('PageModule.manage', 'Hide posts on dashboard'),
                    'url' => $this->page->createUrl('/page/membership/switch-dashboard-display', ['show' => 0]),
                    'icon' => 'eye-slash',
                    'sortOrder' => 800,
                    'htmlOptions' => [
                        'data-method' => 'POST',
                        'class' => 'tt',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                        'title' => Yii::t('PageModule.manage', 'This option will hide new content from this page at your dashboard')
                    ]
                ]));
            } else {
                $this->addEntry(new MenuLink([
                    'label' => Yii::t('PageModule.manage', 'Show posts on dashboard'),
                    'url' => $this->page->createUrl('/page/membership/switch-dashboard-display', ['show' => 1]),
                    'icon' => 'fa-eye',
                    'sortOrder' => 800,
                    'htmlOptions' => ['data-method' => 'POST',
                        'class' => 'tt',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'left',
                        'title' => Yii::t('PageModule.manage', 'This option will show new content from this page at your dashboard')
                    ]
                ]));
            }
        }

        return parent::init();
    }
}
