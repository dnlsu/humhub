<?php

use humhub\modules\page\modules\manage\widgets\DefaultMenu;
use humhub\widgets\Button;
use humhub\modules\ui\form\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this \humhub\components\View
 * @var $model \humhub\modules\page\modules\manage\models\AdvancedSettingsPage
 * @var $indexModuleSection array
 */

?>

<div class="panel panel-default">
    <div>
        <div class="panel-heading">
            <?= Yii::t('PageModule.manage', '<strong>Page</strong> settings'); ?>
        </div>
    </div>

    <?= DefaultMenu::widget(['page' => $model]); ?>

    <div class="panel-body">

        <?php $form = ActiveForm::begin(['options' => ['id' => 'pageIndexForm'], 'enableClientValidation' => false]); ?>
        <?php if (Yii::$app->urlManager->enablePrettyUrl) : ?>
            <?= $form->field($model, 'url')->hint(Yii::t('PageModule.manage', 'e.g. example for {baseUrl}/s/example', ['baseUrl' => Url::base(true)])); ?>
        <?php endif; ?>
        <?= $form->field($model, 'indexUrl')->dropDownList($indexModuleSelection)->hint(Yii::t('PageModule.manage', 'the default start page of this page for members')) ?>
        <?= $form->field($model, 'indexGuestUrl')->dropDownList($indexModuleSelection)->hint(Yii::t('PageModule.manage', 'the default start page of this page for visitors')) ?>

        <?= Button::save()->submit() ?>
        <?= Button::danger(Yii::t('base', 'Delete'))->right()->link($model->createUrl('delete'))->visible($model->canDelete()) ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
