<?php
/**
 * Developer: Daniel
 * Date: 2/14/14
 * Time: 1:33 AM
 */
namespace DevAAC\Models;

// https://github.com/illuminate/database/blob/master/Eloquent/Model.php
// https://github.com/otland/forgottenserver/blob/master/schema.sql
class Account extends \Illuminate\Database\Eloquent\Model {

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
