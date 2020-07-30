<?php

use humhub\libs\Helpers;
use humhub\modules\content\components\ContentContainerController;
use yii\helpers\Html;

if (!Yii::$app->controller instanceof ContentContainerController) {
    echo Yii::t('ActivityModule.base', '{displayName} left the page {spaceName}', [
        '{displayName}' => '<strong>' . Html::encode($originator->displayName) . '</strong>',
        '{spaceName}' => '<strong>' . Html::encode(Helpers::truncateText($source->name, 40)) . '</strong>'
    ]);
} else {
    echo Yii::t('ActivityModule.base', '{displayName} left this page.', [
        '{displayName}' => '<strong>' . Html::encode($originator->displayName) . '</strong>'
    ]);
}
