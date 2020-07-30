<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\models;

use humhub\libs\ProfileImage;
use humhub\modules\search\interfaces\Searchable;
use humhub\modules\search\events\SearchAddEvent;
use humhub\modules\search\jobs\DeleteDocument;
use humhub\modules\search\jobs\UpdateDocument;
use humhub\modules\page\behaviors\PageModelMembership;
use humhub\modules\page\behaviors\PageController;
use humhub\modules\page\components\ActiveQueryPage;
use humhub\modules\user\behaviors\Followable;
use humhub\components\behaviors\GUID;
use humhub\modules\content\components\behaviors\SettingsBehavior;
use humhub\modules\content\components\behaviors\CompatModuleManager;
use humhub\modules\page\permissions\CreatePrivatePage;
use humhub\modules\page\permissions\CreatePublicPage;
use humhub\modules\page\permissions\InviteUsers;
use humhub\modules\content\permissions\CreatePublicContent;
use humhub\modules\page\components\UrlValidator;
use humhub\modules\page\activities\Created;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\user\components\ActiveQueryUser;
use humhub\modules\user\helpers\AuthHelper;
use humhub\modules\user\models\User;
use humhub\modules\user\models\Follow;
use humhub\modules\user\models\Invite;
use humhub\modules\user\models\Group;
use humhub\modules\page\widgets\Wall;
use humhub\modules\page\widgets\Members;
use Yii;

/**
 * This is the model class for table "space".
 *
 * @property integer $id
 * @property string $guid
 * @property string $name
 * @property string $description
 * @property string $url
 * @property integer $join_policy
 * @property integer $visibility
 * @property integer $status
 * @property string $tags
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 * @property integer $auto_add_new_members
 * @property integer $contentcontainer_id
 * @property integer $default_content_visibility
 * @property string $color
 * @property User $ownerUser the owner of this page
 *
 * @mixin \humhub\components\behaviors\GUID
 * @mixin \humhub\modules\content\components\behaviors\SettingsBehavior
 * @mixin \humhub\modules\page\behaviors\PageModelMembership
 * @mixin \humhub\modules\user\behaviors\Followable
 * @mixin \humhub\modules\content\components\behaviors\CompatModuleManager
 */
class Page extends ContentContainerActiveRecord implements Searchable
{

    // Join Policies
    const JOIN_POLICY_NONE = 0; // No Self Join Possible
    const JOIN_POLICY_APPLICATION = 1; // Invitation and Application Possible
    const JOIN_POLICY_FREE = 2; // Free for All
    // Visibility: Who can view the page content.
    const VISIBILITY_NONE = 0; // Private: This page is invisible for non-space-members
    const VISIBILITY_REGISTERED_ONLY = 1; // Only registered users (no guests)
    const VISIBILITY_ALL = 2; // Public: All Users (Members and Guests)
    // Status
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;
    const STATUS_ARCHIVED = 2;
    // UserGroups
    const USERGROUP_OWNER = 'owner';
    const USERGROUP_ADMIN = 'admin';
    const USERGROUP_MODERATOR = 'moderator';
    const USERGROUP_MEMBER = 'member';
    const USERGROUP_USER = 'user';
    const USERGROUP_GUEST = 'guest';
    // Model Scenarios
    const SCENARIO_CREATE = 'create';
    const SCENARIO_EDIT = 'edit';
    const SCENARIO_SECURITY_SETTINGS = 'security_settings';

    /**
     * @inheritdoc
     */
    public $controllerBehavior = PageController::class;

