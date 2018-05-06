<?php
namespace content_cp\permission;


class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Permissions"));
		\dash\data::page_desc(T_("Set and config permission of users and allow them to do something."));


		\dash\data::badge_link(\dash\url::this().'/add');
		\dash\data::badge_text(T_('Add new permission'));

		\dash\data::perm_group(
			[
				'admin'=> ['title' => 'مدیر کل'],
				'editor'=> ['title' => 'ویراستار'],
				'writer'=> ['title' => 'نویسنده'],
			]
		);
		\dash\data::perm_list(
			[
				'cp:news:view'=>
				[
					'title' => 'مشاهده خبر',
					'cat' => 'cp',
					'subcat' => 'news',
					'check' => false,
					'verify' => false,
					'require' => null,
				],
				'cp:news:add'=>
				[
					'title' => 'افزودن خبر',
					'cat' => 'cp',
					'subcat' => 'news',
					'check' => false,
					'verify' => false,
					'require' => ['cp:news:view'],
				],
				'cp:news:edit'=>
				[
					'title' => 'ویرایش خبر',
					'cat' => 'cp',
					'subcat' => 'news',
					'check' => false,
					'verify' => false,
					'require' => ['cp:news:view'],
				],
				'cp:news:delete'=>
				[
					'title' => 'حذف خبر',
					'cat' => 'cp',
					'subcat' => 'news',
					'check' => false,
					'verify' => false,
					'require' => ['cp:news:view'],
				],
			]
		);

	}
}
?>