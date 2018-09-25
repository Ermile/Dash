<?php
namespace dash;

class datetime
{

	/**
	 * return all format supported
	 * @param  [type]  $_type [description]
	 * @param  boolean $_long [description]
	 * @return [type]         [description]
	 */
	public static function format($_type, $_long = true)
	{
		switch ($_type)
		{
			case 'date':
				switch ($_long)
				{
					case 'short':
						// 2018-09-25
						return 'Y-m-d';
						break;

					case 'long':
					default:
						// Tuesday 25 September 2018
						return 'l d F Y';
						break;
				}
				break;

			case 'time':
				switch ($_long)
				{
					case 'short':
						return 'H:i';
						break;

					case 'long':
					default:
						return 'g:i:s A';
						break;
				}
				break;

			case 'datetime':
			default:
				switch ($_long)
				{
					case 'short':
						return 'Y-m-d'. ' '. 'H:i';
						break;

					case 'long':
					default:
						return 'l d F Y'. ' '. 'g:i:s A';
						break;
				}
				break;
		}

	}


}
?>
