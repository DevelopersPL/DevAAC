<?php
/**
 * Developer: Daniel
 * Date: 2/14/14
 * Time: 1:33 AM
 */
namespace DevAAC\Models;

// https://github.com/illuminate/database/blob/master/Eloquent/Model.php
// https://github.com/otland/forgottenserver/blob/master/schema.sql

/**
 * @SWG\Model(required="['id','name','password','type','premdays','lastday','email','creation']")
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
     * @SWG\Property(name="creation", type="UNIX timestamp")
     */
    public $timestamps = false;

    protected $guarded = array('id');

    protected $dates = array('creation');

    public function players()
    {
        return $this->hasMany('DevAAC\Models\Player');
    }

    public function setPasswordAttribute($pass)
    {
        $this->attributes['password'] = sha1($pass);
    }

    public function comparePassword($pass) {
        return $this->password === sha1($pass);
    }
}
