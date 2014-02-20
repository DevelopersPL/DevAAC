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
class AccountBan extends \Illuminate\Database\Eloquent\Model {
    /**
     * @SWG\Property(name="account_id", type="integer")
     * @SWG\Property(name="reason", type="string")
     * @SWG\Property(name="banned_at", type="integer")
     * @SWG\Property(name="expires_at", type="integer")
     * @SWG\Property(name="banned_by", type="integer")
     */
    public $timestamps = false;

    protected $guarded = array();

    public function account()
    {
        return $this->belongsTo('DevAAC\Models\Account');
    }

    public function bannedBy()
    {
        return $this->belongsTo('DevAAC\Models\Player');
    }
}
