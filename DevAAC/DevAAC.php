<?php
/**
 * Developer: Daniel
 * Date: 2/14/14
 * Time: 10:37 AM
 */
// Autoload our dependencies with Composer
$loader = require '../vendor/autoload.php';
$loader->setPsr4('DevAAC\\', APP_ROOT);

use DevAAC\Helpers\DateTime;
use DevAAC\Models\Account;

//////////////////////// CREATE Slim APPLICATION //////////////////////////////////
$DevAAC = new \Slim\Slim(array(
    'debug' => ENABLE_DEBUG
));

$DevAAC->add(new \Slim\Middleware\ContentTypes());
//$DevAAC->response->headers->set('Content-Type', 'application/json'); // by default we return json

////////////////////// ALLOW CROSS-SITE REQUESTS (OR NOT) /////////////////////////
if(CORS_ALLOW_ORIGIN) {
    $DevAAC->response->headers->set('Access-Control-Allow-Origin', CORS_ALLOW_ORIGIN);
    $DevAAC->response->headers->set('Access-Control-Allow-Headers', 'Authorization, Origin, Content-Type, Accept');
    $DevAAC->response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
    $DevAAC->response->headers->set('Access-Control-Allow-Credentials', 'true');
    $DevAAC->options(':a+', function ($a) {}); // Send blank 200 to every OPTIONS request
}

$DevAAC->container->singleton('request', function ($c) {
    return new DevAAC\Http\Request($c['environment']);
});

//////////////////// DEFINE AUTHENTICATION MIDDLEWARE ////////////////////////////
// http://docs.slimframework.com/#Middleware-Overview
class AuthMiddleware extends \Slim\Middleware
{
    /**
     * This method will check the HTTP request headers for previous authentication. If
     * the request has already authenticated, the next middleware is called.
     */
    public function call()
    {
        $req = $this->app->request();
        $res = $this->app->response();
        $auth_user = $req->headers('PHP_AUTH_USER');
        $auth_pass = $req->headers('PHP_AUTH_PW');

        if($auth_user && $auth_pass)
            $this->app->auth_account = Account::where('name', $auth_user)->where('password', sha1($auth_pass))->first();
        //else
        //    $res->header('WWW-Authenticate', sprintf('Basic realm="%s"', 'AAC'));
        $this->next->call();
    }
}
$DevAAC->add(new AuthMiddleware());

////////////////////////////// HANDLE ERRORS ////////////////////////////////////
class InputErrorException extends \Exception {};
$DevAAC->error(function ($e) use ($DevAAC) {
    if($e instanceof Illuminate\Database\Eloquent\ModelNotFoundException)
    {
        $DevAAC->response->headers->set('Content-Type', 'application/json');
        $DevAAC->response->setStatus(404);
        $DevAAC->response->setBody(json_encode(null), JSON_PRETTY_PRINT);
    }
    elseif($e instanceof InputErrorException)
    {
        $DevAAC->response->headers->set('Content-Type', 'application/json');
        $DevAAC->response->setStatus($e->getCode());
        $DevAAC->response->setBody(json_encode(array('code' => $e->getCode(), 'message' => $e->getMessage()), JSON_PRETTY_PRINT));
    }
    else
    {
        $DevAAC->response->headers->set('Content-Type', 'application/json');
        $DevAAC->response->setStatus(500);
        $DevAAC->response->setBody(json_encode(array('code' => $e->getCode(),
            'message' => 'Fatal error occured: ' . $e->getMessage() . ' at line ' . $e->getLine() . ' in file ' . $e->getFile()), JSON_PRETTY_PRINT));
    }
});

//////////////////////////// LOAD TFS CONFIG ////////////////////////////////////
// you need to define TFS_CONFIG to be an array with config.lua options or a path to config.lua
$DevAAC->tfs_config = is_file(TFS_CONFIG) ? parse_ini_file(TFS_CONFIG) : unserialize(TFS_CONFIG) or die('TFS_CONFIG is not defined properly.');

////////////////////////// CONNECT TO DATABASE /////////////////////////////////
// Bootstrap Eloquent ORM
// https://github.com/illuminate/database
use Illuminate\Database\Capsule\Manager as Capsule;
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $DevAAC->tfs_config['mysqlHost'],
    'database'  => $DevAAC->tfs_config['mysqlDatabase'],
    'username'  => $DevAAC->tfs_config['mysqlUser'],
    'password'  => $DevAAC->tfs_config['mysqlPass'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

////////////////////// SERVE API DOCS WITH Swagger ///////////////////////////////
// http://zircote.com/swagger-php/using_swagger.html
// https://github.com/zircote/swagger-php/blob/master/library/Swagger/Swagger.php
use Swagger\Swagger;
$DevAAC->get(ROUTES_PREFIX.'/api-docs(/:path)', function($path = '/') use($DevAAC) {
    $swagger = new Swagger('../', '../vendor');
    $DevAAC->response->headers->set('Access-Control-Allow-Origin', '*');
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    if($path != '/')
        $DevAAC->response->setBody($swagger->getResource('/'.$path, array('output' => 'json')));
    else
        $DevAAC->response->setBody($swagger->getResourceList(array('output' => 'json')));
});

//////////////////////////// DEFINE API ROUTES //////////////////////////////////
require('routes/accounts.php');
require('routes/players.php');

$DevAAC->get(ROUTES_API_PREFIX.'/news', function() use($DevAAC) {
    $news = array();
    if(is_dir(PUBLIC_HTML_PATH.'/news')) {
        foreach (glob(PUBLIC_HTML_PATH.'/news/*.md') as $filename) {
            $date = new \DevAAC\Helpers\DateTime;
            $date->createFromFormat('U', filectime($filename));
            $news[] = array(
                'title' => basename($filename, '.md'),
                'date' => $date,
                'content' => file_get_contents($filename)
            );
        }
    }

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody(json_encode($news, JSON_PRETTY_PRINT));
});

$DevAAC->get(ROUTES_PREFIX.'/debug', function() use($DevAAC) {
    $DevAAC->response->headers->set('Content-Type', 'text');
    $date = new \DevAAC\Helpers\DateTime();
    echo $date . PHP_EOL;
    echo json_encode($date) . PHP_EOL;
    echo serialize($date) . PHP_EOL;
});

////////////////////// PLUGINS SUPPORT ///////////////////////////////
// plugins are loaded here (if they exist)
if(is_dir('../plugins') && !DISABLE_PLUGINS) {
    $DevAAC->enabled_plugins = unserialize(ENABLED_PLUGINS);
    $loaded_plugins = array();
    foreach (glob("../plugins/*.php") as $filename) {
        $p = require $filename;
        if($p)
            if(is_array($p)) {
                array_merge($p, array('id' => basename($filename)));
                $loaded_plugins[] = $p;
            } else
                $loaded_plugins[] = array('id' => basename($filename));
    }
    $DevAAC->plugins = $loaded_plugins;
}

$DevAAC->get(ROUTES_PREFIX.'/plugins', function() use($DevAAC) {
    $DevAAC->response->setBody(json_encode($DevAAC->plugins), JSON_PRETTY_PRINT);
    $DevAAC->response->headers->set('Content-Type', 'application/json');
});

//////////////////////////////////////////////////////////////////////
// all done, any code after this call will not matter to the request
$DevAAC->run();
