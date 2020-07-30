<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\widgets;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\content\helpers\ContentContainerHelper;
use humhub\modules\page\models\Page;
use humhub\modules\ui\menu\MenuLink;
use humhub\modules\ui\menu\widgets\LeftNavigation;
use Yii;
use yii\base\Exception;

/**
 * The Main Navigation for a page. It includes the Modules the Stream
 *
 * @author Luke
 * @since 0.5
 */
class Menu extends LeftNavigation
{

    /** @var Page */
    public $page;

    /** @var Page */
    public $id = 'page-main-menu';

    /**
     * @inheritdoc
     */
    public function init()
    {
        if(!$this->page) {
            $this->page = ContentContainerHelper::getCurrent(Page::class);
        }

        if (!$this->page) {
            throw new Exception('Could not instance page menu without page!');
        }

        $this->panelTitle = Yii::t('PageModule.base', '<strong>Page</strong> menu');

        $this->addEntry(new MenuLink([
            'label' => Yii::t('PageModule.base', 'Stream'),
            'url' => $this->page->createUrl('/page/page/home'),
            'icon' => 'fa-bars',
            'sortOrder' => 100,
            'isActive' => MenuLink::isActiveState('page', 'page', ['index', 'home']),
        ]));

        parent::init();
    }

    /**
     * Searches for urls of modules which are activated for the current page
     * and offer an own site over the page menu.
     * The urls are associated with a module label.
     *
     * Returns an array of urls with associated module labes for modules
     */
    public static function getAvailablePages()
    {
        //Initialize the page Menu to check which active modules have an own page
        $entries = (new static())->getEntries(MenuLink::class);
        $result = [];
        foreach ($entries as $entry) {
            /* @var $entry MenuLink */
            $result[$entry->getUrl()] = $entry->getLabel();
        }

        return $result;
    }

    /**
     * Returns page default / homepage
     *
     * @param Page $page
     * @return string|null the url to redirect or null for default home
     */
    public static function getDefaultPageUrl($page)
    {
        $settings = Yii::$app->getModule('page')->settings;

        $indexUrl = $settings->contentContainer($page)->get('indexUrl');
        if ($indexUrl !== null) {
            $pages = static::getAvailablePages();
            if (isset($pages[$indexUrl])) {
                return $indexUrl;
            }

            //Either the module was deactivated or url changed
            $settings->contentContainer($page)->delete('indexUrl');
        }

        return null;
    }

    /**
     * Returns page default / homepage
     *
     * @param $page Page
     * @return string|null the url to redirect or null for default home
     */
    public static function getGuestsDefaultPageUrl($page)
    {
        $settings = Yii::$app->getModule('page')->settings;

        $indexUrl = $settings->contentContainer($page)->get('indexGuestUrl');
        if ($indexUrl !== null) {
            $pages = static::getAvailablePages();
            if (isset($pages[$indexUrl])) {
                return $indexUrl;
            }

            //Either the module was deactivated or url changed
            $settings->contentContainer($page)->delete('indexGuestUrl');
        }

        return null;
    }

}
