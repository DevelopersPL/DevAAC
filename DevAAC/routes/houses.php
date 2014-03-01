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

/**
 * @SWG\Resource(
 *  basePath="/api",
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
 *  basePath="/api",
 *  resourcePath="/houses",
 *  @SWG\Api(
 *    path="/houses/{id}",
 *    description="Operations on houses",
 *    @SWG\Operation(
 *      summary="Get house based on ID or name",
 *      method="GET",
 *      type="House",
 *      nickname="getGuildByID",
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
