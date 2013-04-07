<?php

class rpd_error_library {

	public static $error_message = ''; 
	
	
	/**
	 *  display application errors
	 *
	 * @param string $message 
	 */
	public static function error($code, $message='', $exception=null)
	{
		ob_get_level() and ob_end_clean();
		if (!is_int($code))
		{
			$message = $code;
			$code = 'error';
		}
		if ($message!='') self::$error_message = $message;
		
		if ($exception)
		{
			$e = $exception;
			$trace = $e->getTraceAsString();
			$file = $e->getFile();
			$line = $e->getLine();
			$source = static::highlight_source($file, $line, 20);
		} else {
			$e = new Exception();
			$trace_array = $e->getTrace();
			$trace = preg_replace("@#0 (.*)#1@Usi", '#1', $e->getTraceAsString());
			$file = $trace_array[1]['file'];
			$line = $trace_array[1]['line'];
			$source = static::highlight_source($file, $line, 20);
		}
		self::$error_message .= "\n\n\nCall Stack:\n$trace\n$source";		
		rpd::run('error/code/'.$code);
		exit(1);
	}

	/**
	 * error handling
	 *
	 * @param int $code
	 * @param type $error
	 * @param string $file
	 * @param int $line
	 * @return mixed 
	 */
	public static function error_handler($code, $error, $file = NULL, $line = NULL)
	{

		if (error_reporting() & $code)
		{
			throw new ErrorException($error, $code, 0, $file, $line);
		}
		return TRUE;
	}

	/**
	 * error handling
	 * 
	 * @param Exception $e 
	 */
	public static function exception_handler(Exception $e)
	{
		ob_get_level() and ob_end_clean();
		try
		{
			$type = get_class($e);
			$code = $e->getCode();
			$message = $e->getMessage();
			$file = $e->getFile();
			$line = $e->getLine();
			$trace = $e->getTraceAsString();

			
			$errors = array(
				E_ERROR => 'Fatal Error',
				E_USER_ERROR => 'User Error',
				E_PARSE => 'Parse Error',
				E_WARNING => 'Warning',
				E_USER_WARNING => 'User Warning',
				E_STRICT => 'Strict',
				E_NOTICE => 'Notice',
				E_RECOVERABLE_ERROR => 'Recoverable Error',
			);
			if (isset($errors[$code]))
			{
				$code = $errors[$code];
			}

			if (!headers_sent())
				header('HTTP/1.1 500 Internal Server Error');


			self::error(500,"'$code $message'", $e);
			exit;

		} catch (Exception $e)
		{
			echo strip_tags($e->getMessage()) . ' on ' . $e->getFile() . ' line [' . $e->getLine() . "]\n";
			exit(1);
		}
	}
	

	
	protected static function highlight_source($file, $linenumber, $showlines)
    {
        $lines = file_get_contents($file);
        $lines = highlight_string($lines, true);
        $lines = explode("<br />", $lines);

        $offset = max(0, $linenumber - ceil($showlines / 2));
        $lines = array_slice($lines, $offset, $showlines);

        $html = '';
        foreach ($lines as $line) {
            $offset++;
            $line = '<em class="lineno">' . sprintf('%4d', $offset) . ' </em>' . $line . '<br/>';
            if ($offset == $linenumber) {
                $html .= '<div style="background: #ffc">' . $line . '</div>';
            } else {
                $html .= $line;
            }
        }

        return "<div>Source: <strong>$file</strong>\n".$html."</div>";
    }
	
	
}