<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\widgets;

use humhub\modules\page\models\Page;
use Yii;
use yii\bootstrap\Html;
use yii\base\Widget;

/**
 * UserFollowButton
 *
 * @author luke
 * @since 0.11
 */
class FollowButton extends Widget
{

    /**
     * @var \humhub\modules\user\models\User
     */
    public $page;

    /**
     * @var string label for follow button (optional)
     */
    public $followLabel = null;

    /**
     * @var string label for unfollow button (optional)
     */
    public $unfollowLabel = null;

    /**
     * @var string options for follow button
     */
    public $followOptions = ['class' => 'btn btn-primary btn-sm'];

    /**
     * @var array options for unfollow button
     */
    public $unfollowOptions = ['class' => 'btn btn-info btn-sm'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->followLabel === null) {
            $this->followLabel = Yii::t('PageModule.base', 'Follow');
        }

        if ($this->unfollowLabel === null) {
            $this->unfollowLabel = Yii::t('PageModule.base', 'Unfollow');
        }

        if (!isset($this->followOptions['class'])) {
            $this->followOptions['class'] = '';
        }

        if (!isset($this->unfollowOptions['class'])) {
            $this->unfollowOptions['class'] = '';
        }

        if (!isset($this->followOptions['style'])) {
            $this->followOptions['style'] = '';
        }

        if (!isset($this->unfollowOptions['style'])) {
            $this->unfollowOptions['style'] = '';
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (Yii::$app->user->isGuest || $this->page->isMember() || $this->page->visibility == Page::VISIBILITY_NONE) {
            return;
        }

        // Add class for javascript handling
        $this->followOptions['class'] .= ' followButton';
        $this->unfollowOptions['class'] .= ' unfollowButton';

        // Hide inactive button
        if ($this->page->isFollowedByUser()) {
            $this->followOptions['style'] .= ' display:none;';
        } else {
            $this->unfollowOptions['style'] .= ' display:none;';
        }

        // Add PageIds
        $this->followOptions['data-content-container-id'] = $this->page->id;
        $this->unfollowOptions['data-content-container-id'] = $this->page->id;

        // Add JS Action
        $this->followOptions['data-action-click'] = 'content.container.follow';
        $this->unfollowOptions['data-action-click'] = 'content.container.unfollow';

        // Add Action Url
        $this->followOptions['data-action-url'] = $this->page->createUrl('/page/page/follow');
        $this->unfollowOptions['data-action-url'] = $this->page->createUrl('/page/page/unfollow');

        // Add Action Url
        $this->followOptions['data-ui-loader'] = '';
        $this->unfollowOptions['data-ui-loader'] = '';

        $module = Yii::$app->getModule('page');

        // still enable unfollow if following was disabled afterwards.
        if ($module->disableFollow) {
            return Html::a($this->unfollowLabel, '#', $this->unfollowOptions);
        }

        return Html::a($this->unfollowLabel, '#', $this->unfollowOptions) .
               Html::a($this->followLabel, '#', $this->followOptions);
    }

}
