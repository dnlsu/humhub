<?php

use humhub\widgets\GridView;
use yii\bootstrap\ActiveForm;
use humhub\modules\page\models\Page;
use humhub\modules\page\modules\manage\widgets\MemberMenu;
use humhub\modules\user\grid\ImageColumn;
use humhub\modules\user\grid\DisplayNameColumn;
use humhub\modules\page\modules\manage\models\MembershipSearch;
use humhub\widgets\TimeAgo;
use yii\helpers\Html;

/* @var $page Page */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?= Yii::t('PageModule.manage', '<strong>Manage</strong> members'); ?>
    </div>
    <?= MemberMenu::widget(['page' => $page]); ?>
    <div class="panel-body">

        <?php $form = ActiveForm::begin(['method' => 'get']); ?>
        <div class="row">
            <div class="col-md-8">
                <div class="input-group">
                    <?= Html::activeTextInput($searchModel, 'freeText', ['class' => 'form-control', 'placeholder' => Yii::t('AdminModule.user', 'Search by name, email or id.')]); ?>
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
                    </span>
                </div>
            </div>
            <div class="col-md-4">
                <?= Html::activeDropDownList($searchModel, 'group_id', MembershipSearch::getRoles($page), ['class' => 'form-control', 'data-action-change' => 'ui.form.submit']); ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
        <div class="table-responsive">

            <?php
            $groups = $page->getUserGroups();
            unset($groups[Page::USERGROUP_OWNER], $groups[Page::USERGROUP_GUEST], $groups[Page::USERGROUP_USER]);

            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'summary' => '',
                'columns' => [
                    ['class' => ImageColumn::class, 'userAttribute' => 'user'],
                    ['class' => DisplayNameColumn::class, 'userAttribute' => 'user'],
                    [
                        'label' => 'Member since',
                        'attribute' => 'created_at',
                        'format' => 'raw',
                        'value' =>
                        function ($data) {
                            if ($data->created_at == '') {
                                return Yii::t('PageModule.manage', '-');
                            }

                            return TimeAgo::widget(['timestamp' => $data->created_at]);
                        }
                    ],
                    [
                        'attribute' => 'last_visit',
                        'format' => 'raw',
                        'value' =>
                        function ($data) use (&$groups) {
                            if (empty($data->last_visit)) {
                                return Yii::t('PageModule.manage', 'never');
                            }

                            return TimeAgo::widget(['timestamp' => $data->last_visit]);
                        }
                    ],
                    [
                        'label' => Yii::t('PageModule.manage', 'Role'),
                        'class' => 'humhub\libs\DropDownGridColumn',
                        'attribute' => 'group_id',
                        'submitAttributes' => ['user_id'],
                        'readonly' => function ($data) use ($page) {
                            if ($page->isPageOwner($data->user->id)) {
                                return true;
                            }
                            return false;
                        },
                        'filter' => $groups,
                        'dropDownOptions' => $groups,
                        'value' =>
                        function ($data) use (&$groups, $page) {
                            return $groups[$data->group_id];
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'options' => ['style' => 'width:40px; min-width:40px;'],
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return false;
                            },
                            'update' => function ($url, $model) {
                                return false;
                            },
                            'delete' => function ($url, $model) use($page) {
                                $url = ['/page/manage/member/remove', 'userGuid' => $model->user->guid, 'container' => $page];
                                return Html::a('<i class="fa fa-times"></i>', $url, [
                                            'title' => Yii::t('PageModule.manage', 'Remove from page'),
                                            'class' => 'btn btn-danger btn-xs tt',
                                            'data-method' => 'POST',
                                            'data-confirm' => 'Are you really sure?'
                                ]);
                            }
                        ],
                    ],
                ],
            ]);
            ?>
        </div>
    </div>
</div>
