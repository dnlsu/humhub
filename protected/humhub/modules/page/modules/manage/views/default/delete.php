<?php

use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\page\modules\manage\widgets\DefaultMenu;
use humhub\widgets\Button;

/* @var $this \humhub\components\View
 * @var $model \humhub\modules\page\modules\manage\models\DeleteForm
 */

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?= Yii::t('PageModule.manage', '<strong>Page</strong> settings'); ?>
    </div>

    <?= DefaultMenu::widget(['page' => $page]); ?>

    <div class="panel-body">
        <p><?= Yii::t('PageModule.manage', 'Are you sure, that you want to delete this page? All published content will be removed!'); ?></p>
        <p><?= Yii::t('PageModule.manage', 'Please type the name of the page to proceed.'); ?></p>
        <br>

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'confirmPageName'); ?>

        <hr>
        <?= Button::danger(Yii::t('PageModule.manage', 'Delete'))->confirm()->submit() ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
