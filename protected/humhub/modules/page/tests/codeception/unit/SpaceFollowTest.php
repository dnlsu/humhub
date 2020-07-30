<?php

namespace tests\codeception\unit\modules\space;

use Yii;
use tests\codeception\_support\HumHubDbTestCase;
use Codeception\Specify;
use humhub\modules\page\models\Page;
use humhub\modules\user\models\Follow;

class PageFollowTest extends HumHubDbTestCase
{

    use Specify;

    public function testPageFollow()
    {
        $this->becomeUser('User1');
        $userId = Yii::$app->user->id;
        $pageId = 4;

        // Follow Page $pageId
        $page = Page::findOne(['id' => $pageId]);
        $page->removeMember(Yii::$app->user->id);

        $page->follow(null, false);

        // Check if follow record was saved
        $follow = Follow::findOne(['object_model' => Page::class, 'object_id' => $page->id, 'user_id' => $userId]);
        $this->assertNotNull($follow);
        $this->assertFalse(boolval($follow->send_notifications));

        // Get all pages this user follows and check if the new page is included
        $pages = Follow::getFollowedPagesQuery(Yii::$app->user->getIdentity())->all();
        $this->assertEquals(count($pages), 1);
        $this->assertEquals($pages[0]->id, $page->id);

        // Get all followers of Page 2 and check if the user is included
        $followers = Follow::getFollowersQuery($page)->all();
        $this->assertEquals(count($followers), 1);

        if ($followers[0]->id == $userId) {
            $this->assertTrue(true);
        } elseif ($followers[1]->id == $userId) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false, 'User not in follower list.');
        }
    }

}
