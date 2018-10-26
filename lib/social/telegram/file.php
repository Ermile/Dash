<?php
namespace dash\social\telegram;

class file
{
	private static $saveDest = root.'public_html/files/telegram/';


	public static function lastProfilePhoto($_userid = null, $_saveAllPhoto = false)
	{
		if(!$_userid)
		{
			$_userid = hook::from();
		}
		$userDetail = tg::getUserProfilePhotos(['user_id' => $_userid]);
		if(!isset($userDetail['ok']))
		{
			return false;
		}
		// if result is not good return false
		if(!isset($userDetail['result']['total_count']) || !isset($userDetail['result']['photos']))
		{
			return false;
		}

		$count  = $userDetail['result']['total_count'];
		$photos = $userDetail['result']['photos'];

		$firstPhotoUrl = null;
		$lastPhotoUrl  = null;

		// loop on all photos
		foreach ($photos as $photoKey => $myPhotoArray)
		{
			$photo = end($myPhotoArray);
			if(isset($photo['file_id']) && $photo['file_id'])
			{
				if($lastPhotoUrl === null)
				{
					$lastPhotoUrl = $photo['file_id'];
					user::setAvatar($_userid, $lastPhotoUrl);
				}
				// get last photo url
				$firstPhotoUrl = $photo['file_id'];

				if($_saveAllPhoto)
				{
					// save file
					self::save($photo['file_id'], $_userid);
				}
			}
		}

		return $lastPhotoUrl;
	}



	public static function save($_fileid, $_prefix = null)
	{
		$myFile = tg::getFile(['file_id' => $_fileid]);

		// check file path is returned by telegram
		if(!isset($myFile['result']['file_id']) || !isset($myFile['result']['file_path']))
		{
			return false;
		}

		// get file detail
		$file_id   = $myFile['result']['file_id'];
		$file_path = $myFile['result']['file_path'];
		$file_ext  = pathinfo($file_path, PATHINFO_EXTENSION);
		$file_type = strtok($file_path, '/');

		// generate save dest
		$fileDest  = self::$saveDest. $file_type. DIRECTORY_SEPARATOR;
		// if dir is not created, create it
		if(!is_dir($fileDest))
		{
			\dash\file::makeDir($fileDest, 0775, true);
		}
		// add file name and ext
		if($_prefix)
		{
			$fileDest .= $_prefix. '-'. $file_id;
		}
		$fileDest .= $file_id;
		if($file_ext)
		{
			$fileDest .= '.'. $file_ext;
		}

		// if file exist then don't need to get it from server, return
		if(is_file($fileDest))
		{
			return null;
		}

		// save file source
		$source    = "https://api.telegram.org/file/bot";
		$source    .= tg::$api_token. "/". $file_path;

		return copy($source, $fileDest);
	}

}
?>