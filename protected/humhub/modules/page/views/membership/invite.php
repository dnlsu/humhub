<?= \humhub\modules\page\widgets\InviteModal::widget([
    'model' => $model,
    'submitText' => Yii::t('PageModule.base', 'Send'),
    'submitAction' => $page->createUrl('/page/membership/invite'),
    'searchUrl' => $page->createUrl('/page/membership/search-invite')
]); ?>
