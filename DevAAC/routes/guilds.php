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

use DevAAC\Models\Guild;
use DevAAC\Models\GuildWar;
use DevAAC\Models\GuildMembership;
use DevAAC\Models\GuildRank;

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/guilds",
 *  @SWG\Api(
 *    path="/guilds",
 *    description="Operations on guilds",
 *    @SWG\Operation(
 *      summary="Get all guilds",
 *      notes="",
 *      method="GET",
 *      type="Guild",
 *      nickname="getGuilds",
 *      @SWG\Parameter( name="embed",
 *                      description="Pass owner to embed player object",
 *                      paramType="query",
 *                      required=false,
 *                      type="string list separated by comma")
 *   )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/guilds', function() use($DevAAC) {
    $req = $DevAAC->request;

    if($req->get('embed') == 'owner')
        $guilds = Guild::with('owner')->get();
    else
        $guilds = Guild::all();

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($guilds->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/guilds",
 *  @SWG\Api(
 *    path="/guilds",
 *    description="Operations on guilds",
 *    @SWG\Operation(
 *      summary="Create a guild",
 *      notes="",
 *      method="POST",
 *      type="Guild",
 *      nickname="createGuilds",
 *      @SWG\Parameter( name="guild",
 *                      description="Guild object",
 *                      paramType="body",
 *                      required=true,
 *                      type="Guild"),
 *      @SWG\ResponseMessage(code=400, message="Input parameter error"),
 *      @SWG\ResponseMessage(code=401, message="Authentication required"),
 *      @SWG\ResponseMessage(code=409, message="Guild with this name already exists")
 *   )
 *  )
 * )
 */
$DevAAC->post(ROUTES_API_PREFIX.'/guilds', function() use($DevAAC) {
    // TODO
    if( ! $DevAAC->auth_account )
        throw new InputErrorException('You are not logged in.', 401);

    $req = $DevAAC->request;

    $guild = Guild::where('name', $req->getAPIParam('name'))->first();
    if($guild)
        throw new InputErrorException('Guild with this name already exists.', 409);

    $guild = new Guild(
        array(
            'name' => $req->getAPIParam('name'),
            'vocation' => $req->getAPIParam('vocation'),
            'sex' => $req->getAPIParam('sex'),
            'level' => NEW_PLAYER_LEVEL
        )
    );

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($guild->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/guilds",
 *  @SWG\Api(
 *    path="/guilds/wars",
 *    description="Operations on guilds",
 *    @SWG\Operation(
 *      summary="Get all guild wars",
 *      notes="",
 *      method="GET",
 *      type="GuildWar",
 *      nickname="getGuildWars"
 *   )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/guilds/wars', function() use($DevAAC) {
    $guildwars = GuildWar::all();
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($guildwars->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/guilds",
 *  @SWG\Api(
 *    path="/guilds/{id/name}",
 *    description="Operations on guilds",
 *    @SWG\Operation(
 *      summary="Get guild based on ID or name",
 *      method="GET",
 *      type="Guild",
 *      nickname="getGuildByID",
 *      @SWG\Parameter( name="id/name",
 *                      description="ID or name of Guild that needs to be fetched",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer/string"),
 *      @SWG\Parameter( name="embed",
 *                      description="Pass a combination of owner, members, invitations and/or ranks to embed",
 *                      paramType="query",
 *                      required=false,
 *                      type="string list separated by comma"),
 *      @SWG\ResponseMessage(code=404, message="Guild not found")
 *    )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/guilds/:id', function($id) use($DevAAC) {
    $req = $DevAAC->request;

    try {
        $guild = Guild::findOrFail($id);
    } catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        $guild = Guild::where('name', $id)->first();
        if(!$guild)
            throw $e;
    }

    $embedded = explode(',', $req->get('embed'));

    if (in_array('owner', $embedded))
        $guild->owner;

    if (in_array('members', $embedded))
        $guild->members;

    if (in_array('invitations', $embedded))
        $guild->invitations;

    if (in_array('ranks', $embedded))
        $guild->ranks;

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($guild->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/guilds",
 *  @SWG\Api(
 *    path="/guilds/{id/name}/invitations",
 *    description="Operations on guilds",
 *    @SWG\Operation(
 *      summary="Get guild invitations based on ID or name",
 *      method="GET",
 *      type="array[GuildInvite]",
 *      nickname="getGuildInvitationsByID",
 *      @SWG\Parameter( name="id/name",
 *                      description="ID or name of Guild that invitations needs to be fetched",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer/string"),
 *      @SWG\ResponseMessage(code=404, message="Guild not found")
 *    )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/guilds/:id/invitations', function($id) use($DevAAC) {
    try {
        $guild = Guild::findOrFail($id);
    } catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        $guild = Guild::where('name', $id)->first();
        if(!$guild)
            throw $e;
    }
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($guild->invitations->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/guilds",
 *  @SWG\Api(
 *    path="/guilds/{id/name}/members",
 *    description="Operations on guilds",
 *    @SWG\Operation(
 *      summary="Get guild members by guild ID or name",
 *      method="GET",
 *      type="Player",
 *      nickname="getGuildMembersByID",
 *      @SWG\Parameter( name="id/name",
 *                      description="ID or name of Guild which members needs to be fetched",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer/string"),
 *      @SWG\ResponseMessage(code=404, message="Guild not found")
 *    )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/guilds/:id/members', function($id) use($DevAAC) {
    try {
        $guild = Guild::findOrFail($id);
    } catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        $guild = Guild::where('name', $id)->first();
        if(!$guild)
            throw $e;
    }
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($guild->members->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/guilds",
 *  @SWG\Api(
 *    path="/guilds/{id}/memberships",
 *    description="Operations on guilds",
 *    @SWG\Operation(
 *      summary="Get guild memberships by guild ID",
 *      method="GET",
 *      type="GuildMembership",
 *      nickname="getGuildMembershipsByID",
 *      @SWG\Parameter( name="id",
 *                      description="ID of Guild which memberships needs to be fetched",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\Parameter( name="embed",
 *                      description="Pass a combination of player, rank and/or guild to embed",
 *                      paramType="query",
 *                      required=false,
 *                      type="string list separated by comma"),
 *      @SWG\ResponseMessage(code=404, message="Guild not found")
 *    )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/guilds/:id/memberships', function($id) use($DevAAC) {
    $req = $DevAAC->request;
    $memberships = GuildMembership::where('guild_id', $id);

    $embedded = explode(',', $req->get('embed'));

    if (in_array('player', $embedded))
        $memberships->with('player');

    if (in_array('rank', $embedded))
        $memberships->with('rank');

    if (in_array('guild', $embedded))
        $memberships->with('guild');

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($memberships->get()->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/guilds",
 *  @SWG\Api(
 *    path="/guilds/{id/name}/ranks",
 *    description="Operations on guilds",
 *    @SWG\Operation(
 *      summary="Get guild ranks by guild ID or name",
 *      method="GET",
 *      type="GuildRank",
 *      nickname="getGuildRanksByID",
 *      @SWG\Parameter( name="id/name",
 *                      description="ID or name of Guild which ranks needs to be fetched",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer/string"),
 *      @SWG\ResponseMessage(code=404, message="Guild not found")
 *    )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/guilds/:id/ranks', function($id) use($DevAAC) {
    try {
        $guild = Guild::findOrFail($id);
    } catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        $guild = Guild::where('name', $id)->first();
        if(!$guild)
            throw $e;
    }
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($guild->ranks->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/guilds",
 *  @SWG\Api(
 *    path="/guilds/{id/name}/ranks",
 *    description="Operations on guilds",
 *    @SWG\Operation(
 *      summary="Create guild rank by guild ID",
 *      method="POST",
 *      type="GuildRank",
 *      nickname="createGuildRankByID",
 *      @SWG\Parameter( name="id",
 *                      description="ID of Guild to create rank for",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\Parameter( name="name",
 *                      description="Name of rank to create",
 *                      paramType="form",
 *                      required=true,
 *                      type="string"),
 *      @SWG\Parameter( name="level",
 *                      description="Level of rank to create",
 *                      paramType="form",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\ResponseMessage(code=400, message="Input parameter error"),
 *      @SWG\ResponseMessage(code=401, message="Authentication required"),
 *      @SWG\ResponseMessage(code=404, message="Guild not found"),
 *      @SWG\ResponseMessage(code=403, message="Permission denied")
 *    )
 *  )
 * )
 */
$DevAAC->post(ROUTES_API_PREFIX.'/guilds/:id/ranks', function($id) use($DevAAC) {
    $req = $DevAAC->request;

    if(! $DevAAC->auth_account )
        throw new InputErrorException('You are not logged in.', 401);

    $guild = Guild::findOrFail($id);

    if($guild->owner->account->id != $DevAAC->auth_account->id && !$DevAAC->auth_account->isGod())
        throw new InputErrorException('You do not have permission to manage this guild.', 403);

    $rank = new GuildRank(
        array(
            'name' => $req->getAPIParam('name'),
            'level' => $req->getAPIParam('level')
        )
    );

    $guild->ranks()->save($rank);

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($rank->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/guilds",
 *  @SWG\Api(
 *    path="/guilds/{id/name}/ranks/{rid}",
 *    description="Operations on guilds",
 *    @SWG\Operation(
 *      summary="Update guild rank by guild ID, rank ID",
 *      method="PUT",
 *      type="GuildRank",
 *      nickname="updateGuildRankByID",
 *      @SWG\Parameter( name="id",
 *                      description="ID of Guild",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\Parameter( name="rid",
 *                      description="ID of Guild Rank to edit",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\Parameter( name="name",
 *                      description="Name of rank to set",
 *                      paramType="form",
 *                      required=false,
 *                      type="string"),
 *      @SWG\Parameter( name="level",
 *                      description="Level of rank to set",
 *                      paramType="form",
 *                      required=false,
 *                      type="integer"),
 *      @SWG\ResponseMessage(code=400, message="Input parameter error"),
 *      @SWG\ResponseMessage(code=401, message="Authentication required"),
 *      @SWG\ResponseMessage(code=404, message="Guild/Rank not found"),
 *      @SWG\ResponseMessage(code=403, message="Permission denied")
 *    )
 *  )
 * )
 */
$DevAAC->put(ROUTES_API_PREFIX.'/guilds/:id/ranks/:rid', function($id, $rid) use($DevAAC) {
    $req = $DevAAC->request;

    if(! $DevAAC->auth_account )
        throw new InputErrorException('You are not logged in.', 401);

    $rank = GuildRank::findOrFail($rid);

    if($rank->guild->owner->account->id != $DevAAC->auth_account->id && !$DevAAC->auth_account->isGod())
        throw new InputErrorException('You do not have permission to manage this guild.', 403);

    if($req->getAPIParam('name', false))
        $rank->name = $req->getAPIParam('name');

    if($req->getAPIParam('level', false))
        $rank->level = $req->getAPIParam('level');

    $rank->save();

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($rank->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/guilds",
 *  @SWG\Api(
 *    path="/guilds/{id/name}/ranks/{rid}",
 *    description="Operations on guilds",
 *    @SWG\Operation(
 *      summary="Delete guild rank by guild ID, rank ID",
 *      method="DELETE",
 *      type="GuildRank",
 *      nickname="deleteGuildRankByID",
 *      @SWG\Parameter( name="id",
 *                      description="ID of Guild",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\Parameter( name="rid",
 *                      description="ID of Guild Rank to delete",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\ResponseMessage(code=401, message="Authentication required"),
 *      @SWG\ResponseMessage(code=404, message="Guild/Rank not found"),
 *      @SWG\ResponseMessage(code=403, message="Permission denied")
 *    )
 *  )
 * )
 */
$DevAAC->delete(ROUTES_API_PREFIX.'/guilds/:id/ranks/:rid', function($id, $rid) use($DevAAC) {
    if(! $DevAAC->auth_account )
        throw new InputErrorException('You are not logged in.', 401);

    $rank = GuildRank::findOrFail($rid);

    if($rank->guild->owner->account->id != $DevAAC->auth_account->id && !$DevAAC->auth_account->isGod())
        throw new InputErrorException('You do not have permission to manage this guild.', 403);

    $rank->delete();

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody(json_encode(null, JSON_PRETTY_PRINT));
});
