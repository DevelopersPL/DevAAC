<?php
/**
 * Developer: Daniel
 * Date: 2/14/14
 * Time: 1:33 AM
 */
namespace App\models;

/**
 * @SWG\Model()
 */
class Player extends \Illuminate\Database\Eloquent\Model {

    public $timestamps = false;

    protected $guarded = array('id');

    public function account()
    {
        return $this->belongsTo('Account');
    }
}
