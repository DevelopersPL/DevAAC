<?php
/**
 * This is the primary "kickstarter" file for the AAC
 * Your webserver should direct all requests here
 * You can put this file anywhere but make sure you adapt APP_ROOT
 */
// LOAD CONFIG IF IT EXISTS
if(is_file('./config.php'))
    include './config.php';

// DEFAULT CONFIG - DO NOT EDIT - PUT YOUR CUSTOMIZATIONS IN config.php
defined('APP_ROOT') or define('APP_ROOT', '../DevAAC');
defined('ENABLE_DEBUG') or define('ENABLE_DEBUG', false);
defined('TFS_CONFIG') or die('Please follow instructions in public_html/config.sample.php');
defined('ROUTES_PREFIX') or define('ROUTES_PREFIX', '');
defined('ROUTES_API_PREFIX') or define('ROUTES_API_PREFIX', '/api');
defined('CORS_ALLOW_ORIGIN') or define('CORS_ALLOW_ORIGIN', false); // origin or false

// GAME
defined('ACCOUNT_TYPE_ADMIN') or define('ACCOUNT_TYPE_ADMIN', 6);
defined('ALLOWED_VOCATIONS') or define('ALLOWED_VOCATIONS', serialize(array(1, 2, 3, 4)));
defined('NEW_PLAYER_LEVEL') or define('NEW_PLAYER_LEVEL', 8);

// PLUGINS CONFIG - DO NOT EDIT - PUT YOUR CUSTOMIZATIONS IN config.php
defined('DISABLE_PLUGINS') or define('DISABLE_PLUGINS', false);
defined('ENABLED_PLUGINS') or define('ENABLED_PLUGINS', serialize(array('ratelimiter.php', 'simple.php', 'ipban.php')));

// DO NOT EDIT
define('PUBLIC_HTML_PATH', realpath('.'));

// IF YOU INSTALL PUBLIC_HTML IN A SUBDIRECTORY, FOR EXAMPLE: http://example.com/ots/aac/index.php
// THEN YOU NEED TO SET APP_ROOT ACCORDINGLY. IN THIS CASE TO '../../../DevAAC'

chdir(APP_ROOT);
require './DevAAC.php';
// NOTHING IN THIS FILE MATTERS AFTER THIS LINE

// GOOD PLACE FOR PLUGINS, CUSTOMIZATIONS, ETC
// IS IN A NEW FILE IN plugins DIRECTORY
// THEY ARE AUTOMATICALLY LOADED