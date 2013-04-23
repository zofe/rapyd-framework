<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


class rpd_validation_library {

	var $error_string	= '';
	var $system_error       = '';
	var $_error_array	= array();
	var $_rules		= array();
	var $_fields		= array();

	var $_current_field  	= '';
	var $_safe_form_data 	= FALSE;
	var $_error_prefix	= '-- ';
	var $_error_suffix	= '';

  var $_error_messages	= array();

	// --------------------------------------------------------------------


   function __construct()
	 {
		 $this->_error_messages = rpd::lang('val.*');
	 }

	/**
	 * Set Fields
	 *
	 * This function takes an array of field names as input
	 * and generates class variables with the same name, which will
	 * either be blank or contain the $_POST value corresponding to it
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	function set_fields($data = '', $field = '')
	{
		if ($data == '')
		{
			if (count($this->_fields) == 0)
			{
				return FALSE;
			}
		}
		else
		{
			if ( ! is_array($data))
			{
				$data = array($data => $field);
			}

			if (count($data) > 0)
			{
				$this->_fields = $data;
			}
		}

		foreach($this->_fields as $key => $val)
		{

			$this->$key = ( ! isset($_POST[$key]) OR is_array($_POST[$key])) ? '' : $this->prep_for_form($_POST[$key]);

			$error = $key.'_error';
			if ( ! isset($this->$error))
			{
				$this->$error = '';
			}

		}

	}

	// --------------------------------------------------------------------

	/**
	 * Set Rules
	 *
	 * This function takes an array of field names and validation
	 * rules as input ad simply stores is for use later.
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	void
	 */
	function set_rules($data, $rules = '')
	{
		if ( ! is_array($data))
		{
			if ($rules == '')
				return;

			$data[$data] = $rules;
		}

		foreach ($data as $key => $val)
		{
			$this->_rules[$key] = $val;
		}
	}

	// --------------------------------------------------------------------

	function set_message($lang, $val = '')
	{
		if ( ! is_array($lang))
		{
			$lang = array($lang => $val);
		}

		$this->_error_messages = array_merge($this->_error_messages, $lang);
	}

	// --------------------------------------------------------------------


	function set_error_delimiters($prefix = '<p>', $suffix = '</p>')
	{
		$this->_error_prefix = $prefix;
		$this->_error_suffix = $suffix;
	}

	// --------------------------------------------------------------------

