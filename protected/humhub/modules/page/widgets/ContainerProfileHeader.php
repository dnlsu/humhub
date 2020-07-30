<?php


namespace humhub\modules\page\widgets;

use humhub\modules\content\controllers\ContainerImageController;
use humhub\modules\page\models\Page;
use humhub\modules\space\models\Space;
use Yii;

/**
 * Class ContainerProfileHeader
 *
 * @package humhub\modules\page\widgets
 */
class ContainerProfileHeader extends \humhub\modules\content\widgets\ContainerProfileHeader
{
    /** @inheritdoc */
    public function init()
    {
        $this->title = $this->container->getDisplayName();
        $this->subTitle = $this->container->getDisplayNameSub();

        if ($this->container instanceof Space) {
            $this->initSpaceData();
        } elseif ($this->container instanceof Page) {
            $this->initPageData();
        } else {
            $this->initUserData();
        }
    }

    public function run()
    {
        return $this->render('@content/widgets/views/containerProfileHeader', [
            'options' => $this->getOptions(),
            'container' => $this->container,
            'canEdit' => $this->canEdit,
            'title' => $this->title,
            'subTitle' => $this->subTitle,
            'classPrefix' => $this->classPrefix,
            'coverCropUrl' => $this->coverCropUrl,
            'imageCropUrl' => $this->imageCropUrl,
            'imageDeleteUrl' => $this->imageDeleteUrl,
            'coverDeleteUrl' => $this->coverDeleteUrl,
            'imageUploadUrl' => $this->imageUploadUrl,
            'coverUploadUrl' => $this->coverUploadUrl,
            'imageUploadName' => $this->imageUploadName,
            'coverUploadName' => $this->coverUploadName,
            'headerControlView' => $this->headerControlView
        ]);
    }

    public function initPageData()
    {
        $this->imageUploadUrl = $this->container->createUrl('/page/manage/image/upload');
        $this->coverUploadUrl = $this->container->createUrl('/page/manage/image/banner-upload');
        $this->coverCropUrl = $this->container->createUrl('/page/manage/image/crop-banner');
        $this->imageCropUrl = $this->container->createUrl('/page/manage/image/crop');
        $this->imageDeleteUrl = $this->container->createUrl('/page/manage/image/delete', ['type' => ContainerImageController::TYPE_PROFILE_IMAGE]);
        $this->coverDeleteUrl = $this->container->createUrl('/page/manage/image/delete', ['type' => ContainerImageController::TYPE_PROFILE_BANNER_IMAGE]);
        $this->headerControlView = '@page/widgets/views/profileHeaderControls.php';
        $this->classPrefix = 'page';

        // This is required in order to stay compatible with old themes...
        $this->imageUploadName = 'pagefiles[]';
        $this->coverUploadName = 'bannerfiles[]';
        $this->canEdit = !Yii::$app->user->isGuest && $this->container->isAdmin();
    }
}
