<?php
namespace dash\app\member;

class export
{
	public static function csv($_data)
	{
		if(!is_array($_data))
		{
			return false;
		}

		$result = [];
		foreach ($_data as $key => $value)
		{
			$temp[T_('id')]                            = @$value['id'];
			$temp[T_(ucfirst('user_id'))]              = @$value['user_id'];
			$temp[T_(ucfirst('academy')). ' '. T_("id")] = @$value['academy_id'];
			if(isset($value['academy_slug']))
			{
				$temp[T_(ucfirst('slug'))]                 = @$value['academy_slug'];
				$temp[T_(ucfirst('academy'))]              = @$value['parentstring_title'];
			}

			$temp[T_(':expert')]                       = @$value['expert'] ? T_("Yes") : T_("No");
			$temp[T_(':teacher')]                      = @$value['teacher']? T_("Yes") : T_("No");
			$temp[T_(':student')]                      = @$value['student']? T_("Yes") : T_("No");
			$temp[T_(ucfirst('firstname'))]            = @$value['firstname'];
			$temp[T_(ucfirst('lastname'))]             = @$value['lastname'];
			$temp[T_(ucfirst('father'))]               = @$value['father'];
			$temp[T_(ucfirst('nationalcode'))]         = @\dash\utility\human::fitNumber($value['nationalcode'], false);
			$temp[T_(ucfirst('pasport code'))]         = @\dash\utility\human::fitNumber($value['pasportcode'], false);
			$temp[T_(ucfirst('birthdate'))]            = @\dash\datetime::fit($value['birthdate'], false, 'date');
			$temp[T_(ucfirst('Passport expire date'))] = @\dash\datetime::fit($value['pasportdate'], false, 'date');;
			$temp[T_(ucfirst('gender'))]               = @T_($value['gender']);
			$temp[T_(ucfirst('marital'))]              = @T_($value['marital']);
			$temp[T_(ucfirst('id number'))]            = @\dash\utility\human::fitNumber($value['shcode'], false);
			$temp[T_(ucfirst('birth city'))]           = @$value['birthcity'];
			$temp[T_(ucfirst('nationality'))]          = @\dash\utility\location\countres::get_localname($value['nationality'], true);
			$temp[T_(ucfirst('zip code'))]             = @\dash\utility\human::fitNumber($value['zipcode'], false);
			$temp[T_(ucfirst('education'))]            = @$value['education'];
			$temp[T_(ucfirst('education2'))]           = @$value['education2'];
			$temp[T_(ucfirst('education Course'))]     = @$value['educationcourse'];
			$temp[T_(ucfirst('city'))]                 = @\dash\utility\location\cites::get_key($value['city']);
			$temp[T_(ucfirst('province'))]             = @\dash\utility\location\provinces::get_localname($value['province'], true);
			$temp[T_(ucfirst('country'))]              = @\dash\utility\location\countres::get_localname($value['country']);
			$temp[T_(ucfirst('address'))]              = @$value['address'];
			$temp[T_(ucfirst('mobile'))]               = @\dash\utility\human::fitNumber($value['mobile'], false);
			$temp[T_(ucfirst('phone'))]                = @\dash\utility\human::fitNumber($value['phone'], false);
			$temp[T_(ucfirst('mobile 2'))]             = @\dash\utility\human::fitNumber($value['mobile2'], false);

			$temp[T_(ucfirst('permission'))]           = @$value['permission'];
			// $temp[T_(ucfirst('desc'))]              = @$value['desc'];
			$temp[T_(ucfirst('Issue Place'))]          = @$value['shfrom'];
			$temp[T_(ucfirst('email'))]                = @$value['email'];
			$temp[T_(ucfirst('code'))]                 = @\dash\utility\human::fitNumber($value['code'], false);
			// $temp[T_(ucfirst('allowgender'))]       = @$value['allowgender'];
			// $temp[T_(ucfirst('credit'))]            = @$value['credit'];
			$temp[T_(ucfirst('date created'))]         = @\dash\datetime::fit($value['datecreated'], true);
			$temp[T_(ucfirst('date modified'))]        = @\dash\datetime::fit($value['datemodified'], true);
			$result[]                                  = $temp;

		}
		return \dash\utility\export::csv_file(['name' => 'export_member', 'data' => $result]);
	}
}
?>