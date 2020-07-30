<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\modules\manage\widgets;

use Yii;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\page\models\Membership;
use humhub\modules\page\modules\manage\models\MembershipSearch;
use humhub\modules\ui\menu\widgets\TabMenu;

/**
 * MemberMenu is a tabbed menu for page member administration
 *
 * @author Basti
 */
class MemberMenu extends TabMenu
{
    /**
     * @var \humhub\modules\page\models\Page
     */
    public $page;

    /**
     * @inheritdoc
     */
    public function init()
    {

        $this->addEntry(new MenuLink([
            'label' => Yii::t('PageModule.manage', 'Members'),
            'url' => $this->page->createUrl('/page/manage/member/index'),
            'sortOrder' => 100,
            'isActive' => MenuLink::isActiveState(null, 'member', 'index')
        ]));

        if ($this->countPendingInvites() != 0) {
            $this->addEntry(new MenuLink([
                'label' => Yii::t('PageModule.manage', 'Pending Invites') . '&nbsp;&nbsp;<span class="label label-danger">' . $this->countPendingInvites() . '</span>',
                'url' => $this->page->createUrl('/page/manage/member/pending-invitations'),
                'sortOrder' => 200,
                'isActive' => MenuLink::isActiveState(null, 'member', 'pending-invitations')
            ]));
        }
        if ($this->countPendingApprovals() != 0) {
            $this->addEntry(new MenuLink([
                'label' => Yii::t('PageModule.manage', 'Pending Approvals') . '&nbsp;&nbsp;<span class="label label-danger">' . $this->countPendingApprovals() . '</span>',
                'url' => $this->page->createUrl('/page/manage/member/pending-approvals'),
                'sortOrder' => 300,
                'isActive' => MenuLink::isActiveState(null, 'member', 'pending-approvals')
            ]));
        }

        if ($this->page->isPageOwner()) {
            $this->addEntry(new MenuLink([
                'label' => Yii::t('PageModule.manage', 'Owner'),
                'url' => $this->page->createUrl('/page/manage/member/change-owner'),
                'sortOrder' => 500,
                'isActive' => MenuLink::isActiveState(null, 'member', 'change-owner')
            ]));
        }


        parent::init();
    }

    /**
     * Returns the number of currently invited users
     *
     * @return int currently invited members
     */
    protected function countPendingInvites()
    {
        $searchModel = new MembershipSearch();
        $searchModel->page_id = $this->page->id;
        $searchModel->status = Membership::STATUS_INVITED;

        return $searchModel->search([])->getCount();
    }

    /**
     * Returns the number of currently pending approvals
     *
     * @return int currently pending approvals
     */
    protected function countPendingApprovals()
    {
        $searchModel = new MembershipSearch();
        $searchModel->page_id = $this->page->id;
        $searchModel->status = Membership::STATUS_APPLICANT;

        return $searchModel->search([])->getCount();
    }

}
