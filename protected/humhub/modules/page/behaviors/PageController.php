<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\behaviors;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\user\helpers\AuthHelper;
use Yii;
use yii\base\Behavior;
use yii\helpers\Json;
use yii\web\HttpException;
use humhub\libs\Html;
use humhub\modules\page\widgets\Image;
use humhub\modules\page\models\Page;
use humhub\components\Controller;

/**
 * PageController Behavior
 *
 * In Page scopes, this behavior will automatically attached to a contentcontainer controller.
 *
 * @see Page::controllerBehavior
 * @see ContentContainerController
 * @property ContentContainerController $owner the controller
 */
class PageController extends Behavior
{

    /**
     * @var \humhub\modules\page\models\Page
     */
    public $page;

    /**
     * @inheritdoc
     */
    public function attach($owner)
    {
        parent::attach($owner);

        if (!$this->owner->contentContainer instanceof Page) {
            throw new \yii\base\InvalidValueException('Invalid contentcontainer type of controller.');
        }

        $this->page = $this->owner->contentContainer;
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction'
        ];
    }

    /**
     * @param $action
     * @throws HttpException
     */
    public function beforeAction($action)
    {
        $this->updateLastVisit();

        if (AuthHelper::isGuestAccessEnabled() && Yii::$app->user->isGuest && $this->page->visibility != Page::VISIBILITY_ALL) {
            throw new HttpException(401, Yii::t('PageModule.base', 'You need to login to view contents of this page!'));
        }

        if ($this->getMembership() === null && $this->page->visibility == Page::VISIBILITY_NONE && !Yii::$app->user->isAdmin()) {
            throw new HttpException(404, Yii::t('PageModule.base', 'Page is invisible!'));
        }

        if(empty($this->owner->subLayout)) {
            $this->owner->subLayout = "@humhub/modules/page/views/page/_layout";
        }

        $this->owner->prependPageTitle($this->page->name);

        if (Yii::$app->request->isPjax || !Yii::$app->request->isAjax) {
            $options = [
                'guid' => $this->owner->contentContainer->guid,
                'name' => Html::encode($this->owner->contentContainer->name),
                'archived' => $this->page->isArchived(),
                'image' => Image::widget([
                    'page' => $this->owner->contentContainer,
                    'width' => 32,
                    'htmlOptions' => [
                        'class' => 'current-space-image',
                    ],
                ]),
            ];

            $this->owner->view->registerJs('humhub.modules.space.setSpace(' . Json::encode($options) . ', ' .
                    Json::encode(Yii::$app->request->isPjax) . ')');
        }
    }

    protected function updateLastVisit()
    {
        $membership = $this->getMembership();
        if ($membership != null) {
            $membership->updateLastVisit();
        }
    }

    protected function getMembership()
    {
        // ToDo: Cache
        return $this->page->getMembership(Yii::$app->user->id);
    }

    public function getPage()
    {
        return $this->page;
    }

}
