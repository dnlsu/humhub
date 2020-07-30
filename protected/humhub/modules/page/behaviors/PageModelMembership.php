<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\behaviors;

use humhub\modules\admin\permissions\ManageSpaces;
use humhub\modules\page\activities\MemberAdded;
use humhub\modules\page\activities\MemberRemoved;
use humhub\modules\page\MemberEvent;
use humhub\modules\page\models\Membership;
use humhub\modules\page\models\Page;
use humhub\modules\page\notifications\ApprovalRequest;
use humhub\modules\page\notifications\ApprovalRequestAccepted;
use humhub\modules\page\notifications\ApprovalRequestDeclined;
use humhub\modules\page\notifications\Invite as InviteNotification;
use humhub\modules\page\notifications\InviteAccepted;
use humhub\modules\page\notifications\InviteDeclined;
use humhub\modules\page\notifications\InviteRevoked;
use humhub\modules\user\components\ActiveQueryUser;
use humhub\modules\user\models\Invite;
use humhub\modules\user\models\User;
use Yii;
use yii\base\Behavior;
use yii\base\Exception;
use yii\validators\EmailValidator;

/**
 * PageModelMemberBehavior bundles all membership related methods of the Page model.
 *
 * @property-read Page $owner
 * @author Lucas Bartholemy <lucas@bartholemy.com>
 */
class PageModelMembership extends Behavior
{

    private $_pageOwner = null;

    /**
     * Checks if given userId is Member of this Page.
     *
     * @param integer $userId
     * @return boolean
     */
    public function isMember($userId = '')
    {
        // Take current userid if none is given
        if ($userId == '' && !Yii::$app->user->isGuest) {
            $userId = Yii::$app->user->id;
        } elseif ($userId == '' && Yii::$app->user->isGuest) {
            return false;
        }

        $membership = $this->getMembership($userId);

        if ($membership != null && $membership->status == Membership::STATUS_MEMBER) {
            return true;
        }

        return false;
    }

    /**
     * Checks if a given Userid is allowed to leave this page.
     * A User is allowed to leave, if the can_cancel_membership flag in the page_membership table is 1. If it is 2, the decision is delegated to the page.
     *
     * @param number $userId , if empty hte currently logged in user is taken.
     * @return bool
     */
    public function canLeave($userId = '')
    {
        // Take current userid if none is given
        if ($userId == '') {
            $userId = Yii::$app->user->id;
        }

        $membership = $this->getMembership($userId);

        if ($membership != null && !empty($membership->can_cancel_membership)) {
            return $membership->can_cancel_membership === 1 || ($membership->can_cancel_membership === 2 && !empty($this->owner->members_can_leave));
        }

        return false;
    }

    /**
     * Checks if given Userid is Admin of this Page or has the permission to manage pages.
     *
     * If no UserId is given, current UserId will be used
     *
     * @param User|integer|null $user User instance or userId
     * @return boolean
     */
    public function isAdmin($user = null)
    {
        $userId = ($user instanceof User) ? $user->id : $user;

        if (empty($userId) && Yii::$app->user->can(new ManageSpaces())) {
            return true;
        }

        if (!$userId) {
            $userId = Yii::$app->user->id;
        }

        if ($this->isPageOwner($userId)) {
            return true;
        }

        $membership = $this->getMembership($userId);

        return ($membership && $membership->group_id == Page::USERGROUP_ADMIN && $membership->status == Membership::STATUS_MEMBER);
    }

    /**
     * Sets Owner for this workspace
     *
     * @param User|integer|null $userId
     * @return boolean
     */
    public function setPageOwner($user = null)
    {
        $userId = ($user instanceof User) ? $user->id : $user;

        if ($userId instanceof User) {
            $userId = $userId->id;
        } elseif (!$userId || $userId == 0) {
            $userId = Yii::$app->user->id;
        }

        $this->setAdmin($userId);

        $this->owner->created_by = $userId;
        $this->owner->update(false, ['created_by']);

        $this->_pageOwner = null;

        return true;
    }

