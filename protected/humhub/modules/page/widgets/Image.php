<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\widgets;

use humhub\modules\ui\widgets\BaseImage;
use yii\bootstrap\Html;

/**
 * Return page image or acronym
 */
class Image extends BaseImage
{
    /**
     * @var \humhub\modules\page\models\Page
     */
    public $page;

    /**
     * @var int number of characters used in the acronym
     */
    public $acronymCount = 2;


    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!isset($this->linkOptions['href'])) {
            $this->linkOptions['href'] = $this->page->getUrl();
        }

        if ($this->page->color != null) {
            $color = Html::encode($this->page->color);
        } else {
            $color = '#d7d7d7';
        }

        if (!isset($this->htmlOptions['class'])) {
            $this->htmlOptions['class'] = '';
        }

        if (!isset($this->htmlOptions['style'])) {
            $this->htmlOptions['style'] = '';
        }

        $acronymHtmlOptions = $this->htmlOptions;
        $imageHtmlOptions = $this->htmlOptions;

        $acronymHtmlOptions['class'] .= " page-profile-acronym-" . $this->page->id . " page-acronym";
        $acronymHtmlOptions['style'] .= " background-color: " . $color . "; width: " . $this->width . "px; height: " . $this->height . "px;";
        $acronymHtmlOptions['style'] .= " " . $this->getDynamicStyles($this->width);
        $acronymHtmlOptions['data-contentcontainer-id'] = $this->page->contentcontainer_id;

        $imageHtmlOptions['class'] .= " page-profile-image-" . $this->page->id . " img-rounded profile-user-photo";
        $imageHtmlOptions['style'] .= " width: " . $this->width . "px; height: " . $this->height . "px";
        $imageHtmlOptions['alt'] = Html::encode($this->page->name);

        $imageHtmlOptions['data-contentcontainer-id'] = $this->page->contentcontainer_id;

        if ($this->showTooltip) {
            $this->linkOptions['data-toggle'] = 'tooltip';
            $this->linkOptions['data-placement'] = 'top';
            $this->linkOptions['data-html'] = 'true';
            $this->linkOptions['data-original-title'] = ($this->tooltipText) ? $this->tooltipText : Html::encode($this->page->name);
            Html::addCssClass($this->linkOptions, 'tt');
        }

        $defaultImage = (basename($this->page->getProfileImage()->getUrl()) == 'default_page.jpg' || basename($this->page->getProfileImage()->getUrl()) == 'default_page.jpg?cacheId=0') ? true : false;

        if (!$defaultImage) {
            $acronymHtmlOptions['class'] .= " hidden";
        } else {
            $imageHtmlOptions['class'] .= " hidden";
        }

        return $this->render('@page/widgets/views/image', [
                    'page' => $this->page,
                    'acronym' => $this->getAcronym(),
                    'link' => $this->link,
                    'linkOptions' => $this->linkOptions,
                    'acronymHtmlOptions' => $acronymHtmlOptions,
                    'imageHtmlOptions' => $imageHtmlOptions
        ]);
    }

    protected function getAcronym()
    {
        $acronym = '';

        foreach (explode(" ", $this->page->name) as $w) {
            if (mb_strlen($w) >= 1) {
                $acronym .= mb_substr($w, 0, 1);
            }
        }

        return mb_substr(mb_strtoupper($acronym), 0, $this->acronymCount);
    }

    protected function getDynamicStyles($elementWidth)
    {

        $fontSize = 44 * $elementWidth / 100;
        $padding = 18 * $elementWidth / 100;
        $borderRadius = 4;

        if ($elementWidth < 140 && $elementWidth > 40) {
            $borderRadius = 3;
        }

        if ($elementWidth < 35) {
            $borderRadius = 2;
        }

        return "font-size: " . $fontSize . "px; padding: " . $padding . "px 0; border-radius: " . $borderRadius . "px;";
    }

}
