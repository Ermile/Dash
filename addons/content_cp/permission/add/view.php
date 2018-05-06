<?php
namespace content_cp\permission\add;


class view
{
	public static function config()
	{
		\dash\data::page_title(T_("Permissions"));
		\dash\data::page_desc(T_("Set and config permission of users and allow them to do something."));


		\dash\data::perm_list(
			[
				'news'=>
				[
					'cp:news:view' =>
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
				],
				'terms'=>
				[
					'cp:terms:view' =>
					[
						'title' => 'مشاهده کلیدواژه',
						'cat' => 'cp',
						'subcat' => 'terms',
						'check' => false,
						'verify' => false,
						'require' => null,
					],
					'cp:terms:add'=>
					[
						'title' => 'افزودن کلیدواژه',
						'cat' => 'cp',
						'subcat' => 'terms',
						'check' => false,
						'verify' => false,
						'require' => ['cp:terms:view'],
					],
				]
			]
		);

	}
}
?>