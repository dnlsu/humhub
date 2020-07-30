<?php

use yii\helpers\Html;
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?= Yii::t('PageModule.manage', '<strong>Page</strong> Modules'); ?>
    </div>
    <div class="panel-body">

        <?php if (count($availableModules) == 0) : ?>
            <p><?= Yii::t('PageModule.manage', 'Currently there are no modules available for this page!'); ?></p>
        <?php else : ?>
            <?= Yii::t('PageModule.manage', 'Enhance this page with modules.'); ?><br>
        <?php endif; ?>


        <?php foreach ($availableModules as $moduleId => $module): ?>
            <hr>
            <div class="media">
                <img class="media-object img-rounded pull-left" data-src="holder.js/64x64" alt="64x64"
                     style="width: 64px; height: 64px;"
                     src="<?= $module->getContentContainerImage($page); ?>">

                <div class="media-body">
                    <h4 class="media-heading"><?= $module->getContentContainerName($page); ?>
                        <?php if ($page->isModuleEnabled($moduleId)) : ?>
                            <small><span class="label label-success"><?= Yii::t('PageModule.manage', 'Activated'); ?></span></small>
                        <?php endif; ?>
                    </h4>

                    <p><?= $module->getContentContainerDescription($page); ?></p>

                    <?php if ($page->canDisableModule($moduleId)) : ?>
                        <a href="#" style="<?= $page->isModuleEnabled($moduleId) ? '' : 'display:none' ?>"
                           data-action-click="content.container.disableModule"
                           data-action-url="<?= $page->createUrl('/page/manage/module/disable', ['moduleId' => $moduleId]) ?>" data-reload="1"
                           data-action-confirm="<?= Yii::t('PageModule.manage', 'Are you sure? *ALL* module data for this page will be deleted!') ?>"
                           class="btn btn-sm btn-primary disable disable-module-<?= $moduleId ?>" data-ui-loader>
                               <?= Yii::t('PageModule.manage', 'Disable') ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($module->getContentContainerConfigUrl($page) && $page->isModuleEnabled($moduleId)) : ?>
                        <a href="<?= $module->getContentContainerConfigUrl($page) ?>" class="btn btn-sm btn-default configure-module-<?= $moduleId ?>">
                            <?= Yii::t('PageModule.manage', 'Configure') ?>
                        </a>
                    <?php endif; ?>

                    <a href="#"  style="<?= $page->isModuleEnabled($moduleId) ? 'display:none' : '' ?>"
                       data-action-click="content.container.enableModule" data-action-url="<?= $page->createUrl('/page/manage/module/enable', ['moduleId' => $moduleId]) ?>" data-reload="1"
                       class="btn btn-sm btn-primary enable enable-module-<?= $moduleId ?>" data-ui-loader>
                        <?= Yii::t('PageModule.manage', 'Enable') ?>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
</div>
