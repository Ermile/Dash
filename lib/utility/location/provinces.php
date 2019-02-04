<?php
namespace dash\utility\location;
/** province managing **/
class provinces
{
	use tools;
	public static $data =
[
	'IR-01' => ['country' => 'IR', 'slug' => 'iran-east-azerbaijan', 'name' => 'east azerbaijan', 'localname' => 'آذربایجان شرقی',  'map_code' => 'ir-wa', 'population' => 3724620, 'width' => '37°9035733', 'length' => '46°2682109', 'phone_code' => 41,],
	'IR-02' => ['country' => 'IR', 'slug' => 'iran-west-azerbaijan', 'name' => 'west azerbaijan', 'localname' => 'آذربایجان غربی', 'map_code' => 'ir-ea', 'population' => 3080576, 'width' => '37°4550062', 'length' => '45°00', 'phone_code' => 44, ],
	'IR-03' => ['country' => 'IR', 'slug' => 'iran-ardabil', 'name' => 'ardabil', 'localname' => 'اردبیل', 'map_code' => 'ir-ar', 'population' => 1248488, 'width' => '38°4853276', 'length' => '47°8911209', 'phone_code' => 45, ],
	'IR-04' => ['country' => 'IR', 'slug' => 'iran-isfahan', 'name' => 'isfahan', 'localname' => 'اصفهان', 'map_code' => 'ir-es', 'population' => 4879312, 'width' => '32°6546275', 'length' => '51°6679826', 'phone_code' => 31, ],
	'IR-05' => ['country' => 'IR', 'slug' => 'iran-ilam', 'name' => 'ilam', 'localname' => 'ایلام', 'map_code' => 'ir-il', 'population' => 557599, 'width' => '33°2957618', 'length' => '46°670534', 'phone_code' => 84, ],
	'IR-06' => ['country' => 'IR', 'slug' => 'iran-bushehr', 'name' => 'bushehr', 'localname' => 'بوشهر', 'map_code' => 'ir-bs', 'population' => 1032949, 'width' => '28°9233837', 'length' => '50°820314', 'phone_code' => 77, ],
	'IR-07' => ['country' => 'IR', 'slug' => 'iran-tehran', 'name' => 'tehran', 'localname' => 'تهران', 'map_code' => 'ir-th', 'population' => 12183391, 'width' => '35°696111', 'length' => '51°423056', 'phone_code' => 21, ],
	'IR-08' => ['country' => 'IR', 'slug' => 'iran-chaharmahal-and-bakhtiari', 'name' => 'chaharmahal and bakhtiari', 'localname' => 'چهارمحال و بختیاری', 'map_code' => 'ir-cm', 'population' => 895263, 'width' => '31°9614348', 'length' => '50°8456323', 'phone_code' => 38, ],
	'IR-10' => ['country' => 'IR', 'slug' => 'iran-khuzestan', 'name' => 'khuzestan', 'localname' => 'خوزستان', 'map_code' => 'ir-kz', 'population' => 4531720, 'width' => '31°4360149', 'length' => '49°041312', 'phone_code' => 61, ],
	'IR-11' => ['country' => 'IR', 'slug' => 'iran-zanjan', 'name' => 'zanjan', 'localname' => 'زنجان', 'map_code' => 'ir-za', 'population' => 1015734, 'width' => '36°5018185', 'length' => '48°3988186', 'phone_code' => 24, ],
	'IR-12' => ['country' => 'IR', 'slug' => 'iran-semnan', 'name' => 'semnan', 'localname' => 'سمنان', 'map_code' => 'ir-sm', 'population' => 631218, 'width' => '35°2255585', 'length' => '54°4342138', 'phone_code' => 23, ],
	'IR-13' => ['country' => 'IR', 'slug' => 'iran-sistan-and-baluchestan', 'name' => 'sistan and baluchestan', 'localname' => 'سیستان و بلوچستان', 'map_code' => 'ir-sb', 'population' => 2534327, 'width' => '27°5299906', 'length' => '60°5820676', 'phone_code' => 54, ],
	'IR-14' => ['country' => 'IR', 'slug' => 'iran-fars', 'name' => 'fars', 'localname' => 'فارس', 'map_code' => 'ir-fa', 'population' => 4596658, 'width' => '29°1043813', 'length' => '53°045893', 'phone_code' => 71, ],
	'IR-15' => ['country' => 'IR', 'slug' => 'iran-kerman', 'name' => 'kerman', 'localname' => 'کرمان', 'map_code' => 'ir-ke', 'population' => 2938988, 'width' => '30°2839379', 'length' => '57°0833628', 'phone_code' => 34, ],
	'IR-16' => ['country' => 'IR', 'slug' => 'iran-kordestan', 'name' => 'kordestan', 'localname' => 'کردستان', 'map_code' => 'ir-kd', 'population' => 1493645, 'width' => '35°9553579', 'length' => '47°1362125', 'phone_code' => 87, ],
	'IR-17' => ['country' => 'IR', 'slug' => 'iran-kermanshah', 'name' => 'kermanshah', 'localname' => 'کرمانشاه', 'map_code' => 'ir-bk', 'population' => 1945227, 'width' => '34°314167', 'length' => '47°065', 'phone_code' => 83, ],
	'IR-18' => ['country' => 'IR', 'slug' => 'iran-kohgiluyeh-and-boyerahmad', 'name' => 'kohgiluyeh and boyerahmad', 'localname' => 'کهگیلویه و بویراحمد', 'map_code' => 'ir-kb', 'population' => 658629, 'width' => '30°6509479', 'length' => '51°60525', 'phone_code' => 74, ],
	'IR-19' => ['country' => 'IR', 'slug' => 'iran-gilan', 'name' => 'gilan', 'localname' => 'گیلان', 'map_code' => 'ir-gi', 'population' => 2480874, 'width' => '37°1171617', 'length' => '49°5279996', 'phone_code' => 13, ],
	'IR-20' => ['country' => 'IR', 'slug' => 'iran-lorestan', 'name' => 'lorestan', 'localname' => 'لرستان', 'map_code' => 'ir-lo', 'population' => 1754243, 'width' => '33°5818394', 'length' => '48°3988186', 'phone_code' => 66, ],
	'IR-21' => ['country' => 'IR', 'slug' => 'iran-mazandaran', 'name' => 'mazandaran', 'localname' => 'مازندران', 'map_code' => 'ir-mn', 'population' => 3073943, 'width' => '36°2262393', 'length' => '52°5318604', 'phone_code' => 11, ],
	'IR-22' => ['country' => 'IR', 'slug' => 'iran-markazi', 'name' => 'markazi', 'localname' => 'مرکزی', 'map_code' => 'ir-mk', 'population' => 1413959, 'width' => '33°5093294', 'length' => '-92°396119', 'phone_code' => 86, ],
	'IR-23' => ['country' => 'IR', 'slug' => 'iran-hormozgan', 'name' => 'hormozgan', 'localname' => 'هرمزگان', 'map_code' => 'ir-hg', 'population' => 1578183, 'width' => '27°138723', 'length' => '55°1375834', 'phone_code' => 76, ],
	'IR-24' => ['country' => 'IR', 'slug' => 'iran-hamedan', 'name' => 'hamedan', 'localname' => 'همدان', 'map_code' => 'ir-hd', 'population' => 1758268, 'width' => '34°7607999', 'length' => '48°3988186', 'phone_code' => 81, ],
	'IR-25' => ['country' => 'IR', 'slug' => 'iran-yazd', 'name' => 'yazd', 'localname' => 'یزد', 'map_code' => 'ir-ya', 'population' => 1074428, 'width' => '32°1006387', 'length' => '54°4342138', 'phone_code' => 35, ],
	'IR-26' => ['country' => 'IR', 'slug' => 'iran-qom', 'name' => 'qom', 'localname' => 'قم', 'map_code' => 'ir-qm', 'population' => 1151672, 'width' => '34°6399443', 'length' => '50°8759419', 'phone_code' => 25, ],
	'IR-27' => ['country' => 'IR', 'slug' => 'iran-golestan', 'name' => 'golestan', 'localname' => 'گلستان', 'map_code' => 'ir-go', 'population' => 1777014, 'width' => '37°2898123', 'length' => '55°1375834', 'phone_code' => 17, ],
	'IR-28' => ['country' => 'IR', 'slug' => 'iran-qazvin', 'name' => 'qazvin', 'localname' => 'قزوین', 'map_code' => 'ir-qz', 'population' => 1201565, 'width' => '36°0881317', 'length' => '49°8547266', 'phone_code' => 28, ],
	'IR-29' => ['country' => 'IR', 'slug' => 'iran-south-khorasan', 'name' => 'south khorasan', 'localname' => 'خراسان جنوبی', 'map_code' => 'ir-kj', 'population' => 662534, 'width' => '32°5175643', 'length' => '59°1041758', 'phone_code' => 56, ],
	'IR-30' => ['country' => 'IR', 'slug' => 'iran-razavi-khorasan', 'name' => 'razavi khorasan', 'localname' => 'خراسان رضوی', 'map_code' => 'ir-kv', 'population' => 5994402, 'width' => '35°1020253', 'length' => '59°1041758', 'phone_code' => 51, ],
	'IR-31' => ['country' => 'IR', 'slug' => 'iran-north-khorasan', 'name' => 'north khorasan', 'localname' => 'خراسان شمالی', 'map_code' => 'ir-ks', 'population' => 867727, 'width' => '37°4710353', 'length' => '57°1013188', 'phone_code' => 58, ],
	'IR-32' => ['country' => 'IR', 'slug' => 'iran-alborz', 'name' => 'alborz', 'localname' => 'البرز', 'map_code' => 'ir-al', 'population' => 2412513, 'width' => '35°9960467', 'length' => '50°9289246', 'phone_code' => 26, ],
];

}
?>