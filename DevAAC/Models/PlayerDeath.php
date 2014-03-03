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

use DevAAC\Helpers\DateTime;

// https://github.com/illuminate/database/blob/master/Eloquent/Model.php
// https://github.com/otland/forgottenserver/blob/master/schema.sql

/**
 * @SWG\Model(required="['player_id','time','level','killed_by','is_player','mostdamage_by','mostdamage_is_player','unjustified','mostdamage_unjustified']")
 */
class PlayerDeath extends \Illuminate\Database\Eloquent\Model {
    /**
     * @SWG\Property(name="player_id", type="integer")
     * @SWG\Property(name="time", type="integer")
     * @SWG\Property(name="level", type="integer")
     * @SWG\Property(name="killed_by", type="string")
     * @SWG\Property(name="is_player", type="boolean")
     * @SWG\Property(name="mostdamage_by", type="string")
     * @SWG\Property(name="mostdamage_is_player", type="boolean")
     * @SWG\Property(name="unjustified", type="boolean")
     * @SWG\Property(name="mostdamage_unjustified", type="boolean")
     */
    public $timestamps = false;

    protected $primaryKey = null;

    public $incrementing = false;

    public function player()
    {
        return $this->belongsTo('DevAAC\Models\Player');
    }

    public function getTimeAttribute()
    {
        $date = new DateTime();
        $date->setTimestamp($this->attributes['time']);
        return $date;
    }

    public function setTimeAttribute($d)
    {
        if($d instanceof \DateTime)
            $this->attributes['time'] = $d->getTimestamp();
        elseif((int)$d != (string)$d) { // it's not a UNIX timestamp
            $dt = new DateTime($d);
            $this->attributes['time'] = $dt->getTimestamp();
        } else // it is a UNIX timestamp
            $this->attributes['time'] = $d;
    }

    public function getIsPlayerAttribute()
    {
        return (bool)$this->attributes['is_player'];
    }

    public function setIsPlayerAttribute($v)
    {
        $this->attributes['is_player'] = (int)$v;
    }

    public function getMostdamageIsPlayerAttribute()
    {
        return (bool)$this->attributes['mostdamage_is_player'];
    }

    public function setMostdamageIsPlayerAttribute($v)
    {
        $this->attributes['mostdamage_is_player'] = (int)$v;
    }

    public function getUnjustifiedAttribute()
    {
        return (bool)$this->attributes['unjustified'];
    }

    public function setUnjustifiedAttribute($v)
    {
        $this->attributes['unjustified'] = (int)$v;
    }

    public function getMostdamageUnjustifiedAttribute()
    {
        return (bool)$this->attributes['mostdamage_unjustified'];
    }

    public function setMostdamageUnjustifiedAttribute($v)
    {
        $this->attributes['mostdamage_unjustified'] = (int)$v;
    }
}
