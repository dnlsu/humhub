<?php

use humhub\components\View;
use humhub\modules\directory\permissions\AccessDirectory;
use humhub\modules\page\assets\PageChooserAsset;
use humhub\modules\page\models\Membership;
use humhub\modules\page\models\Page;
use humhub\modules\page\widgets\PageChooserItem;
use humhub\modules\page\widgets\Image;
use yii\helpers\Url;


/* @var $this View */
/* @var $currentPage Page */
/* @var $memberships Membership[] */
/* @var $followPages Page[] */
/* @var $canCreatePage boolean */

PageChooserAsset::register($this);

$noPageView = '<div class="no-space"><i class="fa fa-dot-circle-o"></i><br>' . Yii::t('PageModule.chooser', 'My pages') . '<b class="caret"></b></div>';

$this->registerJsConfig('page.chooser', [
    'noPage' => $noPageView,
    'remoteSearchUrl' =>  Url::to(['/page/browse/search-json']),
    'text' => [
        'info.remoteAtLeastInput' => Yii::t('PageModule.chooser', 'To search for other pages, type at least {count} characters.', ['count' => 2]),
        'info.emptyOwnResult' => Yii::t('PageModule.chooser', 'No member or following pages found.'),
        'info.emptyResult' => Yii::t('PageModule.chooser', 'No result found for the given filter.'),
    ],
]);

/* @var $directoryModule \humhub\modules\directory\Module */
$directoryModule = Yii::$app->getModule('directory');
$canAccessDirectory = $directoryModule->active && Yii::$app->user->can(AccessDirectory::class);

?>

<li class="dropdown">
    <a href="#" id="space-menu" class="dropdown-toggle" data-toggle="dropdown">
        <!-- start: Show page image and name if chosen -->
        <?php if ($currentPage) : ?>
            <?= Image::widget([
                'page' => $currentPage,
                'width' => 32,
                'htmlOptions' => [
                    'class' => 'current-space-image',
                ]
            ]);
            ?>
            <b class="caret"></b>
        <?php endif; ?>

        <?php if (!$currentPage) : ?>
            <?= $noPageView ?>
        <?php endif; ?>
        <!-- end: Show page image and name if chosen -->
    </a>

    <ul class="dropdown-menu" id="space-menu-dropdown">
        <li>
            <form action="" class="dropdown-controls">
                <div <?php if($canAccessDirectory) : ?>class="input-group"<?php endif; ?>>
                    <input type="text" id="space-menu-search" class="form-control" autocomplete="off"
                           placeholder="<?= Yii::t('PageModule.chooser', 'Search'); ?>"
                           title="<?= Yii::t('PageModule.chooser', 'Search for pages'); ?>">
                    <?php if($canAccessDirectory) : ?>
                        <span id="space-directory-link" class="input-group-addon" >
                            <a href="<?= Url::to(['/directory/directory/pages']); ?>">
                                <i class="fa fa-book"></i>
                            </a>
                        </span>
                    <?php endif; ?>
                    <div class="search-reset" id="space-search-reset"><i class="fa fa-times-circle"></i></div>
                </div>
            </form>
        </li>

        <li class="divider"></li>
        <li>
            <ul class="media-list notLoaded" id="space-menu-pages">
                <?php foreach ($memberships as $membership) : ?>
                    <?= PageChooserItem::widget([
                        'page' => $membership->page,
                        'updateCount' => $membership->countNewItems(),
                        'isMember' => true
                    ]);
                    ?>
                <?php endforeach; ?>
                <?php foreach ($followPages as $followPage) : ?>
                    <?= PageChooserItem::widget([
                        'page' => $followPage,
                        'isFollowing' => true
                    ]);
                    ?>
                <?php endforeach; ?>
            </ul>
        </li>
        <li class="remoteSearch">
            <ul id="space-menu-remote-search" class="media-list notLoaded"></ul>
        </li>

    <?php if ($canCreatePage) : ?>
        <li>
            <div class="dropdown-footer">
                <a href="#" class="btn btn-info col-md-12" data-action-click="ui.modal.load" data-action-url="<?= Url::to(['/page/create/create']) ?>">
                    <?= Yii::t('PageModule.chooser', 'Create new page') ?>
                </a>
            </div>
        </li>
    <?php endif; ?>
    </ul>
</li>
