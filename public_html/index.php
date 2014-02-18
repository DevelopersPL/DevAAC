<?php
/**
 * This is the primary "kickstarter" file for the AAC
 * Your webserver should direct all requests here
 * You can put this file anywhere but make sure you adapt APP_ROOT
 */
// LOAD CONFIG IF IT EXISTS
if(is_file('./config.php'))
    include './config.php';

// DEFAULT CONFIG - DO NOT EDIT
// PUT YOUR CUSTOMIZATIONS IN config.php
defined('TFS_CONFIG') or die('Please follow instructions in public_html/config.sample.php');
defined('ROUTES_PREFIX') or define('ROUTES_PREFIX', '/devaac');
defined('DISABLE_PLUGINS') or define('DISABLE_PLUGINS', false);
defined('ENABLE_DEBUG') or define('ENABLE_DEBUG', false);
defined('APP_ROOT') or define('APP_ROOT', '../DevAAC');
defined('CORS_ALLOW_ORIGIN') or define('CORS_ALLOW_ORIGIN', false); // origin or false

// IF YOU INSTALL PUBLIC_HTML IN A SUBDIRECTORY, FOR EXAMPLE: http://example.com/ots/aac/index.php
// THEN YOU NEED TO SET APP_ROOT ACCORDINGLY. IN THIS CASE TO '../../../app'

chdir(APP_ROOT);
require './DevAAC.php';
// NOTHING IN THIS FILE MATTERS AFTER THIS LINE

// GOOD PLACE FOR PLUGINS, CUSTOMIZATIONS, ETC
// IS IN A NEW FILE IN plugins DIRECTORY
// THEY ARE AUTOMATICALLY LOADED