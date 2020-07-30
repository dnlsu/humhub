<?php

use humhub\modules\page\widgets\Image;
use yii\helpers\Html;
?>

<div class="panel panel-default">
    <div class="panel-body">

        <div class="media">
            <a href="<?= $page->getUrl(); ?>" class="pull-left">
                <!-- Show page image -->
                <?= Image::widget([
                    'page' => $page,
                    'width' => 40
                ]);
                ?>
            </a>
            <div class="media-body">
                <!-- show username with link and creation time-->
                <h4 class="media-heading"><a href="<?= $page->getUrl(); ?>"><?= Html::encode($page->displayName); ?></a> </h4>
                <h5><?= Html::encode($page->description); ?></h5>
            </div>
        </div>

    </div>
</div>
