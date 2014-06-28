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

use \DevAAC\Models\ServerConfig;
use \DevAAC\Models\Player;
use \DevAAC\Models\IpBan;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/server",
 *  @SWG\Api(
 *    path="/server/config",
 *    description="Operations on server",
 *    @SWG\Operation(
 *      summary="Get server config values",
 *      notes="",
 *      method="GET",
 *      type="ServerConfig",
 *      nickname="getServerConfig"
 *   )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/server/config', function() use($DevAAC) {
    $config = ServerConfig::all();
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($config->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/server",
 *  @SWG\Api(
 *    path="/server/ipBans",
 *    description="Operations on server",
 *    @SWG\Operation(
 *      summary="Get IP bans",
 *      notes="",
 *      method="GET",
 *      type="array[IpBan]",
 *      nickname="getIPBans"
 *   )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/server/ipBans', function() use($DevAAC) {
    $ipbans = IpBan::all();
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($ipbans->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/server",
 *  @SWG\Api(
 *    path="/server/ipBans",
 *    description="Operations on server",
 *    @SWG\Operation(
 *      summary="Ban IP",
 *      notes="Need to have admin rights<br />
 *      Do not provide banned_at in IpBan object - it will be ignored<br />
 *      The ID of player in banned_by must be of group_id > 1<br />
 *      expires_at defaults to 0 which means the ban does not expire",
 *      method="POST",
 *      type="IpBan",
 *      nickname="banIP",
 *      @SWG\Parameter( name="ban",
 *                      description="IpBan object",
 *                      paramType="body",
 *                      required=true,
 *                      type="IpBan"),
 *      @SWG\ResponseMessage(code=403, message="IP address is not valid"),
 *      @SWG\ResponseMessage(code=403, message="Permission denied"),
 *      @SWG\ResponseMessage(code=404, message="banned_by player not found"),
 *      @SWG\ResponseMessage(code=406, message="banned_by player group_id < 2 / banned_by player not on account"),
 *      @SWG\ResponseMessage(code=409, message="IP is already banned")
 *   )
 *  )
 * )
 */
$DevAAC->post(ROUTES_API_PREFIX.'/server/ipBans', function() use($DevAAC) {
    $req = $DevAAC->request;

    if(!$DevAAC->auth_account || !$DevAAC->auth_account->isGod())
        throw new InputErrorException('You are not an admin.', 403);

    $ipban = IpBan::find(ip2long($req->getAPIParam('ip')));
    if($ipban)
        throw new InputErrorException('This IP is already banned.', 409);

    if( !filter_var($req->getAPIParam('ip'), FILTER_VALIDATE_IP) )
        throw new InputErrorException('IP address is not valid.', 400);

    $player = Player::find($req->getAPIParam('banned_by'));
    if(!$player)
        throw new InputErrorException('The banned_by player not found.', 404);

    if($player->account->id !== $DevAAC->auth_account->id)
        throw new InputErrorException('The banned_by player is not yours!', 406);

    if($player->group_id < 2)
        throw new InputErrorException('The banned_by player must have group_id > 1.', 406);

    $ban = new IpBan(
        array(
            'ip' => $req->getAPIParam('ip'),
            'reason' => $req->getAPIParam('reason'),
            'banned_at' => new \DevAAC\Helpers\DateTime(),
            'expires_at' => $req->getAPIParam('expires_at', 0),
            'banned_by' => $player->id
        )
    );

    $ban->save();

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($ban->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/server",
 *  @SWG\Api(
 *    path="/server/ipBans/{ip}",
 *    description="Operations on server",
 *    @SWG\Operation(
 *      summary="Delete IP ban",
 *      notes="Need to have admin rights",
 *      method="DELETE",
 *      type="null",
 *      nickname="deleteIPBan",
 *      @SWG\Parameter( name="ip",
 *                      description="IP to lift ban",
 *                      paramType="path",
 *                      required=true,
 *                      type="string"),
 *      @SWG\ResponseMessage(code=403, message="Permission denied"),
 *      @SWG\ResponseMessage(code=404, message="IP is not banned")
 *   )
 *  )
 * )
 */
$DevAAC->delete(ROUTES_API_PREFIX.'/server/ipBans/:ip', function($ip) use($DevAAC) {
    if(!$DevAAC->auth_account || !$DevAAC->auth_account->isGod())
        throw new InputErrorException('You are not an admin', 403);

    $ipban = IpBan::find(ip2long($ip));
    if(!$ipban)
        throw new InputErrorException('This IP is not banned.', 404);

    $ipban->delete();

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody(json_encode(null, JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/server",
 *  @SWG\Api(
 *    path="/server/info",
 *    description="Operations on server",
 *    @SWG\Operation(
 *      summary="Get some information",
 *      notes="The result of this call is cached, last refresh time is given as 'timestamp'",
 *      method="GET",
 *      type="array",
 *      nickname="getServerInfo"
 *   )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/server/info', function() use($DevAAC) {
    if(HAS_APC)
        $result = apc_fetch('server_info');

    if(!HAS_APC || !$result)
    {
        $result = array(
            'players_online_count' => Capsule::table('players_online')->count(),
            'players_online' => Capsule::table('players')->join('players_online', 'players.id', '=', 'players_online.player_id')->select('players.name')->get(),
            'players_count' => Capsule::table('players')->count(),
            'accounts_count' => Capsule::table('accounts')->count(),
            'guilds_count' => Capsule::table('guilds')->count(),
            'guild_wars_count' => Capsule::table('guild_wars')->count(),
            'houses_count' => Capsule::table('houses')->count(),
            'allowed_vocations' => unserialize(ALLOWED_VOCATIONS),

            // config.lua
            'worldType' => $DevAAC->tfsConfigFile['worldType'],
            'ip' => $DevAAC->tfsConfigFile['ip'],
            'loginProtocolPort' => $DevAAC->tfsConfigFile['loginProtocolPort'],
            'statusProtocolPort' => $DevAAC->tfsConfigFile['statusProtocolPort'],
            'maxPlayers' => $DevAAC->tfsConfigFile['maxPlayers'],
            'serverName' => $DevAAC->tfsConfigFile['serverName'],
            'statusTimeout' => $DevAAC->tfsConfigFile['statusTimeout'],
            'ownerName' => $DevAAC->tfsConfigFile['ownerName'],
            'ownerEmail' => $DevAAC->tfsConfigFile['ownerEmail'],
            'url' => $DevAAC->tfsConfigFile['url'],
            'location' => $DevAAC->tfsConfigFile['location'],
            'motd' => $DevAAC->tfsConfigFile['motd'],
            'houseRentPeriod' => $DevAAC->tfsConfigFile['houseRentPeriod'],

            // cache hint
            'timestamp' => new \DevAAC\Helpers\DateTime()

        );

        if(HAS_APC)
            apc_store('server_info', $result, 60);
    }
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody(json_encode($result, JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/server",
 *  @SWG\Api(
 *    path="/server/vocations",
 *    description="Operations on server",
 *    @SWG\Operation(
 *      summary="Get vocations",
 *      notes="",
 *      method="GET",
 *      type="array",
 *      nickname="getVocations"
 *   )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/server/vocations', function() use($DevAAC) {
    $result = xml2array($DevAAC->vocations)['vocation'];
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody(json_encode($result, JSON_PRETTY_PRINT));
});
