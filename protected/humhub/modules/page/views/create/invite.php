<?= \humhub\modules\page\widgets\InviteModal::widget([
    'model' => $model,
    'submitText' => Yii::t('PageModule.base', 'Done'),
    'submitAction' => \yii\helpers\Url::to(['/page/create/invite', 'pageId' => $page->id])
]); ?>
