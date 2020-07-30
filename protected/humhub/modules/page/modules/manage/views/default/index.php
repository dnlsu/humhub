<?php

use humhub\modules\page\models\Page;
use humhub\modules\page\modules\manage\widgets\DefaultMenu;
use humhub\modules\page\widgets\PageNameColorInput;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\widgets\Button;

/* @var $this \humhub\components\View
 * @var $model \humhub\modules\page\models\Page
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

        <?= PageNameColorInput::widget(['form' => $form, 'model' => $model]) ?>
        <?= $form->field($model, 'description')->textarea(['rows' => 6]); ?>
        <?= $form->field($model, 'tags')->textInput(['maxlength' => 200]); ?>

        <?= Button::save()->submit() ?>

        <div class="pull-right">
            <?= Button::warning(Yii::t('PageModule.manage', 'Archive'))
                ->action('page.archive', $model->createUrl('/page/manage/default/archive'))
                ->cssClass('archive')->style(($model->status == Page::STATUS_ENABLED) ? 'display:inline' : 'display:none') ?>

            <?= Button::warning(Yii::t('PageModule.manage', 'Unarchive'))
                ->action('page.unarchive', $model->createUrl('/page/manage/default/unarchive'))
                ->cssClass('unarchive')->style(($model->status == Page::STATUS_ARCHIVED) ? 'display:inline' : 'display:none') ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>
