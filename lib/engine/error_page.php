<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
 <meta charset="UTF-8"/>
 <title>(>_<?php echo $_code;?>_<)</title>
 <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/><![endif]-->
 <meta name ="viewport" content="width=device-width, initial-scale=1.0, height=device-height, minimum-scale=1.0 maximum-scale=1.5, minimal-ui"/>
 <link rel="shortcut icon" href="/static/images/favicon-error.png"/>
 <!-- <linkss rel="stylesheet"  href="/static/siftal/css/siftal.css"> -->
 <link rel="stylesheet"  href="http://siftal.local/dist/css/siftal.css">
</head>
<body class='<?php echo (\dash\language::current('direction')); ?> errorPage s<?php echo(substr($_code,0,2));?>'>
 <div id="nodes"></div>
 <div class="cn">
  <div class="wrapper">
   <div class="f">
    <div class="c s12">
     <h1><?php echo $translatedDesc;?></h1>
     <h2><?php echo $_text;?></h2>
     <a href="<?php echo(\dash\url::site()? \dash\url::site(): '/'); ?>" class='btn lg secondary'><?php echo T_("Return to Homepage")?></a>
    </div>
    <div class="cauto os s12">
     <a href="/" id='ermileBadge' class="f">
      <div class="cauto">
       <img src="/static/images/logo.png" alt='error on azvir' class="cauto">
      </div>
<?php if(\dash\url::domain())
{ ?>
      <div class="c pLa10 f align-center">
       <h2><?php echo(ucfirst(\dash\url::domain()));?></h2>
      </div>
<?php
} ?>
     </a>
    </div>
   </div>
<?php if(\dash\option::config('debug') || \dash\url::isLocal()) {?>
   <ol>
<?php
$debug_backtrace = array_reverse($debug_backtrace);
foreach ($debug_backtrace as $key => $value):?>
<?php
  $fileaddr = isset($debug_backtrace[$key]['file'])? $debug_backtrace[$key]['file']: null;
  if($fileaddr)
  {
    $fileaddr = substr($fileaddr ,mb_strlen(core)-mb_strlen(core_name)-1);
    $FILE = $fileaddr;
    $FILE = str_replace("/", "<span class='slash'>/</span>", $FILE);
    $FILE = str_replace("\\", "<span class='slash'>/</span>", $FILE);
?>
     <li><?php echo $FILE.": Line ".$debug_backtrace[$key]['line'];?></li>
<?php } ?>
<?php endforeach; ?>
    </ol>
<?php } else {?>
   <div id="smile">:(</div>
<?php } ?>


  </div>
 </div>
 <div id="no"><?php echo $_code?></div>

 <script src="http://siftal.local/dist/js/error_page.js"></script>
</body>
</html>