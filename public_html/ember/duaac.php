<?php
// LOAD CONFIG IF IT EXISTS
if(is_file('./config.php'))
    include './config.php';

// DEFAULT CONFIG - DO NOT EDIT
// PUT YOUR CUSTOMIZATIONS IN config.php
defined('TFS_CONFIG') or die('Please follow instructions in public_html/config.sample.php');
defined('ROUTES_PREFIX') or define('ROUTES_PREFIX', '/duaac');
defined('DISABLE_PLUGINS') or define('DISABLE_PLUGINS', false);
defined('ENABLE_DEBUG') or define('ENABLE_DEBUG', false);

require '../app/app.php';
// NOTHING IN THIS FILE MATTERS AFTER THIS LINE

// GOOD PLACE FOR PLUGINS, CUSTOMIZATIONS, ETC
// IS IN A NEW FILE IN plugins DIRECTORY
// THEY ARE AUTOMATICALLY LOADED