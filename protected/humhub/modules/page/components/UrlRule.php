<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\components;

use yii\web\UrlRuleInterface;
use yii\base\BaseObject;
use humhub\modules\page\models\Page;

/**
 * Page URL Rule
 *
 * @author luke
 */
class UrlRule extends BaseObject implements UrlRuleInterface
{

    /**
     * @var string default route to page home
     */
    public $defaultRoute = 'page/page';

    /**
     * @var array map with page guid/url pairs
     */
    public static $pageUrlMap = [];

    /**
     * @inheritdoc
     */
    public function createUrl($manager, $route, $params)
    {
        if (isset($params['cguid'])) {
            if ($route == $this->defaultRoute) {
                $route = '';
            }

            $urlPart = static::getUrlByPageGuid($params['cguid']);
            if ($urlPart !== null) {
                $url = "s/" . urlencode($urlPart) . "/" . $route;
                unset($params['cguid']);

                if (!empty($params) && ($query = http_build_query($params)) !== '') {
                    $url .= '?' . $query;
                }
                return $url;
            }
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();
        if (substr($pathInfo, 0, 2) == "s/") {
            $parts = explode('/', $pathInfo, 3);
            if (isset($parts[1])) {
                $page = Page::find()->where(['guid' => $parts[1]])->orWhere(['url' => $parts[1]])->one();
                if ($page !== null) {
                    if (!isset($parts[2]) || $parts[2] == "") {
                        $parts[2] = $this->defaultRoute;
                    }

                    $params = $request->get();
                    $params['cguid'] = $page->guid;

                    return [$parts[2], $params];
                }
            }
        }
        return false;
    }

    /**
     * Gets page url name by given guid
     *
     * @param string $guid
     * @return string|null the page url part
     */
    public static function getUrlByPageGuid($guid)
    {
        if (isset(static::$pageUrlMap[$guid])) {
            return static::$pageUrlMap[$guid];
        }

        $page = Page::findOne(['guid' => $guid]);
        if ($page !== null) {
            static::$pageUrlMap[$page->guid] = ($page->url != '') ? $page->url : $page->guid;
        } else {
            static::$pageUrlMap[$guid] = null;
        }

        return static::$pageUrlMap[$guid];
    }

}
