<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


class rpd_date_helper {

	protected $iso_format = array('yyyy-mm-dd');
	
	//map for predefined date formats, http://php.net/manual/en/function.date.php

	
	
	public static function iso2human($date)
    {
        if ((strpos($date,"0000-00-00")!==false) || ($date==""))
            return "";

		return self::from_format_to_format($date, 'Y-m-d', rpd::get_lang('dateformat'));
        //return preg_replace('#^(\d{4})-(\d{2})-(\d{2})( \d{2}:\d{2}:\d{2})?#', '$3/$2/$1$4', $date);
    }

    // --------------------------------------------------------------------

    public static function human2iso($date)
    {
        //return preg_replace('#^(\d{2})/(\d{2})/(\d{4})( \d{2}:\d{2}:\d{2})?#', '$3-$2-$1$4', $date);
		return self::from_format_to_format($date, rpd::get_lang('dateformat'), 'Y-m-d');
    }

    public static function from_format_to_format($date, $from_dateformat, $to_dateformat)
	{
		$dt = array();
		$stadard = array('Y', 'm', "d");
		$semplified = array("yyyy", 'mm', "dd");
		$from_dateformat = str_replace($stadard, $semplified, $from_dateformat);
		$to_dateformat = str_replace($stadard, $semplified, $to_dateformat);

		$mask = array(
			'y'=>'yyyy',
			'm'=>'mm',
			'd'=>'dd'
		);
		$format = preg_split('//', $from_dateformat, -1, PREG_SPLIT_NO_EMPTY);  
		$date = preg_split('//', $date, -1, PREG_SPLIT_NO_EMPTY);  
		foreach ($date as $k => $v) {
			if (isset($from_dateformat[$k], $mask[$from_dateformat[$k]]))
				@$dt[$mask[$from_dateformat[$k]]] .= $v;
		}
		

		$result = str_replace(array_keys($dt), array_values($dt), $to_dateformat);
		return $result;
	}
	

    public static function ago($date,
                               $singular = array('year', 'month', 'day', 'hour', 'mitune', 'second'),
                               $plurals = array('years', 'months', 'days', 'hours', 'minutes', 'seconds'), $ago = 'ago')
    {
        $date = getdate(strtotime($date));
        $current = getdate();
        $p = array('year', 'mon', 'mday', 'hours', 'minutes', 'seconds');
        $factor = array(0, 12, 30, 24, 60, 60);

        for ($i = 0; $i < 6; $i++) {
            if ($i > 0) {
                $current[$p[$i]] += $current[$p[$i - 1]] * $factor[$i];
                $date[$p[$i]] += $date[$p[$i - 1]] * $factor[$i];
            }
            if ($current[$p[$i]] - $date[$p[$i]] > 1) {
                $value = $current[$p[$i]] - $date[$p[$i]];
                return $value . ' ' . (($value != 1) ? $plurals[$i] :  $singular[$i]) . ' ' . $ago;
            }
        }

        return '';
    }


}
