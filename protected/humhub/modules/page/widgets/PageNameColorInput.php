<?php

namespace humhub\modules\page\widgets;

use humhub\components\Widget;

class PageNameColorInput extends Widget
{

    public $model;
    public $form;

    /**
     * Displays / Run the Widgets
     */
    public function run()
    {
        return $this->render('pageNameColorInput', [
                    'model' => $this->model,
                    'form' => $this->form
        ]);
    }
}
