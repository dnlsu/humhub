<?php

use humhub\modules\page\assets\PageAsset;
use humhub\libs\Helpers;
use yii\helpers\Url;

PageAsset::register($this);

?>
<div class="modal-dialog modal-dialog-medium animated fadeIn">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">
                <?= Yii::t('PageModule.manage', 'Add <strong>Modules</strong>') ?>
            </h4>
        </div>
        <div class="modal-body">
            <br><br>

            <div class="row">

                <?php foreach ($availableModules as $moduleId => $module) :

                    if (($page->isModuleEnabled($moduleId) && !$page->canDisableModule($moduleId)) ||
                        (!$page->isModuleEnabled($moduleId) && !$page->canEnableModule($moduleId))) {
                        continue;
                    }
                    ?>
                    <div class="col-md-6">
                        <div class="media well well-small ">
                            <img class="media-object img-rounded pull-left" data-src="holder.js/64x64" alt="64x64"
                                 style="width: 64px; height: 64px;"
                                 src="<?= $module->getContentContainerImage($page); ?>">

                            <div class="media-body">
                                <h4 class="media-heading"><?= $module->getContentContainerName($page); ?></h4>

                                <p style="height: 35px;"><?= Helpers::truncateText($module->getContentContainerDescription($page), 75); ?></p>

                                <a href="#" class="btn btn-sm btn-primary enable"
                                   data-action-click="content.container.enableModule"
                                   data-ui-loader
                                   <?php if ($page->isModuleEnabled($moduleId)): ?>style="display:none"<?php endif; ?>
                                   data-action-url="<?= $page->createUrl('/page/manage/module/enable', ['moduleId' => $moduleId]); ?>">
                                    <?= Yii::t('PageModule.manage', 'Enable'); ?>
                                </a>

                                <a href="#" class="btn btn-sm btn-primary disable"
                                   <?php if (!$page->isModuleEnabled($moduleId)): ?>style="display:none"<?php endif; ?>
                                   data-action-click="content.container.disableModule"
                                   data-ui-loader
                                   data-action-url="<?= $page->createUrl('/page/manage/module/disable', ['moduleId' => $moduleId]); ?>">
                                    <?= Yii::t('PageModule.manage', 'Disable'); ?>
                                </a>

                            </div>
                        </div>
                        <br>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="modal-footer">
            <a href="#" class="btn btn-primary"
               data-action-click="ui.modal.post"
               data-ui-loader
               data-action-url="<?= Url::to(['/page/create/invite', 'pageId' => $page->id]); ?>">
                <?= Yii::t('PageModule.manage', 'Next'); ?>
            </a>
        </div>
    </div>
</div>