	function run()
	{


		if (count($_POST) == 0 OR count($this->_rules) == 0)
		{
			return FALSE;
		}

		// Cycle through the rules and test for errors
		foreach ($this->_rules as $field => $rules)
		{
			//Explode out the rules!
			$ex = explode('|', $rules);

			// Is the field required?  If not, if the field is blank  we'll move on to the next text
			if ( ! in_array('required', $ex, TRUE) AND strpos($rules, 'callback_') === FALSE)
			{
				if ( ! isset($_POST[$field]) OR $_POST[$field] == '')
				{
					continue;
				}
			}

			if ( ! isset($_POST[$field]))
			{
				if (in_array('isset', $ex, TRUE) OR in_array('required', $ex))
				{
					$line = $this->_error_messages['isset'];
					$field = ( ! isset($this->_fields[$field])) ? $field : $this->_fields[$field];
					$this->_error_array[] = sprintf($line, $field);
				}

				continue;
			}


			$this->_current_field = $field;


			foreach ($ex As $rule)
			{
				///////da rimuovere
				// Is the rule a callback?
				$callback = FALSE;
				if (substr($rule, 0, 9) == 'callback_')
				{
					$rule = substr($rule, 9);
					$callback = TRUE;
				}

				// Strip the parameter (if exists) from the rule
				// Rules can contain a parameter: max_length[5]
				/*$param = FALSE;
				if (preg_match("/(.*?)\[(.*?)\]/", $rule, $match))
				{
					$rule	= $match[1];
					$param	= $match[2];
				}*/
			// Handle params
        $params = FALSE;
        if (preg_match('/([^\[]*+)\[(.+)\]/', $rule, $match))
        {
          $rule   = $match[1];
          $params = explode(',', $match[2]);
        }


				//applica funzione  php/custom di formattazione
				if (function_exists($rule))
				{
					$_POST[$field] = $rule($_POST[$field]);
					$this->$field = $_POST[$field];
					continue;
				}
		
					
				if ($callback === TRUE)
				{
					
					if ( ! method_exists(rpd::$controller, $rule))
					{ 		
						continue;
					}
					
					$result = rpd::$controller->$rule($_POST[$field], $params);
					
					//if you return a value and not a boolean, it's intended as "formatting function" for current field.
					if (!is_bool($result))
					{
						$_POST[$field] = $result;
						$this->$field = $result;
						continue;
					}
					
					// If the field isn't required and we just processed a callback we'll move on...
					if ( ! in_array('required', $ex, TRUE) AND $result !== FALSE)
					{
						continue 2;
					}
					
				}
				else
				{
					$result = $this->$rule($_POST[$field], $params);
				}

				// Did the rule test negatively?  If so, grab the error.
				if ($result === FALSE)
				{

					$line = $this->_error_messages[$rule];

					// Build the error message
					$mfield = ( ! isset($this->_fields[$field])) ? $field : $this->_fields[$field];
					//$mparam = ( ! isset($this->_fields[$param])) ? $param : $this->_fields[$param];
					$mparams = (array)$mfield;

					if ($params)
					{
						foreach ($params as $param)
						{
							$mparams[] = (!isset($this->_fields[$param])) ? $param : $this->_fields[$param];
						}
					}
					//var_dump($line);
					$message = vsprintf($line, $mparams);

					// Set the error variable.  Example: $this->username_error
					$error = $field.'_error';
					$this->$error = $this->_error_prefix.$message.$this->_error_suffix;

					// Add the error to the error array
					$this->_error_array[] = $message;
					continue 2;
				}
			}
		}

		$total_errors = count($this->_error_array);

		if ($total_errors > 0)
		{
			$this->_safe_form_data = TRUE;
		}

		$this->set_fields();

		// Did we end up with any errors?
		if ($total_errors == 0)
		{
			return TRUE;
		}

		// Generate the error string
		foreach ($this->_error_array as $val)
		{
			$this->error_string .= $this->_error_prefix.$val.$this->_error_suffix."\n";
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Required
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function required($str)
	{
		if ( ! is_array($str))
		{
			return (trim($str) == '') ? FALSE : TRUE;
		}
		else
		{
			return ( ! empty($str));
		}
	}

	// --------------------------------------------------------------------

	function matches($str, $field)
	{
		if ( ! isset($_POST[$field]))
		{
			return FALSE;
		}
		return ($str !== $_POST[$field]) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	function min_length($str, $val)
	{
    $val = $val[0];
		if ( ! is_numeric($val))
		{
			return FALSE;
		}

		return (strlen($str) < $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	function max_length($str, $val)
	{
    $val = $val[0];
		if ( ! is_numeric($val))
		{
			return FALSE;
		}

		return (strlen($str) > $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	function exact_length($str, $val)
	{
    $val = $val[0];
		if ( ! is_numeric($val))
		{
			return FALSE;
		}

		return (strlen($str) != $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	function valid_email($str)
	{
		return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	function valid_ip($ip)
	{
		return ( ! preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	function alpha($str)
	{
		return ( ! preg_match("/^([-a-z])+$/i", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	function alpha_numeric($str)
	{
		return ( ! preg_match("/^([-a-z0-9])+$/i", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	function alpha_dash($str)
	{
		return ( ! preg_match("/^([-a-z0-9_-])+$/i", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	function numeric($str)
	{
		return ( ! ereg("^[0-9\.]+$", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------


	function in_range($num, $ranges)
	{

		if (is_array($ranges) AND ! empty($ranges))
		{
			// Number is always an integer
      //if (!is_numeric($num)) return FALSE;

			$num = (float) $num;

      list($low, $high) = $ranges;

      if ($low == 'FALSE' AND $num <= (float) $high)
      {
        return TRUE;
      }
      elseif ($high == 'FALSE' AND $num >= (float) $low)
      {
        return TRUE;
      }
      elseif ($num >= (float) $low AND $num <= (float) $high)
      {
        return TRUE;
      }
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	function is_numeric($str)
	{
		return ( ! is_numeric($str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	function prep_for_form($str = '')
	{
		if ($this->_safe_form_data == FALSE OR $str == '')
		{
			return $str;
		}


		return str_replace(array("'", '"', '<', '>'), array("&#39;", "&quot;", '&lt;', '&gt;'), stripslashes($str));
	}

	// --------------------------------------------------------------------

	function prep_url($str = '')
	{
		if ($str == 'http://' OR $str == '')
		{
			$_POST[$this->_current_field] = '';
			return;
		}

		if (substr($str, 0, 7) != 'http://' && substr($str, 0, 8) != 'https://')
		{
			$str = 'http://'.$str;
		}

		$_POST[$this->_current_field] = $str;
	}

	// --------------------------------------------------------------------

	function unique($str, $val)
	{
		if (isset($this->model))
		{
			return ( $this->model->is_unique($this->_current_field, $str) );
		}
		else
		{
			rpd::$db->select('1')->from($val)->where($this->_current_field, $str);
			rpd::$db->get();
			$result = rpd::$db->num_rows();
			return !(bool)$result;
		}
	}

	// --------------------------------------------------------------------

	function captcha($str)
	{
		if (!isset($_SESSION["captcha"])) return false;
		$match = ( strtolower($_SESSION["captcha"]) == strtolower($str) ) ? true : false;
		unset($_SESSION["captcha"]);
		return $match;
	}

	// --------------------------------------------------------------------

	function policy($str)
	{
    $value = (int)$str;
		return (bool)$value;
	}

	// --------------------------------------------------------------------

	function url_title($str)
	{
		$search		= '-';
		$replace='_';
		$trans = array(
				$search				=> $replace,
				"\s+"				=> $replace,
				"[^a-z0-9".$replace."]"		=> '',
				$replace."+"			=> $replace,
				$replace."$"			=> '',
				"^".$replace			=> ''
				 );

		$str = strip_tags(strtolower($str));

		foreach ($trans as $key => $val)
		{
			$str = preg_replace("#".$key."#", $val, $str);
		}

		return trim(stripslashes($str));
	}




}
// END Validation Class
