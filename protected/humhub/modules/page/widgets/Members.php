<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\widgets;

use humhub\modules\page\models\Membership;
use humhub\modules\page\models\Page;
use yii\db\Expression;
use yii\base\Widget;

/**
 * Page Members Snippet
 *
 * @author Luke
 * @since 0.5
 */
class Members extends Widget
{

    /**
     * @var int maximum members to display
     */
    public $maxMembers = 23;

    /**
     * @var Page the page
     */
    public $page;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $users = $this->getUserQuery()->all();

        return $this->render('members', [
                    'page' => $this->page,
                    'maxMembers' => $this->maxMembers,
                    'users' => $users,
                    'showListButton' => (count($users) == $this->maxMembers),
                    'urlMembersList' => $this->page->createUrl('/page/membership/members-list'),
                    'privilegedUserIds' => $this->getPrivilegedUserIds(),
                    'totalMemberCount' => Membership::getPageMembersQuery($this->page)->visible()->count()
        ]);
    }

    /**
     * Returns a query for members of this page
     *
     * @return \yii\db\ActiveQuery the query
     */
    protected function getUserQuery()
    {
        $query = Membership::getPageMembersQuery($this->page)->active()->visible();
        $query->limit($this->maxMembers);
        $query->orderBy(new Expression('FIELD(page_membership.group_id, "' . Page::USERGROUP_OWNER . '", "' . Page::USERGROUP_MODERATOR . '", "' . Page::USERGROUP_MEMBER . '")'));

        return $query;
    }

    /**
     * Returns an array with a list of privileged user ids.
     *
     * @return array the user ids separated by priviledged group id.
     */
    protected function getPrivilegedUserIds()
    {
        $privilegedMembers = [Page::USERGROUP_OWNER => [], Page::USERGROUP_ADMIN => [], Page::USERGROUP_MODERATOR => []];

        $query = Membership::find()->where(['page_id' => $this->page->id]);
        $query->andWhere(['IN', 'group_id', [Page::USERGROUP_OWNER, Page::USERGROUP_ADMIN, Page::USERGROUP_MODERATOR]]);
        foreach ($query->all() as $membership) {
            if (isset($privilegedMembers[$membership->group_id])) {
                $privilegedMembers[$membership->group_id][] = $membership->user_id;
            }
        }

        // Add owner manually, since it's not handled as user group yet
        $privilegedMembers[Page::USERGROUP_OWNER][] = $this->page->created_by;

        return $privilegedMembers;
    }

}
