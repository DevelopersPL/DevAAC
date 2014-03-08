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
 * @SWG\Model(required="['id','guild1','guild2','name1','name2']")
 */
class GuildWar extends \Illuminate\Database\Eloquent\Model {
    /**
     * @SWG\Property(name="id", type="integer")
     * @SWG\Property(name="guild1", type="integer")
     * @SWG\Property(name="guild2", type="integer")
     * @SWG\Property(name="name1", type="string")
     * @SWG\Property(name="name2", type="string")
     * @SWG\Property(name="status", type="integer")
     * @SWG\Property(name="started", type="DateTime::ISO8601")
     * @SWG\Property(name="ended", type="DateTime::ISO8601")
     */

    public $timestamps = false;

    protected $guarded = array('id');

    public function getStartedAttribute()
    {
        $date = new DateTime();
        $date->setTimestamp($this->attributes['started']);
        return $date;
    }

    public function setStartedAttribute($d)
    {
        if($d instanceof \DateTime)
            $this->attributes['started'] = $d->getTimestamp();
        elseif((int)$d != (string)$d) { // it's not a UNIX timestamp
            $dt = new DateTime($d);
            $this->attributes['started'] = $dt->getTimestamp();
        } else // it is a UNIX timestamp
            $this->attributes['started'] = $d;
    }

    public function getEndedAttribute()
    {
        $date = new DateTime();
        $date->setTimestamp($this->attributes['ended']);
        return $date;
    }

    public function setEndedAttribute($d)
    {
        if($d instanceof \DateTime)
            $this->attributes['ended'] = $d->getTimestamp();
        elseif((int)$d != (string)$d) { // it's not a UNIX timestamp
            $dt = new DateTime($d);
            $this->attributes['ended'] = $dt->getTimestamp();
        } else // it is a UNIX timestamp
            $this->attributes['ended'] = $d;
    }

    public function guild1()
    {
        return $this->belongsTo('DevAAC\Models\Guild', 'guild1');
    }

    public function guild2()
    {
        return $this->belongsTo('DevAAC\Models\Guild', 'guild2');
    }
}
