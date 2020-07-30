<?php

namespace humhub\modules\page\widgets;

use humhub\components\Widget;
use humhub\modules\content\components\ContentContainerActiveRecord;

class PageContent extends Widget
{
    /**
     * @var string
     */
    public $content = '';

    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    public function run()
    {
        return $this->content;
    }
}
