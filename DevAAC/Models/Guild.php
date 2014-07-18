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
 * @SWG\Model(required="['name','ownerid','creationdata','motd']")
 */
class Guild extends \Illuminate\Database\Eloquent\Model {
    /**
     * @SWG\Property(name="id", type="integer")
     * @SWG\Property(name="name", type="string")
     * @SWG\Property(name="ownerid", type="integer")
     * @SWG\Property(name="creationdata", type="DateTime::ISO8601")
     * @SWG\Property(name="motd", type="string")
     */

    public $timestamps = false;

    protected $guarded = array('id');

    protected $attributes = array(
        'motd' => ''
    );

    protected $appends = array('total_level', 'average_level', 'members_count', 'online_members');

    public function getCreationdataAttribute()
    {
        $date = new DateTime();
        $date->setTimestamp($this->attributes['creationdata']);
        return $date;
    }

    public function setCreationdataAttribute($d)
    {
        if($d instanceof \DateTime)
            $this->attributes['creationdata'] = $d->getTimestamp();
        elseif((int)$d != (string)$d) { // it's not a UNIX timestamp
            $dt = new DateTime($d);
            $this->attributes['creationdata'] = $dt->getTimestamp();
        } else // it is a UNIX timestamp
            $this->attributes['creationdata'] = $d;
    }

    public function owner()
    {
        return $this->belongsTo('DevAAC\Models\Player', 'ownerid');
    }

    public function members()
    {
        return $this->hasManyThrough('DevAAC\Models\Player', 'DevAAC\Models\GuildMembership', 'guild_id', 'id');
    }

    public function memberships()
    {
        return $this->hasMany('DevAAC\Models\GuildMembership');
    }

    public function invitations()
    {
        return $this->hasMany('DevAAC\Models\GuildInvite');
    }

    public function ranks()
    {
        return $this->hasMany('DevAAC\Models\GuildRank');
    }

    public function getTotalLevelAttribute()
    {
        $sum = 0;
        foreach($this->members as $member)
            $sum += $member->level;
        return $sum;
    }

    public function getAverageLevelAttribute()
    {
        $sum = 0;
        foreach($this->members as $member)
            $sum += $member->level;
        return round($sum/count($this->members));
    }

    public function getMembersCountAttribute()
    {
        return count($this->members);
    }

    public function getOnlineMembersAttribute()
    {
        $count = 0;
        foreach($this->members as $member)
            if($member->is_online)
                $count++;
        return $count;
    }
}
