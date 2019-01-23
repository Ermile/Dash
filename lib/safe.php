<?php
namespace dash;


class safe
{
	/**
	 * safe string for sql injection and XSS
	 * @param  string $_string unsafe string
	 * @return string          safe string
	 */
	public static function safe($_string, $_type = null)
	{
		if(is_array($_string) || is_object($_string))
		{
			return self::walk($_string, $_type);
		}

		if(gettype($_string) == 'integer' || gettype($_string) == 'double' || gettype($_string) == 'boolean' ||	$_string === null)
		{
			return $_string;
		}

		$remove_inject    = null;
		$htmlspecialchars = true;
		$trim             = true;

		if(strpos($_type, '-') !== false)
		{
			$_type = explode('-', $_type);
		}
		else
		{
			$_type = [$_type];
		}

		foreach ($_type as $key => $value)
		{
			switch ($value)
			{
				case 'get_url':
					$remove_inject = ["'", '"', '\\\\\\', '`', '\*', ';'];
					break;

				case 'sqlinjection':
					$remove_inject = ["'", '"', '\\\\\\', '`', '\*', "\\?", ';'];
					break;

				case 'raw':
					$htmlspecialchars = false;
					break;

				case 'nottrim':
					$trim = false;
					break;

				default:
					// nothing
					break;
			}
		}

		if($remove_inject)
		{
			$_string = preg_replace("/\s?[" . join('', $remove_inject) . "]/", "", $_string);
		}

		$_string = self::persian_char($_string);

		if($trim)
		{
			$_string = trim($_string);

			$_string = self::remove_2s($_string);
		}

		$_string = self::remove_2nl($_string);

		$_string = self::remove_php_tag($_string);

		if($htmlspecialchars)
		{
			$_string = strip_tags($_string);
			$_string = htmlspecialchars($_string, ENT_QUOTES | ENT_HTML5);
		}

		$_string = addslashes($_string);

		return $_string;
	}


	public static function persian_char($_string)
	{
		if(\dash\language::current() === 'fa')
		{
			$_string = str_replace(['ي', 'ك'], ['ی', 'ک'], $_string);
			$_string = \dash\utility\convert::ar_to_fa_number($_string);
		}
		return $_string;
	}


	public static function remove_php_tag($_string)
	{
		$_string = str_replace('<?', '', $_string);
		$_string = str_replace('?>', '', $_string);
		return $_string;
	}


	public static function remove_2nl($_string)
	{
		$_string = preg_replace("/[\r\n]{2,}/", "\n", $_string);
		return $_string;
	}


	public static function remove_nl($_string)
	{
		$_string = preg_replace("/[\n]/", " ", $_string);
		return $_string;
	}


	public static function remove_2s($_string)
	{
		$_string = preg_replace("/\h+/", " ", $_string);
		return $_string;
	}


	public static function forQueryString($_string)
	{
		$_string = self::safe($_string, 'get_url-sqlinjection');
		$_string = mb_ereg_replace('([^ءئؤيكإأةآا-ی۰-۹a-z0-9A-Z\.\@\!\#\$\&\^\%\-\=\_])+', ' ', $_string);
		return $_string;
	}


	public static function forJson($_string)
	{
		$_string = preg_replace("/\<\/[\w]\>/", ' ', $_string);
		$_string = strip_tags($_string);
		$_string = trim($_string);
		$_string = self::remove_2nl($_string);
		$_string = self::remove_nl($_string);
		$_string = self::remove_2s($_string);
		$_string = trim($_string);
		return $_string;
	}

	/**
	 * Nested function for walk array or object
	 * @param  array or object $_value unpack array or object
	 * @return array or object         safe array or object
	 */
	private static function walk($_value, $_type = null)
	{
		foreach ($_value as $key => $value)
		{
			if(is_array($value) || is_object($value))
			{
				if(is_array($_value))
				{
					$_value[$key] = self::walk($value, $_type);
				}
				elseif(is_object($_value))
				{
					$_value->$key = self::walk($value, $_type);
				}
			}
			else
			{
				if(is_array($_value))
				{
					$_value[$key] = self::safe($value, $_type);
				}
				elseif(is_object($_value))
				{
					$_value->$key = self::safe($value, $_type);
				}
			}
		}
		return $_value;
	}
}
?>