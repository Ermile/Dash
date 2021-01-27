<?php
namespace dash\utility;

/** zip files management **/
class zip
{
	public static function create($_zipAddr, $_file, $_fileNewName = null)
	{
		$zip = new \ZipArchive();

		if ($zip->open($_zipAddr, \ZIPARCHIVE::OVERWRITE) !== true)
		{
			// if file not exist, add to existing file
			if ($zip->open($_zipAddr, \ZipArchive::CREATE) !== true)
			{
				return("cannot open <$_zipAddr>\n");
			}
		}

		// add file to zip archive
		$zip->addFile($_file, $_fileNewName);
		$zip->close();

		return true;
	}



	public static function multi_file($_zipAddr, $_file)
	{
		$zip = new \ZipArchive();

		if ($zip->open($_zipAddr, \ZIPARCHIVE::OVERWRITE) !== true)
		{
			// if file not exist, add to existing file
			if ($zip->open($_zipAddr, \ZipArchive::CREATE) !== true)
			{
				return("cannot open <$_zipAddr>\n");
			}
		}

		foreach ($_file as $value)
		{
			// add file to zip archive
			$zip->addFile($value, basename($value));
		}

		$zip->close();

		return true;
	}


	/**
	 * [download_on_fly description]
	 * @param  [type] $_addr [description]
	 * @param  [type] $_name [description]
	 * @return [type]        [description]
	 */
	public static function download_on_fly($_addr, $_name = null)
	{
		if($_name)
		{
			$_name .= '.zip';
		}
		else
		{
			$_name = 'test.zip';
		}
		\dash\file::download($_addr, $_name, 'archive/zip');
		// exit to download it
		\dash\code::boom();
	}


	public static function folder($_zipAddr, $_addr)
	{
		$zip = new \ZipArchive;

		if ($zip->open($_zipAddr, \ZIPARCHIVE::OVERWRITE) !== true)
		{
			// if file not exist, add to existing file
			if ($zip->open($_zipAddr, \ZipArchive::CREATE) !== true)
			{
				return false;
			}
		}

	   self::addFolderToZip($_addr, $zip);

	   $zip->close();

	   return true;
	}


	private static function addFolderToZip($dir, $zipArchive, $zipdir = '')
	{
	    if(is_dir($dir))
	    {
	        if($dh = opendir($dir))
	        {
	            // Loop through all the files
	            while (($file = readdir($dh)) !== false)
	            {
	                //If it's a folder, run the function again!
	                if(!is_file($dir . $file))
	                {
			            //Add the directory
			            if($zipdir)
			            {
			            	$zipArchive->addEmptyDir($zipdir);
			            }

	                    // Skip parent and root directories
	                    if(($file !== ".") && ($file !== ".."))
	                    {
	                        self::addFolderToZip($dir. '/' . $file . "/", $zipArchive, $zipdir. '/' . $file);
	                    }
	                }
	                else
	                {
	                    // Add the files
	                    $zipArchive->addFile($dir. '/' . $file, $zipdir .'/'. $file);
	                }
	            }
	        }
	    }
	}
}
?>