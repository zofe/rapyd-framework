<?php

namespace Modules\Demos\Models;

class Comment extends \Rapyd\Model
{

    protected $table = 'demo_comments';
    public $primaryKey = 'comment_id';

    public function article()
    {
        return $this->belongsTo('Modules\Demos\Models\Article', 'article_id');
    }

    public function user()
    {
        return $this->belongsTo('Modules\Demos\Models\User', 'user_id');
    }
}
