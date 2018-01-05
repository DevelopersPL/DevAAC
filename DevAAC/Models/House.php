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
 * @SWG\Model(required="['id','owner','paid','warnings','name','rent','town_id','bid','bid_end','highest_bidder','size','beds']")
 */
class House extends \Illuminate\Database\Eloquent\Model {
    /**
     * @SWG\Property(name="id", type="integer")
     * @SWG\Property(name="owner", type="integer")
     * @SWG\Property(name="paid", type="DateTime::ISO8601")
     * @SWG\Property(name="warnings", type="integer")
     * @SWG\Property(name="name", type="string")
     * @SWG\Property(name="rent", type="integer")
     * @SWG\Property(name="town_id", type="integer")
     * @SWG\Property(name="bid", type="integer")
     * @SWG\Property(name="bid_end", type="DateTime::ISO8601")
     * @SWG\Property(name="last_bid", type="integer")
     * @SWG\Property(name="highest_bidder", type="integer")
     * @SWG\Property(name="size", type="integer")
     * @SWG\Property(name="beds", type="integer")
     */
    public $timestamps = false;

    protected $guarded = array('id');

    protected $hidden = array('last_bid', 'town');

    protected $appends = array('town_name');

    public function owner()
    {
        return $this->belongsTo('DevAAC\Models\Player', 'owner');
    }

    public function town()
    {
        return $this->belongsTo('DevAAC\Models\Town');
    }

    public function getTownNameAttribute()
    {
        return $this->town->name;
    }

    public function getPaidAttribute()
    {
        if($this->attributes['paid'] === 0)
            return 0;

        $date = new DateTime();
        $date->setTimestamp($this->attributes['paid']);
        return $date;
    }

    public function setPaidAttribute($d)
    {
        if($d instanceof \DateTime)
            $this->attributes['paid'] = $d->getTimestamp();
        elseif((int)$d != (string)$d) { // it's not a UNIX timestamp
            $dt = new DateTime($d);
            $this->attributes['paid'] = $dt->getTimestamp();
        } else // it is a UNIX timestamp
            $this->attributes['paid'] = $d;
    }

    public function getBidEndAttribute()
    {
        if($this->attributes['bid_end'] === 0)
            return 0;

        $date = new DateTime();
        $date->setTimestamp($this->attributes['bid_end']);
        return $date;
    }

    public function setBidEndAttribute($d)
    {
        if($d instanceof \DateTime)
            $this->attributes['bid_end'] = $d->getTimestamp();
        elseif((int)$d != (string)$d) { // it's not a UNIX timestamp
            $dt = new DateTime($d);
            $this->attributes['bid_end'] = $dt->getTimestamp();
        } else // it is a UNIX timestamp
            $this->attributes['bid_end'] = $d;
    }

    public function highestBidder()
    {
        return $this->belongsTo('DevAAC\Models\Player', 'highest_bidder');
    }

    public function lists()
    {
        return $this->hasMany('DevAAC\Models\HouseList');
    }
}
