<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\page\widgets;

use yii\helpers\Url;
use humhub\components\Widget;

/**
 * This widget is used to show a single comment.
 * It will used by the CommentsWidget and the CommentController to show comments.
 */
class Comment extends Widget
{

    /**
     * @var \humhub\modules\comment\models\Comment the comment
     */
    public $comment = null;

    /**
     * @var boolean indicator that comment has just changed
     */
    public $justEdited = false;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $deleteUrl = Url::to(['/page/comment/delete',
            'objectModel' => $this->comment->object_model, 'objectId' => $this->comment->object_id, 'id' => $this->comment->id]);
        $editUrl = Url::to(['/page/comment/edit',
            'objectModel' => $this->comment->object_model, 'objectId' => $this->comment->object_id, 'id' => $this->comment->id]);
        $loadUrl = Url::to(['/page/comment/load',
            'objectModel' => $this->comment->object_model, 'objectId' => $this->comment->object_id, 'id' => $this->comment->id]);

        $updatedAt = null;
        if (!empty($this->comment->updated_at) && $this->comment->created_at != $this->comment->updated_at) {
            $updatedAt = $this->comment->updated_at;
        }

        return $this->render('comment', [
            'comment' => $this->comment,
            'user' => $this->comment->user,
            'justEdited' => $this->justEdited,
            'deleteUrl' => $deleteUrl,
            'editUrl' => $editUrl,
            'loadUrl' => $loadUrl,
            'createdAt' => $this->comment->created_at,
            'updatedAt' => $updatedAt,
            'canEdit' => $this->comment->canEdit(),
            'canDelete' => $this->comment->canDelete(),
        ]);
    }

}
