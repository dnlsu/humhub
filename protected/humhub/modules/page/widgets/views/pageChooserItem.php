<?php

/* @var $page \humhub\modules\page\models\Page */

use humhub\modules\page\widgets\Image;
use humhub\libs\Helpers;
use yii\helpers\Html;
?>

<li<?= (!$visible) ? ' style="display:none"' : '' ?> data-space-chooser-item <?= $data ?> data-space-guid="<?= $page->guid; ?>">
    <a href="<?= $page->getUrl(); ?>">
        <div class="media">
            <?= Image::widget([
                'page' => $page,
                'width' => 24,
                'htmlOptions' => [
                    'class' => 'pull-left',
            ]]);
            ?>
            <div class="media-body">
                <strong class="space-name"><?= Html::encode($page->name); ?></strong>
                    <?= $badge ?>
                <div data-message-count="<?= $updateCount; ?>" style="display: none;" class="badge badge-space messageCount pull-right tt" title="<?= Yii::t('PageModule.chooser', '{n,plural,=1{# new entry} other{# new entries}} since your last visit', ['n' => $updateCount]); ?>">
                    <?= $updateCount; ?>
                </div>
                <br>
                <p><?= Html::encode(Helpers::truncateText($page->description, 60)); ?></p>
            </div>
        </div>
    </a>
</li>
