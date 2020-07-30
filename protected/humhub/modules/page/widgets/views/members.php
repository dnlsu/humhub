<?php

use humhub\libs\Html;
use humhub\widgets\PanelMenu;
use humhub\modules\user\widgets\Image;
use humhub\modules\page\models\Page;
?>

<div class="panel panel-default members" id="space-members-panel">
    <?= PanelMenu::widget(['id' => 'page-members-panel']); ?>
    <div class="panel-heading"><?= Yii::t('PageModule.base', '<strong>Page</strong> members'); ?> (<?= $totalMemberCount ?>)</div>
    <div class="panel-body">
        <?php foreach ($users as $user) : ?>
            <?php
            if (in_array($user->id, $privilegedUserIds[Page::USERGROUP_OWNER])) {
                // Show Owner image & tooltip
                echo Image::widget([
                    'user' => $user, 'width' => 32, 'showTooltip' => true,
                    'tooltipText' => Yii::t('PageModule.base', 'Owner:') . "\n" . Html::encode($user->displayName),
                    'imageOptions' => ['style' => 'border:1px solid ' . $this->theme->variable('success')]
                ]);
            } elseif (in_array($user->id, $privilegedUserIds[Page::USERGROUP_ADMIN])) {
                // Show Admin image & tooltip
                echo Image::widget([
                    'user' => $user, 'width' => 32, 'showTooltip' => true,
                    'tooltipText' => Yii::t('PageModule.base', 'Administrator:') . "\n" . Html::encode($user->displayName),
                    'imageOptions' => ['style' => 'border:1px solid ' . $this->theme->variable('success')]
                ]);
            } elseif (in_array($user->id, $privilegedUserIds[Page::USERGROUP_MODERATOR])) {
                // Show Moderator image & tooltip
                echo Image::widget([
                    'user' => $user, 'width' => 32, 'showTooltip' => true,
                    'tooltipText' => Yii::t('PageModule.base', 'Moderator:') . "\n" . Html::encode($user->displayName),
                    'imageOptions' => ['style' => 'border:1px solid ' . $this->theme->variable('info')]
                ]);
            } else {
                // Standard member
                echo Image::widget(['user' => $user, 'width' => 32, 'showTooltip' => true]);
            }
            ?>
        <?php endforeach; ?>

        <?php if ($showListButton) : ?>
            <br>
            <a href="<?= $urlMembersList; ?>" data-target="#globalModal" class="btn btn-default btn-sm"><?= Yii::t('PageModule.base', 'Show all'); ?></a>
        <?php endif; ?>

    </div>
</div>
