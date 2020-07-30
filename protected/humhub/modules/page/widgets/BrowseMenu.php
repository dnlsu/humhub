<?php

namespace humhub\modules\page\widgets;

use Yii;

/**
 * The Main Navigation for a page. It includes the Modules the Stream
 *
 * @author Luke
 * @package humhub.modules_core.space.widgets
 * @since 0.5
 */
class BrowseMenu extends MenuWidget
{

    public $template = 'application.widgets.views.leftNavigation';

    public function init()
    {

        $this->addItemGroup([
            'id' => 'browse',
            'label' => Yii::t('PageModule.base', 'Pages'),
            'sortOrder' => 100,
        ]);

        $this->addItem([
            'label' => Yii::t('PageModule.base', 'My Page List'),
            'url' => Yii::app()->createUrl('/page/browse', []),
            'sortOrder' => 100,
            'isActive' => (Yii::app()->controller->id == "spacebrowse" && Yii::app()->controller->action->id === 'index'),
        ]);

        $this->addItem([
            'label' => Yii::t('PageModule.base', 'My page summary'),
            'url' => Yii::app()->createUrl('/dashboard', []),
            'sortOrder' => 100,
            'isActive' => (Yii::app()->controller->id == "spacebrowse" && Yii::app()->controller->action->id === 'index'),
        ]);

        $this->addItem([
            'label' => Yii::t('PageModule.base', 'Page directory'),
            'url' => Yii::app()->createUrl('/community/workpages', []),
            'sortOrder' => 200,
            'isActive' => (Yii::app()->controller->id == "spacebrowse" && Yii::app()->controller->action->id === 'index'),
        ]);

        parent::init();
    }

}
