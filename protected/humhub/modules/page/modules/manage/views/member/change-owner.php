<?php

use humhub\modules\page\modules\manage\models\ChangeOwnerForm;
use yii\helpers\Html;
use humhub\modules\page\modules\manage\widgets\MemberMenu;
use yii\widgets\ActiveForm;
use humhub\widgets\Button;

/* @var $model ChangeOwnerForm */
?>


<div class="panel panel-default">
    <div class="panel-heading">
        <?= Yii::t('PageModule.manage', '<strong>Manage</strong> members'); ?>
    </div>
    <?= MemberMenu::widget(['page' => $page]); ?>
    <div class="panel-body">

        <p><?= Yii::t('PageModule.manage', 'As owner of this page you can transfer this role to another administrator in page.'); ?></p>

        <?php $form = ActiveForm::begin([]); ?>

            <?= $form->field($model, 'ownerId')->dropDownList($model->getNewOwnerArray()) ?>

            <hr>

            <?= Button::danger(Yii::t('PageModule.manage', 'Transfer ownership'))->action('client.submit')->confirm() ?>

        <?php ActiveForm::end(); ?>

    </div>
</div>