    /**
     * Gets Owner for this workspace
     *
     * @return User
     */
    public function getPageOwner()
    {
        if ($this->_pageOwner != null) {
            return $this->_pageOwner;
        }

        $this->_pageOwner = User::findOne(['id' => $this->owner->created_by]);

        return $this->_pageOwner;
    }

    /**
     * @return bool checks if the current user is allowed to delete this page
     * @since 1.3
     */
    public function canDelete()
    {
        return Yii::$app->user->isAdmin() || $this->isPageOwner();
    }

    /**
     * Is given User owner of this Page
     * @param User|int|null $userId
     * @return bool
     */
    public function isPageOwner($userId = null)
    {
        if (empty($userId) && Yii::$app->user->isGuest) {
            return false;
        } elseif ($userId instanceof User) {
            $userId = $userId->id;
        } elseif (empty($userId)) {
            $userId = Yii::$app->user->id;
        }

        return $this->owner->created_by == $userId;
    }

    /**
     * Sets Owner for this workspace
     *
     * @param integer $userId
     * @return boolean
     */
    public function setAdmin($userId = null)
    {
        if ($userId instanceof User) {
            $userId = $userId->id;
        } elseif (!$userId || $userId == 0) {
            $userId = Yii::$app->user->id;
        }

        $membership = $this->getMembership($userId);
        if ($membership != null) {
            $membership->group_id = Page::USERGROUP_ADMIN;
            $membership->save();
            return true;
        }

        return false;
    }

    /**
     * Returns the PageMembership Record for this Page
     *
     * If none Record is found, null is given
     *
     * @return Membership the membership
     */
    public function getMembership($userId = null)
    {
        if ($userId instanceof User) {
            $userId = $userId->id;
        } elseif (!$userId || $userId == '') {
            $userId = Yii::$app->user->id;
        }

        return Membership::findOne(['user_id' => $userId, 'page_id' => $this->owner->id]);
    }

    /**
     * Invites a not registered member to this page
     *
     * @param string $email
     * @param integer $originatorUserId
     */
    public function inviteMemberByEMail($email, $originatorUserId)
    {
        // Invalid E-Mail
        $validator = new EmailValidator;
        if (!$validator->validate($email)) {
            return false;
        }

        // User already registered
        $user = User::findOne(['email' => $email]);
        if ($user != null) {
            return false;
        }

        $userInvite = Invite::findOne(['email' => $email]);
        // No invite yet
        if ($userInvite == null) {
            // Invite EXTERNAL user
            $userInvite = new Invite();
            $userInvite->email = $email;
            $userInvite->source = Invite::SOURCE_INVITE;
            $userInvite->user_originator_id = $originatorUserId;
            $userInvite->page_invite_id = $this->owner->id;
            // There is a pending registration
            // Steal it and send mail again
            // Unfortunately there are no multiple workspace invites supported
            // So we take the last one
        } else {
            $userInvite->user_originator_id = $originatorUserId;
            $userInvite->page_invite_id = $this->owner->id;
        }

        if ($userInvite->validate() && $userInvite->save()) {
            $userInvite->sendInviteMail();
            return true;
        }

        return false;
    }

    /**
     * Requests Membership
     *
     * @param integer $userId
     * @param string $message
     */
    public function requestMembership($userId, $message = '')
    {
        $user = ($userId instanceof User) ? $userId : User::findOne(['id' => $userId]);

        // Add Membership
        $membership = new Membership([
            'page_id' => $this->owner->id,
            'user_id' => $user->id,
            'status' => Membership::STATUS_APPLICANT,
            'group_id' => Page::USERGROUP_MEMBER,
            'request_message' => $message
        ]);

        $membership->save();

        ApprovalRequest::instance()->from($user)->about($this->owner)->withMessage($message)->sendBulk($this->getAdminsQuery());
    }

