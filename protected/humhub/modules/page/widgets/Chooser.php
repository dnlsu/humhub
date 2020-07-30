<?php

namespace humhub\modules\page\widgets;

use humhub\components\Widget;
use humhub\modules\page\permissions\CreatePrivatePage;
use humhub\modules\page\permissions\CreatePublicPage;
use humhub\modules\page\models\Membership;
use humhub\modules\user\models\Follow;
use humhub\modules\page\widgets\PageChooserItem;
use Yii;
use yii\helpers\Html;

/**
 * Class Chooser
 * @package humhub\modules\page\widgets
 */
class Chooser extends Widget
{

    public static function getPageResult($page, $withChooserItem = true, $options = [])
    {
        $pageInfo = [];
        $pageInfo['guid'] = $page->guid;
        $pageInfo['title'] = $page->name;
        $pageInfo['tags'] = Html::encode($page->tags);
        $pageInfo['image'] = Image::widget(['page' => $page, 'width' => 24]);
        $pageInfo['link'] = $page->getUrl();

        if ($withChooserItem) {
            $options = array_merge(['page' => $page, 'isMember' => false, 'isFollowing' => false], $options);
            $pageInfo['output'] = PageChooserItem::widget($options);
        }

        return $pageInfo;
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        if (Yii::$app->user->isGuest) {
            return '';
        }

        return $this->render('pageChooser', [
                    'currentPage' => $this->getCurrentPage(),
                    'canCreatePage' => $this->canCreatePage(),
                    'memberships' => $this->getMemberships(),
                    'followPages' => $this->getFollowPages()
        ]);
    }

    protected function getFollowPages()
    {
        if (!Yii::$app->user->isGuest) {
            return Follow::getFollowedSpacesQuery(Yii::$app->user->getIdentity())->all();
        }
    }

    protected function getMemberships()
    {
        if (!Yii::$app->user->isGuest) {
            return Membership::findByUser(Yii::$app->user->getIdentity())->all();
        }
    }

    protected function canCreatePage()
    {
        return (Yii::$app->user->permissionmanager->can(new CreatePublicPage) || Yii::$app->user->permissionmanager->can(new CreatePrivatePage()));
    }

    protected function getCurrentPage()
    {
        if (Yii::$app->controller instanceof \humhub\modules\content\components\ContentContainerController) {
            if (Yii::$app->controller->contentContainer !== null && Yii::$app->controller->contentContainer instanceof \humhub\modules\page\models\Page) {
                return Yii::$app->controller->contentContainer;
            }
        }

        return null;
    }

    /**
     * Returns the membership query
     *
     * @deprecated since version 1.2
     * @return type
     */
    protected function getMembershipQuery()
    {
        $query = Membership::find();

        if (Yii::$app->getModule('page')->settings->get('pageOrder') == 0) {
            $query->orderBy('name ASC');
        } else {
            $query->orderBy('last_visit DESC');
        }

        $query->joinWith('page');
        $query->where(['page_membership.user_id' => Yii::$app->user->id, 'page_membership.status' => Membership::STATUS_MEMBER]);

        return $query;
    }

}
