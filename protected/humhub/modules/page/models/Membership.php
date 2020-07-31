<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use humhub\modules\content\models\Content;
use Yii;

/**
 * This is the model class for table "space_membership".
 *
 * @property integer $id
 * @property integer $page_id
 * @property integer $user_id
 * @property string|null $originator_user_id
 * @property integer|null $status
 * @property string|null $request_message
 * @property string|null $last_visit
 * @property integer $show_at_dashboard
 * @property integer $can_cancel_membership
 * @property string $group_id
 * @property string|null $created_at
 * @property integer|null $created_by
 * @property string|null $updated_at
 * @property integer|null $updated_by
 * @property integer $send_notifications
 *
 * @property Page $page
 * @property User $user
 * @property User|null $originator
 */
class Membership extends ActiveRecord
{

    /**
     * @event \humhub\modules\page\MemberEvent
     */
    const EVENT_MEMBER_REMOVED = 'memberRemoved';

    /**
     * @event \humhub\modules\page\MemberEvent
     */
    const EVENT_MEMBER_ADDED = 'memberAdded';

    /**
     * Status Codes
     */
    const STATUS_INVITED = 1;
    const STATUS_APPLICANT = 2;
    const STATUS_MEMBER = 3;

    const USER_SPACES_CACHE_KEY = 'userPages_';
    const USER_SPACEIDS_CACHE_KEY = 'userPageIds_';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'page_membership';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['page_id', 'user_id'], 'required'],
            [['page_id', 'user_id', 'originator_user_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['request_message'], 'string'],
            [['last_visit', 'created_at', 'group_id', 'updated_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'page_id' => 'Page ID',
            'user_id' => 'User ID',
            'originator_user_id' => Yii::t('PageModule.base', 'Originator User ID'),
            'status' => Yii::t('PageModule.base', 'Status'),
            'request_message' => Yii::t('PageModule.base', 'Request Message'),
            'last_visit' => Yii::t('PageModule.base', 'Last Visit'),
            'created_at' => Yii::t('PageModule.base', 'Created At'),
            'created_by' => Yii::t('PageModule.base', 'Created By'),
            'updated_at' => Yii::t('PageModule.base', 'Updated At'),
            'updated_by' => Yii::t('PageModule.base', 'Updated By'),
            'can_leave' => 'Can Leave'
        ];
    }

    /**
     * Determines if this membership is a full accepted membership.
     *
     * @since v1.2.1
     * @return bool
     */
    public function isMember()
    {
        return $this->status == self::STATUS_MEMBER;
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getOriginator()
    {
        return $this->hasOne(User::class, ['id' => 'originator_user_id']);
    }

    public function getPage()
    {
        return $this->hasOne(Page::class, ['id' => 'page_id']);
    }

    public function beforeSave($insert)
    {
        Yii::$app->cache->delete(self::USER_SPACES_CACHE_KEY . $this->user_id);
        Yii::$app->cache->delete(self::USER_SPACEIDS_CACHE_KEY . $this->user_id);
        return parent::beforeSave($insert);
    }

    public function beforeDelete()
    {
        Yii::$app->cache->delete(self::USER_SPACES_CACHE_KEY . $this->user_id);
        Yii::$app->cache->delete(self::USER_SPACEIDS_CACHE_KEY . $this->user_id);
        return parent::beforeDelete();
    }

    /**
     * Update last visit
     */
    public function updateLastVisit()
    {
        $this->last_visit = new \yii\db\Expression('NOW()');
        $this->update(false, ['last_visit']);
    }

    /**
     * Counts all new Items for this membership
     */
    public function countNewItems()
    {
        $query = Content::find();
        $query->where(['stream_channel' => 'default']);
        $query->andWhere(['contentcontainer_id' => $this->page->contentContainerRecord->id]);
        $query->andWhere(['>', 'created_at', $this->last_visit]);

        return $query->count();
    }

    /**
     * Returns a list of all pages of the given userId
     *
     * @param int|string $userId the user id or empty for current user
     * @param boolean $cached use cached result if available
     * @return Page[] an array of pages
     */
    public static function getUserPages($userId = '', $cached = true)
    {
        if ($userId === '') {
            $userId = Yii::$app->user->id;
        }

        $cacheId = self::USER_SPACES_CACHE_KEY . $userId;

        $pages = Yii::$app->cache->get($cacheId);
        if ($pages === false || !$cached) {
            $pages = [];
            foreach (static::getMembershipQuery($userId)->all() as $membership) {
                $pages[] = $membership->page;
            }
            Yii::$app->cache->set($cacheId, $pages);
        }

        return $pages;
    }

    /**
     * Returns a list of all pages' ids of the given userId
     *
     * @param integer $userId
     * @since 1.2.5
     */
    public static function getUserPageIds($userId = '')
    {
        if ($userId === '') {
            $userId = Yii::$app->user->id;
        }

        $cacheId = self::USER_SPACEIDS_CACHE_KEY . $userId;

        $pageIds = Yii::$app->cache->get($cacheId);
        if ($pageIds === false) {
            $pageIds = static::getMembershipQuery($userId)->select('page_id')->column();
            Yii::$app->cache->set($cacheId, $pageIds);
        }

        return $pageIds;
    }

    private static function getMembershipQuery($userId)
    {
        $orderSetting = Yii::$app->getModule('page')->settings->get('pageOrder');
        $orderBy = 'name ASC';
        if ($orderSetting != 0) {
            $orderBy = 'last_visit DESC';
        }

        $query = self::find()->joinWith('page')->orderBy($orderBy);
        $query->where(['user_id' => $userId, 'page_membership.status' => self::STATUS_MEMBER]);

        return $query;
    }

    /**
     * Returns Page for user page membership
     *
     * @since 1.0
     * @param \humhub\modules\user\models\User $user
     * @param boolean $memberOnly include only member status - no pending/invite states
     * @param boolean|null $withNotifications include only memberships with sendNotification setting
     * @return \yii\db\ActiveQuery for page model
     */
    public static function getUserPageQuery(User $user, $memberOnly = true, $withNotifications = null)
    {
        $query = Page::find();
        $query->leftJoin(
            'page_membership',
            'page_membership.page_id=page.id and page_membership.user_id=:userId',
            [':userId' => $user->id]
        );

        if ($memberOnly) {
            $query->andWhere(['page_membership.status' => self::STATUS_MEMBER]);
        }

        if ($withNotifications === true) {
            $query->andWhere(['page_membership.send_notifications' => 1]);
        } elseif ($withNotifications === false) {
            $query->andWhere(['page_membership.send_notifications' => 0]);
        }

        if (Yii::$app->getModule('page')->settings->get('pageOrder') == 0) {
            $query->orderBy('name ASC');
        } else {
            $query->orderBy('last_visit DESC');
        }

        $query->orderBy(['name' => SORT_ASC]);

        return $query;
    }

    /**
     * Returns an ActiveQuery selcting all memberships for the given $user.
     *
     * @param User $user
     * @param integer $membershipStatus the status of the Page by default self::STATUS_MEMBER.
     * @param integer $pageStatus the status of the Page by default Page::STATUS_ENABLED.
     * @return \yii\db\ActiveQuery
     * @since 1.2
     */
    public static function findByUser(
        User $user = null,
        $membershipStatus = self::STATUS_MEMBER,
        $pageStatus = Page::STATUS_ENABLED
    ) {
        if (!$user) {
            $user = Yii::$app->user->getIdentity();
        }

        $query = Membership::find();

        if (Yii::$app->getModule('page')->settings->get('pageOrder') == 0) {
            $query->orderBy('page.name ASC');
        } else {
            $query->orderBy('page_membership.last_visit DESC');
        }

        $query->joinWith('page')->where(['page_membership.user_id' => $user->id]);

        if ($pageStatus) {
            $query->andWhere(['page.status' => $pageStatus]);
        }

        if ($membershipStatus) {
            $query->andWhere(['page_membership.status' => $membershipStatus]);
        }

        return $query;
    }

    /**
     * Returns a user query for page memberships
     *
     * @since 1.1
     * @param Page $page
     * @param boolean $membersOnly Only return approved members
     * @param boolean|null $withNotifications include only memberships with sendNotification setting
     * @return \humhub\modules\user\components\ActiveQueryUser
     */
    public static function getPageMembersQuery(Page $page, $membersOnly = true, $withNotifications = null)
    {
        $query = User::find()->active();
        $query->join('LEFT JOIN', 'page_membership', 'page_membership.user_id=user.id');

        if ($membersOnly) {
            $query->andWhere(['page_membership.status' => self::STATUS_MEMBER]);
        }

        if ($withNotifications === true) {
            $query->andWhere(['page_membership.send_notifications' => 1]);
        } elseif ($withNotifications === false) {
            $query->andWhere(['page_membership.send_notifications' => 0]);
        }

        $query->andWhere(['page_id' => $page->id])->defaultOrder();

        return $query;
    }

    /**
     * Checks if the current logged in user is the related user of this membership record.
     *
     * @since 1.3.9
     * @return bool
     */
    public function isCurrentUser()
    {
        return !Yii::$app->user->isGuest && Yii::$app->user->identity->id === $this->user_id;
    }

}
