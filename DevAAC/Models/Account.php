<?php
/**
 * Developer: Daniel
 * Date: 2/14/14
 * Time: 1:33 AM
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
        elseif((string) (int) $d !== $d) { // it's not a UNIX timestamp
            $this->attributes['creation'] = DateTime($d)->getTimestamp();
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
}
