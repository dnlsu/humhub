<?php

use humhub\modules\page\widgets\Image;
use yii\helpers\Html;
use yii\web\View;

$this->registerJsFile('@web-static/resources/space/spacepicker.js', ['position' => View::POS_END]);

// Resolve guids to page tags
$selectedPages = '';
foreach ($pages as $page) {
    $name = Html::encode($page->name);
    $selectedPages .= '<li class="spaceInput" id="' . $page->guid . '">' . Image::widget(['page' => $page, 'width' => 24]) . ' ' . addslashes($name) . '<i class="fa fa-times-circle"></i></li>';
}
?>

<script <?= \humhub\libs\Html::nonce() ?>>
    $(function () {
        $('#<?= $inputId; ?>').spacepicker({
            inputId: '#<?= $inputId; ?>',
            maxPages: '<?= $maxPages; ?>',
            searchUrl: '<?= $pageSearchUrl; ?>',
            currentValue: '<?= str_replace("\n", " \\", $selectedPages); ?>',
            placeholder: '<?= Html::encode($placeholder); ?>'
        });
    });
</script>
