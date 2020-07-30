<?php

namespace humhub\modules\page\widgets;

use humhub\modules\page\models\Page;
use humhub\modules\ui\form\widgets\BasePicker;
use Yii;
use yii\helpers\Html;

/**
 * Mutliselect input field for selecting page guids.
 *
 * @package humhub.modules_core.space.widgets
 * @since 1.2
 * @author buddha
 */
class PagePickerField extends BasePicker
{
    /**
     * @inheritdoc
     * Min guids string value of Page model equal 2
     */
    public $minInput = 2;

    /**
     * @inheritdoc
     */
    public $defaultRoute = '/page/browse/search-json';
    public $itemClass = Page::class;
    public $itemKey = 'guid';

    /**
     * @inheritdoc
     */
    protected function getData()
    {
        $result = parent::getData();
        $allowMultiple = $this->maxSelection !== 1;
        $result['placeholder'] = Yii::t('PageModule.chooser', 'Select {n,plural,=1{space} other{pages}}', ['n' => ($allowMultiple) ? 2 : 1]);
        $result['placeholder-more'] = Yii::t('PageModule.chooser', 'Add Page');
        $result['no-result'] = Yii::t('PageModule.chooser', 'No pages found for the given query');

        if ($this->maxSelection) {
            $result['maximum-selected'] = Yii::t('PageModule.chooser', 'This field only allows a maximum of {n,plural,=1{# page} other{# pages}}', ['n' => $this->maxSelection]);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function getItemText($item)
    {
        return Html::encode($item->getDisplayName());
    }

    /**
     * @inheritdoc
     */
    protected function getItemImage($item)
    {
        return Image::widget(['page' => $item, 'width' => 24]);
    }

}
