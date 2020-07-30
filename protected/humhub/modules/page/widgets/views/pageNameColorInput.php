<?php

use humhub\widgets\ColorPickerField;

$containerId = time().'page-color-chooser-edit';

if ($model->color === null) {
    $model->color = '#d1d1d1';
}
?>

<div id="<?= $containerId ?>" class="form-group page-color-chooser-edit" style="margin-top: 5px;">
    <?= ColorPickerField::widget(['model' => $model, 'field' => 'color', 'container' => $containerId]); ?>

    <?= $form->field($model, 'name', ['template' => '
        {label}
        <div class="input-group">
            <span class="input-group-addon">
                <i></i>
            </span>
            {input}
        </div>
        {error}{hint}'
        ])->textInput(['placeholder' => Yii::t('PageModule.manage', 'Page name'), 'maxlength' => 45 ]); ?>
</div>
