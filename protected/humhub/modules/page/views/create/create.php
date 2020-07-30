<?php

use yii\bootstrap\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\modules\page\widgets\PageNameColorInput;
use yii\helpers\Url;
use humhub\libs\Html;

/* @var $model \humhub\modules\page\models\Page */
/* @var $visibilityOptions array */
/* @var $joinPolicyOptions array */

$animation = $model->hasErrors() ? 'shake' : 'fadeIn';
?>

<?php ModalDialog::begin(['header' => Yii::t('PageModule.manage', '<strong>Create</strong> new page'), 'size' => 'small']) ?>
    <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>
        <div class="modal-body">

            <?= PageNameColorInput::widget(['form' => $form, 'model' => $model]) ?>

            <?= $form->field($model, 'description')->textarea(['placeholder' => Yii::t('PageModule.manage', 'page description'), 'rows' => '3']); ?>

            <a data-toggle="collapse" id="access-settings-link" href="#collapse-access-settings" style="font-size: 11px;">
                <i class="fa fa-caret-right"></i> <?php echo Yii::t('PageModule.manage', 'Advanced access settings'); ?>
            </a>

            <div id="collapse-access-settings" class="panel-collapse collapse">
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'visibility')->radioList($visibilityOptions)->hint(false); ?>
                    </div>
                    <div class="col-md-6 pageJoinPolicy">
                        <?= $form->field($model, 'join_policy')->radioList($joinPolicyOptions)->hint(false); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <?= ModalButton::submitModal(Url::to(['/page/create/create']), Yii::t('PageModule.manage', 'Next')); ?>
        </div>
    <?php ActiveForm::end(); ?>
<?php ModalDialog::end(); ?>

<script <?= Html::nonce() ?>>

    var $checkedVisibility = $('input[type=radio][name="Page[visibility]"]:checked');
    if ($checkedVisibility.length && $checkedVisibility[0].value == 0) {
        $('.spaceJoinPolicy').hide();
    }

    $('input[type=radio][name="Page[visibility]"]').on('change', function() {
        if (this.value == 0) {
            $('.spaceJoinPolicy').fadeOut();
        } else {
            $('.spaceJoinPolicy').fadeIn();
        }
    });

    $('#collapse-access-settings').on('show.bs.collapse', function () {
        // change link arrow
        $('#access-settings-link i').removeClass('fa-caret-right');
        $('#access-settings-link i').addClass('fa-caret-down');
    });

    $('#collapse-access-settings').on('hide.bs.collapse', function () {
        // change link arrow
        $('#access-settings-link i').removeClass('fa-caret-down');
        $('#access-settings-link i').addClass('fa-caret-right');
    });

</script>
