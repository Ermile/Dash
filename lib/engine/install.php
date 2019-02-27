<?php
$result  = null;
$install = true;
$log     = null;

if(isset($_POST['nu']) && isset($_POST['np']))
{
	$install = false;

	if($_POST['nu'])
	{
		\dash\db::$db_user = $_POST['nu'];
		\dash\db::$db_pass = $_POST['np'];
	}

	$result = \dash\db::install();

	if($result)
	{
		// insert the admin mobile and set her permission as admin
		if(isset($_POST['mob']))
		{
			$mobile = \dash\utility\filter::mobile($_POST['mob']);
			if($mobile)
			{
				$add_user =
				[
					'mobile'     => $mobile,
					'permission' => 'supervisor',
				];

				$check_exist = \dash\db\users::get(array_merge($add_user, ['limit' => 1]));

				if(!$check_exist)
				{
					$insert = \dash\db\users::signup($add_user);
				}
			}
		}

		if(\dash\option::config('debug'))
		{
			$log = $result;
		}
	}
	else
	{
		$result = null;
	}
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
 <meta charset="UTF-8"/>
 <title>Installation</title>
 <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1"/>
 <link rel="shortcut icon" href="/static/images/favicon-error.png"/>
 <style>*{font-family:sans-serif;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;}body{width:90%;height:94%;margin:0 auto;padding:3% 5%;text-align:center;background-color:rgba(61, 174, 255, 0.1);color:#3DAEFF}a{color:#0091D6;}.slash{padding:0 2px;}ol{direction:ltr;font-size:14px;}li{padding-bottom:5px}.addr{font-size:11px; font-weight:normal;}#no{z-index:-1;position:absolute;bottom:5%;right:5%;opacity:0.9;width:250px}#smile{font-size:7em}img{max-width:100%;}.btn{position:relative;width:300px;margin:100px auto 0;font-size:1em}.btn a{text-decoration:none;background-color:#0091D6;color:#fff;font-size:1.5em;text-align:center;padding:5px 10%;border-radius:10px;position:relative;display:block;width:100%;z-index:1}.btn span{background-color:#222;color:#fff;font-size:.8em;text-align:center;padding:5px 10%;position:absolute;width:90%;right:0;left:0;margin:0 auto;max-width:100%;overflow:hidden;transition:.5s}.btn .top{top:0;border-top-right-radius:10px;border-top-left-radius:10px}.btn:hover .top{top:-60%}.btn .bottom{bottom:0;border-bottom-right-radius:10px;border-bottom-left-radius:10px}.btn:hover .bottom{bottom:-60%}.btn span:hover{overflow:visible}pre{overflow:auto;background-color:#eee;font-size:0.7em;padding:1%;} form{margin-top:5em;} form input{display:block;direction:ltr;background-color:#fff;border-radius:3px;border:none;width:300px;min-height:25px;margin:2px auto;padding:10px 5%;text-align:center;opacity:0.6;transition:all .3s ease;} form input:focus{opacity:1;} form input[type=submit]{background-color:#4B8DF8;color:#fff;border-radius:3px;margin-top:20px;font-size:1.6em;cursor:pointer;} pre{text-align:left;}
 </style>
</head>
<body>
 <h1>Dash Installation</h1>
 <progress></progress>
 <p>Welcome to Dash installation process.</p>
 <p>First of all set database connection detail on <b>config.php</b> then we do others!</p>


 <?php if($result) { ?>

 <div class="btn">
  <span class="top">Install Successfully:)</span>
  <a href="/">Lets GO!</a>
 </div>
<!--
 <pre class="result">
	 <?php  // print_r($log); ?>
 </pre> -->
 <?php } ?>


<?php if($install) { ?>
  <form method="post" autocomplete="off">
   <input type="text" name="nu" placeholder='Username of db admin'>
   <input type="password" name="np" placeholder='Password' autocomplete="new-password">
   <br>
   <p>Supervisor mobile number</p>
   <input type="tel" name="mob" placeholder='mobile of supervisor' style=" background: #eee" value="<?php echo isset($_GET['mobile']) ? $_GET['mobile'] : null; ?>" autocomplete="new-password">

   <input type="submit" value="Install">

  </form>
<?php } ?>

<div id="no"><img src="/static/images/logo.png" alt="Logo" id='logo'></div>
</body>
</html>
<?php \dash\code::boom(); ?>