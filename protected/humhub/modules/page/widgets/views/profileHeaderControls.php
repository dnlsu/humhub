<?php
/* @var $this \humhub\components\View */

/* @var $container \humhub\modules\page\models\Page */

use humhub\modules\page\widgets\FollowButton;
use humhub\modules\page\widgets\HeaderControls;
use humhub\modules\page\widgets\HeaderControlsMenu;
use humhub\modules\page\widgets\HeaderCounterSet;
use humhub\modules\page\widgets\InviteButton;
use humhub\modules\page\widgets\MembershipButton;

?>

<div class="panel-body">
    <div class="panel-profile-controls">
        <div class="row">
            <div class="col-md-12">
                <?= HeaderCounterSet::widget(['page' => $container]); ?>

                <div class="controls controls-header pull-right">
                    <?= HeaderControls::widget(['widgets' => [
                        [InviteButton::class, ['page' => $container], ['sortOrder' => 10]],
                        [MembershipButton::class, ['page' => $container], ['sortOrder' => 20]],
                        [FollowButton::class, [
                            'page' => $container,
                            'followOptions' => ['class' => 'btn btn-primary'],
                            'unfollowOptions' => ['class' => 'btn btn-info']
                        ], ['sortOrder' => 30]]
                    ]]); ?>
                    <?= HeaderControlsMenu::widget(['page' => $container]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

