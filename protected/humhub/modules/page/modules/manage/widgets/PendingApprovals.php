<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\modules\manage\widgets;

use yii\base\Widget;

/**
 * PendingApprovals show open member approvals to admin in sidebar
 *
 * @author Luke
 * @since 0.21
 */
class PendingApprovals extends Widget
{

    /**
     * @var \humhub\modules\page\models\Page
     */
    public $page;

    /**
     * @var int number of applicants to show
     */
    public $maxApplicants = 15;

    /**
     * @inheritdoc
     */
    public function run()
    {
        // Only visible for admins
        if (!$this->page->isAdmin()) {
            return;
        }

        $applicants = $this->page->getApplicants()->limit($this->maxApplicants)->all();

        // No applicants
        if (count($applicants) === 0) {
            return;
        }

        return $this->render('pendingApprovals', ['applicants' => $applicants, 'page' => $this->page]);
    }

}
