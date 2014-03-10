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

use DevAAC\Models\Account;
use DevAAC\Models\AccountPublic;
use DevAAC\Models\AccountBan;
use DevAAC\Models\Player;

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
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
        if(array_key_exists('prompt', $DevAAC->request->get()))
            $DevAAC->response->header('WWW-Authenticate', sprintf('Basic realm="%s"', 'DevAAC'));
        throw new InputErrorException('You are not logged in.', 401);
    }
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($DevAAC->auth_account->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
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
        throw new InputErrorException('You are not logged in.', 401);
    }
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($DevAAC->auth_account->players->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
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
    $account = AccountPublic::findOrFail($id);

    if($DevAAC->auth_account && ($DevAAC->auth_account->id == $account->id || $DevAAC->auth_account->isAdmin()))
        $account = Account::findOrFail($id);

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($account->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/accounts",
 *  @SWG\Api(
 *    path="/accounts/{id}/ban",
 *    description="Operations on accounts",
 *    @SWG\Operation(
 *      summary="Get account's ban by ID",
 *      notes="Each account can only have one ban at a time",
 *      method="GET",
 *      type="AccountBan",
 *      nickname="getAccountBanByID",
 *      @SWG\Parameter( name="id",
 *                      description="ID of Account that ban to be fetched",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\ResponseMessage(code=404, message="Account not found")
 *   )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/accounts/:id/ban', function($id) use($DevAAC) {
    $account = Account::findOrFail($id);

    $ban = $account->getBan;
    if(!$ban)
        throw new InputErrorException('This account is not banned', 404);

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($ban->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/accounts",
 *  @SWG\Api(
 *    path="/accounts/{id}/ban",
 *    description="Operations on accounts",
 *    @SWG\Operation(
 *      summary="Ban account by ID",
 *      notes="Need to have admin rights<br />
 *      Do not provide account_id or banned_at in AccountBan object - they will be ignored<br />
 *      The ID of player in banned_by must be of group_id > 1<br />
 *      expires_at defaults to 0 which means the ban does not expire",
 *      method="POST",
 *      type="AccountBan",
 *      nickname="banAccountByID",
 *      @SWG\Parameter( name="id",
 *                      description="ID of Account to ban",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\Parameter( name="ban",
 *                      description="AccountBan object",
 *                      paramType="body",
 *                      required=true,
 *                      type="AccountBan"),
 *      @SWG\ResponseMessage(code=403, message="Permission denied"),
 *      @SWG\ResponseMessage(code=404, message="Account not found / player not found"),
 *      @SWG\ResponseMessage(code=406, message="banned_by player group_id < 2 / banned_by player not on account"),
 *      @SWG\ResponseMessage(code=409, message="Account is already banned")
 *   )
 *  )
 * )
 */
$DevAAC->post(ROUTES_API_PREFIX.'/accounts/:id/ban', function($id) use($DevAAC) {
    $req = $DevAAC->request;

    if(!$DevAAC->auth_account || !$DevAAC->auth_account->isAdmin())
        throw new InputErrorException('You are not an admin.', 403);

    $account = Account::findOrFail($id);
    if($account->ban)
        throw new InputErrorException('This account is already banned.', 409);

    $player = Player::find($req->getAPIParam('banned_by'));
    if(!$player)
        throw new InputErrorException('The banned_by player not found.', 404);

    if($player->account->id !== $DevAAC->auth_account->id)
        throw new InputErrorException('The banned_by player is not yours!', 406);

    if($player->group_id < 2)
        throw new InputErrorException('The banned_by player must have group_id > 1.', 406);

    $ban = new AccountBan(
        array(
            'reason' => $req->getAPIParam('reason'),
            'banned_at' => new \DevAAC\Helpers\DateTime(),
            'expires_at' => $req->getAPIParam('expires_at', 0),
            'banned_by' => $player->id
        )
    );

    //$ban->bannedByPlayer()->associate($player);

    $account->ban()->save($ban);

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($ban->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/accounts",
 *  @SWG\Api(
 *    path="/accounts/{id}/ban",
 *    description="Operations on accounts",
 *    @SWG\Operation(
 *      summary="Ban account by ID",
 *      notes="Need to have admin rights and be the one who created the ban, you can only change reason and expiration date, other parameters are ignored",
 *      method="PUT",
 *      type="AccountBan",
 *      nickname="updateAccountBanByID",
 *      @SWG\Parameter( name="id",
 *                      description="ID of Account to update ban",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\Parameter( name="ban",
 *                      description="AccountBan object",
 *                      paramType="body",
 *                      required=true,
 *                      type="AccountBan"),
 *      @SWG\ResponseMessage(code=400, message="Missing parameters"),
 *      @SWG\ResponseMessage(code=403, message="Permission denied"),
 *      @SWG\ResponseMessage(code=404, message="Account not found"),
 *      @SWG\ResponseMessage(code=406, message="Account is not banned")
 *   )
 *  )
 * )
 */
$DevAAC->put(ROUTES_API_PREFIX.'/accounts/:id/ban', function($id) use($DevAAC) {
    $req = $DevAAC->request;

    if(!$DevAAC->auth_account || !$DevAAC->auth_account->isAdmin())
        throw new InputErrorException('You are not an admin.', 403);

    $account = Account::findOrFail($id);
    $ban = $account->ban;
    if(!$ban)
        throw new InputErrorException('This account is not banned.', 406);

    // TODO: Calling bannedByPlayer and account here makes these two embedded in the $ban that is returned
    // TODO: Fortunately, we restrict this route to only the owner of the ban, so they can see their own information only
    if($ban->bannedByPlayer->account->id !== $DevAAC->auth_account->id)
        throw new InputErrorException('The banned_by player is not yours! You cannot change a ban issued by someone else.', 406);

    if( !$req->getAPIParam('reason', false) && !$req->getAPIParam('expires_at', false) && $req->getAPIParam('expires_at', false) !== 0 )
        throw new InputErrorException('You need to provide a new reason or expires_at.', 400);

    try
    {
        $ban->reason = $req->getAPIParam('reason');
    }
    catch(\InputErrorException $e) {}

    try
    {
        $ban->expiresAt = $req->getAPIParam('expires_at');
    }
    catch(\InputErrorException $e) {}

    $ban->save();

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($ban->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/accounts",
 *  @SWG\Api(
 *    path="/accounts/{id}/ban",
 *    description="Operations on accounts",
 *    @SWG\Operation(
 *      summary="Delete account's ban by ID",
 *      notes="Need to have admin rights, once removed, the ban will NOT appear in ban history!",
 *      method="DELETE",
 *      type="null",
 *      nickname="deleteAccountBanByID",
 *      @SWG\Parameter( name="id",
 *                      description="ID of Account to lift ban",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\ResponseMessage(code=403, message="Permission denied"),
 *      @SWG\ResponseMessage(code=404, message="Account not found"),
 *      @SWG\ResponseMessage(code=406, message="Account is not banned")
 *   )
 *  )
 * )
 */
$DevAAC->delete(ROUTES_API_PREFIX.'/accounts/:id/ban', function($id) use($DevAAC) {
    if(!$DevAAC->auth_account || !$DevAAC->auth_account->isAdmin())
        throw new InputErrorException('You are not an admin', 403);

    $account = Account::findOrFail($id);
    $ban = $account->ban;
    if(!$ban)
        throw new InputErrorException('This account is not banned', 406);

    $ban->delete();

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody(json_encode(null, JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/accounts",
 *  @SWG\Api(
 *    path="/accounts/{id}/banHistory",
 *    description="Operations on accounts",
 *    @SWG\Operation(
 *      summary="Get account's ban history by ID",
 *      notes="",
 *      method="GET",
 *      type="array[AccountBanHistory]",
 *      nickname="getAccountBanHistoryByID",
 *      @SWG\Parameter( name="id",
 *                      description="ID of Account that ban history to be fetched",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\ResponseMessage(code=404, message="Account not found")
 *   )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/accounts/:id/banHistory', function($id) use($DevAAC) {
    $account = Account::findOrFail($id);
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($account->banHistory->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/accounts",
 *  @SWG\Api(
 *    path="/accounts/{id}",
 *    description="Operations on accounts",
 *    @SWG\Operation(
 *      summary="Update account by ID",
 *      notes="Owner can update password and email, admin can update everything except id and creation",
 *      method="PUT",
 *      type="Account",
 *      nickname="updateAccountByID",
 *      @SWG\Parameter( name="id",
 *                      description="ID of Account that needs to be updated",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\Parameter( name="account",
 *                      description="Account object",
 *                      paramType="body",
 *                      required=true,
 *                      type="Account"),
 *      @SWG\ResponseMessage(code=401, message="Authentication required"),
 *      @SWG\ResponseMessage(code=404, message="Account not found"),
 *      @SWG\ResponseMessage(code=400, message="Input parameter error"),
 *      @SWG\ResponseMessage(code=403, message="Permission denied")
 *   )
 *  )
 * )
 */
$DevAAC->put(ROUTES_API_PREFIX.'/accounts/:id', function($id) use($DevAAC) {
    $account = Account::findOrFail($id);
    $req = $DevAAC->request;

    if( ! $DevAAC->auth_account )
        throw new InputErrorException('You are not logged in.', 401);

    if($account->id != !$DevAAC->auth_account->id or !$DevAAC->auth_account->isAdmin())
        throw new InputErrorException('You do not have permission to change this account.', 403);

    if( !$DevAAC->auth_account->isAdmin() )
    {
        if($req->getAPIParam('name')) {
            if( !filter_var($req->getAPIParam('name'), FILTER_VALIDATE_REGEXP,
                array("options" => array("regexp" => "/^[a-zA-Z]{2,12}$/"))) )
                throw new InputErrorException('Account name must have 2-12 characters, only letters.', 400);

            $account->name = $req->getAPIParam('name');
        }

        if($req->getAPIParam('type'))
            $account->type = $req->getAPIParam('type');

        if($req->getAPIParam('premdays'))
            $account->premdays = $req->getAPIParam('premdays');

        if($req->getAPIParam('lastday'))
            $account->lastday = $req->getAPIParam('lastday');
    }

    if($req->getAPIParam('password'))
    {
        if( !filter_var($req->getAPIParam('password'), FILTER_VALIDATE_REGEXP,
            array("options" => array("regexp" => "/^(.{2,20}|.{40})$/"))) )
            throw new InputErrorException('Password must have 2-20 characters or be an SHA-1 hash (40 hexadecimal characters).', 400);

        $account->password = $req->getAPIParam('password');
    }

    if($req->getAPIParam('email'))
    {
        if( !filter_var($req->getAPIParam('email'), FILTER_VALIDATE_EMAIL) or !getmxrr(explode('@', $req->getAPIParam('email'))[1], $trash_) )
            throw new InputErrorException('Email address is not valid.', 400);

        $account->email = $req->getAPIParam('email');
    }

    $account->save();

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($account->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/accounts",
 *  @SWG\Api(
 *    path="/accounts/{id}",
 *    description="Operations on accounts",
 *    @SWG\Operation(
 *      summary="Delete account by ID",
 *      notes="Owner and admin only",
 *      method="DELETE",
 *      type="Account",
 *      nickname="deleteAccountByID",
 *      @SWG\Parameter( name="id",
 *                      description="ID of Account that needs to be deleted",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\ResponseMessage(code=401, message="Authentication required"),
 *      @SWG\ResponseMessage(code=404, message="Account not found"),
 *      @SWG\ResponseMessage(code=403, message="Permission denied")
 *   )
 *  )
 * )
 */
$DevAAC->delete(ROUTES_API_PREFIX.'/accounts/:id', function($id) use($DevAAC) {
    $account = Account::findOrFail($id);

    if( ! $DevAAC->auth_account )
        throw new InputErrorException('You are not logged in.', 401);

    if($account->id != !$DevAAC->auth_account->id && !$DevAAC->auth_account->isAdmin())
        throw new InputErrorException('You do not have permission to delete this account.', 403);

    $account->delete();

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody(json_encode(null, JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/accounts",
 *  @SWG\Api(
 *    path="/accounts",
 *    description="Operations on accounts",
 *    @SWG\Operation(
 *      summary="Get all accounts",
 *      notes="name, password and email are returned only to authenticated user and admin",
 *      method="GET",
 *      type="Account",
 *      nickname="getAccounts"
 *   )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/accounts', function() use($DevAAC) {
    if($DevAAC->auth_account && $DevAAC->auth_account->isAdmin())
        $accounts = Account::all();
    else
        $accounts = AccountPublic::all();
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($accounts->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
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
