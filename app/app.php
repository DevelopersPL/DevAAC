<?php
/**
 * Developer: Daniel
 * Date: 2/14/14
 * Time: 10:37 AM
 */
// Autoload our dependencies with Composer
$loader = require '../vendor/autoload.php';
$loader->setPsr4('App\\', __DIR__.'/../app');

// define authentication Middleware
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
    'debug' => true
));
$app->add(new \AuthMiddleware());
//$app->response->headers->set('Content-Type', 'application/json'); // by default we return json

$app->error(function (Illuminate\Database\Eloquent\ModelNotFoundException $e) use ($app) {
    $app->response->setStatus(404);
    $app->response->setBody(json_encode(null));
});

// you need to define TFS_CONFIG to be an array with config.lua options or a path to config.lua
$tfs_config = is_array(TFS_CONFIG) ? TFS_CONFIG : parse_ini_file(TFS_CONFIG);

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

$app->post('/duaac/login', function($id) use($app) {
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

    $players = Player::findOrFail($id);
    $app->response->setBody($players->toJson());
    $app->response->headers->set('Content-Type', 'application/json');
});

// API docs with Swagger
// http://zircote.com/swagger-php/using_swagger.html
// https://github.com/zircote/swagger-php/blob/master/library/Swagger/Swagger.php
use Swagger\Swagger;
$app->get('/duaac/api-docs(/:path)', function($path = '/') use($app) {
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
$app->get('/duaac/players/:id', function($id) use($app) {
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
$app->get('/duaac/players', function() use($app) {
    $players = Player::all();
    $app->response->setBody('{"players":'. $players->toJson() .'}');
    $app->response->headers->set('Content-Type', 'application/json');
});

use App\models\Account;
$app->get('/duaac/accounts/:id', function($id) use($app) {
    $accounts = Account::findOrFail($id);
    $app->response->setBody($accounts->toJson());
    $app->response->headers->set('Content-Type', 'application/json');
});

$app->get('/duaac/accounts', function() use($app) {
    $accounts = Account::all();
    $app->response->setBody('{"accounts":'.$accounts->toJson() .'}');
    $app->response->headers->set('Content-Type', 'application/json');
});
