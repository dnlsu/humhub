<?php

namespace humhub\modules\page\widgets;

use yii\helpers\ArrayHelper;

/**
 * WallEntryAddonWidget is an instance of StackWidget for wall entries.
 *
 * This widget is used to add some widgets to a wall entry.
 * e.g. Likes or Comments.
 *
 * @package humhub.modules_core.wall.widgets
 */
class WallEntryAddons extends \humhub\modules\content\widgets\WallEntryAddons
{
    /** @inheritdoc */
    public function addWidget($className, $params = [], $options = [])
    {
        if (!strcmp($className, 'humhub\modules\comment\widgets\Comments')) {
            $className = 'humhub\modules\page\widgets\Comments';
        }
        if (isset($this->widgetOptions[$className])) {
            $params = ArrayHelper::merge($params, $this->widgetOptions[$className]);
        }

        parent::addWidget($className, $params, $options);
    }

}

?>
