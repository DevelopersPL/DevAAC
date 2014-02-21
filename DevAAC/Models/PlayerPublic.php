<?php
/**
 * Developer: Daniel
 * Date: 2/19/14
 * Time: 4:10 PM
 */

namespace DevAAC\Models;

use Illuminate\Database\Capsule\Manager as Capsule;

class PlayerPublic extends Player {

    protected $table = 'players';

    // not needed
    protected $hidden = array(
        'health', 'mana', 'manaspent', 'conditions', 'lastip', 'save', 'offlinetraining_time', 'offlinetraining_skill',
        'skill_fist_tries', 'skill_club_tries', 'skill_sword_tries', 'skill_axe_tries','skill_dist_tries',
        'skill_shielding_tries', 'skill_fishing_tries'
    );

    protected $visible = array(
        'id', 'name', 'group_id', 'account_id', 'level', 'vocation', 'healthmax', 'experience', 'lookbody', 'lookfeet',
        'lookhead', 'looklegs', 'looktype', 'lookaddons', 'maglevel', 'manamax', 'soul', 'town_id', 'posx', 'posy', 'posz',
        'cap', 'sex', 'lastlogin', 'skull', 'skulltime', 'lastlogout', 'blessings', 'onlinetime', 'deletion', 'balance',
        'stamina', 'skill_fist', 'skill_club', 'skill_sword', 'skill_axe', 'skill_dist', 'skill_shielding', 'skill_fishing'
    );

    public function getVisibleFields() {
        return $this->visible;
    }

}
