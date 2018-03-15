<?php
namespace addons\content_api\v1\user\tools;
use \lib\utility;
use \lib\debug;
use \lib\db\logs;
/**
 *
 * marital
 * gender
 * status
 * type
 * fileid
 * fileurl
 * email
 * parent
 * permission
 * username
 * group
 * pin
 * ref
 * notification
 * nationality
 * region
 * insurancetype
 * insurancecode
 * dependantscount
 * unit_id
 * language
 * childcount
 * birthplace
 * shfrom
 * shcode
 * education
 * job
 * passportcode
 * passportexpire
 * paymentaccountnumber
 * cardnumber
 * shaba
 * nationalcode
 * father
 * birthday
 * postion
 * personnelcode
 * name
 * lastname
 * displayname
 * twostep
 * setup
 */
trait user_check_args
{
	public function user_check_args($_args, &$args, $_log_meta, $_type = 'insert')
	{
		$log_meta = $_log_meta;

		// get firstname
		$displayname = utility::request("displayname");
		$displayname = trim($displayname);
		if($displayname && mb_strlen($displayname) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:teacher:displayname:max:length', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You can set the displayname less than 50 character"), 'displayname', 'arguments');
			return false;
		}

		// get firstname
		$firstname = utility::request("firstname");
		$firstname = trim($firstname);
		if($firstname && mb_strlen($firstname) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:teacher:firstname:max:length', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You can set the firstname less than 50 character"), 'firstname', 'arguments');
			return false;
		}

		// get lastname
		$lastname = utility::request("lastname");
		$lastname = trim($lastname);
		if($lastname && mb_strlen($lastname) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:teacher:lastname:max:length', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You can set the lastname less than 50 character"), 'lastname', 'arguments');
			return false;
		}

		// get postion
		$postion = utility::request('postion');
		if($postion && mb_strlen($postion) > 100)
		{
			if($_args['save_log']) logs::set('addon:api:teacher:postion:max:length', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You can set the postion less than 100 character"), 'postion', 'arguments');
			return false;
		}

		// get the code
		$personnelcode = utility::request('personnel_code');
		$personnelcode = trim($personnelcode);
		if($personnelcode && mb_strlen($personnelcode) > 9)
		{
			if($_args['save_log']) logs::set('addon:api:teacher:code:max:length', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You can set the personnel_code less than 9 character "), 'personnel_code', 'arguments');
			return false;
		}


		// get file code
		$file_code = utility::request('file');
		$file_id   = null;
		$file_url  = null;
		if($file_code)
		{
			$file_id = \lib\utility\shortURL::decode($file_code);
			if($file_id)
			{
				$logo_record = \lib\db\posts::is_attachment($file_id);
				if(!$logo_record)
				{
					$file_id = null;
				}
				elseif(isset($logo_record['meta']['url']))
				{
					$file_url = $logo_record['meta']['url'];
				}
			}
			else
			{
				$file_id = null;
			}
		}

		// get status
		$status = utility::request('status');
		if($status && mb_strlen($status) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:teacher:status:invalid', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Invalid parameter status"), 'status', 'arguments');
			return false;
		}

		if(!$status && $_type === 'insert')
		{
			$status = 'awaiting';
		}

		$nationalcode = utility::request('nationalcode');
		if($nationalcode && mb_strlen($nationalcode) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:teacher:nationalcode:max:length', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You must set the national code less than 50 character"), 'nationalcode', 'arguments');
			return false;
		}

		$father = utility::request('father');
		if($father && mb_strlen($father) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:teacher:father:max:length', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You must set the father name less than 50 character"), 'father', 'arguments');
			return false;
		}

		$birthday = utility::request('birthday');
		if($birthday && mb_strlen($birthday) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:teacher:birthday:max:length', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You must set the birthday name less than 50 character"), 'birthday', 'arguments');
			return false;
		}

		if($birthday)
		{
			$birthday = \lib\utility\human::number($birthday, 'en');
			if(strtotime($birthday) === false)
			{
				$birthday = utility::request('birthday');
			}
			else
			{
				$birthday = date("Y/m/d", strtotime($birthday));
			}
		}

		$gender = utility::request('gender');
		if($gender && !in_array($gender, ['male', 'female']))
		{
			if($_args['save_log']) logs::set('addon:api:teacher:gender:invalid', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Invalid gender field"), 'gender', 'arguments');
			return false;
		}

		$type = utility::request('type');
		if($type && mb_strlen($type) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:teacher:type:max:length', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You must set the type less than 50 character"), 'type', 'arguments');
			return false;
		}

		$marital = utility::request('marital');
		if($marital && !in_array($marital, ['single', 'married']))
		{
			if($_args['save_log']) logs::set('addon:api:user:marital:invalid', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Invalid marital field"), 'marital', 'arguments');
			return false;
		}

		$child = utility::request('child');
		if($child && mb_strlen($child) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:user:child:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You must set the child less than 50 character"), 'child', 'arguments');
			return false;
		}

		$birthplace = utility::request('birthplace');
		if($birthplace && mb_strlen($birthplace) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:user:birthplace:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You must set the birthplace less than 50 character"), 'birthplace', 'arguments');
			return false;
		}

		$shfrom = utility::request('shfrom');
		if($shfrom && mb_strlen($shfrom) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:user:shfrom:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You must set the shfrom less than 50 character"), 'shfrom', 'arguments');
			return false;
		}

		$shcode = utility::request('shcode');
		if($shcode && mb_strlen($shcode) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:user:shcode:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You must set the shcode less than 50 character"), 'shcode', 'arguments');
			return false;
		}

		$education = utility::request('education');
		if($education && mb_strlen($education) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:user:education:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You must set the education less than 50 character"), 'education', 'arguments');
			return false;
		}

		$job = utility::request('job');
		if($job && mb_strlen($job) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:user:job:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You must set the job less than 50 character"), 'job', 'arguments');
			return false;
		}

		$passportcode = utility::request('passportcode');
		if($passportcode && mb_strlen($passportcode) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:user:passportcode:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You must set the passportcode less than 50 character"), 'passportcode', 'arguments');
			return false;
		}

		$passportexpire = utility::request('passportexpire');
		if($passportexpire && mb_strlen($passportexpire) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:user:passportexpire:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You must set the passportexpire less than 50 character"), 'passportexpire', 'arguments');
			return false;
		}

		$paymentaccountnumber = utility::request('paymentaccountnumber');
		if($paymentaccountnumber && mb_strlen($paymentaccountnumber) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:user:paymentaccountnumber:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You must set the paymentaccountnumber less than 50 character"), 'paymentaccountnumber', 'arguments');
			return false;
		}

		$shaba = utility::request('shaba');
		if($shaba && mb_strlen($shaba) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:user:shaba:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You must set the shaba less than 50 character"), 'shaba', 'arguments');
			return false;
		}

		$cardnumber = utility::request('cardnumber');
		if($cardnumber && mb_strlen($cardnumber) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:user:cardnumber:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("You must set the cardnumber less than 50 character"), 'cardnumber', 'arguments');
			return false;
		}

		// we never get password password
		// the password only get in enter

		$email = utility::request('email');
		if($email && mb_strlen($email) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:user:email:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Email is incorrect"), 'email', 'arguments');
			return false;
		}

		$parent = utility::request('parent');
		$parent = utility\shortURL::decode($parent);
		if(!$parent && utility::request('parent'))
		{
			if($_args['save_log']) logs::set('addon:api:user:parent:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Parent is incorrect"), 'parent', 'arguments');
			return false;
		}

		$permission = utility::request('permission');
		if($permission && mb_strlen($permission) > 900)
		{
			if($_args['save_log']) logs::set('addon:api:user:permission:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Permission is incorrect"), 'permission', 'arguments');
			return false;
		}

		$username = utility::request('username');
		if($username && mb_strlen($username) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:user:username:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Username is incorrect"), 'username', 'arguments');
			return false;
		}

		$group = utility::request('group');
		if($group && mb_strlen($group) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:user:group:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Group is incorrect"), 'group', 'arguments');
			return false;
		}

		$pin = utility::request('pin');
		if(($pin && mb_strlen($pin) > 4) || ($pin && !is_numeric($pin)))
		{
			if($_args['save_log']) logs::set('addon:api:user:pin:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Pin is incorrect"), 'pin', 'arguments');
			return false;
		}

		$ref = utility::request('ref');
		$ref = utility\shortURL::decode($ref);
		if(!$ref && utility::request('ref'))
		{
			if($_args['save_log']) logs::set('addon:api:user:ref:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Ref is incorrect"), 'ref', 'arguments');
			return false;
		}

		if(utility::isset_request('twostep'))
		{
			$twostep = utility::request('twostep');
			$twostep = $twostep ? 1 : 0;
		}

		$notification = utility::request('notification');
		if($notification && mb_strlen($notification) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:user:notification:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Notification is incorrect"), 'notification', 'arguments');
			return false;
		}

		if(utility::isset_request('setup'))
		{
			$setup = utility::request('setup');
			$setup = $setup ? 1 : 0;
		}

		$nationality = utility::request('nationality');
		if($nationality && mb_strlen($nationality) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:user:nationality:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Nationality is incorrect"), 'nationality', 'arguments');
			return false;
		}

		$region = utility::request('region');
		if($region && mb_strlen($region) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:user:region:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Region is incorrect"), 'region', 'arguments');
			return false;
		}

		$insurancetype = utility::request('insurancetype');
		if($insurancetype && mb_strlen($insurancetype) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:user:insurancetype:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Insurancetype is incorrect"), 'insurancetype', 'arguments');
			return false;
		}

		$insurancecode = utility::request('insurancecode');
		if($insurancecode && mb_strlen($insurancecode) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:user:insurancecode:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Insurancecode is incorrect"), 'insurancecode', 'arguments');
			return false;
		}

		$dependantscount = utility::request('dependantscount');
		if($dependantscount && mb_strlen($dependantscount) > 50)
		{
			if($_args['save_log']) logs::set('addon:api:user:dependantscount:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Dependantscount is incorrect"), 'dependantscount', 'arguments');
			return false;
		}

		$unit_id = utility::request('unit_id');
		if($unit_id && !is_numeric($unit_id))
		{
			if($_args['save_log']) logs::set('addon:api:user:unit_id:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Unit id is incorrect"), 'unit_id', 'arguments');
			return false;
		}

		$language = utility::request('language');
		if($language && !\lib\language::check($language))
		{
			if($_args['save_log']) logs::set('addon:api:user:language:max:lenght', $this->user_id, $log_meta);
			if($_args['debug']) debug::error(T_("Language is incorrect"), 'language', 'arguments');
			return false;
		}


		$args['marital']              = $marital;
		$args['gender']               = $gender;
		$args['status']               = $status;
		$args['type']                 = $type;
		$args['fileid']               = $file_id;
		$args['fileurl']              = $file_url;
		$args['email']                = trim($email);
		$args['parent']               = trim($parent);
		$args['permission']           = trim($permission);
		$args['username']             = trim($username);
		$args['group']                = trim($group);
		$args['pin']                  = trim($pin);
		$args['ref']                  = trim($ref);
		$args['notification']         = trim($notification);
		$args['nationality']          = trim($nationality);
		$args['region']               = trim($region);
		$args['insurancetype']        = trim($insurancetype);
		$args['insurancecode']        = trim($insurancecode);
		$args['dependantscount']      = trim($dependantscount);
		$args['unit_id']              = trim($unit_id);
		$args['language']             = trim($language);
		$args['childcount']           = trim($child);
		$args['birthplace']           = trim($birthplace);
		$args['shfrom']               = trim($shfrom);
		$args['shcode']               = trim($shcode);
		$args['education']            = trim($education);
		$args['job']                  = trim($job);
		$args['passportcode']         = trim($passportcode);
		$args['passportexpire']       = trim($passportexpire);
		$args['paymentaccountnumber'] = trim($paymentaccountnumber);
		$args['cardnumber']           = trim($cardnumber);
		$args['shaba']                = trim($shaba);
		$args['nationalcode']         = trim($nationalcode);
		$args['father']               = trim($father);
		$args['birthday']             = trim($birthday);
		$args['postion']              = trim($postion);
		$args['personnelcode']        = trim($personnelcode);
		$args['name']                 = trim($firstname);
		$args['lastname']             = trim($lastname);

		if($displayname)
		{
			$args['displayname']    = trim($displayname);
		}
		elseif($firstname || $lastname)
		{
			$args['displayname']    = trim($firstname. ' '. $lastname);
		}

		if(isset($twostep))
		{
			$args['twostep'] = $twostep;
		}

		if(isset($setup))
		{
			$args['setup'] = $setup;
		}
	}



	/**
	 * check args and make where
	 *
	 * @param      <type>  $_args      The arguments
	 * @param      <type>  $where      The where
	 * @param      <type>  $_log_meta  The log meta
	 */
	public function user_make_where($_args, &$where, $_log_meta)
	{
		$type = utility::request('type');
		if($type && is_string($type) || is_numeric($type))
		{
			$where['type'] = $type;
		}

		if(!$type && utility::isset_request('type'))
		{
			$where['type'] = null;
		}
	}
}
?>