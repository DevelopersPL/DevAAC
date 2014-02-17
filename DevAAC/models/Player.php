<?php
/**
 * Developer: Daniel
 * Date: 2/14/14
 * Time: 1:33 AM
 */
namespace DevAAC\Models;

/**
 * @SWG\Model()
 */
class Player extends \Illuminate\Database\Eloquent\Model {

    public $timestamps = false;

    protected $guarded = array('id');

    protected $attributes = array(
        'vocation' => 1, // we set some vocation by default in case we need it for a formula before setting it
    );

    public function account()
    {
        return $this->belongsTo('DevAAC\Models\Account');
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
}
