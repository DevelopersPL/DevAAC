<?php
/**
 * Developer: Daniel
 * Date: 2/14/14
 * Time: 10:37 AM
 */
// Autoload our dependencies with Composer
$loader = require '../vendor/autoload.php';
$loader->setPsr4('DevAAC\\', APP_ROOT);

use DevAAC\Models\Player;
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
    $DevAAC->response->headers->set('Access-Control-Allow-Headers', 'Authorization');
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
// MUST DEFINE HANDLERS IN ORDER FROM THE LOWEST CLASS \Exception TO THE HIGHEST
$DevAAC->error(function (\Exception $e) use ($DevAAC) {
    $DevAAC->halt(500, 'Fatal error occured: ' . $e->getMessage() . ' at line ' . $e->getLine() . ' in file ' . $e->getFile());
});

$DevAAC->error(function (Illuminate\Database\Eloquent\ModelNotFoundException $e) use ($DevAAC) {
    $DevAAC->response->setStatus(404);
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody(json_encode(null), JSON_PRETTY_PRINT);
});

class InputErrorException extends \Exception {};
$DevAAC->error(function (\InputErrorException $e) use ($DevAAC) {
    $DevAAC->response->setStatus($e->getCode());
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody(json_encode(array('code' => $e->getCode(), 'message' => $e->getMessage()), JSON_PRETTY_PRINT));
});

