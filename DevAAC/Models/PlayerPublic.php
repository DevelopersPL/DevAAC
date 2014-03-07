<?php
/**
 * DevAAC
 *
 * Automatic Account Creator by developers.pl for TFS 1.0
 *
 *
 * LICENSE: Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
 * PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE
 * FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package    DevAAC
 * @author     Daniel Speichert <daniel@speichert.pl>
 * @author     Wojciech Guziak <wojciech@guziak.net>
 * @copyright  2014 Developers.pl
 * @license    http://opensource.org/licenses/MIT MIT
 * @version    master
 * @link       https://github.com/DevelopersPL/DevAAC
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
