<?php
namespace content_su\log;

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
				$name = basename($value);
				$is_old = false;

				if(strtotime(substr($name, 0, 10)) !== false)
				{
					$is_old = true;
				}

				$list[] =
				[
					'name'  => $name,
					'mtime' => filemtime($value),
					'size'  => round((filesize($value) / 1024) / 1024, 2),
					'is_old' => $is_old,
				];
			}

			$list = array_reverse($list);
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
				$name = str_replace(root.'includes/log/', '', $value);

				switch ($name)
				{
					case 'php':
						$icon = 'bug';
						break;

					case 'database':
						$icon = 'database';
						break;

					case 'header':
						$icon = 'flag-1';
						break;

					default:
						$icon = 'bug-2';
						break;
				}

				$list[] =
				[
					'icon' => $icon,
					'name' => $name,
				];
			}
			\dash\data::logList($list);
		}

	}

	private static function load_file($_filepath)
	{
		if(\dash\request::get('clear'))
		{
			$mbasename = basename($_filepath);
			$basename  = date("Y-m-d_H-i-s_"). $mbasename;
			$clearURL  = str_replace($mbasename, $basename, $_filepath);
			\dash\file::rename($_filepath, $clearURL);
			\dash\redirect::to(\dash\url::current(). '?folder='. \dash\request::get('folder'));
		}


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

		if (\dash\request::get('download'))
		{
			\dash\file::download($filepath);
			return;
		}
		// read file data
		$fileData = @file_get_contents($filepath, FILE_USE_INCLUDE_PATH, null, $page, $lenght);
		$myURL    = \dash\url::static(). '/';
		$myCommon = \dash\url::static(). '/siftal/js/siftal.min.js';
		$myCode   = \dash\url::static(). '/siftal/';

		$output .= "<head>";
		$output .= ' <title>Log | '. $name. '</title>';
		$output .= ' <script src="'. $myCommon. '"></script>';
		$output .= ' <script src="'. $myCode. 'js/highlight.min.js"></script>';
		$output .= ' <link rel="stylesheet" href="'. $myCode. 'css/highlight-atom-one-dark.css">';
		$output .= ' <style>';
		$output .= 'body{margin:0;height:100%;} .clear{position:absolute;top:1em;right:2em;border:1px solid #fff;color:#fff;border-radius:3px;padding:0.5em 1em;text-decoration:none} .zip{position:absolute;bottom:1.5em;right:2em;background-color:#000;color:#fff;border-radius:3px;padding:0.5em 1em;text-decoration:none}
		.downloaditnow{position:absolute;top:5em;right:2em;border:1px solid #fff;color:#fff;border-radius:3px;padding:0.5em 1em;text-decoration:none}
		.hljs{padding:0;max-height:100%;height:100%;}';
		$output .= ' </style>';

		$output .= ' <script>$(document).ready(function() {$("pre").each(function(i, block) {hljs.highlightBlock(block);}); });</script>';
		$output .= "</head><body>";
		// $output .= '<a class="clear primary" href="'. \dash\url::this(). '?folder='.\dash\request::get('folder').'">Back!</a>';
		$output .= '<a class="clear" href="'. \dash\url::this(). '?clear=1&folder='.\dash\request::get('folder').'&file='.\dash\request::get('file').'">Clear it!</a>';
		$output .= '<a class="downloaditnow" href="'. \dash\url::this(). '?download=1&folder='.\dash\request::get('folder').'&file='.\dash\request::get('file').'">Download it!</a>';
		$output .= '<a class="zip" href="?name='. $name. '&zip=true">ZIP it!</a>';
		$output .= "<pre class=''>";
		$output .= $fileData;
		$output .= "</pre>";

		$output .= "</body></html>";
		echo $output;
		\dash\code::boom();

	}
}
?>