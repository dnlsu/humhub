<?php

namespace humhub\modules\page\widgets;

use humhub\components\Widget;
use Yii;

/**
 * Used to render a single page chooser result.
 *
 */
class PageChooserItem extends Widget
{

    /**
     * @var string
     */
    public $page;

    /**
     * @var integer
     */
    public $updateCount = 0;

    /**
     * @var boolean
     */
    public $visible = true;

    /**
     * If true the item will be marked as a following page
     * @var boolean
     */
    public $isFollowing = false;

    /**
     * If true the item will be marked as a member page
     * @var string
     */
    public $isMember = false;

    public function run()
    {

        $data = $this->getDataAttribute();
        $badge = $this->getBadge();

        return $this->render('pageChooserItem', [
                    'page' => $this->page,
                    'updateCount' => $this->updateCount,
                    'visible' => $this->visible,
                    'badge' => $badge,
                    'data' => $data
        ]);
    }

    public function getBadge()
    {
        if ($this->isMember) {
            return '<i class="fa fa-users badge-space pull-right type tt" title="' . Yii::t('PageModule.chooser', 'You are a member of this page') . '" aria-hidden="true"></i>';
        } elseif ($this->isFollowing) {
            return '<i class="fa fa-star badge-space pull-right type tt" title="' . Yii::t('PageModule.chooser', 'You are following this page') . '" aria-hidden="true"></i>';
        } elseif ($this->page->isArchived()) {
            return '<i class="fa fa-history badge-space pull-right type tt" title="' . Yii::t('PageModule.chooser', 'This page is archived') . '" aria-hidden="true"></i>';
        }
    }

    public function getDataAttribute()
    {
        if ($this->isMember) {
            return 'data-space-member';
        } elseif ($this->isFollowing) {
            return 'data-space-following';
        } elseif ($this->page->isArchived()) {
            return 'data-space-archived';
        } else {
            return 'data-space-none';
        }
    }
}
