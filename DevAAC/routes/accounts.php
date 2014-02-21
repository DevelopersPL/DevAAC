<?php
/**
 * Developer: Daniel
 * Date: 2/19/14
 * Time: 3:31 PM
 */

use DevAAC\Models\Account;
use DevAAC\Models\AccountPublic;
use DevAAC\Models\Player;

/**
 * @SWG\Resource(
 *  basePath="/api",
 *  resourcePath="/accounts",
 *  @SWG\Api(
 *    path="/accounts/my",
 *    description="Operations on accounts",
 *    @SWG\Operation(
 *      summary="Get authenticated account",
 *      notes="The parameter presented here applies to all API requests",
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
$DevAAC->get(ROUTES_API_PREFIX.'/accounts/my', function() use($DevAAC) {
    if( ! $DevAAC->auth_account ) {
        //$DevAAC->response->header('WWW-Authenticate', sprintf('Basic realm="%s"', 'AAC'));
        $DevAAC->halt(401);
    }
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($DevAAC->auth_account->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api",
 *  resourcePath="/accounts",
 *  @SWG\Api(
 *    path="/accounts/my/players",
 *    description="Operations on accounts",
 *    @SWG\Operation(
 *      summary="Get players on authenticated account",
 *      notes="",
 *      method="GET",
 *      type="Player",
 *      nickname="getAccountByID",
 *      @SWG\ResponseMessage(code=401, message="No or bad authentication provided")
 *   )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/accounts/my/players', function() use($DevAAC) {
    if( ! $DevAAC->auth_account ) {
        $DevAAC->halt(401);
    }
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($DevAAC->auth_account->players->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api",
 *  resourcePath="/accounts",
 *  @SWG\Api(
 *    path="/accounts/{id}",
 *    description="Operations on accounts",
 *    @SWG\Operation(
 *      summary="Get account by ID",
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
$DevAAC->get(ROUTES_API_PREFIX.'/accounts/:id', function($id) use($DevAAC) {
    $accounts = Account::findOrFail($id);
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($accounts->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api",
 *  resourcePath="/accounts",
 *  @SWG\Api(
 *    path="/accounts",
 *    description="Operations on accounts",
 *    @SWG\Operation(
 *      summary="Get all accounts",
 *      notes="name, password and email are returned only to authenticated user and admin",
 *      method="GET",
 *      type="Account",
 *      nickname="getAccounts",
 *      @SWG\Parameter( name="id",
 *                      description="ID of Account that needs to be fetched",
 *                      paramType="query",
 *                      required=false,
 *                      type="integer"),
 *      @SWG\ResponseMessage(code=404, message="Account not found")
 *   )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/accounts', function() use($DevAAC) {
    $accounts = AccountPublic::all();
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($accounts->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api",
 *  resourcePath="/accounts",
 *  @SWG\Api(
 *    path="/accounts",
 *    description="Operations on accounts",
 *    @SWG\Operation(
 *      summary="Create new account",
 *      notes="You can also pass account object's attributes as form input, do not specify fields marked as optional",
 *      method="POST",
 *      type="Account",
 *      nickname="createAccount",
 *      @SWG\Parameter( name="account",
 *                      description="Account object",
 *                      paramType="body",
 *                      required=true,
 *                      type="Account"),
 *      @SWG\ResponseMessage(code=400, message="Input parameter error")
 *   )
 *  )
 * )
 */
$DevAAC->post(ROUTES_API_PREFIX.'/accounts', function() use($DevAAC) {
    $req = $DevAAC->request;
    if( !filter_var($req->getAPIParam('name'), FILTER_VALIDATE_REGEXP,
        array("options" => array("regexp" => "/^[a-zA-Z]{2,12}$/"))) )
        throw new InputErrorException('Account name must have 2-12 characters, only letters.', 400);

    if( !filter_var($req->getAPIParam('password'), FILTER_VALIDATE_REGEXP,
        array("options" => array("regexp" => "/^(.{2,20}|.{40})$/"))) )
        throw new InputErrorException('Password must have 2-20 characters or be an SHA-1 hash (40 hexadecimal characters).', 400);

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
