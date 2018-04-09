<?php
namespace dash\utility;
require_once (addons.'lib/social/google/libraries/Google/autoload.php');

class google
{

     //Insert your cient ID and secret
     //You can get it from : https://console.developers.google.com/
     private static $client_id     = null;
     private static $client_secret = null;
     private static $redirect_url  = null;
     private static $client        = null;
     private static $service       = null;
     private static $authUrl       = null;
     private static $userinfo      = null;

     /**
     * ready to connect to google
     */
     private static function config()
     {
          if(!\dash\option::social('google', 'status'))
          {
               return false;
          }
          /**
           * get client id
           */
          self::$client_id     = \dash\option::social('google', 'client_id');
          /**
           * get client secret
           */
          self::$client_secret = \dash\option::social('google', 'client_secret');

          if(\dash\option::social('google', 'redirect_url'))
          {
               self::$redirect_url  = \dash\option::social('google', 'redirect_url');
          }
          else
          {
               // self::$redirect_url  = \dash\url::protocol(). '://';
               // self::$redirect_url .= \dash\url::host();
               // self::$redirect_url .= '/google';
               self::$redirect_url = \dash\url::here(). '/google';
          }

          /************************************************
          Make an API request on behalf of a user. In
          this case we need to have a valid OAuth 2.0
          token for the user, so we need to send them
          through a login flow. To do this we need some
          information from our API console project.
          ************************************************/
          self::$client = new \Google_Client();
          self::$client->setClientId(self::$client_id);
          self::$client->setClientSecret(self::$client_secret);
          self::$client->setRedirectUri(self::$redirect_url);
          self::$client->addScope("email");
          self::$client->addScope("profile");

          /************************************************
          When we create the service here, we pass the
          client to it. The client then queries the service
          for the required scopes, and uses that when
          generating the authentication URL later.
          ************************************************/
          self::$service = new \Google_Service_Oauth2(self::$client);

          return true;
     }


     /**
      * return auth url
      */
     public static function auth_url()
     {
          if(!self::config())
          {
               return false;
          }

          self::$authUrl = self::$client->createAuthUrl();
          return self::$authUrl;
     }


     /**
     * check access token
     *
     * @return     boolean  ( description_of_the_return_value )
     */
     public static function check()
     {
          /************************************************
          If we have a code back from the OAuth 2.0 flow,
          we need to exchange that with the authenticate()
          function. We store the resultant access token
          bundle in the session, and redirect to ourself.
          */
          if(isset($_GET['code']))
          {
               if(!self::config())
               {
                    return false;
               }
               self::$client->authenticate($_GET['code']);

               $access = self::$client->getAccessToken();

               if($access)
               {
                    self::$client->setAccessToken($access);

                    self::$userinfo = self::$service->userinfo->get();

                    return true;
               }
               else
               {
                    \dash\notif::error(T_("Invalid access token"));
                    return false;
               }
          }
          return false;
     }


     /**
      * get user info
      *
      * @return     <type>  ( description_of_the_return_value )
      */
     public static function user_info($_key = null)
     {
          if(!$_key)
          {
               return self::$userinfo;
          }
          else
          {
               if(isset(self::$userinfo[$_key]))
               {
                    return self::$userinfo[$_key];
               }
               else
               {
                    return null;
               }
          }
     }
}
?>