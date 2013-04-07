<?php if (!defined('CORE_PATH')) exit('No direct script access allowed');


class rpd_html_helper {

 public static $tags = array (
      'js'      => '<script language="javascript" type="text/javascript" src="%s"></script>',
      'script'  => '<script language="javascript" type="text/javascript">%s</script>',
        'style'   => '<style type="text/css">%s</style>',
      'css'     => '<link rel="stylesheet" href="%s" type="text/css" />',
      'img'     => '<img src="%s" %s />',
      'button'  => '<span class="button large"><input type="%s" name="%s" value="%s" onclick="%s"  class="%s" /></span>',
  );
	protected static $js = array('bootstrap/js/bootstrap.js');
	protected static $css = array('rapyd.css','bootstrap/css/bootstrap.css','bootstrap/css/bootstrap-responsive.css');
    protected static $external_js = array();
    protected static $external_css = array();

    protected static $scripts = array();
    protected static $style = array();

	// --------------------------------------------------------------------

	public static function attributes($attrs)
	{
		if (is_string($attrs))
			return ($attrs == FALSE) ? '' : ' '.$attrs;

		$compiled = '';
		foreach($attrs as $key => $val)
		{
			$compiled .= ' '.$key.'="'.$val.'"';
		}
		return $compiled;
	}

	// --------------------------------------------------------------------

  public function js ($js, $external = false)
  {
    if ($external)
    {
        if(!in_array($js, self::$external_js)) self::$external_js[] = $js;
    }
    else
        if(!in_array($js, self::$js)) self::$js[] = $js;
        if(!in_array($js, self::$js)) 
			if (rpd::find_asset($js))
				self::$js[] = rpd::find_asset($js);
  }

	// --------------------------------------------------------------------

  public function css ($css, $external = false)
  {
     if ($external)
     {
         if(!in_array($css, self::$external_css)) self::$external_css[] = $css;
     }
     else
        if(!in_array($css, self::$css)) 
			if (rpd::find_asset($css))
				self::$css[] = rpd::find_asset($css);
	//echo $css."\n";
  }

	// --------------------------------------------------------------------

  public function image ($src, $attrs=array())
  {
	  if (rpd::find_asset($src))
		return sprintf(self::$tags['img'], rpd::find_asset($src), self::attributes($attrs));
  }

	// --------------------------------------------------------------------

  public function script ($script)
  {
    return sprintf(self::$tags['script'], $script)."\n";
  }

	// --------------------------------------------------------------------

  public function style($style)
  {
    return sprintf(self::$tags['style'], $style)."\n";
  }

	// --------------------------------------------------------------------

  public function head_script($script)
  {
    if(!in_array($script, self::$scripts)) self::$scripts[] = $script;
  }

	// --------------------------------------------------------------------

  public function head_style($style)
  {
    if(!in_array($style, self::$style)) self::$style[] = $style;
  }
	// --------------------------------------------------------------------

  public function button ($name, $value, $onclick="", $type="button", $class="") //button
  {
    return sprintf(self::$tags['button'], $type, $name, $value, $onclick, $class)."\n";
  }

	// --------------------------------------------------------------------

	public function head()
	{
		$buffer = "";


            //external css links
            foreach (self::$external_css as $item)
            {
                    $buffer .= sprintf(self::$tags['css'], $item )."\n";
            }
            //external js links
            foreach (self::$external_js as $item)
            {
                    $buffer .= sprintf(self::$tags['js'], $item )."\n";
            }

            //css links
            foreach (self::$css as $item)
            {
					if (rpd::find_asset($item))
                    $buffer .= sprintf(self::$tags['css'], rpd::find_asset($item) )."\n";
            }
            //js links
            foreach (self::$js as $item)
            {
					if (rpd::find_asset($item))
						$buffer .= sprintf(self::$tags['js'], rpd::find_asset($item) )."\n";
            }
	    
            //javascript in page, head section
            if (count(self::$scripts))
            {
                $buffer .=sprintf(self::$tags['script'], "\njQuery(document).ready(function(){\n".implode("\n",self::$scripts)."\n});\n")."\n";
            }

            //style in page, head section
            if (count(self::$style))
            {
                $buffer .=sprintf(self::$tags['style'], implode("\n",self::$style))."\n";
            }

		return $buffer;
	}

	
	public static function anchor($uri, $title = '', $attributes = '')
	{
		$site_url = url_helper::url($uri);
		if ($title == '')
		{
			$title = $site_url;
		}

		if ($attributes != '')
		{
			$attributes = self::attributes($attributes);
		}

		return '<a href="'.$site_url.'"'.$attributes.'>'.$title.'</a>';
	}

}
