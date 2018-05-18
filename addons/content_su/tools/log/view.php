<?php
namespace content_su\tools\log;

class view
{
	public static function config()
	{
		if(\dash\request::get('folder') && \dash\request::get('file'))
		{
			$addr = root. 'includes/log/'. \dash\request::get('folder'). '/'. \dash\request::get('file');
			$addr = \autoload::fix_os_path($addr);
			if(!is_file($addr))
			{
				\dash\header::status(404, "File not Found");
			}

			self::load_file($addr);

		}
		elseif(\dash\request::get('folder'))
		{
			$folder = '/'. trim(\dash\request::get('folder'),'/');
			$addr = root. 'includes/log'. $folder. '/*';
			$addr = \autoload::fix_os_path($addr);

			$glob = glob($addr);
			$list = [];
			foreach ($glob as $key => $value)
			{
				$list[] =
				[
					'name'  => basename($value),
					'mtime' => filemtime($value),
					'size'  => round((filesize($value) / 1024) / 1024, 2),
				];
			}

			\dash\data::logFileList($list);
		}
		else
		{
			$addr = root. 'includes/log/*';
			$addr = \autoload::fix_os_path($addr);
			$glob = glob($addr);
			$list = [];
			foreach ($glob as $key => $value)
			{
				$list[] =
				[
					'name' => str_replace(root.'includes/log/', '', $value),
				];
			}
			\dash\data::logList($list);
		}

	}

	private static function load_file($_filepath)
	{

		$output     = '<html>';
		$name       = \dash\request::get('file');
		$isClear    = \dash\request::get('clear');
		$isZip      = \dash\request::get('zip');
		$clearName  = '';
		$clearURL   = '';
		$page       = \dash\request::get('p') * 100000;

		if($page< 0)
		{
			$page = 0;
		}

		$lenght      = \dash\request::get('lenght');

		if($lenght< 100000)
		{
			$lenght = 100100;
		}

		$filepath   = $_filepath;

		if($isClear)
		{
			\dash\file::rename($filepath, $clearURL);
			\dash\redirect::to(\dash\url::this());
		}

		if($isZip)
		{
			$newZipAddr = database.'log/dl.zip';
			// create zip
			if(\dash\utility\zip::create($filepath, $newZipAddr) === true)
			{
				\dash\utility\zip::download_on_fly($newZipAddr, $clearName);
			}
		}

		// read file data
		$fileData = @file_get_contents($filepath, FILE_USE_INCLUDE_PATH, null, $page, $lenght);

		$myURL    = \dash\url::site().'/static';
		$myCommon = \dash\url::site().'/static/siftal/js/siftal.min.js';
		$myCode   = \dash\url::site().'/static/siftal/';

		$output .= "<head>";
		$output .= ' <title>Log | '. $name. '</title>';
		$output .= ' <script src="'. $myCommon. '"></script>';
		$output .= ' <script src="'. $myCode. 'js/highlight.min.js"></script>';
		$output .= ' <link rel="stylesheet" href="'. $myCode. 'css/highlight-atom-one-dark.css">';
		$output .= ' <style>';
		$output .= 'body{margin:0;height:100%;} .clear{position:absolute;top:1em;right:2em;border:1px solid #fff;color:#fff;border-radius:3px;padding:0.5em 1em;text-decoration:none} .zip{position:absolute;bottom:1.5em;right:2em;background-color:#000;color:#fff;border-radius:3px;padding:0.5em 1em;text-decoration:none} .hljs{padding:0;max-height:100%;height:100%;}';
		$output .= ' </style>';

		$output .= ' <script>$(document).ready(function() {$("pre").each(function(i, block) {hljs.highlightBlock(block);}); });</script>';
		$output .= "</head><body>";
		$output .= '<a class="clear primary" href="'. \dash\url::this(). '/log?folder='.\dash\request::get('folder').'">Back!</a>';
		// $output .= '<a class="clear" href="?name='. $name. '&clear=true">Clear it!</a>';
		$output .= '<a class="zip" href="?name='. $name. '&zip=true">ZIP it!</a>';
		$output .= "<pre class=''>";
		$output .= $fileData;
		$output .= "</pre>";

		$output .= "</body></html>";
		echo $output;
		\dash\code::exit();

	}
}
?>