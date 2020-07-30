<?php

use humhub\compat\CActiveForm;
use humhub\libs\Html;
?>
<div class="modal-dialog animated fadeIn">
    <div class="modal-content">
        <?php $form = CActiveForm::begin(); ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"
                id="myModalLabel"><?php echo Yii::t('PageModule.base', "<strong>Request</strong> page membership"); ?></h4>
        </div>
        <div class="modal-body">

            <?php echo Yii::t('PageModule.base', 'Please shortly introduce yourself, to become an approved member of this page.'); ?>

            <br/>
            <br/>

            <?php //echo $form->labelEx($model, 'message');  ?>
            <?php echo $form->textArea($model, 'message', ['rows' => '8', 'class' => 'form-control', 'id' => 'request-message']); ?>
            <?php echo $form->error($model, 'message'); ?>

        </div>
        <div class="modal-footer">
            <hr/>
            <?php
            echo \humhub\widgets\AjaxButton::widget([
                'label' => Yii::t('PageModule.base', 'Send'),
                'ajaxOptions' => [
                    'type' => 'POST',
                    'beforeSend' => new yii\web\JsExpression('function(){ setModalLoader(); }'),
                    'success' => new yii\web\JsExpression('function(html){ $("#globalModal").html(html); }'),
                    'url' => $page->createUrl('/page/membership/request-membership-form'),
                ],
                'htmlOptions' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
            ?>
            <button type="button" class="btn btn-primary"
                    data-dismiss="modal"><?php echo Yii::t('PageModule.base', 'Close'); ?></button>

            <?php echo \humhub\widgets\LoaderWidget::widget(['id' => 'send-loader', 'cssClass' => 'loader-modal hidden']); ?>

        </div>

        <?php CActiveForm::end(); ?>
    </div>

</div>


<script <?= Html::nonce() ?>>

    // set focus to input field
    $('#request-message').focus()

    // Shake modal after wrong validation
<?php if ($model->hasErrors()) { ?>
        $('.modal-dialog').removeClass('fadeIn');
        $('.modal-dialog').addClass('shake');
<?php } ?>

</script>
