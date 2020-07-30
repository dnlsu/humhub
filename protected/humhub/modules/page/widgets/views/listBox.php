<?php

use humhub\widgets\AjaxLinkPager;
use humhub\widgets\ModalDialog;
use humhub\libs\Html;

/* @var $pages \humhub\modules\page\models\Page[] */
?>


<?php ModalDialog::begin(['header' => $title]) ?>

    <?php if (count($pages) === 0) : ?>
        <div class="modal-body">
            <p><?= Yii::t('PageModule.base', 'No pages found.'); ?></p>
        </div>
    <?php endif; ?>

    <div id="spacelist-content">

        <ul class="media-list">
            <!-- BEGIN: Results -->
            <?php foreach ($pages as $page) : ?>
                <li>
                    <a href="<?= $page->getUrl(); ?>" data-modal-close="1">

                        <div class="media">
                            <img class="media-object img-rounded pull-left"
                                 src="<?= $page->getProfileImage()->getUrl(); ?>" width="50"
                                 height="50" style="width: 50px; height: 50px;">

                            <div class="media-body">
                                <h4 class="media-heading"><?= Html::encode($page->name); ?></h4>
                                <h5><?= Html::encode($page->description); ?></h5>
                            </div>
                        </div>
                    </a>
                </li>

            <?php endforeach; ?>
            <!-- END: Results -->

        </ul>

        <div class="pagination-container">
            <?= AjaxLinkPager::widget(['pagination' => $pagination]); ?>
        </div>

    </div>
    <script <?= Html::nonce() ?>>

        // scroll to top of list
        $(".modal-body").animate({scrollTop: 0}, 200);

    </script>
<?php ModalDialog::end() ?>