    /**
     * @inheritdoc
     */
    public $defaultRoute = '/page/page';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'page';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['join_policy', 'visibility', 'status', 'auto_add_new_members', 'default_content_visibility'], 'integer'],
            [['name'], 'required'],
            [['description', 'tags', 'color'], 'string'],
            [['join_policy'], 'in', 'range' => [0, 1, 2]],
            [['visibility'], 'in', 'range' => [0, 1, 2]],
            [['visibility'], 'checkVisibility'],
            [['url'], 'unique', 'skipOnEmpty' => 'true'],
            [['guid', 'name'], 'string', 'max' => 45, 'min' => 2],
            [['url'], 'string', 'max' => Yii::$app->getModule('page')->maximumPageUrlLength, 'min' => Yii::$app->getModule('page')->minimumPageUrlLength],
            [['url'], UrlValidator::class],
        ];

        if (Yii::$app->getModule('page')->useUniquePageNames) {
            $rules[] = [['name'], 'unique', 'targetClass' => static::class, 'when' => function($model) {
                return $model->isAttributeChanged('name');
            }];
        }

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[static::SCENARIO_EDIT] = ['name', 'color', 'description', 'tags', 'join_policy', 'visibility', 'default_content_visibility', 'url'];
        $scenarios[static::SCENARIO_CREATE] = ['name', 'color', 'description', 'join_policy', 'visibility'];
        $scenarios[static::SCENARIO_SECURITY_SETTINGS] = ['default_content_visibility', 'join_policy', 'visibility'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('PageModule.base', 'Name'),
            'color' => Yii::t('PageModule.base', 'Color'),
            'description' => Yii::t('PageModule.base', 'Description'),
            'join_policy' => Yii::t('PageModule.base', 'Join Policy'),
            'visibility' => Yii::t('PageModule.base', 'Visibility'),
            'status' => Yii::t('PageModule.base', 'Status'),
            'tags' => Yii::t('PageModule.base', 'Tags'),
            'created_at' => Yii::t('PageModule.base', 'Created At'),
            'created_by' => Yii::t('PageModule.base', 'Created By'),
            'updated_at' => Yii::t('PageModule.base', 'Updated At'),
            'updated_by' => Yii::t('PageModule.base', 'Updated by'),
            'ownerUsernameSearch' => Yii::t('PageModule.base', 'Owner'),
            'default_content_visibility' => Yii::t('PageModule.base', 'Default content visibility')
        ];
    }

    public function attributeHints()
    {
        return [
            'visibility' => Yii::t('PageModule.manage', 'Choose the security level for this workspace to define the visibleness.'),
            'join_policy' => Yii::t('PageModule.manage', 'Choose the kind of membership you want to provide for this workspace.'),
            'default_content_visibility' => Yii::t('PageModule.manage', 'Choose if new content should be public or private by default')
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            GUID::class,
            SettingsBehavior::class,
            PageModelMembership::class,
            Followable::class,
            CompatModuleManager::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        Yii::$app->queue->push(new UpdateDocument([
            'activeRecordClass' => get_class($this),
            'primaryKey' => $this->id
        ]));

        $user = User::findOne(['id' => $this->created_by]);

        if ($insert) {
            // Auto add creator as admin
            $membership = new Membership();
            $membership->page_id = $this->id;
            $membership->user_id = $user->id;
            $membership->status = Membership::STATUS_MEMBER;
            $membership->group_id = self::USERGROUP_ADMIN;
            $membership->save();

            $activity = new Created;
            $activity->source = $this;
            $activity->originator = $user;
            $activity->create();
        }

        Yii::$app->cache->delete('userPages_' . $user->id);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->url = UrlValidator::autogenerateUniquePageUrl($this->name);
        }

        if ($this->url == '') {
            $this->url = new \yii\db\Expression('NULL');
        } else {
            $this->url = mb_strtolower($this->url);
        }

        // Make sure visibility attribute is not empty
        if (empty($this->visibility)) {
            $this->visibility = self::VISIBILITY_NONE;
        }

        if ($this->visibility == self::VISIBILITY_NONE) {
            $this->join_policy = self::JOIN_POLICY_NONE;
            $this->default_content_visibility = Content::VISIBILITY_PRIVATE;
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        foreach ($this->getAvailableModules() as $moduleId => $module) {
            if ($this->isModuleEnabled($moduleId)) {
                $this->disableModule($moduleId);
            }
        }

        foreach ($this->moduleManager->getEnabled() as $module) {
            $this->moduleManager->disable($module);
        }

        Yii::$app->queue->push(new DeleteDocument([
            'activeRecordClass' => get_class($this),
            'primaryKey' => $this->id
        ]));


        $this->getProfileImage()->delete();
        $this->getProfileBannerImage()->delete();

        Follow::deleteAll(['object_id' => $this->id, 'object_model' => 'Page']);

        foreach (Membership::findAll(['page_id' => $this->id]) as $pageMembership) {
            $pageMembership->delete();
        }

        Invite::deleteAll(['page_invite_id' => $this->id]);

        // When this workspace is used in a group as default workspace, delete the link
        foreach (Group::findAll(['page_id' => $this->id]) as $group) {
            $group->page_id = '';
            $group->save();
        }

        return parent::beforeDelete();
    }

    /**
     * @inheritdoc
     * @return ActiveQueryPage
     */
    public static function find()
    {
        return new ActiveQueryPage(get_called_class());
    }


    /**
     * Indicates that this user can join this workspace
     *
     * @param $userId User Id of User
     */
    public function canJoin($userId = '')
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        // Take current userId if none is given
        if ($userId == '') {
            $userId = Yii::$app->user->id;
        }

        // Checks if User is already member
        if ($this->isMember($userId)) {
            return false;
        }

        // No one can join
        if ($this->join_policy == self::JOIN_POLICY_NONE) {
            return false;
        }

        return true;
    }

    /**
     * Indicates that this user can join this workspace w
     * ithout permission
     *
     * @param $userId User Id of User
     */
    public function canJoinFree($userId = '')
    {
        // Take current userid if none is given
        if ($userId == '') {
            $userId = Yii::$app->user->id;
        }

        // Checks if User is already member
        if ($this->isMember($userId)) {
            return false;
        }

        // No one can join
        if ($this->join_policy == self::JOIN_POLICY_FREE) {
            return true;
        }

        return false;
    }

    /**
     * Returns an array of informations used by search subsystem.
     * Function is defined in interface ISearchable
     *
     * @return Array
     */
    public function getSearchAttributes()
    {
        $attributes = [
            'title' => $this->name,
            'tags' => $this->tags,
            'description' => $this->description
        ];

        $this->trigger(self::EVENT_SEARCH_ADD, new SearchAddEvent($attributes));

        return $attributes;
    }

    /**
     * Checks if page has tags
     *
     * @return boolean has tags set
     */
    public function hasTags()
    {
        return ($this->tags != '');
    }

    /**
     * Returns an array with assigned Tags
     */
    public function getTags()
    {
        // split tags string into individual tags
        return preg_split("/[;,# ]+/", $this->tags);
    }

    /**
     * Archive this Page
     */
    public function archive()
    {
        $this->status = self::STATUS_ARCHIVED;
        $this->save();
    }

    /**
     * Unarchive this Page
     */
    public function unarchive()
    {
        $this->status = self::STATUS_ENABLED;
        $this->save();
    }

    /**
     * Returns wether or not a Page is archived.
     *
     * @return boolean
     * @since 1.2
     */
    public function isArchived()
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    /**
     * Validator for visibility
     *
     * Used in edit scenario to check if the user really can create pages
     * on this visibility.
     *
     * @param type $attribute
     * @param type $params
     */
    public function checkVisibility($attribute, $params)
    {
        $visibility = $this->$attribute;

        // Not changed
        if (!$this->isNewRecord && $visibility == $this->getOldAttribute($attribute)) {
            return;
        }

        if ($visibility == self::VISIBILITY_NONE && !Yii::$app->user->permissionManager->can(new CreatePrivatePage())) {
            $this->addError($attribute, Yii::t('PageModule.base', 'You cannot create private visible pages!'));
        }

        if (($visibility == self::VISIBILITY_REGISTERED_ONLY || $visibility == self::VISIBILITY_ALL) && !Yii::$app->user->permissionManager->can(new CreatePublicPage())) {
            $this->addError($attribute, Yii::t('PageModule.base', 'You cannot create public visible pages!'));
        }
    }

    /**
     * @inheritdoc
     */
    public function getDisplayName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getDisplayNameSub()
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function getProfileImage()
    {
        return new ProfileImage($this, 'default_page');
    }

    /**
     * @inheritdoc
     */
    public function canAccessPrivateContent(User $user = null)
    {
        $user = !$user && !Yii::$app->user->isGuest ? Yii::$app->user->getIdentity() : $user;

        if(!$user) {
            return false;
        }

        if (Yii::$app->getModule('page')->globalAdminCanAccessPrivateContent && $user->isSystemAdmin()) {
            return true;
        }

        return ($this->isMember($user));
    }

    /**
     * @inheritdoc
     */
    public function getWallOut()
    {
        return Wall::widget(['page' => $this]);
    }

    /**
     * Returns all Membership relations with status = STATUS_MEMBER.
     *
     * Be aware that this function will also include disabled users, in order to only include active and visible users use:
     *
     * ```
     * Membership::getPageMembersQuery($this->page)->active()->visible()->count()
     * ```
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMemberships()
    {
        $query = $this->hasMany(Membership::class, ['page_id' => 'id']);
        $query->andWhere(['page_membership.status' => Membership::STATUS_MEMBER]);
        $query->addOrderBy(['page_membership.group_id' => SORT_DESC]);

        return $query;
    }

    public function getMembershipUser($status = null)
    {
        $status = ($status == null) ? Membership::STATUS_MEMBER : $status;
        $query = User::find();
        $query->leftJoin('page_membership', 'page_membership.user_id=user.id AND page_membership.space_id=:space_id AND page_membership.status=:member', ['page_id' => $this->id, 'member' => $status]);
        $query->andWhere('page_membership.space_id IS NOT NULL');
        $query->addOrderBy(['page_membership.group_id' => SORT_DESC]);

        return $query;
    }

    public function getNonMembershipUser()
    {
        $query = User::find();
        $query->leftJoin('page_membership', 'page_membership.user_id=user.id AND page_membership.space_id=:space_id ', ['page_id' => $this->id]);
        $query->andWhere('page_membership.space_id IS NULL');
        $query->orWhere(['!=', 'page_membership.status', Membership::STATUS_MEMBER]);
        $query->addOrderBy(['page_membership.group_id' => SORT_DESC]);

        return $query;
    }

    public function getApplicants()
    {
        $query = $this->hasMany(Membership::class, ['page_id' => 'id']);
        $query->andWhere(['page_membership.status' => Membership::STATUS_APPLICANT]);

        return $query;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwnerUser()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Return user groups
     *
     * @return array user groups
     */
    public function getUserGroups()
    {
        $groups = [
            self::USERGROUP_OWNER => Yii::t('PageModule.base', 'Owner'),
            self::USERGROUP_ADMIN => Yii::t('PageModule.base', 'Administrators'),
            self::USERGROUP_MODERATOR => Yii::t('PageModule.base', 'Moderators'),
            self::USERGROUP_MEMBER => Yii::t('PageModule.base', 'Members'),
            self::USERGROUP_USER => Yii::t('PageModule.base', 'Users')
        ];

        // Add guest groups if enabled
        if (AuthHelper::isGuestAccessEnabled()) {
            $groups[self::USERGROUP_GUEST] = 'Guests';
        }

        return $groups;
    }

    /**
     * @inheritdoc
     */
    public function getUserGroup(User $user = null)
    {
        $user = !$user && !Yii::$app->user->isGuest ? Yii::$app->user->getIdentity() : $user;

        if (!$user) {
            return self::USERGROUP_GUEST;
        }

        /* @var  $membership  Membership */
        $membership = $this->getMembership($user);

        if ($membership && $membership->isMember()) {
            if ($this->isPageOwner($user->id)) {
                return self::USERGROUP_OWNER;
            }
            return $membership->group_id;
        } else {
            return self::USERGROUP_USER;
        }
    }

    /**
     * Returns the default content visibility
     *
     * @return int the default visiblity
     * @see Content
     */
    public function getDefaultContentVisibility()
    {
        if ($this->default_content_visibility === null) {
            $globalDefault = Yii::$app->getModule('page')->settings->get('defaultContentVisibility');
            if ($globalDefault == Content::VISIBILITY_PUBLIC) {
                return Content::VISIBILITY_PUBLIC;
            }
        } elseif ($this->default_content_visibility === 1) {
            return Content::VISIBILITY_PUBLIC;
        }

        return Content::VISIBILITY_PRIVATE;
    }

}
