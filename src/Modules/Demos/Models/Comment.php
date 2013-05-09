<?php

namespace Modules\Demos\Models;

class Comment extends \Rapyd\Model
{

    protected $table = 'demo_comments';
    public $primaryKey = 'comment_id';

    public function article()
    {
        return $this->belongsTo('Article', 'article_id');
    }

}
