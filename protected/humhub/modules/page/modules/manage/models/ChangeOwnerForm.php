<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\modules\manage\models;

use humhub\modules\page\models\Page;
use Yii;
use yii\base\Model;
use humhub\modules\page\models\Membership;

/**
 * Form Model for page owner change
 *
 * @since 0.5
 */
class ChangeOwnerForm extends Model
{

    /**
     * @var \humhub\modules\page\models\Page
     */
    public $page;

    /**
     * @var string owner id
     */
    public $ownerId;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['ownerId', 'required'],
            ['ownerId', 'in', 'range' => array_keys($this->getNewOwnerArray())]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ownerId' => Yii::t('PageModule.manage', 'Page owner'),
        ];
    }

    /**
     * Returns an array of all possible page owners
     *
     * @return array containing the user id as key and display name as value
     */
    public function getNewOwnerArray()
    {
        $possibleOwners = [];

        $query = Membership::find()->joinWith(['user', 'user.profile'])->andWhere(['page_membership.group_id' => Page::USERGROUP_ADMIN, 'page_membership.space_id' => $this->page->id]);
        foreach ($query->all() as $membership) {
            $possibleOwners[$membership->user->id] = $membership->user->displayName;
        }

        return $possibleOwners;
    }

}
