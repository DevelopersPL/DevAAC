<?php
/**
 * Developer: Daniel
 * Date: 2/19/14
 * Time: 4:10 PM
 */

namespace DevAAC\Models;

class AccountPublic extends Account {

    protected $table = 'accounts';

    protected $hidden = array('name', 'password', 'email');

}
