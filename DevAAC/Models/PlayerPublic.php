<?php
/**
 * Developer: Daniel
 * Date: 2/19/14
 * Time: 4:10 PM
 */

namespace DevAAC\Models;

class PlayerPublic extends Player {

    protected $table = 'players';

    protected $hidden = array(
        'health', 'mana', 'manaspent', 'conditions', 'lastip', 'save', 'offlinetraining_time', 'offlinetraining_skill',
        'skill_fist_tries', 'skill_club_tries', 'skill_sword_tries', 'skill_axe_tries','skill_dist_tries',
        'skill_shielding_tries', 'skill_fishing_tries'
    );

}
