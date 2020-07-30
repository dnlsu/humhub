<?php

use humhub\modules\page\models\Page;
use humhub\modules\page\models\Membership;
use yii\helpers\Html;

if ($membership === null) {
    if ($page->canJoin()) {
        if ($page->join_policy == Page::JOIN_POLICY_APPLICATION) {
            echo Html::a(Yii::t('PageModule.base', 'Request membership'), $page->createUrl('/page/membership/request-membership-form'), ['id' => 'requestMembershipButton', 'class' => 'btn btn-primary', 'data-target' => '#globalModal']);
        } else {
            echo Html::a(Yii::t('PageModule.base', 'Become member'), $page->createUrl('/page/membership/request-membership'), ['id' => 'requestMembershipButton', 'class' => 'btn btn-primary', 'data-method' => 'POST']);
        }
    }
} elseif ($membership->status == Membership::STATUS_INVITED) {
    ?>
    <div class="btn-group">
        <?= Html::a(Yii::t('PageModule.base', 'Accept Invite'), $page->createUrl('/page/membership/invite-accept'), ['class' => 'btn btn-info', 'data-method' => 'POST']); ?>
        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <ul class="dropdown-menu">
            <li><?= Html::a(Yii::t('PageModule.base', 'Decline Invite'), $page->createUrl('/page/membership/revoke-membership'), ['data-method' => 'POST']); ?></li>
        </ul>
    </div>
    <?php
} elseif ($membership->status == Membership::STATUS_APPLICANT) {
    echo Html::a(Yii::t('PageModule.base', 'Cancel pending membership application'), $page->createUrl('/page/membership/revoke-membership'), ['data-method' => 'POST', 'class' => 'btn btn-primary']);
}
