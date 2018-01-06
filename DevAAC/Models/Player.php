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

/**
 * @SWG\Model(required="['name', 'vocation', 'sex']")
 */
class Player extends \Illuminate\Database\Eloquent\Model {

    /**
     * @SWG\Property(name="id", type="integer")
     * @SWG\Property(name="name", type="string")
     * @SWG\Property(name="group_id", type="integer")
     * @SWG\Property(name="account_id", type="integer")
     * @SWG\Property(name="level", type="integer")
     * @SWG\Property(name="vocation", type="integer")
     * @SWG\Property(name="health", type="integer")
     * @SWG\Property(name="healthmax", type="integer")
     * @SWG\Property(name="experience", type="integer")
     * @SWG\Property(name="lookbody", type="integer")
     * @SWG\Property(name="lookfeet", type="integer")
     * @SWG\Property(name="lookhead", type="integer")
     * @SWG\Property(name="looklegs", type="integer")
     * @SWG\Property(name="looktype", type="integer")
     * @SWG\Property(name="lookaddons", type="integer")
     * @SWG\Property(name="maglevel", type="integer")
     * @SWG\Property(name="mana", type="integer")
     * @SWG\Property(name="manamax", type="integer")
     * @SWG\Property(name="manaspent", type="integer")
     * @SWG\Property(name="soul", type="integer")
     * @SWG\Property(name="town_id", type="integer")
     * @SWG\Property(name="posx", type="integer")
     * @SWG\Property(name="posy", type="integer")
     * @SWG\Property(name="posz", type="integer")
     * @SWG\Property(name="conditions", type="integer")
     * @SWG\Property(name="cap", type="integer")
     * @SWG\Property(name="sex", type="integer")
     * @SWG\Property(name="lastlogin", type="integer")
     * @SWG\Property(name="lastip", type="integer")
     * @SWG\Property(name="save", type="integer")
     * @SWG\Property(name="skull", type="integer")
     * @SWG\Property(name="skulltime", type="integer")
     * @SWG\Property(name="lastlogout", type="integer")
     * @SWG\Property(name="blessings", type="integer")
     * @SWG\Property(name="onlinetime", type="integer")
     * @SWG\Property(name="deletion", type="integer")
     * @SWG\Property(name="balance", type="integer")
     * @SWG\Property(name="offlinetraining_time", type="integer")
     * @SWG\Property(name="offlinetraining_skill", type="integer")
     * @SWG\Property(name="stamina", type="integer")
     * @SWG\Property(name="skill_fist", type="integer")
     * @SWG\Property(name="skill_fist_tries", type="integer")
     * @SWG\Property(name="skill_club", type="integer")
     * @SWG\Property(name="skill_club_tries", type="integer")
     * @SWG\Property(name="skill_sword", type="integer")
     * @SWG\Property(name="skill_sword_tries", type="integer")
     * @SWG\Property(name="skill_axe", type="integer")
     * @SWG\Property(name="skill_axe_tries", type="integer")
     * @SWG\Property(name="skill_dist", type="integer")
     * @SWG\Property(name="skill_dist_tries", type="integer")
     * @SWG\Property(name="skill_shielding", type="integer")
     * @SWG\Property(name="skill_shielding_tries", type="integer")
     * @SWG\Property(name="skill_fishing", type="integer")
     * @SWG\Property(name="skill_fishing_tries", type="integer")
     * @SWG\Property(name="online", type="boolean")
     */
    public $timestamps = false;

    protected $guarded = array('id');

    protected $attributes = array(
        'id' => 0,
        'name' => 0,
        'group_id' => 1,
        'account_id' => 0,
        'level' => 1,
        'vocation' => 1, // we set some vocation by default in case we need it for a formula before setting it
        'health' => 150,
        'healthmax' => 150,
        'experience' => 0,
        'lookbody' => 0,
        'lookfeet' => 0,
        'lookhead' => 0,
        'looklegs' => 0,
        'looktype' => 136,
        'lookaddons' => 0,
        'maglevel' => 0,
        'mana' => 0,
        'manamax' => 0,
        'manaspent' => 0,
        'soul' => 0,
        'town_id' => NEW_PLAYER_TOWN_ID,
        'posx' => 0,
        'posy' => 0,
        'posz' => 0,
        'conditions' => '',
        'cap' => 0,
        'sex' => 0,
        'lastlogin' => 0,
        'lastip' => 0,
        'save' => 1,
        'skull' => 0,
        'skulltime' => 0,
        'lastlogout' => 0,
        'blessings' => 0,
        'onlinetime' => 0,
        'deletion' => 0,
        'balance' => 0,
        'offlinetraining_time' => 43200,
        'offlinetraining_skill' => -1,
        'stamina' => 2520,
        'skill_fist' => 10,
        'skill_fist_tries' => 0,
        'skill_club' => 10,
        'skill_club_tries' => 0,
        'skill_sword' => 10,
        'skill_sword_tries' => 0,
        'skill_axe' => 10,
        'skill_axe_tries' => 0,
        'skill_dist' => 10,
        'skill_dist_tries' => 0,
        'skill_shielding' => 10,
        'skill_shielding_tries' => 0,
        'skill_fishing' => 10,
        'skill_fishing_tries' => 0
    );

