<?php

/**
 * HumHub
 * Copyright © 2014 The HumHub Project
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 */

namespace humhub\modules\page\commands;

use Yii;
use humhub\modules\user\models\User;
use humhub\modules\page\models\Page;
use yii\helpers\Console;

/**
 * Console tools for manage pages
 *
 * @package humhub.modules_core.space.console
 * @since 0.5
 */
class PageController extends \yii\console\Controller
{

    public function actionAssignAllMembers($pageId)
    {
        $page = Page::findOne(['id' => $pageId]);
        if ($page == null) {
            print "Error: Page not found! Check id!\n\n";
            return;
        }

        $countMembers = 0;
        $countAssigns = 0;

        $this->stdout("\nAdding Members:\n\n");

        foreach (User::find()->active()->all() as $user) {
            if ($page->isMember($user->id)) {
                $countMembers++;
            } else {
                $this->stdout("\t" . $user->displayName . " added. \n", Console::FG_YELLOW);

                #Yii::app()->user->setId($user->id);

                Yii::$app->user->switchIdentity($user);
                $page->addMember($user->id);
                $countAssigns++;
            }
        }

        $this->stdout("\nAdded " . $countAssigns . " new members to page " . $page->name . "\n", Console::FG_GREEN);
    }

}
