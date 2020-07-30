<?php

use yii\helpers\Html;
use humhub\widgets\GridView;
use humhub\modules\page\modules\manage\widgets\MemberMenu;
use humhub\widgets\TimeAgo;
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
                    [
                        'attribute' => 'last_visit',
                        'format' => 'raw',
                        'value' => function ($data) use (&$groups) {
                            if (empty($data->last_visit)) {
                                return Yii::t('PageModule.manage', 'never');
                            }
                            return TimeAgo::widget(['timestamp' => $data->last_visit]);
                        }
                    ],
                    [
                        'label' => Yii::t('PageModule.manage', 'Invited By'),
                        'attribute' => 'originator',
                        'format' => 'raw',
                        'value' =>
                            function ($data) {
                                if (is_null($data->originator)) {
                                    return Yii::t('PageModule.manage', '-');
                                }

                                return Html::a(HTML::encode($data->originator->getDisplayName()), $data->originator->getUrl());
                            }
                    ],
                    [
                        'header' => Yii::t('PageModule.manage', 'Actions'),
                        'class' => 'yii\grid\ActionColumn',
                        'buttons' => [
                            'view' => function() {
                                return;
                            },
                            'delete' => function($url, $model) use($page) {
                                return Html::a('Cancel', $page->createUrl('remove', ['userGuid' => $model->user->guid]), ['class' => 'btn btn-danger btn-sm', 'data-confirm' => 'Are you sure?', 'data-method' => 'POST']);
                            },
                            'update' => function() {
                                return;
                            },
                        ],
                    ],
                ],
            ]);
            ?>
        </div>
    </div>
</div>
