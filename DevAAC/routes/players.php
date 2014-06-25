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

use DevAAC\Models\Player;
use DevAAC\Models\PlayerPublic;
use DevAAC\Models\PlayerOnline;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/players",
 *  @SWG\Api(
 *    path="/players/online",
 *    description="Operations on players",
 *    @SWG\Operation(
 *      summary="Get online players",
 *      notes="Non-admins get only public fields",
 *      method="GET",
 *      type="array[Player]",
 *      nickname="getOnlinePlayers",
 *      @SWG\Parameter( name="embed",
 *                      description="Pass player to embed player object instead of showing just ID",
 *                      paramType="query",
 *                      required=false,
 *                      type="string list separated by comma")
 *     )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/players/online', function() use($DevAAC) {
    $req = $DevAAC->request;

    if($req->get('embed') == 'player')
        $players = Player::has('online')->get();
    else
        $players = PlayerOnline::all();
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($players->toJson(JSON_PRETTY_PRINT));
});


/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/players",
 *  @SWG\Api(
 *    path="/players/{id/name}",
 *    description="Operations on players",
 *    @SWG\Operation(
 *      summary="Get player based on ID or name",
 *      method="GET",
 *      type="Player",
 *      nickname="getPlayerByID",
 *      @SWG\Parameter( name="id/name",
 *                      description="ID or name of Player that needs to be fetched",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer/string"),
 *      @SWG\ResponseMessage(code=404, message="Player not found")
 *    )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/players/:id', function($id) use($DevAAC) {
    try {
        $player = Player::findOrFail($id);
    } catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        $player = Player::where('name', $id)->first();
        if(!$player)
            throw $e;
    }
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($player->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/players",
 *  @SWG\Api(
 *    path="/players/{id/name}/spells",
 *    description="Operations on players",
 *    @SWG\Operation(
 *      summary="Get player's spells based on ID or name",
 *      method="GET",
 *      type="PlayerSpell",
 *      nickname="getPlayerSpellsByID",
 *      @SWG\Parameter( name="id/name",
 *                      description="ID or name of Player whose spells need to be fetched",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer/string"),
 *      @SWG\ResponseMessage(code=404, message="Player not found")
 *    )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/players/:id/spells', function($id) use($DevAAC) {
    try {
        $player = Player::findOrFail($id);
    } catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        $player = Player::where('name', $id)->first();
        if(!$player)
            throw $e;
    }
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($player->spells->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/players",
 *  @SWG\Api(
 *    path="/players/{id/name}/deaths",
 *    description="Operations on players",
 *    @SWG\Operation(
 *      summary="Get player's deaths based on ID or name",
 *      method="GET",
 *      type="PlayerDeath",
 *      nickname="getPlayerDeathsByID",
 *      @SWG\Parameter( name="id/name",
 *                      description="ID or name of Player whose deaths need to be fetched",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer/string"),
 *      @SWG\ResponseMessage(code=404, message="Player not found")
 *    )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/players/:id/deaths', function($id) use($DevAAC) {
    try {
        $player = Player::findOrFail($id);
    } catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        $player = Player::where('name', $id)->first();
        if(!$player)
            throw $e;
    }
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($player->deaths->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/players",
 *  @SWG\Api(
 *    path="/players",
 *    description="Operations on players",
 *    @SWG\Operation(
 *      summary="Get all players",
 *      notes="Non-admins get only public fields",
 *      method="GET",
 *      type="array[Player]",
 *      nickname="getPlayers",
 *      @SWG\Parameter( name="sort",
 *                      description="The field or fields (separated by comma) to sort by ascending, specify -field to sort descending, e.g.: ?sort=level,-skill_fist",
 *                      paramType="query",
 *                      required=false,
 *                      type="string"),
 *      @SWG\Parameter( name="fields",
 *                      description="The field to return (Non-admin: only public fields)",
 *                      paramType="query",
 *                      required=false,
 *                      type="string"),
 *      @SWG\Parameter( name="offset",
 *                      description="The number of records to skip",
 *                      paramType="query",
 *                      required=false,
 *                      type="string"),
 *      @SWG\Parameter( name="limit",
 *                      description="The number of records to return at maximum (Non-admin: max 100)",
 *                      paramType="query",
 *                      required=false,
 *                      type="string")
 *    )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/players', function() use($DevAAC) {
    $req = $DevAAC->request;
    $players = Capsule::table('players');

    // for field validation - it's not the best way ;/
    $tmp = new Player();
    $visible = $tmp->getVisibleFields();

    // support ?sort=level,-skill_club
    if($req->get('sort'))
    {
        $sort_rules = explode(',', $req->get('sort'));
        foreach($sort_rules as $rule)
        {
            if(0 === strpos($rule, '-')) {
                $rule = trim($rule, '-');
                $players->orderBy($rule, 'desc');
            }
            else
                $players->orderBy($rule, 'asc');

            // check if has permission to sort by this field
            if(!in_array($rule, $visible) && ( !$DevAAC->auth_account || !$DevAAC->auth_account->isGod() ) )
                throw new InputErrorException('You cannot sort by '.$rule, 400);
        }
    }

    // support ?fields=id,name,level
    if($req->get('fields'))
    {
        $fields = explode(',', $req->get('fields'));
        foreach($fields as $field)
        {
            // check if has permission to select this field
            if(!in_array($field, $visible) && ( !$DevAAC->auth_account || !$DevAAC->auth_account->isGod() ) )
                throw new InputErrorException('You cannot select '.$field, 400);
        }
        $players->select($fields);
    }
    elseif(!$DevAAC->auth_account || !$DevAAC->auth_account->isGod())
        $players->select($visible);

    if(intval($req->get('offset')))
        $players->skip($req->get('offset'));

    $limit = intval($req->get('limit'));
    if($limit && ($limit <= 100 or ( $DevAAC->auth_account && $DevAAC->auth_account->isGod() ) ) )
        $players->take($limit);
    else
        $players->take(100);

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody(json_encode($players->get(), JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/players",
 *  @SWG\Api(
 *    path="/players/{id}",
 *    description="Operations on players",
 *    @SWG\Operation(
 *      summary="Delete player by ID",
 *      notes="Owner and admin only",
 *      method="DELETE",
 *      type="Player",
 *      nickname="deletePlayerByID",
 *      @SWG\Parameter( name="id",
 *                      description="ID of Player that needs to be deleted",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\ResponseMessage(code=401, message="Authentication required"),
 *      @SWG\ResponseMessage(code=404, message="Player not found"),
 *      @SWG\ResponseMessage(code=403, message="Permission denied")
 *   )
 *  )
 * )
 */
$DevAAC->delete(ROUTES_API_PREFIX.'/players/:id', function($id) use($DevAAC) {
    $player = Player::findOrFail($id);

    if( ! $DevAAC->auth_account )
        throw new InputErrorException('You are not logged in.', 401);

    if($player->account->id != $DevAAC->auth_account->id && !$DevAAC->auth_account->isGod())
        throw new InputErrorException('You do not have permission to delete this player.', 403);

    $player->delete();

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody(json_encode(null, JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/players",
 *  @SWG\Api(
 *    path="/players",
 *    description="Operations on players",
 *    @SWG\Operation(
 *      summary="Create new player",
 *      notes="You can also pass player object's attributes as form input, do not specify fields marked as optional",
 *      method="POST",
 *      type="Player",
 *      nickname="createPlayer",
 *      @SWG\Parameter( name="player",
 *                      description="Player object",
 *                      paramType="body",
 *                      required=true,
 *                      type="Player"),
 *      @SWG\ResponseMessage(code=400, message="Input parameter error"),
 *      @SWG\ResponseMessage(code=401, message="Authentication required")
 *   )
 *  )
 * )
 */
$DevAAC->post(ROUTES_API_PREFIX.'/players', function() use($DevAAC) {
    if( ! $DevAAC->auth_account )
        throw new InputErrorException('You are not logged in.', 401);

    $req = $DevAAC->request;

    if( !filter_var($req->getAPIParam('name'), FILTER_VALIDATE_REGEXP,
        array("options" => array("regexp" => "/^[a-zA-Z ]{5,20}$/"))) )
        throw new InputErrorException('Player name must have 5-20 characters, only letters and space.', 400);

    if (filter_var($req->getAPIParam('name'), FILTER_VALIDATE_REGEXP,
          array('options' => array('regexp' => '/[Tutor|GM|God|CM|Admin]/i')))
          && !$DevAAC->auth_account->isGameMaster())
        throw new InputErrorException('Player name must not include GM/CM/God/Admin words.', 400);

    if( !in_array($req->getAPIParam('vocation'), unserialize(ALLOWED_VOCATIONS)) )
        throw new InputErrorException('Vocation is out of bounds.', 400);

    if( !in_array($req->getAPIParam('sex'), array(0, 1)) )
        throw new InputErrorException('Sex is invalid.', 400);

    $player = Player::where('name', $req->getAPIParam('name'))->first();
    if($player)
        throw new InputErrorException('Player with this name already exists.', 400);

    $player = new Player(
        array(
            'name' => $req->getAPIParam('name'),
            'vocation' => $req->getAPIParam('vocation'),
            'sex' => $req->getAPIParam('sex'),
            'level' => NEW_PLAYER_LEVEL
        )
    );

    $DevAAC->auth_account->players()->save($player);

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($player->toJson(JSON_PRETTY_PRINT));
});
