<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\modules\manage\models;

use humhub\modules\user\components\CheckPasswordValidator;
use Yii;
use yii\base\Model;

/**
 * Form Model for Page Deletion
 *
 * @since 0.5
 */
class DeleteForm extends Model
{

    /**
     * @var string the page name to check
     */
    public $pageName;


    /**
     * @var string the page name given by the user
     */
    public $confirmPageName;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['confirmPageName', 'required'],
            ['confirmPageName', 'compare', 'compareValue' => $this->pageName],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'confirmPageName' => Yii::t('PageModule.manage', 'Page name'),
        ];
    }

}
