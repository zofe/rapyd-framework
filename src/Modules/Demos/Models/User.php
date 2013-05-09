<?php

namespace Modules\Demos\Models;

 
class User extends \Rapyd\Model
{
	protected $table = 'demo_users';
    public $primaryKey = 'user_id';
    
	public function coments(){
		return $this->hasMany('Comment', 'user_id');
	}
		
	public function articles(){
		return $this->hasMany('Articles', 'author_id');
	}
}
