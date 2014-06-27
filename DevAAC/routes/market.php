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

use DevAAC\Models\MarketHistory;
use DevAAC\Models\MarketOffer;

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/market",
 *  @SWG\Api(
 *    path="/market/history",
 *    description="Operations on market",
 *    @SWG\Operation(
 *      summary="Get market history",
 *      notes="",
 *      method="GET",
 *      type="MarketHistory",
 *      nickname="getMarketHistory",
 *      @SWG\Parameter( name="embed",
 *                      description="Pass player to embed player object",
 *                      paramType="query",
 *                      required=false,
 *                      type="string list separated by comma")
 *   )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/market/history', function() use($DevAAC) {
    $req = $DevAAC->request;

    if($req->get('embed') == 'player')
        $history = MarketHistory::with('player')->get();
    else
        $history = MarketHistory::all();

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($history->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api/v1",
 *  resourcePath="/market",
 *  @SWG\Api(
 *    path="/market/offers",
 *    description="Operations on market",
 *    @SWG\Operation(
 *      summary="Get market offers",
 *      notes="",
 *      method="GET",
 *      type="MarketOffer",
 *      nickname="getMarketHistory",
 *      @SWG\Parameter( name="embed",
 *                      description="Pass player to embed player object",
 *                      paramType="query",
 *                      required=false,
 *                      type="string list separated by comma")
 *   )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/market/offers', function() use($DevAAC) {
    $req = $DevAAC->request;

    // TODO: should not expose player if offer is anonymous
    if($req->get('embed') == 'player')
        $offers = MarketOffer::with('player')->get();
    else
        $offers = MarketOffer::all();

    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($offers->toJson(JSON_PRETTY_PRINT));
});
