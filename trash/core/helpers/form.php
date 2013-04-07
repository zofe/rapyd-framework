<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


class rpd_form_helper {


	public static function open($action = '', $attr = array(), $hidden = array())
	{
		// Make sure that the method is always set
		$attr += array
		(
			'method' => 'post',
			'class'	 => 'form'
		);

		// Make sure that the method is valid
		$attr['method'] = ($attr['method'] == 'post') ? 'post' : 'get';

		if ($action == '')
		{
			$action = url_helper::get_url();
		}

		// Form opening tag
		$form = '<form action="'.$action.'"'.self::attributes($attr).'>'."\n";

		// Add hidden fields
		if (is_array($hidden) AND count($hidden > 0))
		{
			$form .= self::hidden($hidden);
		}

		return $form;
	}


	public static function open_multipart($action = '', $attr = array(), $hidden = array())
	{
		// Set multi-part form type
		$attr['enctype'] = 'multipart/form-data';

		return self::open($action, $attr, $hidden);
	}


	public static function hidden($data, $value = '')
	{
		if ( ! is_array($data))
		{
			$data = array
			(
				$data => $value
			);
		}

		$input = '';
		foreach($data as $name => $value)
		{
			$attr = array
			(
				'type'  => 'hidden',
				'name'  => $name,
				'value' => $value
			);

			$input .= self::input($attr)."\n";
		}
		return $input;
	}

	public static function input($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

        if (!isset($data['type']) || in_array($data['type'], array('input')) ) $data['type']= 'text';
		$data['value']= $value;

		// Form elements should have the same id as name
		if ( ! isset($data['id']))
		{
			$data['id'] = $data['name'];
		}

		// For safe form data
		$data['value'] = htmlspecialchars($data['value']);

		return '<input'.self::attributes($data).$extra.' />';
	}


	public static function password($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'password';

		return self::input($data, $value, $extra);
	}


	public static function upload($data = '', $value = '', $extra = '')
	{

		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'file';

		return self::input($data, $value, $extra);
	}


	public static function textarea($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		// Use the value from $data if possible, or use $value
		$value = isset($data['value']) ? $data['value'] : $value;

		// Value is not part of the attributes
		unset($data['value']);

		return '<textarea'.self::attributes($data).'>'.htmlspecialchars($value).'</textarea>';
	}


	public static function dropdown($data = '', $options = array(), $selected = '', $extra = '', $disabled = null)
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}
		unset($data['type']);
		$input = '<select '.self::attributes($data).$extra.'>'."\n";
		foreach ($options as $key => $val)
		{
			$sel = ($selected == $key) ? ' selected="selected"' : '';
			$dis = ($disabled === $key) ? ' disabled="disabled"' : '';
			$input .= '<option value="'.$key.'"'.$sel.$dis.'>'.$val.'</option>'."\n";
		}
		$input .= '</select>';

		return $input;
	}


	public static function checkbox($data = '', $value = '', $checked = FALSE, $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'checkbox';

		if ($checked == TRUE OR (isset($data['checked']) AND $data['checked'] == TRUE))
		{
			$data['checked'] = 'checked';
		}
		else
		{
			unset($data['checked']);
		}

		return self::input($data, $value, $extra);
	}


	public static function radio($data = '', $value = '', $checked = FALSE, $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'radio';

		if ($checked == TRUE OR (isset($data['checked']) AND $data['checked'] == TRUE))
		{
			$data['checked'] = 'checked';
		}
		else
		{
			unset($data['checked']);
		}

		return self::input($data, $value, $extra);
	}


	public static function submit($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'submit';

		return self::input($data, $value, $extra);
	}

	public static function button($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data += array
		(
			'type'  => 'button'
		);

		if (isset($data['value']))
		{
			$value = $data['value'];
      unset($data['value']);
    }

		return '<button'.self::attributes($data).$extra.'>'.htmlspecialchars($value).'</button>';
	}

	public static function close($extra = '')
	{
		return '</form>'."\n".$extra;
	}

	public static function label($data = '', $text = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			if (strpos($data, '[') !== FALSE)
			{
				$data = preg_replace('/\[.*\]/', '', $data);
			}

			$data = array
			(
				'for' => $data
			);
		}

		return '<label'.self::attributes($data).$extra.'>'.$text.'</label>';
	}


	public static function attributes($attr)
	{
		$order = array
		(
			'type',
			'id',
			'name',
			'value',
			'src',
			'size',
			'maxlength',
			'rows',
			'cols',
			'accept',
			'tabindex',
			'accesskey',
			'align',
			'alt',
			'title',
			'class',
			'style',
			'selected',
			'checked',
			'readonly',
			'disabled'
		);

		$sorted = array();
		foreach($order as $key)
		{
			if (isset($attr[$key]))
			{
				$sorted[$key] = $attr[$key];
			}
		}

		$sorted = array_merge($sorted, $attr);

		return html_helper::attributes($sorted);
	}

} // End form
