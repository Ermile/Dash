<?php
namespace content_su\update;

class controller
{
	public static function routing()
	{
		$name         = \dash\request::get('git');
		if(!$name)
		{
			return;
		}

		$result = self::gitUpdate($name);
		if(is_array($result))
		{
			foreach ($result as $key => $value)
			{
				echo $value;
			}
		}

		\content_api\v5\git\model::save_detail(false);

		\dash\code::boom();
	}


	public static function gitUpdate($_name, $_password = null)
	{
		$result   = [];
		if(!self::ping())
		{
			$result[] = "<h1>We have not any response from https://ermile.com!</h1>";

			return $result;
		}

		\dash\log::set('su_gitUpdateStart');
		// switch by name of repository
		switch ($_name)
		{
			case 'dash':
				$result[] = self::updateDash();
				break;

			case 'all':
				// pull dash
				$result[] = self::updateDash();

				// pull current project
				$_name = \dash\url::root();
				$result[] = "<h1>$_name <small>Current Project</small></h1>";
				$result[] = "<p>Project location is <b>". root. "</b></p>";
				$result[] =  \dash\utility\git::pull(root, false, $_password);
				break;

			case '':
				break;

			default:
				$result[] =  \dash\utility\git::pull(root, false, $_password);

				// return;
				break;
		}
		\dash\log::set('su_gitUpdate');
		return $result;
	}



	public static function updateDash()
	{
		$dashLocation = null;
		// check dash location
		if(is_dir(root. 'dash'))
		{
			$dashLocation = '../dash';
		}
		elseif(is_dir(root. '../dash'))
		{
			$dashLocation = '../../dash';
		}

		$back = "<h1><a href='".\dash\url::kingdom()."/su/update' >Back to su update</a></h1>";
		return $back. "<h1>Dash</h1>". \dash\utility\git::pull($dashLocation);
	}



	private static function ping($_url = null)
	{
	    if($_url === null)
	    {
	    	$_url = 'https://ermile.com/fa';
	    }

	    $ch = curl_init($_url);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $data = curl_exec($ch);
	    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    curl_close($ch);

	    if($httpcode >= 200 && $httpcode <= 301)
	    {
	        return true;
	    }
	    else
	    {
	        return false;
	    }
	}

}
?>