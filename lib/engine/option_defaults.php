<?php
/**
* The base configurations of the Dash.
*/


/**
 * try to fix url and set some settings of them like slash, www or fix tld or use main domain
 */
// on set below value to off, skip all of this part
self::$url['fix']   = true;

// if true set www else try to remove from url
self::$url['www']   = false;

// if true add slash at end of url, else remove
self::$url['slash'] = false;

// if set below value of host, try to fix them
// self::$url['protocol'] = 'https';
// self::$url['root']     = 'ermile';
// self::$url['tld']      = 'com';
// self::$url['port']     = 80;


// ----- debug
self::$config['debug']         = false;

// ------ can set upload subdomain to set in upload file
// for example set upload_subdomain on 'dl' to make dl.domain.tld
// self::$config['upload_subdomain']  = null;

// max upload file size
// byte
// self::$config['max_upload'] = 100 * 1024 * 1024; // 100 MB;


self::$config['site']['title']  = "Ermile Dash";
self::$config['site']['desc']   = "Another Project with Ermile dash";
self::$config['site']['slogan'] = "Ermile is intelligent ;)";

// self::$config['dev']           = [];
// self::$config['dev']['siftal'] = false;
// self::$config['dev']['dash']   = false;

// the project have help center or no
self::$config['help_center']         = true;


// self::$config['default_payment'] = "[paymentName]";

// ----- language
self::$language           =
[
	'default'                 => 'en',
	'list'                    => ['fa','en']
];

// check ip api key
// http://botscout.com
self::$config['botscout'] = null;


// the project have no any subdomain
// in ticket not load in all subdomain
self::$config['no_subdomain'] = true;

// free subdomain
// for set cookie not in
//self::$config['free_subdomain']    = true;


// self::$config['crop_size']['wide']     = [1200, 1200];
// self::$config['crop_size']['verywide'] = [1600, 1800];
// self::$config['crop_size']['smally']   = [16, 16];
// self::$config['crop_size']['extera']   = [1600, 100];

// self::$config['notif']['image'] = '/static/images/logo.png';

// default tag of slider
// self::$config['slider_tag'][]  = "slider";

// try to find database template module
// for example posts, tags, term , ...
self::$config['cms'] = true;

// redirect to this path after login
self::$config['redirect']    = null;

// below values must be @check
// ---------------------------------------------------------

// self::$config['short_url']                             = null;
self::$config['visitor']                          = false;
// self::$config['passphrase']                            = null;
// self::$config['passkey']                               = null;
// self::$config['passvalue']                             = null;
// self::$config['default']                               = null;
// self::$config['register']                              = false;
// self::$config['recovery']                              = false;
// self::$config['sms']                                   = false;
// self::$config['account']                               = false;

// cronjob status
self::$config['cronjob']['status'] = true;

self::$config['allow_post_type'] =
[
	'post',
	'page',
	'help',
	'attachment',
	'meeting',
];

// load billing page
// in some project we need to lock this page
self::$config['billing_page'] = false;
self::$config['billing_promo'] = false;
self::$config['address_page'] = false;

// ----- comming soon page
self::$config['coming']                                = false;

// ---- ftp options
// self::$config['ftp']['host']                           = null;
// self::$config['ftp']['port']                           = null;
// self::$config['ftp']['user']                           = null;
// self::$config['ftp']['pass']                           = null;

// ----- unit options
self::$config['units']                                 =
[
1                                                      =>
[
'title'                                                => 'toman',
'desc'                                                 => "Toman",
],

2                                                      =>
[
'title'                                                => 'dollar',
'desc'                                                 => "$",
],
];
// -----  the unit id for default
self::$config['default_unit']                          = 1;
// force change unit to this unit
self::$config['force_unit']                            = 1;



// ----- enter options
// self::$config['enter']['force_verify']                = true;

// every wrong pass or code wate for ? [second]
self::$config['enter']['wait']                         = 10;
// life time code for ? [second]
self::$config['enter']['life_time_code']               = 60 * 5;
// after signup user redirect to different page
// self::$config['enter']['signup_redirect']              = null;
// after signup user redirect to different page
self::$config['enter']['singup_username']              = false;
// save remember me to login
self::$config['enter']['remember_me']              = true;
// after login redirect to what?
// self::$config['enter']['redirect']                     = null;
// check if call mode is enable to call to user
// self::$config['enter']['call']                         = false;
// get template of call for every language
// self::$config['enter']['call_template']['fa']          = null;
// self::$config['enter']['call_template']['en']          = null;


self::$config['enter']['verify_telegram']              = true;
self::$config['enter']['verify_sms']                   = true;
self::$config['enter']['verify_call']                  = true;
// self::$config['enter']['verify_sendsms']               = false;


// ----- favicon option
self::$config['favicon']['complete']                   = true;
self::$config['favicon']['version']                    = 1;


// ----- social network options
// self::$social['status']                                = false;

// ---- list
// self::$social['list']['telegram']                      = null;
// self::$social['list']['facebook']                      = null;
// self::$social['list']['twitter']                       = null;
// self::$social['list']['googleplus']                    = null;
// self::$social['list']['github']                        = null;
// self::$social['list']['linkedin']                      = null;
// self::$social['list']['aparat']                        = null;

/**
* TELEGRAM
* t.me
*/
// self::$social['telegram']['status']                    = false;
// self::$social['telegram']['name']                      = null;
// self::$social['telegram']['key']                       = null;
// self::$social['telegram']['bot']                       = null;
// self::$social['telegram']['hookFolder']                = null;
// self::$social['telegram']['hook']                      = null;
// self::$social['telegram']['debug']                     = false;
// self::$social['telegram']['channel']                   = null;
// self::$social['telegram']['botan']                     = null;

