<?php

/**
 * @var \humhub\modules\ui\view\components\View $this
 */

use humhub\modules\activity\widgets\ActivityStreamViewer;
use humhub\modules\post\widgets\Form;
use humhub\modules\page\modules\manage\widgets\PendingApprovals;
use humhub\modules\page\widgets\Members;
use humhub\modules\page\widgets\Sidebar;
use humhub\modules\stream\widgets\StreamViewer;


echo Form::widget(['contentContainer' => $page]);

$emptyMessage = '';
if ($canCreatePosts) {
    $emptyMessage = Yii::t('PageModule.base', '<b>This page is still empty!</b><br>Start by posting something here...');
} elseif ($isMember) {
    $emptyMessage = Yii::t('PageModule.base', '<b>This page is still empty!</b>');
} else {
    $emptyMessage = Yii::t('PageModule.base', '<b>You are not member of this page and there is no public content, yet!</b>');
}

echo StreamViewer::widget([
    'contentContainer' => $page,
    'streamAction' => '/page/page/stream',
    'messageStreamEmpty' => $emptyMessage,
    'messageStreamEmptyCss' => ($canCreatePosts) ? 'placeholder-empty-stream' : '',
]);

?>

<?php $this->beginBlock('sidebar'); ?>
<?= Sidebar::widget(['page' => $page, 'widgets' => [
//    [ActivityStreamViewer::class, ['contentContainer' => $page], ['sortOrder' => 10]],
//    [PendingApprovals::class, ['page' => $page], ['sortOrder' => 20]],
//    [Members::class, ['page' => $page], ['sortOrder' => 30]]
]]);
?>
<?php $this->endBlock(); ?>
