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

use DevAAC\Models\House;
use DevAAC\Models\Player;

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/houses",
 *  @SWG\Api(
 *    path="/houses",
 *    description="Operations on houses",
 *    @SWG\Operation(
 *      summary="Get all houses",
 *      notes="",
 *      method="GET",
 *      type="House",
 *      nickname="getHouses"
 *   )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/houses', function() use($DevAAC) {
    $houses = House::all();
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($houses->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/houses",
 *  @SWG\Api(
 *    path="/houses/{id}",
 *    description="Operations on houses",
 *    @SWG\Operation(
 *      summary="Get house based on ID or name",
 *      method="GET",
 *      type="House",
 *      nickname="getHouseByID",
 *      @SWG\Parameter( name="id",
 *                      description="ID of House that needs to be fetched",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\ResponseMessage(code=404, message="House not found")
 *    )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/houses/:id', function($id) use($DevAAC) {
    $house = House::findOrFail($id);
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($house->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/houses",
 *  @SWG\Api(
 *    path="/houses/{id}/lists",
 *    description="Operations on houses",
 *    @SWG\Operation(
 *      summary="Get house lists based on ID or name",
 *      method="GET",
 *      type="array[HouseList]",
 *      nickname="getHouseListsByID",
 *      @SWG\Parameter( name="id",
 *                      description="ID of House that lists needs to be fetched",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\ResponseMessage(code=404, message="House not found")
 *    )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/houses/:id/lists', function($id) use($DevAAC) {
    $house = House::findOrFail($id);
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($house->lists->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/houses",
 *  @SWG\Api(
 *    path="/houses/{id}/bid",
 *    description="Operations on houses",
 *    @SWG\Operation(
 *      summary="Bid on house by ID",
 *      notes="Admins can bid with any player",
 *      method="POST",
 *      type="House",
 *      nickname="bidHouseByID",
 *      @SWG\Parameter( name="id",
 *                      description="ID of House to bid on",
 *                      paramType="path",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\Parameter( name="player_id",
 *                      description="ID of player which bids",
 *                      paramType="form",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\Parameter( name="bid",
 *                      description="the amount of bid",
 *                      paramType="form",
 *                      required=true,
 *                      type="integer"),
 *      @SWG\ResponseMessage(code=400, message="Bad request"),
 *      @SWG\ResponseMessage(code=401, message="Not logged in"),
 *      @SWG\ResponseMessage(code=402, message="Not enough money in player's bank"),
 *      @SWG\ResponseMessage(code=403, message="Player not on authenticated account"),
 *      @SWG\ResponseMessage(code=404, message="House not found / player not found"),
 *      @SWG\ResponseMessage(code=405, message="Exceeded limit of houses per player/acount"),
 *      @SWG\ResponseMessage(code=409, message="The bid is too low"),
 *      @SWG\ResponseMessage(code=410, message="Auction has ended"),
 *      @SWG\ResponseMessage(code=412, message="House not on auction")
 *    )
 *  )
 * )
 */
$DevAAC->post(ROUTES_API_PREFIX.'/houses/:id/bid', function($id) use($DevAAC) {
    if( ! $DevAAC->auth_account )
        throw new InputErrorException('You are not logged in.', 401);

    $request = $DevAAC->request;
    $house = House::findOrFail($id);

    if($house->owner()->first() instanceof Player)
        throw new InputErrorException('This house is not on auction, '.$house->owner()->first()->name.' owns it.', 412);

    if($house->bid_end !== 0 && new DateTime() > $house->bid_end)
        throw new InputErrorException('Auction has ended.', 410);

    if($request->getAPIParam('bid') < $house->bid + HOUSES_BID_RAISE
        || $request->getAPIParam('bid') < $house->bid + $house->bid * HOUSES_BID_RAISE_PERCENT)
        throw new InputErrorException('The bid is too low! You need to offer at least '.max($house->bid + HOUSES_BID_RAISE,
            $house->bid + $house->bid * HOUSES_BID_RAISE_PERCENT), 409);

    $player = Player::findOrFail($request->getAPIParam('player_id'));

    if($player->account->id != $DevAAC->auth_account->id && !$DevAAC->auth_account->isGod())
        throw new InputErrorException('You do not have permission to bid with this player.', 403);

    if( count($player->houses()->get()->toArray()) + count($player->houseBids()->get()->toArray()) >= HOUSES_PER_PLAYER )
        throw new InputErrorException('Your player already owns or participates in an auction for a maximum number of houses ('.HOUSES_PER_PLAYER.')!', 405);

    if( count($player->account->houses()->get()->toArray()) + count($player->account->houseBids()->get()->toArray()) >= HOUSES_PER_ACCOUNT )
        throw new InputErrorException('Your account already owns or participates in an auction for a maximum number of houses ('.HOUSES_PER_ACCOUNT.')!', 405);

    if($player->balance < $request->getAPIParam('bid') + $house->rent)
        throw new InputErrorException('You do not have enough money! You need the bid amount plus '.$house->rent.' for first rent payment.', 402);

    $house->highest_bidder = $player->id; // this would break JSON output: $house->highestBidder()->associate($player);
    $house->bid = $request->getAPIParam('bid');
    $house->last_bid = new DateTime();

    if($house->bid_end === 0)
    {
        $house->bid_end = new DateTime();
        $house->bid_end = $house->bid_end->add(new DateInterval(HOUSES_AUCTION_TIME));
    }

    $house->save();

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($house->toJson(JSON_PRETTY_PRINT));
});