    // this is redundant
    protected $hidden = array(
        'health', 'mana', 'manaspent', 'conditions', 'lastip', 'save', 'offlinetraining_time', 'offlinetraining_skill',
        'skill_fist_tries', 'skill_club_tries', 'skill_sword_tries', 'skill_axe_tries','skill_dist_tries',
        'skill_shielding_tries', 'skill_fishing_tries'
    );

    protected $visible = array(
        'id', 'name', 'group_id', 'account_id', 'level', 'vocation', 'healthmax', 'experience', 'lookbody', 'lookfeet',
        'lookhead', 'looklegs', 'looktype', 'lookaddons', 'maglevel', 'manamax', 'soul', 'town_id', 'town_name', 'posx', 'posy', 'posz',
        'cap', 'sex', 'lastlogin', 'skull', 'skulltime', 'lastlogout', 'blessings', 'onlinetime', 'deletion', 'balance',
        'stamina', 'skill_fist', 'skill_club', 'skill_sword', 'skill_axe', 'skill_dist', 'skill_shielding', 'skill_fishing'
    );

    public function getVisibleFields() {
        return $this->visible;
    }

    protected $appends = array('is_online', 'membership', 'town_name');

    public function account()
    {
        return $this->belongsTo('DevAAC\Models\Account');
    }

    public function spells()
    {
        return $this->hasMany('DevAAC\Models\PlayerSpell');
    }

    public function deaths()
    {
        return $this->hasMany('DevAAC\Models\PlayerDeath');
    }

    public function namelock()
    {
        return $this->hasOne('DevAAC\Models\PlayerNamelock');
    }

    public function online()
    {
        return $this->hasOne('DevAAC\Models\PlayerOnline');
    }

    public function getIsOnlineAttribute()
    {
        return $this->online !== null;
    }

    public function guildMembership()
    {
        return $this->hasOne('DevAAC\Models\GuildMembership');
    }

    public function getMembershipAttribute()
    {
        return $this->guildMembership;
    }

    public function houses()
    {
        return $this->hasMany('DevAAC\Models\House', 'owner');
    }

    public function houseBids()
    {
        return $this->hasMany('DevAAC\Models\House', 'highest_bidder');
    }

    public function town()
    {
        return $this->belongsTo('DevAAC\Models\Town');
    }

    public function getTownNameAttribute()
    {
        if(!is_null($this->town))
            return $this->town->name;
    }

    public function setLevelAttribute($level)
    {
        // http://tibia.wikia.com/wiki/Formula
        $this->attributes['level'] = $level;

        // experience
        $this->attributes['experience'] = 50/3*(pow($level, 3) - 6*pow($level, 2) + 17*$level - 12);

        $left_rook_at = 8;

        // cap
        if( in_array($this->vocation, array(0, 1, 2, 5, 6, 9, 10)) ) // rookies, sorcerers and druids
            $cap = 10 * ( $level + 39 );
        elseif( in_array($this->vocation, array(3, 7, 11)) ) // paladins
            $cap = 10 * ( 2 * $level - $left_rook_at + 39 );
        else                                                // knights
            $cap = 5 * ( 5 * $level - 5 * $left_rook_at + 94 );
        $this->attributes['cap'] = $cap;

        // HP
        if( in_array($this->vocation, array(0, 1, 2, 5, 6, 9, 10)) ) // rookies, sorcerers and druids
            $hp = 5 * ( $level + 29 );
        elseif( in_array($this->vocation, array(3, 7, 11)) ) // paladins
            $hp = 5 * ( 2 * $level - $left_rook_at + 29 );
        else                                                // knights
            $hp = 5 * ( 3 * $level - 2 * $left_rook_at + 29 );
        $this->attributes['healthmax'] = $this->attributes['health'] = $hp;

        // mana
        if( in_array($this->vocation, array(1, 2, 5, 6, 9, 10)) ) // sorcerers and druids
            $mana = 5 * ( 6 * $level - 5 * $left_rook_at );
        elseif( in_array($this->vocation, array(3, 7, 11)) ) // paladins
            $mana = 5 * ( 3 * $level - 2 * $left_rook_at );
        else                                                // rookies, knights
            $mana = 5 * $level;
        $this->attributes['manamax'] = $this->attributes['mana'] = $mana;
    }

    public function setMaglevelAttribute($mlvl)
    {
        // http://tibia.wikia.com/wiki/Formula
        $this->attributes['maglevel'] = $mlvl;

        // manaspent
        if( in_array($this->vocation, array(1, 2, 5, 6, 9, 10)) ) // sorcerers and druids
            $m = 1.1;
        elseif( in_array($this->vocation, array(3, 7, 11)) ) // paladins
            $m = 1.4;
        else                                                // rookies, knights
            $m = 3;
        $this->attributes['manaspent'] = (1600 * (pow($m, $mlvl) - 1) ) / ( $m - 1 );
    }

    public function getLastipAttribute($longip)
    {
        return long2ip(chbo($longip));
    }

    public function setLastipAttribute($longip)
    {
        $this->attributes['lastip'] = chbo(ip2long($longip));
    }
}
