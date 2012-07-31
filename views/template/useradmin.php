<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml" dir="ltr" lang="en-US">
<head>
   <title><?php echo $title ?></title>
   <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
   <?php foreach ($styles as $file => $type) echo HTML::style($file, array('media' => $type)), "\n" ?>
   <?php foreach ($scripts as $file) echo HTML::script($file), "\n" ?>
   <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
</head>
<body>

<div id="page">

    <div id="header">
        <h1><?php echo __('module.page.title'); ?></h1>
    </div>

    <?php echo View::factory('useradmin/menu'); ?>

    <div id="content">
        <?php
        echo View::factory('useradmin/messages');
        echo $content;
        ?>
    </div>

</div>
   
<?php echo $profile; ?>

</body>
</html>
