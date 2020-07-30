<?php

use yii\helpers\Html;
use humhub\widgets\GridView;
use humhub\modules\page\modules\manage\widgets\MemberMenu;
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?= Yii::t('PageModule.manage', '<strong>Manage</strong> members'); ?>
    </div>
    <?= MemberMenu::widget(['page' => $page]); ?>
    <div class="panel-body">
        <div class="table-responsive">
            <?php
            $groups = $page->getUserGroups();

            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'user.username',
                    'user.profile.firstname',
                    'user.profile.lastname',
                    'request_message',
                    [
                        'header' => Yii::t('PageModule.manage', 'Actions'),
                        'class' => 'yii\grid\ActionColumn',
                        'buttons' => [
                            'view' => function() {
                                return;
                            },
                            'delete' => function ($url, $model) use ($page) {
                                return Html::a('Reject', $page->createUrl('reject-applicant', ['userGuid' => $model->user->guid]), ['class' => 'btn btn-danger btn-sm', 'data-method' => 'POST']);
                            },
                            'update' => function ($url, $model) use ($page) {
                                return Html::a('Approve', $page->createUrl('approve-applicant', ['userGuid' => $model->user->guid]), ['class' => 'btn btn-primary btn-sm', 'data-method' => 'POST']);
                            },
                        ],
                    ],
                ],
            ]);
            ?>
        </div>
    </div>
</div>
