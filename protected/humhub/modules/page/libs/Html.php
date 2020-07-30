<?php

namespace humhub\modules\page\libs;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\page\models\Page;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use yii\base\InvalidArgumentException;

class Html extends \humhub\libs\Html
{
    /** @inheritdoc */
    public static function containerLink(ContentContainerActiveRecord $container, $options = [])
    {
        $options['data-contentcontainer-id'] = $container->contentcontainer_id;

        if ($container instanceof Space) {
            return static::a(static::encode($container->name), $container->getUrl(), $options);
        } elseif ($container instanceof Page) {
            return static::a(static::encode($container->name), $container->getUrl(), $options);
        } elseif ($container instanceof User) {
            if ($container->status == User::STATUS_SOFT_DELETED) {
                return static::beginTag('strike') . static::encode($container->displayName) . static::endTag('strike');
            }

            return static::a(static::encode($container->displayName), $container->getUrl(), $options);
        } else {
            throw new InvalidArgumentException('Content container type not supported!');
        }
    }
}