$DevAAC->error(function (\ErrorException $e) use ($DevAAC) {
    $DevAAC->halt(500, 'Fatal error occured: ' . $e->getMessage() . ' at line ' . $e->getLine() . ' in file ' . $e->getFile());
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
/**
 * @SWG\Resource(
 *  basePath="/devaac",
 *  resourcePath="/players",
 *  @SWG\Api(
 *    path="/players/{id}",
 *    description="Operations on players",
 *    @SWG\Operation(
 *      summary="Retrieve player based on ID",
 *      method="GET",
 *      type="Player",
 *      nickname="getPlayerByID",
 *      @SWG\Parameter( name="id",
 *                      description="ID of Player that needs to be fetched",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\ResponseMessage(code=404, message="Account not found")
 *    )
 *  )
 * )
 */
$DevAAC->get(ROUTES_PREFIX.'/players/:id', function($id) use($DevAAC) {
    $player = Player::findOrFail($id);
    $DevAAC->response->setBody($player->toJson(JSON_PRETTY_PRINT));
    $DevAAC->response->headers->set('Content-Type', 'application/json');
});

/**
 * @SWG\Resource(
 *  basePath="/devaac",
 *  resourcePath="/players",
 *  @SWG\Api(
 *    path="/players",
 *    description="Operations on players",
 *    @SWG\Operation(
 *      summary="Retrieve all players",
 *      method="GET",
 *      type="array[Player]",
 *      nickname="getPlayers"
 *    )
 *  )
 * )
 */
$DevAAC->get(ROUTES_PREFIX.'/players', function() use($DevAAC) {
    $players = Player::all();
    $DevAAC->response->setBody($players->toJson(JSON_PRETTY_PRINT));
    $DevAAC->response->headers->set('Content-Type', 'application/json');
});

$DevAAC->get(ROUTES_PREFIX.'/topplayers', function() use($DevAAC) {
    $players = Player::take(5)->orderBy('level', 'DESC')->orderBy('experience', 'DESC')->get();
    $DevAAC->response->setBody($players->toJson(JSON_PRETTY_PRINT));
    $DevAAC->response->headers->set('Content-Type', 'application/json');
});

/**
 * @SWG\Resource(
 *  basePath="/devaac",
 *  resourcePath="/accounts",
 *  @SWG\Api(
 *    path="/accounts/my",
 *    description="Operations on accounts",
 *    @SWG\Operation(
 *      summary="Retrieve authenticated account",
 *      notes="name, password and email are returned only to authenticated user and admin",
 *      method="GET",
 *      type="Account",
 *      nickname="getAccountByID",
 *      @SWG\Parameter( name="Authorization",
 *                      description="HTTP Basic Authorization: Basic base64(name:password)",
 *                      paramType="header",
 *                      required=false,
 *                      type="string"),
 *      @SWG\ResponseMessage(code=401, message="No or bad authentication provided")
 *   )
 *  )
 * )
 */
// THIS ONE IS USED TO DISCOVER IF USER/PASS COMBINATION IS OK
$DevAAC->get(ROUTES_PREFIX.'/accounts/my', function() use($DevAAC) {
    if( ! $DevAAC->auth_account ) {
        //$DevAAC->response->header('WWW-Authenticate', sprintf('Basic realm="%s"', 'AAC'));
        $DevAAC->halt(401);
    }
    $DevAAC->response->setBody($DevAAC->auth_account->toJson(JSON_PRETTY_PRINT));
    $DevAAC->response->headers->set('Content-Type', 'application/json');
});

/**
 * @SWG\Resource(
 *  basePath="/devaac",
 *  resourcePath="/accounts",
 *  @SWG\Api(
 *    path="/accounts/{id}",
 *    description="Operations on accounts",
 *    @SWG\Operation(
 *      summary="Retrieve account by ID",
 *      notes="name, password and email are returned only to authenticated user and admin",
 *      method="GET",
 *      type="Account",
 *      nickname="getAccountByID",
 *      @SWG\Parameter( name="id",
 *                      description="ID of Account that needs to be fetched",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\ResponseMessage(code=404, message="Account not found")
 *   )
 *  )
 * )
 */
$DevAAC->get(ROUTES_PREFIX.'/accounts/:id', function($id) use($DevAAC) {
    $accounts = Account::findOrFail($id);
    $DevAAC->response->setBody($accounts->toJson(JSON_PRETTY_PRINT));
    $DevAAC->response->headers->set('Content-Type', 'application/json');
});

$DevAAC->get(ROUTES_PREFIX.'/accounts', function() use($DevAAC) {
    $accounts = Account::all();
    $DevAAC->response->setBody($accounts->toJson(JSON_PRETTY_PRINT));
    $DevAAC->response->headers->set('Content-Type', 'application/json');
});

/**
 * @SWG\Resource(
 *  basePath="/devaac",
 *  resourcePath="/accounts",
 *  @SWG\Api(
 *    path="/accounts/{id}",
 *    description="Operations on accounts",
 *    @SWG\Operation(
 *      summary="Create new account",
 *      notes="You can also pass account object's attributes as form input",
 *      method="POST",
 *      type="Account",
 *      nickname="createAccount",
 *      @SWG\Parameter( name="id",
 *                      description="Account object",
 *                      paramType="body",
 *                      required=true,
 *                      type="Account"),
 *      @SWG\ResponseMessage(code=400, message="Input parameter error")
 *   )
 *  )
 * )
 */
$DevAAC->post(ROUTES_PREFIX.'/accounts', function() use($DevAAC) {
    $req = $DevAAC->request;
    if( !filter_var($req->getAPIParam('name'), FILTER_VALIDATE_REGEXP,
      array("options" => array("regexp" => "/^[a-zA-Z]{2,12}$/"))) )
        throw new InputErrorException('Account name must have 2-12 characters, only letters.', 400);

    if( !filter_var($req->getAPIParam('password'), FILTER_VALIDATE_REGEXP,
      array("options" => array("regexp" => "/^(.{2,20}|.{40})$/"))) )
        throw new InputErrorException('Password must have 2-20 characters.', 400);

    if( !filter_var($req->getAPIParam('email'), FILTER_VALIDATE_EMAIL) or !getmxrr(explode('@', $req->getAPIParam('email'))[1], $trash_) )
        throw new InputErrorException('Email address is not valid.', 400);

    $account = Account::where('name', $req->getAPIParam('name'))->first();
    if($account)
        throw new InputErrorException('Account with this name already exists.', 400);

    $account = DevAAC\Models\Account::create(
        array(
            'name' => $req->getAPIParam('name'),
            'password' => $req->getAPIParam('password'),
            'email' => $req->getAPIParam('email'),
            'creation' => new \DateTime()
        )
    );
    $account->save();
    $DevAAC->response->setBody($account->toJson(JSON_PRETTY_PRINT));
    $DevAAC->response->headers->set('Content-Type', 'application/json');
});

$DevAAC->get(ROUTES_PREFIX.'/debug', function() use($DevAAC) {
    $date = new \DevAAC\Helpers\DateTime();
    echo json_encode($date);

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
