<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


class rpd_model_model { 

	public function __construct() {
        
        rpd::connect();
        $this->db = rpd::$db;
        
	}

}