/**
* FACEBOOK
*/
// self::$social['facebook']['status']                    = false;
// self::$social['facebook']['name']                      = null;
// self::$social['facebook']['key']                       = null;
// self::$social['facebook']['app_id']                    = null;
// self::$social['facebook']['app_secret']                = null;
// self::$social['facebook']['redirect_url']              = null;
// self::$social['facebook']['required_scope']            = null;
// self::$social['facebook']['page_id']                   = null;
// self::$social['facebook']['access_token']              = null;
// self::$social['facebook']['client_token']              = null;

/**
* TWITTER
*/
// self::$social['twitter']['status']                     = false;
// self::$social['twitter']['name']                       = null;
// self::$social['twitter']['key']                        = null;
// self::$social['twitter']['ConsumerKey']                = null;
// self::$social['twitter']['ConsumerSecret']             = null;
// self::$social['twitter']['AccessToken']                = null;
// self::$social['twitter']['AccessTokenSecret']          = null;

/**
* GOOGLE PLUS
*/
// self::$social['googleplus']['status']                  = false;
// self::$social['googleplus']['name']                    = null;
// self::$social['googleplus']['key']                     = null;


/**
* GITHUB
*/
// self::$social['github']['status']                      = false;
// self::$social['github']['name']                        = null;
// self::$social['github']['key']                         = null;


/**
* LINKDIN
*/
// self::$social['linkedin']['status']                    = false;
// self::$social['linkedin']['name']                      = null;
// self::$social['linkedin']['key']                       = null;


/**
* APARAT
*/
// self::$social['aparat']['status']                      = false;
// self::$social['aparat']['name']                        = null;
// self::$social['aparat']['key']                         = null;


// ------ sms settions
/**
* sms kavenegar config
*/
// self::$sms['kavenegar']['status']                      = false;
// self::$sms['kavenegar']['apikey']                      = null;
// self::$sms['kavenegar']['debug']                       = null;
// self::$sms['kavenegar']['line']                        = null;
// self::$sms['kavenegar']['iran']                        = true;
// self::$sms['kavenegar']['header']                      = null;
// self::$sms['kavenegar']['footer']                      = null;
// self::$sms['kavenegar']['one']                         = false;




/**
* cofig of cloudflare
*/
// Origin CA Key View API KeyChange API Key
// self::$config['cloudflare']['status']                  = false;
// self::$config['cloudflare']['ZoneID']                  = null;
// self::$config['cloudflare']['X-Auth-Email']            = null;
// self::$config['cloudflare']['X-Auth-Key']              = null;
// self::$config['cloudflare']['X-Auth-User-Service-Key'] = null;


// /**
// * send notification status
// */
// self::$config['notification']['status']                = true;
// self::$config['notification']['sort']                  =
// [
// 'telegram',
// 'email',
// 'sms',
// ];


// /**
// * the notification cats
// */
// self::$config['notification']['cat']                   = [];
// // news
// self::$config['notification']['cat'][1]                =
// [
// 'title'                                                => 'news',
// 'send_by'                                              =>
// [
// 'telegram',
// 'email',
// ],
// ];
// // invoice
// self::$config['notification']['cat'][2]                =
// [
// 'title'                                                => 'invoice',
// 'send_by'                                              =>
// [
// 'telegram',
// 'email',
// ],
// ];



/**
* transaction code
*/
// self::$config['transactions_code'][100]                = null;
// self::$config['transactions_code'][150]                = null;


// ----- payment options
/**
* cofig of zarinpal payment
*/
// self::$config['zarinpal']['status']                    = false;
// self::$config['zarinpal']['MerchantID']                = null;
// self::$config['zarinpal']['Description']               = null;
// self::$config['zarinpal']['CallbackURL']               = null;
// self::$config['zarinpal']['exchange']                  = 1;


/**
* cofig op pay.ir
*/
// self::$config['payir']['status']                       = false;
// self::$config['payir']['api']                          = null;
// self::$config['payir']['redirect']                     = null;


/**
* config of parsian payment
*/
// self::$config['parsian']['status']                     = false;
// self::$config['parsian']['LoginAccount']               = null;
// self::$config['parsian']['CallBackUrl']                = null;



/**
* config of irkish payment
*/
// self::$config['irkish']['status']                      = false;
// self::$config['irkish']['merchantId']                  = null;
// self::$config['irkish']['revertURL']                   = null;
// self::$config['irkish']['description']                 = null;
// self::$config['irkish']['paymentId']                   = null;
// self::$config['irkish']['sha1']                        = null;


// self::$config['asanpardakht']['status']                = false;
// self::$config['asanpardakht']['MerchantID']            = null;
// self::$config['asanpardakht']['MerchantConfigID']      = null;
// self::$config['asanpardakht']['Username']              = null;
// self::$config['asanpardakht']['Password']              = null;
// self::$config['asanpardakht']['EncryptionKey']         = null;
// self::$config['asanpardakht']['EncryptionVector']      = null;
// self::$config['asanpardakht']['MerchantIP']            = null;
// self::$config['asanpardakht']['MerchantName']          = null;

// self::$config['mellat']['status']       = false;
// self::$config['mellat']['TerminalId']   = null;
// self::$config['mellat']['UserName']     = null;
// self::$config['mellat']['UserPassword'] = null;



// self::$config['sep']['status']   = false;
// self::$config['sep']['MID']      = null;
// self::$config['sep']['Password'] = null;



?>