<?php

namespace Modules\Demos\Models;

class User extends \Rapyd\Model
{

    protected $table = 'demo_users';
    public $primaryKey = 'user_id';

    public function coments()
    {
        return $this->hasMany('Modules\Demos\Models\Comment', 'user_id');
    }

    public function articles()
    {
        return $this->hasMany('Modules\Demos\Models\Article', 'author_id');
    }

}
