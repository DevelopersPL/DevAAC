<?php
/**
 * Developer: Daniel
 * Date: 2/15/14
 * Time: 10:12 PM
 */

//return; // UNCOMMENT TO DISABLE THIS PLUGIN

defined('RATELIMITER_RULES') or define('RATELIMITER_RULES', serialize(array(
    '/plugins' => 5
)));

// http://docs.slimframework.com/#How-to-Use-Hooks
$app->hook('slim.before.dispatch', function () use ($app) {

    // $app->router->currentRoute is NULL in this hook plus it's prtoected
    // we cannot use route names (even if we assigned them)
    // unfortunately we need to base on path

    if( array_key_exists(ROUTES_PREFIX . $app->request->getPath(), unserialize(RATELIMITER_RULES)) ) {

    }
});

return array('name' => 'Rate limiter',
             'version' => '0.1',
             'author' => 'Don Daniello',
             'link' => 'http://blablabal');
