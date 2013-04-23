<?php
class rpd_controller_controller extends rpd {

	function __construct()
	{

	}
	
	function __get($name)
	{
		if (in_array($name, array('db','qs','uri','uri_string','ruri_string','auth')))
			if (isset(self::$$name))
					return self::$$name;
	}

    function index()
    {
        echo 'welcome on rapyd framework';
    }
}
?>
