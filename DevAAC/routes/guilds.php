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

/**
 * @SWG\Resource(
 *  basePath="/api",
 *  resourcePath="/guilds",
 *  @SWG\Api(
 *    path="/guilds",
 *    description="Operations on guilds",
 *    @SWG\Operation(
 *      summary="Get all guilds",
 *      notes="",
 *      method="GET",
 *      type="Guild",
 *      nickname="getGuilds"
 *   )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/guilds', function() use($DevAAC) {
    $guilds = Guild::all();
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($guilds->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api",
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
 *  basePath="/api",
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
 *      @SWG\ResponseMessage(code=404, message="Guild not found")
 *    )
 *  )
 * )
 */
$DevAAC->get(ROUTES_API_PREFIX.'/guilds/:id', function($id) use($DevAAC) {
    try {
        $guild = Guild::findOrFail($id);
    } catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        $guild = Guild::where('name', $id)->first();
        if(!$guild)
            throw $e;
    }
    $DevAAC->response->headers->set('Content-Type', 'application/json');
    $DevAAC->response->setBody($guild->toJson(JSON_PRETTY_PRINT));
});

/**
 * @SWG\Resource(
 *  basePath="/api",
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
