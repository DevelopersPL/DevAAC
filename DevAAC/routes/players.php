<?php
/**
 * Developer: Daniel
 * Date: 2/19/14
 * Time: 3:32 PM
 */

use DevAAC\Models\Player;
use DevAAC\Models\PlayerPublic;

/**
 * @SWG\Resource(
 *  basePath="/devaac",
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
$DevAAC->get(ROUTES_PREFIX.'/players/:id', function($id) use($DevAAC) {
    $player = PlayerPublic::findOrFail($id);
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($player->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/devaac",
 *  resourcePath="/players",
 *  @SWG\Api(
 *    path="/players",
 *    description="Operations on players",
 *    @SWG\Operation(
 *      summary="Get all players",
 *      method="GET",
 *      type="array[Player]",
 *      nickname="getPlayers"
 *    )
 *  )
 * )
 */
$DevAAC->get(ROUTES_PREFIX.'/players', function() use($DevAAC) {
    $players = PlayerPublic::all();
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($players->toJson(JSON_PRETTY_PRINT));
});

$DevAAC->get(ROUTES_PREFIX.'/topplayers', function() use($DevAAC) {
    $players = PlayerPublic::take(5)->orderBy('level', 'DESC')->orderBy('experience', 'DESC')->get();
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($players->toJson(JSON_PRETTY_PRINT));
});
