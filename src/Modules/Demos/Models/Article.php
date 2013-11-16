<?php

namespace Modules\Demos\Models;

class Article extends \Rapyd\Model
{

    protected $table = 'demo_articles';
    public $primaryKey = 'article_id';

    public function comments()
    {
        return $this->hasMany('Modules\Demos\Models\Comment', 'article_id');
    }

    public function author()
    {
        return $this->belongsTo('Modules\Demos\Models\User', 'author_id');
    }
    
    public function getPublicAttribute($value)
    {
        return (bool)$value;
    }
    
}
