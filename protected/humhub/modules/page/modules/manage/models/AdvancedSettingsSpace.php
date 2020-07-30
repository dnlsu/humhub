<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\modules\manage\models;

use humhub\modules\page\models\Page;
use Yii;

/**
 * AdvancedSettingsPage
 *
 * @author Luke
 */
class AdvancedSettingsPage extends Page
{

    /**
     * Contains the form value for indexUrl setting
     * @var string|null
     */
    public $indexUrl = null;

    /**
     * Contains the form value for indexGuestUrl setting
     * @var string|null
     */
    public $indexGuestUrl = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['indexUrl'], 'string'];
        $rules[] = [['indexGuestUrl'], 'string'];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['edit'][] = 'indexUrl';
        $scenarios['edit'][] = 'indexGuestUrl';

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['indexUrl'] = Yii::t('PageModule.base', 'Homepage');
        $labels['indexGuestUrl'] = Yii::t('PageModule.base', 'Homepage (Guests)');

        return $labels;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($this->indexUrl != null) {
            Yii::$app->getModule('page')->settings->contentContainer($this)->set('indexUrl', $this->indexUrl);
        } else {
            //Remove entry from db
            Yii::$app->getModule('page')->settings->contentContainer($this)->delete('indexUrl');
        }

        if ($this->indexGuestUrl != null) {
            Yii::$app->getModule('page')->settings->contentContainer($this)->set('indexGuestUrl', $this->indexGuestUrl);
        } else {
            //Remove entry from db
            Yii::$app->getModule('page')->settings->contentContainer($this)->delete('indexGuestUrl');
        }

        return parent::afterSave($insert, $changedAttributes);
    }

}
