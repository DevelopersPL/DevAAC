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
 * @SWG\Model(required="['id','player_id','sale','itemtype','amount','created','anonymous','price']")
 */
class MarketOffer extends \Illuminate\Database\Eloquent\Model {
    /**
     * @SWG\Property(name="id", type="integer")
     * @SWG\Property(name="player_id", type="integer")
     * @SWG\Property(name="sale", type="boolean")
     * @SWG\Property(name="itemtype", type="integer")
     * @SWG\Property(name="amount", type="integer")
     * @SWG\Property(name="created", type="DateTime::ISO8601")
     * @SWG\Property(name="anonymous", type="boolean")
     * @SWG\Property(name="price", type="integer")
     */

    protected $table = 'market_history';

    public $timestamps = false;

    public $incrementing = false;

    public function player()
    {
        return $this->belongsTo('DevAAC\Models\Player');
    }

    public function getCreatedAttribute()
    {
        $date = new DateTime();
        $date->setTimestamp($this->attributes['created']);
        return $date;
    }

    public function setCreatedAttribute($d)
    {
        if($d instanceof \DateTime)
            $this->attributes['created'] = $d->getTimestamp();
        elseif((int) $d != (string) $d) { // it's not a UNIX timestamp
            $dt = new DateTime($d);
            $this->attributes['created'] = $dt->getTimestamp();
        } else // it is a UNIX timestamp
            $this->attributes['created'] = $d;
    }
}
