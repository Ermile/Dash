<?php
namespace dash\social\telegram;

class file
{
	private $saveDest = root.'public_html/files/telegram/';

	public static function lastProfilePhoto($_data)
	{

		return null;
	}






	// private static function saveProfile($_data)
	// {
	// 	// if this result is not okay return false
	// 	if(!isset($_data['ok']))
	// 	{
	// 		return false;
	// 	}
	// 	// if result is not good return false
	// 	if(!isset($_data['result']['total_count']) || !isset($_data['result']['photos']))
	// 	{
	// 		return false;
	// 	}

	// 	// now we are giving photos
	// 	$count  = $_data['result']['total_count'];
	// 	$photos = $_data['result']['photos'];
	// 	$result = [];
	// 	// if has more than one image
	// 	// if($count === 0)
	// 	// {
	// 	// 	self::createUserDetail($img['file_id']);
	// 	// }
	// 	// elseif($count > 0)
	// 	// {
	// 	// 	// get biggest size of first image(last profile photo)
	// 	// 	$img = end($photos[0]);
	// 	// 	// if file_id is exist
	// 	// 	if(isset($img['file_id']))
	// 	// 	{
	// 	// 		self::createUserDetail($img['file_id']);
	// 	// 	}
	// 	// }


	// 	// if dir is not created, create it
	// 	if(!is_dir(self::$saveDest))
	// 	{
	// 		\lib\utility\file::makeDir(self::$saveDest, 0775, true);
	// 	}

	// 	// loop on all photos
	// 	foreach ($photos as $photoKey => $photo)
	// 	{
	// 		$photo = end($photo);
	// 		if(isset($photo['file_id']) && $photo['file_id'])
	// 		{
	// 			$myFile = self::getFile(['file_id' => $photo['file_id']]);
	// 			// save file
	// 			$result[$photoKey] = self::save($myFile, $photoKey, '.jpg');
	// 		}
	// 	}
	// 	return $result;
	// }



	// public static function save($_response, $_prefix = null, $_ext = null)
	// {
	// 	if(!isset($_response['ok']) || !isset($_response['result']) || !isset($_response['result']['file_path']))
	// 	{
	// 		return false;
	// 	}
	// 	$file_id   = $_response['result']['file_id'];
	// 	$file_path = $_response['result']['file_path'];
	// 	$dest      = self::$saveDest;
	// 	$exist     = glob($dest.'/*'. $file_id. $_ext);
	// 	// if file exist then don't need to get it from server, return
	// 	if(count($exist))
	// 	{
	// 		return null;
	// 	}
	// 	// add prefix if exits
	// 	if($_prefix)
	// 	{
	// 		$dest .= $_prefix .'-';
	// 	}
	// 	// add file_id
	// 	$dest      .= $file_id;
	// 	if($_ext)
	// 	{
	// 		$dest = $dest. $_ext;
	// 	}
	// 	// save file source
	// 	$source    = "https://api.telegram.org/file/bot";
	// 	$source    .= tg::$api_key. "/". $file_path;

	// 	return copy($source, $dest);
	// }

}
?>