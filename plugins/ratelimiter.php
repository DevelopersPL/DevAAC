<?php
/**
 * Developer: Daniel
 * Date: 2/15/14
 * Time: 10:12 PM
 */

//return; // UNCOMMENT TO DISABLE THIS PLUGIN

// THIS PLUGIN CURRENTLY SUPPORTS APC ONLY
if(!extension_loaded('apc') or !ini_get('apc.enabled'))
    return array('name' => 'Rate limiter',
        'description' => 'Rate limiter is disabled because APC or APCu is missing.',
        'version' => '0.1',
        'author' => 'Don Daniello',
        'link' => 'http://blablabal',
        'enabled' => false);

// DEFAULT CONFIG
defined('RATELIMITER_RULES') or define('RATELIMITER_RULES', serialize(array(
    // DEFINE RULES WITHOUT ROUTES_PREFIX
    // PATH -> NUMBER OF SECONDS TO WAIT BETWEEN REQUESTS
    '/plugins' => 5
)));
// SHOULD WE RESET THE TIMER ON EVERY ATTEMPT?
defined('RATELIMITER_PENALIZE') or define('RATELIMITER_PENALIZE', false);

// http://docs.slimframework.com/#How-to-Use-Hooks
$app->hook('slim.before.dispatch', function () use ($app) {

    $rules = unserialize(RATELIMITER_RULES);

    // $app->router->currentRoute is NULL in this hook plus it's prtoected
    // we cannot use route names (even if we assigned them)
    // unfortunately we need to base on path

    $req = $app->request;
    $path = $req->getPath();

    // REMOVE ROUTES_PREFIX FROM PATH
    if (substr($path, 0, strlen(ROUTES_PREFIX)) == ROUTES_PREFIX)
        $path = substr($path, strlen(ROUTES_PREFIX));

    if( array_key_exists($path, $rules) ) {
        // every path for every IP is a separate object to be thread safe
        $objname = $req->getIp() . '_' . $path;

        if(apc_fetch($objname) + $rules[$path] > time()) {
            $app->halt(429, 'Too many requests.');

            if(RATELIMITER_PENALIZE)
                apc_store($objname, time());
        } else
            apc_store($objname, time());
    }
});

return array('name' => 'Rate limiter',
             'version' => '0.1',
             'author' => 'Don Daniello',
             'link' => 'http://blablabal');
