<!DOCTYPE html>
<html lang="<?php echo (\dash\language::current()); ?>" dir="ltr">
<head>
 <meta charset="UTF-8"/>
 <title><?php echo T_('Redirect');?> >>></title>
 <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/><![endif]-->
 <meta name ="viewport" content="width=device-width, initial-scale=1.0, height=device-height, minimum-scale=1.0 maximum-scale=1.5, minimal-ui"/>
 <link rel="shortcut icon" href="<?php echo(\dash\url::static()); ?>/images/favicon-error.png"/>
 <link rel="stylesheet"  href="<?php echo(\dash\url::siftal()); ?>/css/siftal.css?v=3">
 <meta http-equiv="refresh" content="2; URL='<?php echo($_loc);?>'">
</head>
<body class='redirecting'>

  <div class='longfazers'>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
  </div>
  <div class='jet'>
    <span>
      <span></span>
      <span></span>
      <span></span>
      <span></span>
    </span>
    <div class='base'>
      <span></span>
      <div class='face'></div>
    </div>
  </div>
  <div class="detail">
    <h1><?php echo T_('REDIRECTING ...') ?></h1>
    <?php echo '  <h2><a href='. $_loc. '>'. strtok($_loc, '?') .'</a></h2>';
    if($_txt)
    {
      echo '  <br><p>'. $_txt .'</p>';
    }
    ?>

  </div>

 <script src="<?php echo(\dash\url::siftal()); ?>/js/siftal.min.js?v=3"></script>
</body>
</html>