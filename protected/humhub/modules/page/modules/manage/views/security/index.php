<?php

use humhub\modules\page\models\Page;
use humhub\modules\page\modules\manage\widgets\SecurityTabMenu;
use humhub\modules\user\helpers\AuthHelper;
use humhub\widgets\DataSaved;
use yii\bootstrap\ActiveForm;
use humhub\libs\Html;

/* @var $model Page */
?>

<div class="panel panel-default">
    <div>
        <div class="panel-heading">
            <?= Yii::t('PageModule.manage', '<strong>Security</strong> settings'); ?>
        </div>
    </div>

    <?= SecurityTabMenu::widget(['page' => $model]); ?>

    <div class="panel-body">
        <?php $form = ActiveForm::begin(); ?>

        <?php
        $visibilities = [
            Page::VISIBILITY_NONE => Yii::t('PageModule.base', 'Private (Invisible)'),
            Page::VISIBILITY_REGISTERED_ONLY => Yii::t('PageModule.base', 'Public (Registered users only)')
        ];
        if (AuthHelper::isGuestAccessEnabled()) {
            $visibilities[Page::VISIBILITY_ALL] = Yii::t('PageModule.base', 'Visible for all (members and guests)');
        }
        ?>
        <?= $form->field($model, 'visibility')->dropDownList($visibilities); ?>

        <?php $joinPolicies = [0 => Yii::t('PageModule.base', 'Only by invite'), 1 => Yii::t('PageModule.base', 'Invite and request'), 2 => Yii::t('PageModule.base', 'Everyone can enter')]; ?>
        <?= $form->field($model, 'join_policy')->dropDownList($joinPolicies, ['disabled' => $model->visibility == Page::VISIBILITY_NONE]); ?>

        <?php $defaultVisibilityLabel = Yii::t('PageModule.base', 'Default') . ' (' . ((Yii::$app->getModule('page')->settings->get('defaultContentVisibility') == 1) ? Yii::t('PageModule.base', 'Public') : Yii::t('PageModule.base', 'Private')) . ')'; ?>
        <?php $contentVisibilities = ['' => $defaultVisibilityLabel, 0 => Yii::t('PageModule.base', 'Private'), 1 => Yii::t('PageModule.base', 'Public')]; ?>
        <?= $form->field($model, 'default_content_visibility')->dropDownList($contentVisibilities, ['disabled' => $model->visibility == Page::VISIBILITY_NONE]); ?>

        <?= Html::submitButton(Yii::t('base', 'Save'), ['class' => 'btn btn-primary', 'data-ui-loader' => '']); ?>

        <?= DataSaved::widget(); ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<script <?= Html::nonce() ?>>
    $('#space-visibility').on('change', function() {
        if (this.value == 0) {
            $('#space-join_policy, #space-default_content_visibility').val('0').prop('disabled', true);
        } else {
            $('#space-join_policy, #space-default_content_visibility').val('0').prop('disabled', false);
        }
    });
</script>
