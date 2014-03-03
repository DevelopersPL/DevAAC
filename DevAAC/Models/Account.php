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
 * @SWG\Model(required="['name','password','email']")
 */
class Account extends \Illuminate\Database\Eloquent\Model {
    /**
     * @SWG\Property(name="id", type="integer")
     * @SWG\Property(name="name", type="string")
     * @SWG\Property(name="password", type="SHA1 hash")
     * @SWG\Property(name="type", type="integer")
     * @SWG\Property(name="premdays", type="integer")
     * @SWG\Property(name="lastday", type="integer")
     * @SWG\Property(name="email", type="string")
     * @SWG\Property(name="creation", type="DateTime::ISO8601")
     */
    public $timestamps = false;

    protected $guarded = array('id');

    protected $attributes = array(
        'type' => 1,
        'premdays' => 0,
        'lastday' => 0
    );

    public function getCreationAttribute()
    {
        $date = new DateTime();
        $date->setTimestamp($this->attributes['creation']);
        return $date;
    }

    public function setCreationAttribute($d)
    {
        if($d instanceof \DateTime)
            $this->attributes['creation'] = $d->getTimestamp();
        elseif((int) $d != (string) $d) { // it's not a UNIX timestamp
            $dt = new DateTime($d);
            $this->attributes['creation'] = $dt->getTimestamp();
        } else // it is a UNIX timestamp
            $this->attributes['creation'] = $d;
    }

    public function players()
    {
        return $this->hasMany('DevAAC\Models\Player');
    }

    public function setPasswordAttribute($pass)
    {
        if( !filter_var($pass, FILTER_VALIDATE_REGEXP,
            array("options" => array("regexp" => "/^[0-9a-f]{40}$/i"))) )
            $pass = sha1($pass);

        $this->attributes['password'] = $pass;
    }

    public function comparePassword($pass)
    {
        return $this->password === sha1($pass);
    }

    public function isAdmin()
    {
        return $this->type >= ACCOUNT_TYPE_ADMIN;
    }

    public function ban()
    {
        return $this->hasOne('DevAAC\Models\AccountBan');
    }

    public function banHistory()
    {
        return $this->hasMany('DevAAC\Models\AccountBanHistory');
    }
}
