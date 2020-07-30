<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2019 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */


namespace humhub\modules\page\components;

use humhub\modules\page\models\Membership;
use humhub\modules\page\models\Page;
use humhub\modules\user\components\ActiveQueryUser;
use humhub\modules\user\models\User;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Expression;


/**
 * ActiveQueryPage is used to query Page records.
 *
 * @since 1.4
 */
class ActiveQueryPage extends ActiveQuery
{
    const MAX_SEARCH_NEEDLES = 5;

    /**
     * Only returns pages which are visible for this user
     *
     * @param User|null $user
     * @return ActiveQueryPage the query
     */
    public function visible(User $user = null)
    {
        if ($user === null && !Yii::$app->user->isGuest) {
            try {
                $user = Yii::$app->user->getIdentity();
            } catch (\Throwable $e) {
                Yii::error($e, 'page');
            }
        }

        if ($user !== null) {

            $pageIds = array_map(function (Membership $membership) {
                return $membership->page_id;
            }, Membership::findAll(['user_id' => $user->id]));

            $this->andWhere(['OR',
                ['!=', 'page.visibility', Page::VISIBILITY_NONE],
                ['IN', 'page.id', $pageIds]
            ]);
        } else {
            $this->andWhere(['page.visibility' => Page::VISIBILITY_ALL]);
        }
        return $this;
    }

    /**
     * Performs a page full text search
     *
     * @param string|array $keywords
     *
     * @return ActiveQueryPage the query
     */
    public function search($keywords)
    {
        if (empty($keywords)) {
            return $this;
        }

        if (!is_array($keywords)) {
            $keywords = explode(' ', $keywords);
        }

        foreach (array_slice($keywords, 0, static::MAX_SEARCH_NEEDLES) as $keyword) {
            $conditions = [];
            foreach (['page.name', 'page.description', 'page.tags'] as $field) {
                $conditions[] = ['LIKE', $field, $keyword];
            }
            $this->andWhere(array_merge(['OR'], $conditions));
        }

        return $this;
    }
}
