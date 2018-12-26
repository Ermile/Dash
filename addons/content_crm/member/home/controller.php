<?php
namespace content_crm\member\home;


class controller
{
	public static function routing()
	{
		\dash\permission::access('aMemberView');
		if(in_array(\dash\request::get('list'), ['student', 'teacher', 'expert']))
		{
			$query_string = \dash\request::get('q');
			if(!$query_string)
			{
				$query_string = null;
			}

			$student_search_meta = ['type' => \dash\request::get('list'), 'order' => 'desc'];

			$studentList       = \dash\app\member::list($query_string, $student_search_meta);
			$result            = [];
			$result['success'] = true;
			$result['result']  = [];

			foreach ($studentList as $key => $value)
			{
				$myName = '<img class="ui avatar image" src="'.  $value['avatar'] .'">';
				$myName .= '<span class="pRa10">'. \dash\utility\human::fitNumber($value['code'], false). '</span>';
				$myName .= '   '. $value['firstname']. ' <b>'. $value['lastname']. '</b> <small class="badge light mLa5">'. $value['father'].'</small>';

				$nationalcode = $value['nationalcode'];
				if(!$value['nationalcode'] && $value['pasportcode'])
				{
					$nationalcode = $value['pasportcode'];
				}

				$myName .= '<span class="badge mLa10 info2">'. \dash\utility\human::fitNumber($nationalcode, false). '</span>';

				if($value['mobile'])
				{
					$myName .= '<span class="description ">'. \dash\utility\human::fitNumber($value['mobile'], 'mobile'). '</span>';
				}




				$result['result'][] =
				[
					'name'  => $myName,
					'value' => $value['id'],
				];
			}

			$result = json_encode($result, JSON_UNESCAPED_UNICODE);
			echo $result;
			\dash\code::boom();
		}
	}
}
?>