<?php
/**
 * Developer: Daniel
 * Date: 2/14/14
 * Time: 1:33 AM
 */
namespace App\models;

class Account extends \Illuminate\Database\Eloquent\Model {

    public $timestamps = false;

    protected $guarded = array('id');

    public function players()
    {
        return $this->hasMany('App\models\Player');
    }

    public function comparePassword($pass) {
        return $this->password === sha1($pass);
    }
}
