<?php

use humhub\modules\page\widgets\Header;
use humhub\modules\page\widgets\Menu;
use humhub\modules\page\widgets\PageContent;
use humhub\widgets\FooterMenu;

/**
 * @var \humhub\modules\ui\view\components\View $this
 * @var \humhub\modules\page\models\Page $page
 * @var string $content
 */

/** @var \humhub\modules\content\components\ContentContainerController $context */
$context = $this->context;
$page = $context->contentContainer;

?>
<div class="container page-layout-container">
    <div class="row">
        <div class="col-md-12">
            <?= Header::widget(['page' => $page]); ?>
        </div>
    </div>
    <div class="row page-content">
        <div class="col-md-2 layout-nav-container">
            <?= Menu::widget(['page' => $page]); ?>
        </div>
        <div class="col-md-<?= ($this->hasSidebar()) ? '7' : '10' ?> layout-content-container">
            <?= PageContent::widget(['contentContainer' => $page, 'content' => $content]) ?>
        </div>
        <?php if ($this->hasSidebar()): ?>
            <div class="col-md-3 layout-sidebar-container">
                <?= $this->getSidebar() ?>
                <?= FooterMenu::widget(['location' => FooterMenu::LOCATION_SIDEBAR]); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!$this->hasSidebar()): ?>
        <?= FooterMenu::widget(['location' => FooterMenu::LOCATION_FULL_PAGE]); ?>
    <?php endif; ?>
</div>
