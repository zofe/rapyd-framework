<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');



class rpd_sess_helper {


	public static function get_persistence($url=null)
	{
		$self = url_helper::remove_all(null, $url);
		$session  = @$_SESSION['rapyd'];

		if ($session===FALSE)
			return array();
		return (isset($session[$self])) ? $session[$self] : array();

	}

    // --------------------------------------------------------------------

	public static function save_persistence($url=null)
	{
		$self = url_helper::remove_all(null,$url);
		$page = self::get_persistence();

		if (count($_POST)<1)
		{
			if ( isset($page["back_post"]) )
			{
				if (url_helper::get_url() != $page["back_url"] && rpd::$params == array())
					header('Location: '.$page["back_url"]);
				$_POST = $page["back_post"];
			} elseif (url_helper::value('search')) {
                            
                                $key_value = explode(";", url_helper::value('search'));    
                                foreach($key_value as $kv)
                                {
                                    list($k, $v) = explode(":", $kv);
                                    $_POST[$k] = $v; 
                                }
                                
                        }
		} else {
			$page["back_post"]= $_POST;
		}

		$page["back_url"]= rawurldecode(url_helper::get_url());
		$_SESSION['rapyd'][$self] = $page;
	}

	// --------------------------------------------------------------------

	public static function clear_persistence()
	{
		$self = url_helper::remove_all();
		unset($_SESSION['rapyd'][$self]);
	}


}
