<?php
/**
 * Created by PhpStorm.
 * User: kemal
 * Date: 26.09.18
 * Time: 16:19
 */

namespace Inensus\Ticket\Services;


use Inensus\Ticket\Trello\Comments;

class CommentService
{

    private $comments;

    public function __construct(Comments $comments)
    {
        $this->comments = $comments;
    }

    public function createComment($cardId, $comment)
    {
        return $this->comments->newComment($cardId, $comment);
    }
}
