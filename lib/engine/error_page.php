<!DOCTYPE html>
<html>
<head>
 <meta charset="UTF-8"/>
 <title>(>_<?php echo $_code;?>_<)</title>
 <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=no"/>
 <link rel="shortcut icon"    href="/static/images/favicon-error.png"/>
 <style type="text/css">
	*{font-family:sans-serif;box-sizing:border-box;}
	html{min-height:100%;text-rendering:optimizeLegibility;-webkit-font-smoothing:antialiased;}
	body{width:90%;margin:0 auto;padding:3% 0;background-color:#0e5860;color:#fff}
	body.s50{background-color:#750700;}
	a{color:#eee;}
	.slash{padding:0 2px;}
	ol{direction:ltr;font-size:14px;}
	li{padding-bottom:5px}
	.addr{font-size:11px; font-weight:normal;}
	#no{z-index:-1;position:absolute;bottom:5%;right:5%;opacity:0.3;line-height:1;font-size:100px;font-size:20vw;animation:pulse 2s infinite ease-in-out;user-select:none;}
	#smile{font-size:7em}
	.site{display:inline-block;margin-top:3em;user-select:none;}
	@keyframes pulse { 0%{opacity:.3} 50%{opacity:.5} 100%{opacity:.3}}
</style>
</head>
<body class='s<?php echo(substr($_code,0,2));?>'>

 <h1><?php echo $desc?></h1>
 <b class='slash'><?php echo $_title; ?></b>
<?php if(defined("DEBUG") && DEBUG && \lib\url::isLocal()  ) {?>
 <ol>
<?php
$debug_backtrace = array_reverse($debug_backtrace);
foreach ($debug_backtrace as $key => $value):?>
<?php
  $fileaddr = isset($debug_backtrace[$key]['file'])? $debug_backtrace[$key]['file']: null;
  if($fileaddr)
  {
  	$fileaddr = substr($fileaddr ,mb_strlen(core)-mb_strlen(core_name)-1);
    $FILE = '<span class="addr">'.$fileaddr;
    $FILE = str_replace("/", "<span class='slash'>/</span>", $FILE);
    $FILE = preg_replace("/([^\/<>]+)$/", "</span>$1", $FILE);
?>
   <li><?php echo $FILE.": Line ".$debug_backtrace[$key]['line'];?></li>
<?php } ?>
<?php endforeach; ?>
  </ol>
<?php } else {?>
 <div id="smile">:(</div>
<?php } ?>

 <a href="<?php echo \lib\url::site(); ?>" class='site'>Return to Homepage</a>

 <div id="no"><?php echo $_code?></div>
</body>
</html>