    /**
     * Returns the admins of the page
     *
     * @return User[] the admin users of the page
     */
    public function getAdmins()
    {
        return $this->getAdminsQuery()->all();
    }

    /**
     * Returns user query for admins of the page
     *
     * @since 1.3
     * @return ActiveQueryUser
     */
    public function getAdminsQuery()
    {
        $query = Membership::getPageMembersQuery($this->owner);
        $query->andWhere(['page_membership.group_id' => Page::USERGROUP_ADMIN]);

        return $query;
    }

    /**
     * Invites a registered user to this page
     *
     * If user is already invited, retrigger invitation.
     * If user is applicant approve it.
     *
     * @param integer $userId
     * @param integer $originatorId
     * @param bool $sendInviteNotification
     */
    public function inviteMember($userId, $originatorId, $sendInviteNotification = true)
    {
        $membership = $this->getMembership($userId);

        if ($membership != null) {
            switch ($membership->status) {
                case Membership::STATUS_APPLICANT:
                    // If user is an applicant of this page add user and return.
                    $this->addMember(Yii::$app->user->id);
                case Membership::STATUS_MEMBER:
                    // If user is already a member just ignore the invitation.
                    return;
                case Membership::STATUS_INVITED:
                    // If user is already invited, remove old invite notification and retrigger
                    $oldNotification = new InviteNotification(['source' => $this->owner]);
                    $oldNotification->delete(User::findOne(['id' => $userId]));
                    break;
            }
        } else {
            $membership = new Membership([
                'page_id' => $this->owner->id,
                'user_id' => $userId,
                'status' => Membership::STATUS_INVITED,
                'group_id' => Page::USERGROUP_MEMBER
            ]);
        }

        // Update or set originator
        $membership->originator_user_id = $originatorId;

        if (!$membership->save()) {
            throw new Exception('Could not save membership!' . print_r($membership->getErrors(), 1));
        }

        if ($sendInviteNotification) {
            $this->sendInviteNotification($userId, $originatorId);
        }
    }

    /**
     * Sends an Invite Notification to the given user.
     *
     * @param integer $userId
     * @param integer $originatorId
     */
    protected function sendInviteNotification($userId, $originatorId)
    {
        $notification = new InviteNotification([
            'source' => $this->owner,
            'originator' => User::findOne(['id' => $originatorId])
        ]);

        $notification->send(User::findOne(['id' => $userId]));
    }

    /**
     * Adds an member to this page.
     *
     * This can happens after an clicking "Request Membership" Link
     * after Approval or accepting an invite.
     *
     * @param integer $userId
     * @param integer $canLeave 0: user cannot cancel membership | 1: can cancel membership | 2: depending on page flag members_can_leave
     * @param bool $silent add member without any notifications
     * @return bool
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function addMember($userId, $canLeave = 1, $silent = false)
    {
        $user = User::findOne(['id' => $userId]);
        $membership = $this->getMembership($userId);

        if ($membership == null) {
            // Add Membership
            $membership = new Membership([
                'page_id' => $this->owner->id,
                'user_id' => $userId,
                'status' => Membership::STATUS_MEMBER,
                'group_id' => Page::USERGROUP_MEMBER,
                'can_cancel_membership' => $canLeave
            ]);

            $userInvite = Invite::findOne(['email' => $user->email]);

            if ($userInvite !== null && $userInvite->source == Invite::SOURCE_INVITE && !$silent) {
                InviteAccepted::instance()->from($user)->about($this->owner)
                    ->send(User::findOne(['id' => $userInvite->user_originator_id]));
            }
        } else {
            // User is already member
            if ($membership->status == Membership::STATUS_MEMBER) {
                return true;
            }

            // User requested membership
            if ($membership->status == Membership::STATUS_APPLICANT && !$silent) {
                ApprovalRequestAccepted::instance()
                    ->from(Yii::$app->user->getIdentity())->about($this->owner)->send($user);
            }

            // User was invited
            if ($membership->status == Membership::STATUS_INVITED && !$silent) {
                InviteAccepted::instance()->from($user)->about($this->owner)
                    ->send(User::findOne(['id' => $membership->originator_user_id]));
            }

            // Update Membership
            $membership->status = Membership::STATUS_MEMBER;
        }

        $membership->save();

        MemberEvent::trigger(Membership::class, Membership::EVENT_MEMBER_ADDED, new MemberEvent([
            'page' => $this->owner, 'user' => $user
        ]));

        if (!$silent) {
            // Create Activity
            MemberAdded::instance()->from($user)->about($this->owner)->save();
        }

        // Members can't also follow the page
        $this->owner->unfollow($userId);

        // Delete invite notification for this user
        InviteNotification::instance()->about($this->owner)->delete($user);

        // Delete pending approval request notifications for this user
        ApprovalRequest::instance()->from($user)->about($this->owner)->delete();
    }

    /**
     * Remove Membership
     *
     * @param integer $userId of User to Remove
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * @throws \Throwable
     */
    public function removeMember($userId = '')
    {
        if ($userId == '') {
            $userId = Yii::$app->user->id;
        }

        $user = User::findOne(['id' => $userId]);
        $membership = $this->getMembership($userId);

        if (!$membership) {
            return true;
        }

        if ($this->isPageOwner($userId)) {
            return false;
        }

        Membership::getDb()->transaction(function($db) use ($membership, $user) {
            foreach (Membership::findAll(['user_id' => $user->id, 'page_id' => $this->owner->id]) as $obsoleteMembership) {
                $obsoleteMembership->delete();
            }

            $this->handleRemoveMembershipEvent($membership, $user);
        });
    }

