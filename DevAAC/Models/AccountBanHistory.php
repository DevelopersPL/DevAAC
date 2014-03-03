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
 * @SWG\Model(required="['id','account_id','reason','banned_at','expired_at','banned_by']")
 */
class AccountBanHistory extends \Illuminate\Database\Eloquent\Model {
    /**
     * @SWG\Property(name="id", type="integer")
     * @SWG\Property(name="account_id", type="integer")
     * @SWG\Property(name="reason", type="string")
     * @SWG\Property(name="banned_at", type="DateTime::ISO8601")
     * @SWG\Property(name="expired_at", type="DateTime::ISO8601")
     * @SWG\Property(name="banned_by", type="integer")
     */
    public $timestamps = false;

    protected $guarded = array('id');

    protected $table = 'account_ban_history';

    public function account()
    {
        return $this->belongsTo('DevAAC\Models\Account');
    }

    public function bannedBy()
    {
        return $this->belongsTo('DevAAC\Models\Player', 'banned_by');
    }

    public function getBannedAtAttribute()
    {
        $date = new DateTime();
        $date->setTimestamp($this->attributes['banned_at']);
        return $date;
    }

    public function setBannedAtAttribute($d)
    {
        if($d instanceof \DateTime)
            $this->attributes['banned_at'] = $d->getTimestamp();
        elseif((int)$d != (string)$d) { // it's not a UNIX timestamp
            $dt = new DateTime($d);
            $this->attributes['banned_at'] = $dt->getTimestamp();
        } else // it is a UNIX timestamp
            $this->attributes['banned_at'] = $d;
    }

    public function getExpiredAtAttribute()
    {
        $date = new DateTime();
        $date->setTimestamp($this->attributes['expired_at']);
        return $date;
    }

    public function setExpiredAtAttribute($d)
    {
        if($d instanceof \DateTime)
            $this->attributes['expired_at'] = $d->getTimestamp();
        elseif((int) $d != (string) $d) { // it's not a UNIX timestamp
            $dt = new DateTime($d);
            $this->attributes['expired_at'] = $dt->getTimestamp();
        } else // it is a UNIX timestamp
            $this->attributes['expired_at'] = $d;
    }
}
