<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\components;

use Yii;
use yii\validators\Validator;
use URLify;
use humhub\modules\page\models\Page;

/**
 * UrlValidator for page urls
 *
 * @since 1.1
 * @author Luke
 */
class UrlValidator extends Validator
{

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if (mb_strtolower($value) != URLify::filter($value, 45)) {
            $this->addError($model, $attribute, Yii::t('PageModule.manage', 'The url contains illegal characters!'));
        }
    }

    /**
     * Generate a unique page url
     *
     * @param string $name
     * @return string a unique page url
     */
    public static function autogenerateUniquePageUrl($name)
    {
        $maxUrlLength = 45;

        $url = URLify::filter($name, $maxUrlLength - 4);

        // Get a list of all similar page urls
        $existingPageUrls = [];
        foreach (Page::find()->where(['LIKE', 'url', $url . '%', false])->all() as $page) {
            $existingPageUrls[] = $page->url;
        }

        // Url is free
        if (!in_array($url, $existingPageUrls)) {
            return $url;
        }

        // Add number to taken url
        for ($i = 0, $existingPageUrlsCount = count($existingPageUrls); $i <= $existingPageUrlsCount; $i++) {
            $tryUrl = $url . ($i + 2);
            if (!in_array($tryUrl, $existingPageUrls)) {
                return $tryUrl;
            }
        }

        // Shouldn't never happen - failed
        return "";
    }

}
