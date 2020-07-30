<?php

use humhub\modules\page\widgets\MembershipButton;
use humhub\libs\Html;
?>

<div class="modal-dialog animated fadeIn">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">
                <?= Yii::t('PageModule.base', "<strong>Request</strong> page membership"); ?>
            </h4>
        </div>
        <div class="modal-body">

            <div class="text-center">
                <?= Yii::t('PageModule.base', 'Your request was successfully submitted to the page administrators.'); ?>
            </div>

        </div>
        <div class="modal-footer">
            <hr>
            <button type="button" class="btn btn-primary" data-dismiss="modal">
                <?= Yii::t('PageModule.base', 'Close'); ?>
            </button>
        </div>
    </div>
</div>
<script <?= Html::nonce() ?>>
    $('#requestMembershipButton').replaceWith('<?= MembershipButton::widget(['page' => $page]) ?>');
</script>
