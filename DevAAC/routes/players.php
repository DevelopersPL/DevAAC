<?php
/**
 * Developer: Daniel
 * Date: 2/19/14
 * Time: 3:32 PM
 */

use DevAAC\Models\Player;
use DevAAC\Models\PlayerPublic;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * @SWG\Resource(
 *  basePath="/api",
 *  resourcePath="/players",
 *  @SWG\Api(
 *    path="/players/{id}",
 *    description="Operations on players",
 *    @SWG\Operation(
 *      summary="Get player based on ID",
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
$DevAAC->get(ROUTES_API_PREFIX.'/players/:id', function($id) use($DevAAC) {
    $player = PlayerPublic::findOrFail($id);
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($player->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api",
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
    $tmp = new PlayerPublic();
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
            if(!in_array($rule, $visible) && ( !$DevAAC->auth_account || !$DevAAC->auth_account->isAdmin() ) )
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
            if(!in_array($field, $visible) && ( !$DevAAC->auth_account || !$DevAAC->auth_account->isAdmin() ) )
                throw new InputErrorException('You cannot select '.$field, 400);
        }
        $players->select($fields);
    }
    elseif(!$DevAAC->auth_account || !$DevAAC->auth_account->isAdmin())
        $players->select($visible);

    if(intval($req->get('offset')))
        $players->skip($req->get('offset'));

    $limit = intval($req->get('limit'));
    if($limit && ($limit <= 100 or ( $DevAAC->auth_account && $DevAAC->auth_account->isAdmin() ) ) )
        $players->take($limit);
    else
        $players->take(100);

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody(json_encode($players->get(), JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api",
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

    if($player->account->id != !$DevAAC->auth_account->id && !$DevAAC->auth_account->isAdmin())
        throw new InputErrorException('You do not have permission to delete this player.', 403);

    $player->delete();

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody(json_encode(null, JSON_PRETTY_PRINT));
});
