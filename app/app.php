<?php
/**
 * Developer: Daniel
 * Date: 2/14/14
 * Time: 10:37 AM
 */
// Autoload our dependencies with Composer
$loader = require '../vendor/autoload.php';
$loader->setPsr4('App\\', APP_ROOT);

// define authentication Middleware (application-level)
// http://docs.slimframework.com/#Middleware-Overview
class AuthMiddleware extends \Slim\Middleware
{
    public function call()
    {
        // Get reference to application
        $app = $this->app;

        $auth = $app->getCookie('duaac_auth');

        // Run inner middleware and application
        $this->next->call();

        $res = $app->response;
        //$body = $res->getBody();
        //$res->setBody(strtoupper($body));
    }
}

// Create Slim app
$app = new \Slim\Slim(array(
    'debug' => ENABLE_DEBUG
));
$app->add(new \AuthMiddleware());
//$app->response->headers->set('Content-Type', 'application/json'); // by default we return json
ENABLE_DEBUG or $app->response->headers->set('Access-Control-Allow-Origin', '*'); // DEBUG ONLY

// HANDLE ERRORS
$app->error(function (Illuminate\Database\Eloquent\ModelNotFoundException $e) use ($app) {
    $app->response->setStatus(404);
    $app->response->setBody(json_encode(null));
});

$app->error(function (\Exception $e) use ($app) {
    $app->halt(500, 'Fatal error occured.');
});

// you need to define TFS_CONFIG to be an array with config.lua options or a path to config.lua
$tfs_config = is_file(TFS_CONFIG) ? parse_ini_file(TFS_CONFIG) : unserialize(TFS_CONFIG) or die('TFS_CONFIG is not defined properly.');

// Bootstrap Eloquent ORM // https://github.com/illuminate/database
use Illuminate\Database\Capsule\Manager as Capsule;
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $tfs_config['mysqlHost'],
    'database'  => $tfs_config['mysqlDatabase'],
    'username'  => $tfs_config['mysqlUser'],
    'password'  => $tfs_config['mysqlPass'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$app->post(ROUTES_PREFIX.'/login', function($id) use($app) {
    $name = $app->request->params('name');
    $pass = $app->request->params('password');
    try {
        $account = Account::where('name', $name)->where('password', sha1($pass))->firstOrFail();
    } catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        $app->halt(403);
    }

    $app->setCookie(
        'duaac_auth',
        $auth,
        '1 month'
    );

    $app->response->setBody($players->toJson());
    $app->response->headers->set('Content-Type', 'application/json');
});

// API docs with Swagger
// http://zircote.com/swagger-php/using_swagger.html
// https://github.com/zircote/swagger-php/blob/master/library/Swagger/Swagger.php
use Swagger\Swagger;
$app->get(ROUTES_PREFIX.'/api-docs(/:path)', function($path = '/') use($app) {
    $swagger = new Swagger('../', '../vendor');
    $app->response->headers->set('Access-Control-Allow-Origin', '*');
    $app->response->headers->set('Content-Type', 'application/json');
    if($path != '/')
        $app->response->setBody($swagger->getResource('/'.$path, array('output' => 'json')));
    else
        $app->response->setBody($swagger->getResourceList(array('output' => 'json')));
});

//
use App\models\Player;
/**
 * @SWG\Resource(
 *  basePath="http://example.com/api",
 *  resourcePath="/players",
 *  @SWG\Api(
 *    path="/players/{playerId}",
 *    description="Operations about players",
 *    @SWG\Operation(
 *      summary="Retrieve player based on ID",
 *      method="GET",
 *      type="object[Player]",
 *      nickname="getPlayerByID",
 *      @SWG\Parameter(name="playerId",type="integer")
 *    )
 *  )
 * )
 */
$app->get(ROUTES_PREFIX.'/players/:id', function($id) use($app) {
    $player = Player::findOrFail($id);
    $app->response->setBody('{"players":'. $player->toJson() .'}');
    $app->response->headers->set('Content-Type', 'application/json');
});

/**
 * @SWG\Resource(
 *  basePath="http://example.com/api",
 *  resourcePath="/players",
 *  @SWG\Api(
 *    path="/players",
 *    description="Operations about players",
 *    @SWG\Operation(
 *      summary="Retrieve all players",
 *      method="GET",
 *      type="array[Player]",
 *      nickname="getPlayers"
 *    )
 *  )
 * )
 */
$app->get(ROUTES_PREFIX.'/players', function() use($app) {
    $players = Player::all();
    //$app->response->setBody('{"players":'. $players->toJson() .'}');
    $app->response->setBody($players->toJson());
    $app->response->headers->set('Content-Type', 'application/json');
});

use App\models\Account;
$app->get(ROUTES_PREFIX.'/accounts/:id', function($id) use($app) {
    $accounts = Account::findOrFail($id);
    $app->response->setBody($accounts->toJson());
    $app->response->headers->set('Content-Type', 'application/json');
});

$app->get(ROUTES_PREFIX.'/accounts', function() use($app) {
    $accounts = Account::all();
    $app->response->setBody('{"accounts":'.$accounts->toJson() .'}');
    $app->response->headers->set('Content-Type', 'application/json');
});

////////////////////// PLUGINS SUPPORT ///////////////////////////////
// plugins are loaded here (if they exist)
if(is_dir('../plugins') && !DISABLE_PLUGINS) {
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
    $app->plugins = $loaded_plugins;
}

$app->get(ROUTES_PREFIX.'/plugins', function() use($app) {
    $app->response->setBody('{"plugins":'.json_encode($app->plugins) .'}');
    $app->response->headers->set('Content-Type', 'application/json');
});

//////////////////////////////////////////////////////////////////////
// all done, any code after this call will not matter to the request
$app->run();
