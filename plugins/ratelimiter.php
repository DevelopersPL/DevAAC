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

$meta = array('name' => 'Rate limiter',
    'description' => 'Rate limiter requires APC user cache (APC or APCu)',
    'version' => '1.0',
    'author' => 'Don Daniello',
    'link' => 'https://github.com/DevelopersPL/DevAAC'
);

if( !in_array(basename(__FILE__), $DevAAC->enabled_plugins) )
    return array_merge($meta, array('enabled' => false));

// THIS PLUGIN CURRENTLY SUPPORTS APC ONLY
if(!extension_loaded('apc') || !ini_get('apc.enabled'))
    return array_merge($meta, array('enabled' => false));

// DEFAULT CONFIG
defined('RATELIMITER_RULES') || define('RATELIMITER_RULES', serialize(array(
    // DEFINE RULES WITHOUT ROUTES_PREFIX OR ROUTES_API_PREFIX
    // PATH -> NUMBER OF SECONDS TO WAIT BETWEEN REQUESTS
    'GET' => array(
        '/plugins' => 5
    ),
    'POST' => array(
        '/' => 5, // for Simple AAC (plugin)
        '/accounts' => 10,
        '/players' => 5,
    )
)));
// SHOULD WE RESET THE TIMER ON EVERY ATTEMPT?
defined('RATELIMITER_PENALIZE') || define('RATELIMITER_PENALIZE', false);

// http://docs.slimframework.com/#How-to-Use-Hooks
$DevAAC->hook('slim.before.dispatch', function () use ($DevAAC) {

    $rules = unserialize(RATELIMITER_RULES);

    // $DevAAC->router->currentRoute is NULL in this hook plus it's protected
    // we cannot use route names (even if we assigned them)
    // unfortunately we need to base on path

    $req = $DevAAC->request;
    $path = $req->getPath();
    $method = $req->getMethod();

    // REMOVE ROUTES_API_PREFIX FROM PATH
    if (substr($path, 0, strlen(ROUTES_API_PREFIX)) == ROUTES_API_PREFIX)
        $path = substr($path, strlen(ROUTES_API_PREFIX));

    // REMOVE ROUTES_PREFIX FROM PATH
    if (substr($path, 0, strlen(ROUTES_PREFIX)) == ROUTES_PREFIX)
        $path = substr($path, strlen(ROUTES_PREFIX));

    // DO WE HAVE A RULE?
    if (array_key_exists($method, $rules) && array_key_exists($path, $rules[$method])) {
        // every path for every IP is a separate object to be thread safe
        $objname = $req->getIp() . '_' . $path;

        if(apc_fetch($objname) + $rules[$method][$path] > time()) {
            $DevAAC->response->headers->set('Content-Type', 'application/json');
            $DevAAC->halt(503, json_encode(array('code' => 503, 'message' => 'Too many requests. Please wait a minute and try again.'))); // 429 IS NOT SUPPORTED BY NGINX SO WE USE 503

            if(!RATELIMITER_PENALIZE)
                return;
        }
        apc_store($objname, time());
        $DevAAC->expires('+'.$rules[$method][$path].' seconds');
    }
});

return array_merge($meta, array('enabled' => true));
