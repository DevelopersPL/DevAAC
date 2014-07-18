<?php
/**
 * DevAAC
 *
 * Automatic Account Creator by developers.pl for TFS 1.0
 *
 *
 * LICENSE: Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE
 * FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package    DevAAC
 * @author     Daniel Speichert <daniel@speichert.pl>
 * @author     Wojciech Guziak <wojciech@guziak.net>
 * @copyright  2014 Developers.pl
 * @license    http://opensource.org/licenses/MIT MIT
 * @version    master
 * @link       https://github.com/DevelopersPL/DevAAC
 */

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
defined('TFS_ROOT') or define('TFS_ROOT', false); // directory where config.lua is located
defined('ROUTES_PREFIX') or define('ROUTES_PREFIX', '');
defined('ROUTES_API_PREFIX') or define('ROUTES_API_PREFIX', '/api/v1');
defined('CORS_ALLOW_ORIGIN') or define('CORS_ALLOW_ORIGIN', 'otls.net'); // origin or false
defined('ACCOUNT_RECOVERY_INTERVAL') or define('ACCOUNT_RECOVERY_INTERVAL', 10800); // 3 hours (in seconds)

// GAME
defined('ACCOUNT_TYPE_GOD') or define('ACCOUNT_TYPE_GOD', 5);
defined('ACCOUNT_TYPE_GAMEMASTER') or define('ACCOUNT_TYPE_GAMEMASTER', 4);
defined('ACCOUNT_TYPE_SENIORTUTOR') or define('ACCOUNT_TYPE_SENIORTUTOR', 3);
defined('ACCOUNT_TYPE_TUTOR') or define('ACCOUNT_TYPE_TUTOR', 2);

defined('ALLOWED_VOCATIONS') or define('ALLOWED_VOCATIONS', serialize(array(1, 2, 3, 4)));
defined('NEW_PLAYER_LEVEL') or define('NEW_PLAYER_LEVEL', 8);
defined('NEW_PLAYER_TOWN_ID') or define('NEW_PLAYER_TOWN_ID', 1);

defined('HOUSES_AUCTIONS') or define('HOUSES_AUCTIONS', true); // enable house auction system?
defined('HOUSES_AUCTION_TIME') or define('HOUSES_AUCTION_TIME', 'P7D'); // DateInterval spec notation: http://www.php.net/manual/en/dateinterval.construct.php
defined('HOUSES_PER_PLAYER') or define('HOUSES_PER_PLAYER', 1);
defined('HOUSES_PER_ACCOUNT') or define('HOUSES_PER_ACCOUNT', 1);

// both HOUSES_BID_RAISE and HOUSES_BID_RAISE_PERCENT are enforced at the same time so it is usually enough to set one of them to 0
defined('HOUSES_BID_RAISE') or define('HOUSES_BID_RAISE', 1000); // the minimum difference between last bid,
// e.g. if the house is currently offered at 15000 and HOUSES_BID_RAISE is 500, you need to bid at least 15500
defined('HOUSES_BID_RAISE_PERCENT') or define('HOUSES_BID_RAISE_PERCENT', 0); // the minimum difference between last bid in %,
// e.g. if the house is currently offered at 15000 and HOUSES_BID_RAISE_PERCENT is 20, then you need to bid at least 18000 (3000 is 20% of 15000)

// PLUGINS CONFIG - DO NOT EDIT - PUT YOUR CUSTOMIZATIONS IN config.php
defined('DISABLE_PLUGINS') or define('DISABLE_PLUGINS', false);
defined('ENABLED_PLUGINS') or define('ENABLED_PLUGINS', serialize(array('ratelimiter.php', 'simple.php', 'ipban.php')));

// DO NOT EDIT
define('PUBLIC_HTML_PATH', realpath('.'));
define('HAS_APC', extension_loaded('apc') && ini_get('apc.enabled'));

// IF YOU INSTALL PUBLIC_HTML IN A SUBDIRECTORY, FOR EXAMPLE: http://example.com/ots/aac/index.php
// THEN YOU NEED TO SET APP_ROOT ACCORDINGLY. IN THIS CASE TO '../../../DevAAC'

chdir(APP_ROOT);
require './DevAAC.php';
// NOTHING IN THIS FILE MATTERS AFTER THIS LINE

// GOOD PLACE FOR PLUGINS, CUSTOMIZATIONS, ETC
// IS IN A NEW FILE IN plugins DIRECTORY
// THEY ARE AUTOMATICALLY LOADED
