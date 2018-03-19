<?php
namespace lib;

class redirect
{
	/**
	 * try to redirect to new location
	 * @param  [type]  $_url address
	 * @param  boolean $_php use php
	 * @param  [type]  $_arg special argument like txt in html or type in php
	 * @return [type]        [description]
	 */
	public static function to($_url, $_php = true, $_arg = null)
	{
		if(\lib\request::json_accept() || \lib\temp::get('api') || \lib\request::ajax())
		{
			self::via_pushstate($_url);
		}

		if($_php)
		{
			self::via_php($_url, $_arg);
		}
		else
		{
			self::via_html($_url, $_arg);
		}

		\lib\code::die();
	}

	/**
	 * redirect to current location
	 */
	public static function pwd()
	{
		self::to(\lib\url::pwd());
	}


	/**
	 * via pushstate
	 * @param  [type] $_loc [description]
	 * @return [type]       [description]
	 */
	private static function via_pushstate($_loc)
	{
		header('Content-Type: application/json');
		\lib\notif::redirect($_loc);
		echo \lib\notif::json();
		\lib\code::die();
	}


	/**
	 * with php
	 * @param  [type]  $_loc  [description]
	 * @param  integer $_type [description]
	 * @return [type]         [description]
	 */
	private static function via_php($_loc, $_type = 301)
	{
		if (!headers_sent())
		{
			header('Pragma: no-cache');
			header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
			header("Expires: Fri, 08 Sep 2017 06:12:00 GMT");
			header('Location: '. $_loc, true , $_type);
		}
	}


	/**
	 * with html and design
	 * @param  [type] $_loc [description]
	 * @return [type]       [description]
	 */
	private static function via_html($_loc, $_txt = null)
	{
		echo '<html><head>';
		echo '<meta http-equiv="refresh" content="2; URL='.$_loc.'">';
		echo '<meta charset="utf-8">';
		echo '<style type="text/css">body{background-color:#ffffff;background-attachment:fixed;background-repeat:repeat;font-size:12px;font-family:lato;text-align:center;line-height:14px;text-transform:none;color:#E0E0E0;}#main{position:fixed;height:494px;width:650px;top:50%;margin-top:-100px;left:50%;margin-left:-325px;font-size:50px;line-height:59px;}a{display:block;text-decoration:none;color:#a3a3a3;-webkit-transition:all 0.4s linear;-moz-transition:all 0.4s linear;transition:all 0.4s linear;}a:link,a:active,a:visited{color:#a3a3a3;padding-bottom:5px;border-bottom:2px solid #a3a3a3;}a:hover{color:#a3a3a3;}.smaller{font-size:20px;text-transform:lowercase;}</style>';
		echo '</head></body>';
		echo ' <div id="main">';
		echo '  <a href="'.$_loc.'">REDIRECTING YOU</a>';
		echo '  <span class="smaller">'. strtok($_loc, '?') .'</span><br>';
		if($_txt)
		{
			echo '  <span class="smaller">'. $_txt .'</span><br>';
		}
		echo ' </div>';
		echo '</body></html>';
	}
}
?>