    /**
     * Responsible for event,activity and notification handling in case of a page membership removal.
     *
     * @param Membership $membership
     * @param User $user
     * @throws Exception
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    private function handleRemoveMembershipEvent(Membership $membership, User $user)
    {
        // Get rid of old notifications
        ApprovalRequest::instance()->from($user)->about($this->owner)->delete();
        InviteNotification::instance()->about($this->owner)->delete($user);

        switch ($membership->status) {
            case Membership::STATUS_MEMBER:
                return $this->handleCancelMemberEvent($user);
            case Membership::STATUS_INVITED:
                return $this->handleCancelInvitationEvent($membership, $user);
            case Membership::STATUS_APPLICANT:
                return $this->handleCancelApplicantEvent($membership, $user);
        }
    }

    /**
     * @param User $user
     * @throws Exception
     */
    private function handleCancelMemberEvent(User $user)
    {
        MemberRemoved::instance()->about($this->owner)->from($user)->create();
        MemberEvent::trigger(Membership::class, Membership::EVENT_MEMBER_REMOVED,
            new MemberEvent(['page' => $this->owner, 'user' => $user]));
    }

    /**
     * Handles the cancellation of an invitation. An invitation can be declined by the invited user or canceled by a
     * page admin.
     *
     * @param Membership $membership
     * @param User $user
     * @throws \yii\base\InvalidConfigException
     */
    private function handleCancelInvitationEvent(Membership $membership, User $user)
    {
        if ($membership->originator && $membership->isCurrentUser()) {
            InviteDeclined::instance()->from(Yii::$app->user->identity)->about($this->owner)->send($membership->originator);
        } else if(Yii::$app->user->identity) {
            InviteRevoked::instance()->from(Yii::$app->user->identity)->about($this->owner)->send($user);
        }
    }

    /**
     * Handles the cancellation of an page application. An application can be canceled by the applicant himself or
     * declined by an page admin.
     *
     * @param Membership $membership
     * @param User $user
     * @throws \yii\base\InvalidConfigException
     */
    private function handleCancelApplicantEvent(Membership $membership, User $user)
    {
        // Only send a declined notification if the user did not cancel the request himself.
        if(Yii::$app->user->identity && !$membership->isCurrentUser()) {
            ApprovalRequestDeclined::instance()->from(Yii::$app->user->identity)->about($this->owner)->send($user);
        }
    }

}
