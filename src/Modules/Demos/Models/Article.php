<?php

namespace Modules\Demos\Models;

class Article extends \Rapyd\Model
{

    protected $table = 'demo_articles';
    public $primaryKey = 'article_id';

    public function coments()
    {
        return $this->hasMany('Comment', 'article_id');
    }

    public function author()
    {
        return $this->belongsTo('User', 'author_id');
    }

    public function setPublicAttribute($value)
    {
        $this->attributes['first_name'] = $public ? 1 : 0;
    }

    public function getPublicAttribute($value)
    {
        return (bool)$value;
    }
    
}
