<?php
/**
 * Developer: Daniel
 * Date: 2/15/14
 * Time: 9:54 PM
 */

// AT MINIMUM UNCOMMENT AND CONFIGURE ONE OF THE FOLLOWING OPTIONS:

// OPTION ONE: READ MYSQL CONFIG FROM config.lua - RECOMMENDED
// define('TFS_CONFIG', '/path/to/config.lua');

// OPTION TWO: SPECIFY MYSQL CONNECTION DETAILS HERE
// define('TFS_CONFIG', array('mysqlHost' => '127.0.0.1', 'mysqlDatabase' => 'tfs', 'mysqlUser' => 'tfs', 'mysqlPass' => 'tfs'));
// THIS OPTION IS DISCOURAGED AS SOME CODE MIGHT DEPEND ON OTHER VALUES FROM TFS CONFIG

// GAME
// define('ALLOWED_VOCATIONS', serialize(array(0))); // if you want to everyone to start with no vocation (RL style)
// define('ALLOWED_VOCATIONS', serialize(array(1, 2, 3, 4))); // default
// define('NEW_PLAYER_LEVEL', 8);

// IF YOU WANT TO CHANGE SOMETHING ELSE, BE SMART AND FOLLOW THE PATTERN