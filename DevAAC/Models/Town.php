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

namespace DevAAC\Models;

// https://github.com/illuminate/database/blob/master/Eloquent/Model.php
// https://github.com/otland/forgottenserver/blob/master/schema.sql

/**
 * @SWG\Model(required="['id','name','posx','posy','posz']")
 */
class Town extends \Illuminate\Database\Eloquent\Model {
    /**
     * @SWG\Property(name="id", type="integer")
     * @SWG\Property(name="name", type="string")
     * @SWG\Property(name="posx", type="integer")
     * @SWG\Property(name="posy", type="integer")
     * @SWG\Property(name="posz", type="integer")
     */
    public $timestamps = false;

    // this is redundant
    protected $hidden = array(
        'posx', 'posy', 'posz'
    );

    protected $visible = array(
        'id', 'name'
    );

    public function players()
    {
        return $this->hasMany('DevAAC\Models\Player');
    }

    public function houses()
    {
        return $this->hasMany('DevAAC\Models\House');
    }
}
