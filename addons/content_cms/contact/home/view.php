<?php
namespace content_cms\contact\home;


class view
{
	public static function config()
	{
		\dash\permission::access('cpContactView');

		\dash\redirect::to(\dash\url::kingdom(). '/support/ticket?access=all&status=all&q='. T_("Contact Us"));
		return;

		// \dash\data::page_title(T_("Contact"));
		// \dash\data::page_desc(T_('Check list of contact and search or filter in them to find your contact.'). ' '. T_('Also edit specefic contact.'));
		// \dash\data::page_pictogram('comment-o');

		// // add back level to summary link
		// \dash\data::badge2_text(T_('Back to dashboard'));
		// \dash\data::badge2_link(\dash\url::here());


		// $search_string            = \dash\request::get('q');
		// if($search_string)
		// {
		// 	\dash\data::page_title(\dash\data::page_title(). ' | '. T_('Search for :search', ['search' => $search_string]));
		// }

		// $args =
		// [
		// 	'sort'  => \dash\request::get('sort'),
		// 	'order' => \dash\request::get('order'),
		// ];

		// if(\dash\request::get('status'))
		// {
		// 	$args['status'] = \dash\request::get('status');
		// }

		// if(!isset($args['status']))
		// {
		// 	$args['comments.status']     = ["NOT IN", "('cancel', 'draft', 'deleted')"];
		// }


		// $args['type'] = 'contact';

		// if(!$args['order'])
		// {
		// 	$args['order'] = 'DESC';
		// }

		// if(!$args['sort'])
		// {
		// 	$args['sort'] = 'id';
		// }


		// \dash\data::sortLink(\content_cms\view::make_sort_link(\dash\app\comment::$sort_field, \dash\url::this()));
		// \dash\data::dataTable(\dash\app\comment::list(\dash\request::get('q'), $args));

		// $filterArray = $args;
		// unset($filterArray['type']);
		// unset($filterArray['comments.status']);
		// if(isset($filterArray['status']))
		// {
		// 	if(is_string($filterArray['status']))
		// 	{
		// 		$filterArray[T_("Status")] = $filterArray['status'];
		// 	}
		// 	unset($filterArray['status']);
		// }

		// // set dataFilter
		// $dataFilter = \dash\app\sort::createFilterMsg($search_string, $filterArray);
		// \dash\data::dataFilter($dataFilter);


	}
}
?>