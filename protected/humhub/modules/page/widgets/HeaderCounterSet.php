<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\widgets;

use humhub\modules\content\models\Content;
use humhub\modules\post\models\Post;
use humhub\modules\page\models\Membership;
use humhub\modules\page\models\Page;
use humhub\modules\ui\widgets\CounterSetItem;
use humhub\modules\ui\widgets\CounterSet;
use Yii;
use yii\helpers\Url;


/**
 * Class HeaderCounterSet
 * @package humhub\modules\page\widgets
 */
class HeaderCounterSet extends CounterSet
{
    /**
     * @var Page
     */
    public $page;


    /**
     * @inheritdoc
     */
    public function init()
    {

        $postQuery = Content::find()
            ->where(['object_model' => Post::class, 'contentcontainer_id' => $this->page->contentContainerRecord->id]);
        $this->counters[] = new CounterSetItem([
            'label' => Yii::t('PageModule.base', 'Posts'),
            'value' => $postQuery->count()
        ]);

        $this->counters[] = new CounterSetItem([
            'label' => Yii::t('PageModule.base', 'Likes'),
            'value' => Membership::getPageMembersQuery($this->page)->active()->visible()->count(),
            'url' => (Yii::$app->user->isGuest) ? null : Url::to(['/page/membership/members-list', 'container' => $this->page]),
            'linkOptions' => ['data-action-click' => 'ui.modal.load']

        ]);

        if (!Yii::$app->getModule('page')->disableFollow) {
            $this->counters[] = new CounterSetItem([
                'label' => Yii::t('PageModule.base', 'Followers'),
                'value' => $this->page->getFollowerCount(),
                'url' => (Yii::$app->user->isGuest) ? null :  Url::to(['/page/page/follower-list', 'container' => $this->page]),
                'linkOptions' => ['data-action-click' => 'ui.modal.load']
            ]);
        }

        parent::init();
    }

}
