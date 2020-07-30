<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\widgets;

use humhub\modules\page\models\Page;
use Yii;
use yii\base\Widget;
use yii\helpers\Url;

/**
 * Picker displays a page picker instead of an input field.
 *
 * To use this widget, you may insert the following code in a view:
 * <pre>
 *
 * echo humhub\modules\page\widgets\Picker::widget([
 *    'inputId' => 'page_filter',
 *    'value' => $pageGuidsString,
 *    'maxPages' => 3
 * ]);
 *
 * </pre>
 *
 * @since 0.5
 * @deprecated since version 1.2 use PagePickerField instead
 * @author Luke
 */
class Picker extends Widget
{
    /**
     * @var string The id of input element which should replaced
     */
    public $inputId = '';

    /**
     * JSON Search URL - default: /page/browse/search-json
     * The token -keywordPlaceholder- will replaced by the current search query.
     *
     * @var string the search url
     */
    public $pageSearchUrl = '';

    /**
     * @var int the maximum of pages
     */
    public $maxPages = 10;

    /**
     * @var \yii\base\Model the data model associated with this widget. (Optional)
     */
    public $model = null;

    /**
     * The name can contain square brackets (e.g. 'name[1]') which is used to collect tabular data input.
     * @var string the attribute associated with this widget. (Optional)
     */
    public $attribute = null;

    /**
     * @var string the initial value of comma separated page guids
     */
    public $value = '';

    /**
     * @var string placeholder message, when no page is set
     */
    public $placeholder = null;

    /**
     * Displays / Run the Widgets
     */
    public function run()
    {
        // Try to get current field value, when model & attribute attributes are specified.
        if ($this->model != null && $this->attribute != null) {
            $attribute = $this->attribute;
            $this->value = $this->model->$attribute;
        }

        if ($this->pageSearchUrl == '')
            $this->pageSearchUrl = Url::to(['/page/browse/search-json', 'keyword' => '-keywordPlaceholder-']);

        if ($this->placeholder === null) {
            $this->placeholder = Yii::t('PageModule.chooser', 'Add {n,plural,=1{space} other{pages}}', ['n' => $this->maxPages]);
        }

        // Currently populated pages
        $pages = [];
        foreach (explode(",", $this->value) as $guid) {
            $page = Page::findOne(['guid' => trim($guid)]);
            if ($page != null) {
                $pages[] = $page;
            }
        }

        return $this->render('pagePicker', [
                    'pageSearchUrl' => $this->pageSearchUrl,
                    'maxPages' => $this->maxPages,
                    'value' => $this->value,
                    'pages' => $pages,
                    'placeholder' => $this->placeholder,
                    'inputId' => $this->inputId,
        ]);
    }

}
