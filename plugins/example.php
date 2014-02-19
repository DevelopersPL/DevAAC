<?php
/**
 * Developer: Daniel
 * Date: 2/15/14
 * Time: 9:14 PM
 */

// THIS IS ONLY OPTIONAL, YOU CAN DEFINE PLUGIN META DATA
// THIS CAN BE ANY INFORMATION YOU WANT
$meta = array('name' => 'Example plugin',
    'description' => 'This is an example how to write plugins. It exposes /uptime. It requires shell_exec function.',
    'version' => '0.1',
    'author' => 'Don Daniello',
    'link' => 'https://github.com/DonDaniello/DevAAC'
);

// IF THE PLUGIN IS NOT ACTIVATED IN CONFIG, THEN DISABLE IT
if( !in_array(basename(__FILE__), $DevAAC->enabled_plugins) )
    return array_merge($meta, array('enabled' => false));

// YOU CAN ADD EXTRA REQUIREMENTS FOR THE PLUGIN TO BE ENABLED
if( !function_exists('shell_exec') )
    return array_merge($meta, array('enabled' => false));

/**
 * This is an example plugin that is automatically loaded
 * You can find instance of Slim framework app as $DevAAC
 * Check out Slim documentation at http://docs.slimframework.com/
 */
$DevAAC->get(ROUTES_PREFIX.'/uptime', function() use($DevAAC) {
    $DevAAC->response->headers->set('Content-Type', 'text');
    $DevAAC->response->setBody(shell_exec('uptime'));
});

// THIS RETURNS META DATA SPECIFIED ABOVE
return array_merge($meta, array('enabled' => true));
