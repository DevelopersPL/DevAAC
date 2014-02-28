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

// Autoload our dependencies with Composer
$loader = require '../vendor/autoload.php';
$loader->setPsr4('DevAAC\\', APP_ROOT);


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
    public function call()
    {
        $req = $this->app->request();
        $auth_user = $req->headers('PHP_AUTH_USER');
        $auth_pass = $req->headers('PHP_AUTH_PW');

        if($auth_user && $auth_pass)
            $this->app->auth_account = DevAAC\Models\Account::where('name', $auth_user)->where('password', $auth_pass)->first();

        if(!$this->app->auth_account)
            $this->app->auth_account = DevAAC\Models\Account::where('name', $auth_user)->where('password', sha1($auth_pass))->first();
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
if (strpos($capsule->getConnection()->getPdo()->getAttribute(PDO::ATTR_CLIENT_VERSION), 'mysqlnd') === false)
    die('PHP PDO is using non-native MySQL extension, php-mysqlnd native extension is required. You most likely have to execute: apt-get install php5-mysqlnd');
$capsule->setAsGlobal();
$capsule->bootEloquent();

////////////////////// SERVE API DOCS WITH Swagger ///////////////////////////////
// http://zircote.com/swagger-php/using_swagger.html
// https://github.com/zircote/swagger-php/blob/master/library/Swagger/Swagger.php
use Swagger\Swagger;
$DevAAC->get(ROUTES_API_PREFIX.'/docs(/:path)', function($path = '/') use($DevAAC) {
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
require('routes/guilds.php');
require('routes/server.php');

$DevAAC->get(ROUTES_API_PREFIX.'/news', function() use($DevAAC) {
    $news = array();
    if(is_dir(PUBLIC_HTML_PATH.'/news')) {
        foreach (glob(PUBLIC_HTML_PATH.'/news/*.md') as $filename) {
            $date = new \DevAAC\Helpers\DateTime;
            $date->setTimestamp(filectime($filename));
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

$DevAAC->get(ROUTES_PREFIX.'/debug', function() use($DevAAC, $capsule) {
    $DevAAC->response->headers->set('Content-Type', 'text');
    var_dump($capsule->getConnection()->getPdo()->getAttribute(PDO::ATTR_CLIENT_VERSION));
    $date = new \DevAAC\Helpers\DateTime();
    $tmp = \DevAAC\Models\Player::find(2);
    foreach($tmp->toArray() as $key => $value)
        echo "'".$key."' => 0,". PHP_EOL;
        //echo '* @SWG\Property(name="'.$key.'", type="string")'. PHP_EOL;
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

$DevAAC->get(ROUTES_API_PREFIX.'/plugins', function() use($DevAAC) {
    $DevAAC->response->setBody(json_encode($DevAAC->plugins), JSON_PRETTY_PRINT);
    $DevAAC->response->headers->set('Content-Type', 'application/json');
});

//////////////////////////////////////////////////////////////////////
// all done, any code after this call will not matter to the request
$DevAAC->run();
