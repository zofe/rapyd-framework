<?php


class rpd_str_library {
	
	/**
	 * plural helper pluralize string
	 * 
	 * @param string $str
	 * @return string plural version of $str
	 */
	protected static function plural($str)
	{
		if (preg_match('/[sxz]$/', $str) OR preg_match('/[^aeioudgkprt]h$/', $str))
		{
			$str .= 'es';
		} elseif (preg_match('/[^aeiou]y$/', $str))
		{
			$str = substr_replace($str, 'ies', -1);
		} else
		{
			$str .= 's';
		}
		return $str;
	}
	
}

